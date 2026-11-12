<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class SampleTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $outlet = Outlet::first();
        $user = User::where('role', 'kasir')->first() ?? User::first();
        $products = Produk::all();

        if (!$outlet || !$user || $products->isEmpty()) {
            return;
        }

        // Check if transactions already exist
        if (Transaksi::count() > 0) {
            return;
        }

        $methods = ['tunai', 'qris', 'transfer', 'qris', 'tunai'];
        
        // 7 days of sample transactions & expenses
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->setTime(rand(8, 17), rand(10, 50));
            $txCount = rand(2, 5);

            for ($j = 1; $j <= $txCount; $j++) {
                $itemCount = rand(1, 3);
                $selectedProds = $products->random($itemCount);
                $totalHarga = 0;
                $detailsData = [];

                foreach ($selectedProds as $prod) {
                    $qty = rand(1, 4);
                    $subtotal = $prod->harga_jual * $qty;
                    $totalHarga += $subtotal;
                    $detailsData[] = [
                        'produk_id'    => $prod->id,
                        'harga_satuan' => $prod->harga_jual,
                        'kuantitas'    => $qty,
                        'subtotal'     => $subtotal,
                    ];
                }

                $method = $methods[array_rand($methods)];
                $invNumber = 'TRX-' . $date->format('Ymd') . '-' . str_pad(($i * 10) + $j, 4, '0', STR_PAD_LEFT);

                $trx = Transaksi::create([
                    'kode_invoice'         => $invNumber,
                    'outlet_id'            => $outlet->id,
                    'user_id'              => $user->id,
                    'tanggal_transaksi'    => $date,
                    'total_item'           => count($detailsData),
                    'total_harga'          => $totalHarga,
                    'total_bayar'          => $totalHarga,
                    'kembalian'            => 0,
                    'metode_pembayaran'    => $method,
                    'status_pembayaran'    => 'lunas',
                    'catatan'              => 'Pembelian produk olahan susu kambing',
                    'waktu_lunas'          => $date,
                    'stok_dikurangi'       => true,
                ]);

                foreach ($detailsData as $dt) {
                    $dt['transaksi_id'] = $trx->id;
                    DetailTransaksi::create($dt);
                }
            }

            // Sample operational expense every 2 days
            if ($i % 2 === 0) {
                $kategoriList = ['Pakan Kambing & Konsentrat', 'Kemasan Botol & Label', 'Listrik & Utilitas Peternakan', 'Transportasi Distribusi'];
                $kat = $kategoriList[array_rand($kategoriList)];
                $nominal = rand(8, 25) * 10000;

                Pengeluaran::create([
                    'kode_pengeluaran'     => 'KAS-' . $date->format('Ymd') . '-' . rand(100, 999),
                    'outlet_id'            => $outlet->id,
                    'user_id'              => $user->id,
                    'kategori_pengeluaran' => $kat,
                    'keterangan'           => 'Biaya operasional peternakan Telaga Rizqy 21',
                    'nominal'              => $nominal,
                    'tanggal_pengeluaran'  => $date,
                ]);
            }
        }
    }
}

