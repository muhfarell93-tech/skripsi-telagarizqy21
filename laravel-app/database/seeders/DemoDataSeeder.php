<?php

namespace Database\Seeders;

use App\Models\DetailTransaksi;
use App\Models\LogAktivitas;
use App\Models\Outlet;
use App\Models\Pengeluaran;
use App\Models\PemasukanManual;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Data demonstrasi hanya boleh dibuat di lingkungan local/testing.');
        }
        if (Transaksi::where('is_demo', true)->exists() || PemasukanManual::where('is_demo', true)->exists() || Pengeluaran::where('is_demo', true)->exists()) {
            return;
        }

        $outlet = Outlet::query()->first();
        $kasir = User::where('role', 'kasir')->where('outlet_id', $outlet?->id)->first();
        $products = Produk::where('outlet_id', $outlet?->id)->orderBy('id')->take(3)->get();
        if (!$outlet || !$kasir || $products->isEmpty()) {
            throw new \RuntimeException('Jalankan DatabaseSeeder dahulu agar outlet, akun kasir, dan produk tersedia.');
        }

        PemasukanManual::create([
            'kode_pemasukan' => 'DEMO-INC-'.now()->format('Ymd'),
            'outlet_id' => $outlet->id, 'user_id' => $kasir->id,
            'kategori_pemasukan' => 'Ilustrasi pemasukan lain',
            'keterangan' => 'DEMO — contoh tampilan pemasukan. Bukan bukti penerimaan uang nyata.',
            'nominal' => 125000, 'tanggal_pemasukan' => now()->subDays(2), 'is_demo' => true,
        ]);
        Pengeluaran::create([
            'kode_pengeluaran' => 'DEMO-EXP-'.now()->format('Ymd'),
            'outlet_id' => $outlet->id, 'user_id' => $kasir->id,
            'kategori_pengeluaran' => 'Ilustrasi operasional',
            'keterangan' => 'DEMO — contoh tampilan biaya. Bukan transaksi kas nyata.',
            'nominal' => 45000, 'tanggal_pengeluaran' => now()->subDays(1), 'is_demo' => true,
        ]);

        foreach ([1, 2, 3, 4] as $index) {
            $product = $products[($index - 1) % $products->count()];
            $quantity = $index % 2 + 1;
            $total = $product->harga_jual * $quantity;
            $date = now()->subDays($index)->setTime(9 + $index, 15);
            $transaction = Transaksi::create([
                'kode_invoice' => 'DEMO-'.now()->format('Ymd').'-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                'outlet_id' => $outlet->id, 'user_id' => $kasir->id, 'tanggal_transaksi' => $date,
                'total_item' => $quantity, 'total_harga' => $total, 'total_bayar' => 0,
                'kembalian' => 0, 'metode_pembayaran' => $index % 2 ? 'qris' : 'tunai',
                'status_pembayaran' => $index === 4 ? 'batal' : 'belum_lunas',
                'catatan' => 'DEMO — ilustrasi; tidak ada pembayaran yang diterima.',
                'stok_dikurangi' => false, 'is_demo' => true,
            ]);
            DetailTransaksi::create([
                'transaksi_id' => $transaction->id, 'produk_id' => $product->id,
                'harga_satuan' => $product->harga_jual, 'kuantitas' => $quantity, 'subtotal' => $total,
            ]);
        }

        LogAktivitas::catat($kasir->id, 'DEMO', 'tambah', 'DEMO — aktivitas pengguna contoh untuk pratinjau dashboard.');
    }
}
