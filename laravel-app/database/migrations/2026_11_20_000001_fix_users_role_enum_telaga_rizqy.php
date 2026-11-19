<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus akun sisa siswa dan orang_tua (tidak relevan dengan peternakan Telaga Rizqy 21)
        if (Schema::hasTable('users')) {
            DB::table('users')->whereIn('role', ['siswa', 'orang_tua'])->delete();

            // 2. Ubah tipe kolom role pada tabel users murni menjadi enum('admin', 'kasir')
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'siswa', 'orang_tua') NOT NULL DEFAULT 'kasir'");
        }
    }
};
