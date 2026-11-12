<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            DB::table('users')
                ->whereIn('role', ['siswa', 'orang_tua'])
                ->update(['is_active' => false]);
        }
    }

    public function down(): void
    {
        // Accounts remain inactive on rollback so removed portal roles are not
        // silently re-enabled.
    }
};
