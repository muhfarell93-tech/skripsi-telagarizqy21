<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Outlet;
use App\Models\User;
use App\Models\KategoriProduk;
use App\Models\Produk;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoAccounts = [];
        if (app()->environment(['local', 'testing'])) {
            $demoAccounts = [
                ['name' => 'Admin Demo', 'username' => 'demo.admin', 'email' => 'admin.demo@example.test', 'role' => 'admin', 'password' => env('SEED_ADMIN_PASSWORD')],
                ['name' => 'Kasir Demo', 'username' => 'demo.kasir', 'email' => 'kasir.demo@example.test', 'role' => 'kasir', 'password' => env('SEED_CASHIER_PASSWORD')],
            ];

            foreach ($demoAccounts as $account) {
                if (strlen((string) $account['password']) < 4) {
                    throw new \RuntimeException('Set semua SEED_*_PASSWORD minimal 4 karakter di .env untuk membuat akun demo lokal.');
                }
            }
        }

        // 1. Data Outlet Utama
        $outlet = Outlet::firstOrCreate(['kode_outlet' => 'OTL-01'], [
            'nama_outlet' => 'Telaga Rizqy 21 Metro',
            'no_telepon'  => '081234567890',
            'alamat'      => 'Jl. Sepat No. 3 RT/RW 27/11, Kel. Yosodadi, Kec. Metro Timur, Kota Metro',
        ]);

        // Demo accounts exist only in local/testing environments.
        foreach ($demoAccounts as $account) {
            User::firstOrCreate(['username' => $account['username']], [
                'outlet_id' => $outlet->id,
                'name' => $account['name'],
                'username' => $account['username'],
                'email' => $account['email'],
                'password' => Hash::make($account['password']),
                'role' => $account['role'],
                'is_active' => true,
            ]);
        }

        // 3. Kategori Produk Olahan Susu Kambing
        $katBubuk = KategoriProduk::firstOrCreate(['nama_kategori' => 'Susu Bubuk Rizqiku'], [
            'deskripsi'     => 'Susu kambing bubuk premium siap seduh berbagai varian rasa',
        ]);

        $katSegar = KategoriProduk::firstOrCreate(['nama_kategori' => 'Susu Segar & Pasteurisasi'], [
            'deskripsi'     => 'Susu kambing segar murni dan pasteurisasi aneka rasa dingin',
        ]);

        $katFermentasi = KategoriProduk::firstOrCreate(['nama_kategori' => 'Susu Fermentasi & Kefir'], [
            'deskripsi'     => 'Susu kambing fermentasi probiotik alami untuk kesehatan pencernaan',
        ]);

        $katPromo = KategoriProduk::firstOrCreate(['nama_kategori' => 'Paket Hemat & Promo'], [
            'deskripsi'     => 'Paket bundle hemat kombinasi varian susu kambing favorit',
        ]);

        $katKotak = KategoriProduk::firstOrCreate(['nama_kategori' => 'Susu Kotak Fit Goat'], [
            'deskripsi'     => 'Susu kambing kemasan kotak siap minum',
        ]);

        // 4. Katalog Lengkap Varian Produk Susu Kambing
        $katalogProduk = [
            ['kode_produk' => 'PRD-001', 'nama_produk' => 'Susu Bubuk Rizqiku Rasa Jahe (250gr)', 'kategori_id' => $katBubuk->id, 'harga_modal' => 30000, 'harga_jual' => 35000, 'stok' => 45, 'stok_minimum' => 10, 'satuan' => 'kotak', 'foto' => 'images/products/bubuk_jahe.svg'],
            ['kode_produk' => 'PRD-002', 'nama_produk' => 'Susu Bubuk Rizqiku Rasa Original (250gr)', 'kategori_id' => $katBubuk->id, 'harga_modal' => 28000, 'harga_jual' => 35000, 'stok' => 60, 'stok_minimum' => 10, 'satuan' => 'kotak', 'foto' => 'images/products/bubuk_original.svg'],
            ['kode_produk' => 'PRD-003', 'nama_produk' => 'Susu Bubuk Rizqiku Rasa Gula Aren (250gr)', 'kategori_id' => $katBubuk->id, 'harga_modal' => 30000, 'harga_jual' => 37000, 'stok' => 35, 'stok_minimum' => 10, 'satuan' => 'kotak', 'foto' => 'images/products/bubuk_aren.svg'],
            ['kode_produk' => 'PRD-004', 'nama_produk' => 'Susu Bubuk Rizqiku Rasa Cokelat (250gr)', 'kategori_id' => $katBubuk->id, 'harga_modal' => 28000, 'harga_jual' => 35000, 'stok' => 32, 'stok_minimum' => 10, 'satuan' => 'kotak', 'foto' => 'images/products/bubuk_cokelat.svg'],
            ['kode_produk' => 'PRD-005', 'nama_produk' => 'Susu Kambing Murni Segar / Raw Milk (1 Liter)', 'kategori_id' => $katSegar->id, 'harga_modal' => 22000, 'harga_jual' => 30000, 'stok' => 40, 'stok_minimum' => 10, 'satuan' => 'botol', 'foto' => 'images/products/susu_murni.svg'],
            ['kode_produk' => 'PRD-006', 'nama_produk' => 'Susu Pasteurisasi Rasa Vanila (250ml)', 'kategori_id' => $katSegar->id, 'harga_modal' => 11000, 'harga_jual' => 15000, 'stok' => 25, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/vanila.svg'],
            ['kode_produk' => 'PRD-007', 'nama_produk' => 'Susu Pasteurisasi Rasa Stroberi (250ml)', 'kategori_id' => $katSegar->id, 'harga_modal' => 11000, 'harga_jual' => 15000, 'stok' => 20, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/stroberi.svg'],
            ['kode_produk' => 'PRD-008', 'nama_produk' => 'Susu Pasteurisasi Rasa Cokelat (250ml)', 'kategori_id' => $katSegar->id, 'harga_modal' => 11000, 'harga_jual' => 15000, 'stok' => 20, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/cokelat.svg'],
            ['kode_produk' => 'PRD-009', 'nama_produk' => 'Susu Kotak Fit Goat UHT (200ml)', 'kategori_id' => $katKotak->id, 'harga_modal' => 8000, 'harga_jual' => 12000, 'stok' => 80, 'stok_minimum' => 15, 'satuan' => 'kotak', 'foto' => 'images/products/susu_kotak.svg'],
            ['kode_produk' => 'PRD-010', 'nama_produk' => 'Susu Pasteurisasi Rasa Kurma Madu (250ml)', 'kategori_id' => $katSegar->id, 'harga_modal' => 13000, 'harga_jual' => 18000, 'stok' => 25, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/kurma_madu.svg'],
            ['kode_produk' => 'PRD-011', 'nama_produk' => 'Susu Pasteurisasi Rasa Matcha Green Tea (250ml)', 'kategori_id' => $katSegar->id, 'harga_modal' => 12000, 'harga_jual' => 16000, 'stok' => 30, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/matcha.svg'],
            ['kode_produk' => 'PRD-012', 'nama_produk' => 'Susu Pasteurisasi Kopi Gula Aren (250ml)', 'kategori_id' => $katSegar->id, 'harga_modal' => 12500, 'harga_jual' => 17000, 'stok' => 35, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/kopi_aren.svg'],
            ['kode_produk' => 'PRD-013', 'nama_produk' => 'Susu Pasteurisasi Rasa Melon Segar (250ml)', 'kategori_id' => $katSegar->id, 'harga_modal' => 11000, 'harga_jual' => 15000, 'stok' => 20, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/melon.svg'],
            ['kode_produk' => 'PRD-014', 'nama_produk' => 'Susu Kefir Probiotik Murni (250ml)', 'kategori_id' => $katFermentasi->id, 'harga_modal' => 14000, 'harga_jual' => 20000, 'stok' => 20, 'stok_minimum' => 5, 'satuan' => 'botol', 'foto' => 'images/products/kefir.svg'],
            ['kode_produk' => 'PRD-015', 'nama_produk' => 'Susu Bubuk Rizqiku Rasa Madu Propolis (250gr)', 'kategori_id' => $katBubuk->id, 'harga_modal' => 30000, 'harga_jual' => 38000, 'stok' => 28, 'stok_minimum' => 5, 'satuan' => 'kotak', 'foto' => 'images/products/bubuk_propolis.svg'],
            ['kode_produk' => 'PRD-016', 'nama_produk' => 'Susu Bubuk Rizqiku Kolostrum Gold (250gr)', 'kategori_id' => $katBubuk->id, 'harga_modal' => 36000, 'harga_jual' => 45000, 'stok' => 20, 'stok_minimum' => 5, 'satuan' => 'kotak', 'foto' => 'images/products/bubuk_kolostrum.svg'],
            ['kode_produk' => 'PRD-017', 'nama_produk' => 'Paket Trio Segar (Cokelat, Stroberi, Vanila)', 'kategori_id' => $katPromo->id, 'harga_modal' => 32000, 'harga_jual' => 42000, 'stok' => 15, 'stok_minimum' => 3, 'satuan' => 'paket', 'foto' => 'images/products/bundle_trio.svg'],
            ['kode_produk' => 'PRD-018', 'nama_produk' => 'Paket Keluarga Rizqiku (2 Bubuk + 1L Murni)', 'kategori_id' => $katPromo->id, 'harga_modal' => 68000, 'harga_jual' => 92000, 'stok' => 12, 'stok_minimum' => 3, 'satuan' => 'paket', 'foto' => 'images/products/bundle_keluarga.svg'],
        ];

        foreach ($katalogProduk as $item) {
            Produk::updateOrCreate(['kode_produk' => $item['kode_produk']], array_merge($item, [
                'outlet_id' => $outlet->id,
            ]));
        }

        if (app()->environment(['local', 'testing']) && filter_var(env('DEMO_DATA_ENABLED', false), FILTER_VALIDATE_BOOL)) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
