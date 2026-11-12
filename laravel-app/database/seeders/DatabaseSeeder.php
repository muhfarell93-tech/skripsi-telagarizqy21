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
        $outlet = Outlet::create([
            'kode_outlet' => 'OTL-01',
            'nama_outlet' => 'Telaga Rizqy 21 Metro',
            'no_telepon'  => '081234567890',
            'alamat'      => 'Jl. Sepat No. 3 RT/RW 27/11, Kel. Yosodadi, Kec. Metro Timur, Kota Metro',
        ]);

        // Demo accounts exist only in local/testing environments.
        foreach ($demoAccounts as $account) {
            User::create([
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
        $katBubuk = KategoriProduk::create([
            'nama_kategori' => 'Susu Bubuk Rizqiku',
            'deskripsi'     => 'Susu kambing bubuk premium siap seduh berbagai varian rasa',
        ]);

        $katSegar = KategoriProduk::create([
            'nama_kategori' => 'Susu Segar & Pasteurisasi',
            'deskripsi'     => 'Susu kambing segar murni dan pasteurisasi aneka rasa',
        ]);

        $katKotak = KategoriProduk::create([
            'nama_kategori' => 'Susu Kotak Fit Goat',
            'deskripsi'     => 'Susu kambing kemasan kotak siap minum',
        ]);

        // 4. Katalog Produk
        Produk::create([
            'outlet_id'    => $outlet->id,
            'kategori_id'  => $katBubuk->id,
            'kode_produk'  => 'PRD-001',
            'nama_produk'  => 'Susu Bubuk Rizqiku Rasa Jahe',
            'harga_modal'  => 30000,
            'harga_jual'   => 38000,
            'stok'         => 45,
            'stok_minimum' => 10,
            'satuan'       => 'kotak',
        ]);

        Produk::create([
            'outlet_id'    => $outlet->id,
            'kategori_id'  => $katBubuk->id,
            'kode_produk'  => 'PRD-002',
            'nama_produk'  => 'Susu Bubuk Rizqiku Rasa Original',
            'harga_modal'  => 28000,
            'harga_jual'   => 35000,
            'stok'         => 60,
            'stok_minimum' => 10,
            'satuan'       => 'kotak',
        ]);

        Produk::create([
            'outlet_id'    => $outlet->id,
            'kategori_id'  => $katBubuk->id,
            'kode_produk'  => 'PRD-003',
            'nama_produk'  => 'Susu Bubuk Rizqiku Rasa Gula Aren',
            'harga_modal'  => 30000,
            'harga_jual'   => 38000,
            'stok'         => 35,
            'stok_minimum' => 10,
            'satuan'       => 'kotak',
        ]);

        Produk::create([
            'outlet_id'    => $outlet->id,
            'kategori_id'  => $katSegar->id,
            'kode_produk'  => 'PRD-004',
            'nama_produk'  => 'Susu Segar Pasteurisasi Rasa Cokelat',
            'harga_modal'  => 15000,
            'harga_jual'   => 20000,
            'stok'         => 25,
            'stok_minimum' => 5,
            'satuan'       => 'botol',
        ]);

        Produk::create([
            'outlet_id'    => $outlet->id,
            'kategori_id'  => $katSegar->id,
            'kode_produk'  => 'PRD-005',
            'nama_produk'  => 'Susu Kambing Murni Segar',
            'harga_modal'  => 12000,
            'harga_jual'   => 16000,
            'stok'         => 40,
            'stok_minimum' => 10,
            'satuan'       => 'botol',
        ]);

        Produk::create([
            'outlet_id'    => $outlet->id,
            'kategori_id'  => $katKotak->id,
            'kode_produk'  => 'PRD-006',
            'nama_produk'  => 'Susu Fit Goat Kotak',
            'harga_modal'  => 8000,
            'harga_jual'   => 12000,
            'stok'         => 80,
            'stok_minimum' => 15,
            'satuan'       => 'kotak',
        ]);
    }
}

