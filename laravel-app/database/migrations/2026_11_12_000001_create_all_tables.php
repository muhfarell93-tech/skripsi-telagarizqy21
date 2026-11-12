<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_outlet', 20)->unique();
            $table->string('nama_outlet', 100);
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onUpdate('cascade');
            $table->string('name', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'kasir'])->default('kasir');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('kategori_produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 50);
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onUpdate('cascade');
            $table->foreignId('kategori_id')->constrained('kategori_produks')->onUpdate('cascade');
            $table->string('kode_produk', 30)->unique();
            $table->string('nama_produk', 100);
            $table->decimal('harga_modal', 12, 2)->default(0);
            $table->decimal('harga_jual', 12, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->string('satuan', 20)->default('kotak');
            $table->string('foto', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_invoice', 50)->unique();
            $table->foreignId('outlet_id')->constrained('outlets')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');
            $table->dateTime('tanggal_transaksi')->useCurrent();
            $table->integer('total_item')->default(0);
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->decimal('total_bayar', 12, 2)->default(0);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas', 'batal'])->default('lunas');
            $table->text('catatan')->nullable();
            
            // Kolom pendukung Payment Gateway Midtrans
            $table->string('snap_token', 255)->nullable();
            $table->string('gateway_order_id', 100)->nullable()->index();
            $table->string('gateway_trans_id', 100)->nullable();
            $table->string('gateway_payment_type', 50)->nullable();
            $table->dateTime('waktu_kadaluarsa')->nullable();
            $table->dateTime('waktu_lunas')->nullable();
            $table->string('url_bukti_bayar', 255)->nullable();
            $table->boolean('stok_dikurangi')->default(false);
            $table->timestamps();
        });

        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onUpdate('cascade');
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->integer('kuantitas')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengeluaran', 50)->unique();
            $table->foreignId('outlet_id')->constrained('outlets')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');
            $table->string('kategori_pengeluaran', 50);
            $table->text('keterangan');
            $table->decimal('nominal', 12, 2)->default(0);
            $table->dateTime('tanggal_pengeluaran')->useCurrent();
            $table->timestamps();
        });

        Schema::create('pembayaran_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade')->onUpdate('cascade');
            $table->string('order_id', 100)->index();
            $table->string('transaction_id', 100)->nullable();
            $table->string('payment_type', 50);
            $table->decimal('gross_amount', 12, 2);
            $table->string('currency', 10)->default('IDR');
            $table->enum('transaction_status', ['pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'refund'])->default('pending');
            $table->string('fraud_status', 20)->nullable();
            $table->string('status_code', 10);
            $table->string('signature_key', 255)->nullable();
            $table->longText('raw_notification');
            $table->dateTime('waktu_notifikasi')->useCurrent();
            $table->timestamps();
        });

        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade');
            $table->string('modul', 50);
            $table->enum('aksi', ['login', 'logout', 'tambah', 'ubah', 'hapus', 'cetak']);
            $table->text('deskripsi');
            $table->dateTime('waktu')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
        Schema::dropIfExists('pembayaran_gateways');
        Schema::dropIfExists('pengeluarans');
        Schema::dropIfExists('detail_transaksis');
        Schema::dropIfExists('transaksis');
        Schema::dropIfExists('produks');
        Schema::dropIfExists('kategori_produks');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('outlets');
    }
};

