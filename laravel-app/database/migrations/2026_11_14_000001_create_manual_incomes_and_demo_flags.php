<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('transaksis', 'is_demo')) {
            Schema::table('transaksis', function (Blueprint $table): void {
                $table->boolean('is_demo')->default(false)->after('stok_dikurangi');
            });
        }

        if (!Schema::hasColumn('pengeluarans', 'is_demo')) {
            Schema::table('pengeluarans', function (Blueprint $table): void {
                $table->boolean('is_demo')->default(false)->after('tanggal_pengeluaran');
            });
        }

        if (!Schema::hasTable('pemasukan_manual')) {
            Schema::create('pemasukan_manual', function (Blueprint $table): void {
                $table->id();
                $table->string('kode_pemasukan', 50)->unique();
                $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnUpdate();
                $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
                $table->string('kategori_pemasukan', 50);
                $table->text('keterangan');
                $table->decimal('nominal', 12, 2);
                $table->dateTime('tanggal_pemasukan');
                $table->boolean('is_demo')->default(false);
                $table->timestamps();
                $table->index(['outlet_id', 'tanggal_pemasukan']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan_manual');

        if (Schema::hasColumn('pengeluarans', 'is_demo')) {
            Schema::table('pengeluarans', fn (Blueprint $table) => $table->dropColumn('is_demo'));
        }

        if (Schema::hasColumn('transaksis', 'is_demo')) {
            Schema::table('transaksis', fn (Blueprint $table) => $table->dropColumn('is_demo'));
        }
    }
};
