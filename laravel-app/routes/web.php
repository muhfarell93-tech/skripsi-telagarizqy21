<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\DiagramController;

/*
|--------------------------------------------------------------------------
| Web Routes - Telaga Rizqy 21
|--------------------------------------------------------------------------
| Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web
*/

// =========================================================================
// AUTENTIKASI (Guest Only)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:60,1')->name('login.post');
    Route::get('/register', [\App\Http\Controllers\RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\RegistrationController::class, 'store'])->name('register.store');
    Route::get('/password/forgot', [\App\Http\Controllers\PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/password/forgot', [\App\Http\Controllers\PasswordResetController::class, 'sendLink'])->middleware('throttle:10,1')->name('password.email');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->middleware('throttle:10,1')->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Shortcut Ganti Akun / Peran Cepat (Admin <-> Kasir)
Route::get('/demo/switch/{targetRole}', [AuthController::class, 'switchRole'])->name('demo.switch-role');

// =========================================================================
// WEBHOOK MIDTRANS (Bypass CSRF - lihat VerifyCsrfToken middleware)
// =========================================================================
Route::post('/webhook/midtrans', [WebhookController::class, 'handle'])->name('webhook.midtrans');

// =========================================================================
// HALAMAN BERSAMA - ADMIN & KASIR (Harus Login)
// =========================================================================
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    // Dashboard (Kasir akan di-redirect ke POS oleh DashboardController)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Kasir POS & Checkout
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // Riwayat Transaksi (Kasir: hanya miliknya, Admin: seluruh outlet)
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [PosController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/status', [PosController::class, 'checkStatus'])->name('transaksi.status');
    Route::post('/transaksi/{transaksi}/simulasi-lunas', [PosController::class, 'simulasiLunas'])->name('transaksi.simulasi-lunas');

    // Desain Diagram Sistem & Skripsi (Flowchart, ERD, Relasi Tabel, DFD)
    Route::get('/diagram-sistem', [DiagramController::class, 'index'])->name('diagram.index');
});

// =========================================================================
// HALAMAN ADMIN ONLY (Pemasukan, Pengeluaran, Laporan, Produk, dll)
// =========================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Pemasukan Kas
    Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
    Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
    Route::delete('/pemasukan/{pemasukan}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');

    // Pengeluaran Kas Operasional
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

    // Laporan Keuangan & Export
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

    // Produk & Stok (CRUD)
    Route::resource('produk', ProdukController::class)->except(['create', 'show', 'edit']);

    // Kategori Produk
    Route::get('/kategori', [KategoriProdukController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriProdukController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{kategori}', [KategoriProdukController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriProdukController::class, 'destroy'])->name('kategori.destroy');

    // Kelola Akun Kasir
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');
    Route::put('/kasir/{user}/password', [KasirController::class, 'updatePassword'])->name('kasir.password');
    Route::patch('/kasir/{user}/status', [KasirController::class, 'toggleStatus'])->name('kasir.status');

    // Pemantauan Pembayaran QRIS Midtrans
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
});
