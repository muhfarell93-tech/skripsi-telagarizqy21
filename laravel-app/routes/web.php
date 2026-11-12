<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes - Telaga Rizqy 21
|--------------------------------------------------------------------------
*/

// Autentikasi (Bebas untuk Siapa Saja)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:60,1')->name('login.post');
    Route::get('/register', [\App\Http\Controllers\RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\RegistrationController::class, 'store'])->name('register.store');
    Route::get('/login/quick/{role}', [AuthController::class, 'quickLogin'])->name('login.quick');
    Route::get('/password/forgot', [\App\Http\Controllers\PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/password/forgot', [\App\Http\Controllers\PasswordResetController::class, 'sendLink'])->middleware('throttle:10,1')->name('password.email');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/password/reset', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->middleware('throttle:10,1')->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Webhook Midtrans (Bypass CSRF pada middleware VerifyCsrfToken)
Route::post('/webhook/midtrans', [WebhookController::class, 'handle'])->name('webhook.midtrans');

// Protected Routes (Harus Login)
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    // Fitur operasional yang boleh diakses admin dan kasir.
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/transaksi', [\App\Http\Controllers\TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [PosController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/status', [PosController::class, 'checkStatus'])->name('transaksi.status');
    Route::post('/transaksi/{transaksi}/simulasi-lunas', [PosController::class, 'simulasiLunas'])->name('transaksi.simulasi-lunas');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('produk', ProdukController::class)->except(['create', 'show', 'edit']);
    Route::get('/pengeluaran', [\App\Http\Controllers\PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran', [\App\Http\Controllers\PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::delete('/pengeluaran/{pengeluaran}', [\App\Http\Controllers\PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

Route::middleware(['auth', 'role:siswa'])->get('/siswa/dashboard', [\App\Http\Controllers\PortalDashboardController::class, 'siswa'])->name('siswa.dashboard');
Route::middleware(['auth', 'role:orang_tua'])->get('/orang-tua/dashboard', [\App\Http\Controllers\PortalDashboardController::class, 'orangTua'])->name('orangtua.dashboard');
