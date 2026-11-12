<?php
/**
 * Webhook Handler: Penerimaan Notifikasi Otomatis Pembayaran Midtrans
 * Endpoint: POST /webhook_midtrans.php (atau Controller Webhook di CodeIgniter)
 * Sistem Informasi Transaksi Keuangan Telaga Rizqy 21
 */

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/MidtransService.php';

// Konfigurasi Database PDO
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'telagarizqy21_db';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal']);
    exit;
}

// 1. Ambil Raw JSON Payload dari Midtrans Webhook
$rawJson = file_get_contents('php://input');
if (empty($rawJson)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Empty payload']);
    exit;
}

$payload = json_decode($rawJson, true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// 2. Verifikasi Keamanan: SHA-512 Signature Key
$midtransService = new MidtransService();
$isValidSignature = $midtransService->verifyWebhookSignature($payload);

if (!$isValidSignature) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Signature verification failed']);
    exit;
}

// Ekstrak Informasi Kunci dari Notifikasi
$orderId           = $payload['order_id'] ?? '';
$transactionId     = $payload['transaction_id'] ?? null;
$transactionStatus = $payload['transaction_status'] ?? '';
$fraudStatus       = $payload['fraud_status'] ?? 'accept';
$paymentType       = $payload['payment_type'] ?? 'unknown';
$grossAmount       = (float) ($payload['gross_amount'] ?? 0);
$statusCode        = $payload['status_code'] ?? '';
$signatureKey      = $payload['signature_key'] ?? '';
$settlementTime    = $payload['settlement_time'] ?? date('Y-m-d H:i:s');

try {
    $pdo->beginTransaction();

    // 3. Ambil Data Transaksi Berdasarkan Invoice (order_id)
    $stmt = $pdo->prepare("SELECT * FROM transaksi WHERE kode_invoice = ? LIMIT 1 FOR UPDATE");
    $stmt->execute([$orderId]);
    $transaksi = $stmt->fetch();

    if (!$transaksi) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak ditemukan']);
        exit;
    }

    $idTransaksi = $transaksi['id_transaksi'];
    $currentStatus = $transaksi['status_pembayaran'];

    // 4. Cek Idempotency: Jika status transaksi sudah lunas, hindari pemrosesan ganda
    if ($currentStatus === 'lunas' && in_array($transactionStatus, ['settlement', 'capture'])) {
        $pdo->rollBack();
        http_response_code(200);
        echo json_encode(['status' => 'ok', 'message' => 'Transaction already settled']);
        exit;
    }

    $newStatusPembayaran = $currentStatus;
    $shouldDeductStock = false;

    // 5. Evaluasi Status Notifikasi Midtrans
    if ($transactionStatus === 'capture') {
        if ($fraudStatus === 'challenge') {
            $newStatusPembayaran = 'belum_lunas';
        } elseif ($fraudStatus === 'accept') {
            $newStatusPembayaran = 'lunas';
            $shouldDeductStock = true;
        }
    } elseif ($transactionStatus === 'settlement') {
        // Pembayaran sukses (QRIS, E-Wallet, VA, dll.)
        $newStatusPembayaran = 'lunas';
        $shouldDeductStock = true;
    } elseif ($transactionStatus === 'pending') {
        $newStatusPembayaran = 'belum_lunas';
    } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
        $newStatusPembayaran = 'batal';
    }

    // 6. Update Status Transaksi di Tabel transaksi
    $updateStmt = $pdo->prepare("
        UPDATE transaksi SET 
            status_pembayaran    = ?,
            total_bayar          = CASE WHEN ? = 'lunas' THEN ? ELSE total_bayar END,
            kembalian            = CASE WHEN ? = 'lunas' THEN 0.00 ELSE kembalian END,
            metode_pembayaran    = CASE WHEN ? = 'qris' THEN 'qris' ELSE 'transfer' END,
            gateway_order_id     = ?,
            gateway_trans_id     = ?,
            gateway_payment_type = ?,
            waktu_lunas          = CASE WHEN ? = 'lunas' THEN ? ELSE waktu_lunas END
        WHERE id_transaksi = ?
    ");
    $updateStmt->execute([
        $newStatusPembayaran,
        $newStatusPembayaran, $grossAmount,
        $newStatusPembayaran,
        $paymentType,
        $orderId,
        $transactionId,
        $paymentType,
        $newStatusPembayaran, $settlementTime,
        $idTransaksi
    ]);

    // 7. Pengurangan Stok Produk Otomatis jika Pembayaran Lunas
    if ($shouldDeductStock && $currentStatus !== 'lunas') {
        $stockStmt = $pdo->prepare("
            UPDATE produk p
            INNER JOIN detail_transaksi dt ON p.id_produk = dt.id_produk
            SET p.stok = GREATEST(p.stok - dt.kuantitas, 0)
            WHERE dt.id_transaksi = ?
        ");
        $stockStmt->execute([$idTransaksi]);
    }

    // 8. Catat Riwayat Transaksi Gateway & Bukti Notifikasi
    $logGwStmt = $pdo->prepare("
        INSERT INTO pembayaran_gateway 
            (id_transaksi, order_id, transaction_id, payment_type, gross_amount, transaction_status, fraud_status, status_code, signature_key, raw_notification)
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $logGwStmt->execute([
        $idTransaksi,
        $orderId,
        $transactionId,
        $paymentType,
        $grossAmount,
        $transactionStatus,
        $fraudStatus,
        $statusCode,
        $signatureKey,
        $rawJson
    ]);

    // 9. Catat Audit Log Aktivitas Sistem
    $logAktivitasStmt = $pdo->prepare("
        INSERT INTO log_aktivitas (id_user, modul, aksi, deskripsi, waktu)
        VALUES (?, 'TRANSAKSI_ONLINE', 'ubah', ?, NOW())
    ");
    $deskripsiLog = "Pembayaran online otomatis diterima [Invoice: {$orderId}, Metode: {$paymentType}, Status: {$newStatusPembayaran}, Nominal: Rp " . number_format($grossAmount, 0, ',', '.') . "]";
    $logAktivitasStmt->execute([$transaksi['id_user'], $deskripsiLog]);

    $pdo->commit();

    http_response_code(200);
    echo json_encode([
        'status'  => 'success',
        'message' => 'Status pembayaran berhasil diperbarui menjadi ' . $newStatusPembayaran,
        'order_id'=> $orderId,
        'status_pembayaran' => $newStatusPembayaran
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memproses webhook: ' . $e->getMessage()]);
}

