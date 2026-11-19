<?php
require 'c:/Users/advan/Documents/Skripsi kating/laravel-app/vendor/autoload.php';
$app = require_once 'c:/Users/advan/Documents/Skripsi kating/laravel-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
Illuminate\Support\Facades\Auth::login($user);

// 1. Create a dummy test transaction
$invoice = 'TEST-INV-' . time();
$tx = App\Models\Transaksi::create([
    'outlet_id' => 1,
    'user_id' => $user->id,
    'kode_invoice' => $invoice,
    'tanggal_transaksi' => now(),
    'total_item' => 1,
    'total_harga' => 35000,
    'total_bayar' => 0,
    'kembalian' => 0,
    'metode_pembayaran' => 'qris',
    'status_pembayaran' => 'belum_lunas',
    'stok_dikurangi' => false,
]);

$controller = app()->make(App\Http\Controllers\PosController::class);

// Test 1: Pending status
$resPending = $controller->checkStatus($tx)->getData(true);
echo "1. Status Pending: is_lunas=" . ($resPending['is_lunas'] ? 'true' : 'false') . ", is_pending=" . ($resPending['is_pending'] ? 'true' : 'false') . ", is_batal=" . ($resPending['is_batal'] ? 'true' : 'false') . "\n";

// Test 2: Simulate Webhook Settlement from Midtrans
$webhookController = app()->make(App\Http\Controllers\WebhookController::class);
$request = Illuminate\Http\Request::create('/webhook/midtrans', 'POST', [
    'order_id' => $invoice,
    'transaction_status' => 'settlement',
    'fraud_status' => 'accept',
    'payment_type' => 'qris',
    'gross_amount' => 35000,
    'status_code' => '200',
    'mock_test' => true,
]);

$webhookRes = $webhookController->handle($request);
echo "2. Webhook Response: " . $webhookRes->getContent() . "\n";

$tx->refresh();
$resSettled = $controller->checkStatus($tx)->getData(true);
echo "3. Status After Webhook Settlement: is_lunas=" . ($resSettled['is_lunas'] ? 'true' : 'false') . ", is_pending=" . ($resSettled['is_pending'] ? 'true' : 'false') . "\n";

// Cleanup test transaction
$tx->delete();
echo "4. Cleanup test complete.\n";
