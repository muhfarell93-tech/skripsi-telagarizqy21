<?php
/**
 * Class MidtransService
 * Modul Integrasi Payment Gateway Midtrans (Snap API)
 * Untuk Sistem Informasi Transaksi Keuangan Telaga Rizqy 21
 */

class MidtransService
{
    private $serverKey;
    private $clientKey;
    private $isProduction;
    private $snapApiUrl;
    private $apiUrl;
    private $enabledPayments;
    private $expiryDuration;

    public function __construct(array $config = null)
    {
        if ($config === null) {
            $config = require __DIR__ . '/config_payment.php';
        }

        $env = $config['environment'] ?? 'sandbox';
        $this->isProduction = ($env === 'production');

        $credentials = $this->isProduction ? $config['production'] : $config['sandbox'];
        $this->serverKey = $credentials['server_key'];
        $this->clientKey = $credentials['client_key'];
        $this->snapApiUrl = $credentials['snap_api_url'];
        $this->apiUrl = $credentials['api_url'];
        $this->enabledPayments = $config['enabled_payments'] ?? ['qris', 'gopay', 'bca_va', 'bni_va', 'bri_va'];
        $this->expiryDuration = $config['expiry_duration'] ?? 60;
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    /**
     * Membuat Snap Token dan Redirect URL untuk Transaksi Baru
     *
     * @param array $transactionData [id_transaksi, kode_invoice, total_harga]
     * @param array $items Array of [id_produk, nama_produk, harga_satuan, kuantitas]
     * @param array $customer [nama, email, telepon]
     * @return array [token, redirect_url]
     * @throws Exception
     */
    public function createSnapTransaction(array $transactionData, array $items, array $customer = []): array
    {
        // 1. Susun Rincian Item (Item Details)
        $itemDetails = [];
        $calculatedGrossAmount = 0;

        foreach ($items as $item) {
            $price = (int) round($item['harga_satuan']);
            $qty = (int) $item['kuantitas'];
            $itemDetails[] = [
                'id'       => (string) $item['id_produk'],
                'price'    => $price,
                'quantity' => $qty,
                'name'     => mb_substr($item['nama_produk'], 0, 50),
            ];
            $calculatedGrossAmount += ($price * $qty);
        }

        // Pastikan total gross amount sesuai dengan total harga
        $grossAmount = (int) round($transactionData['total_harga']);
        if ($calculatedGrossAmount !== $grossAmount) {
            // Toleransi penyesuaian jika ada selisih pembulatan
            $grossAmount = $calculatedGrossAmount;
        }

        // 2. Susun Payload Midtrans Snap
        $payload = [
            'transaction_details' => [
                'order_id'     => $transactionData['kode_invoice'],
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $customer['nama'] ?? 'Pelanggan Telaga Rizqy 21',
                'email'      => $customer['email'] ?? 'pelanggan@telagarizqy21.com',
                'phone'      => $customer['telepon'] ?? '081234567890',
            ],
            'enabled_payments' => $this->enabledPayments,
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit'       => 'minute',
                'duration'   => $this->expiryDuration,
            ],
            'callbacks' => [
                'finish' => 'http://localhost/telagarizqy21/transaksi/selesai',
            ],
        ];

        // 3. Kirim Request ke Midtrans Snap API via cURL
        $response = $this->sendHttpRequest($this->snapApiUrl, $payload);

        if (!isset($response['token'])) {
            $errorMessage = $response['error_messages'][0] ?? 'Gagal membuat token pembayaran Midtrans.';
            throw new Exception("Midtrans Error: " . $errorMessage);
        }

        return [
            'snap_token'   => $response['token'],
            'redirect_url' => $response['redirect_url'] ?? '',
            'order_id'     => $transactionData['kode_invoice'],
            'gross_amount' => $grossAmount,
        ];
    }

    /**
     * Memverifikasi Keaslian Notifikasi Webhook dengan SHA-512 Signature Key
     * Formula Midtrans: SHA512(order_id + status_code + gross_amount + ServerKey)
     *
     * @param array $payload Payload JSON dari Midtrans
     * @return bool
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        if (!isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
            return false;
        }

        $orderId     = $payload['order_id'];
        $statusCode  = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $receivedSig = $payload['signature_key'];

        $rawString = $orderId . $statusCode . $grossAmount . $this->serverKey;
        $calculatedSig = hash('sha512', $rawString);

        return hash_equals($calculatedSig, $receivedSig);
    }

    /**
     * Mengecek Status Pembayaran Langsung ke Server Midtrans
     *
     * @param string $orderId
     * @return array
     */
    public function getTransactionStatus(string $orderId): array
    {
        $url = rtrim($this->apiUrl, '/') . '/' . urlencode($orderId) . '/status';
        return $this->sendHttpRequest($url, null, 'GET');
    }

    /**
     * Helper untuk HTTP Request cURL aman dengan Basic Auth ServerKey
     */
    private function sendHttpRequest(string $url, ?array $payload, string $method = 'POST'): array
    {
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':'),
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception("Koneksi cURL Gagal: " . $curlError);
        }

        $decoded = json_decode($rawResponse, true);
        if ($httpCode >= 400 && !isset($decoded['token'])) {
            $msg = $decoded['error_messages'][0] ?? ("HTTP " . $httpCode . ": " . $rawResponse);
            throw new Exception("Midtrans HTTP Error: " . $msg);
        }

        return $decoded ?: [];
    }
}

