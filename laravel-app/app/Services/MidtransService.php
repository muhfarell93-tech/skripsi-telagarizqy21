<?php

namespace App\Services;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Http;
use Exception;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapApiUrl;
    protected string $apiUrl;
    protected int $expiryDuration;

    public function __construct()
    {
        $this->isProduction    = config('midtrans.is_production', false);
        $this->serverKey       = config('midtrans.server_key', '');
        $this->clientKey       = config('midtrans.client_key', '');
        $this->snapApiUrl      = config('midtrans.snap_api_url', '');
        $this->apiUrl          = config('midtrans.api_url', '');
        $this->expiryDuration  = (int) config('midtrans.expiry_duration', 60);
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Membuat Snap Token untuk Transaksi Penjualan
     */
    public function createSnapToken(Transaksi $transaksi): array
    {
        $itemDetails = [];
        $totalHitung = 0;

        foreach ($transaksi->details as $item) {
            $harga = (int) round($item->harga_satuan);
            $qty   = (int) $item->kuantitas;
            $itemDetails[] = [
                'id'       => (string) $item->produk_id,
                'price'    => $harga,
                'quantity' => $qty,
                'name'     => mb_substr($item->produk->nama_produk ?? 'Produk Susu', 0, 50),
            ];
            $totalHitung += ($harga * $qty);
        }

        $grossAmount = (int) round($transaksi->total_harga);
        if ($totalHitung !== $grossAmount) {
            $grossAmount = $totalHitung;
        }

        $payload = [
            'transaction_details' => [
                'order_id'     => $transaksi->kode_invoice,
                'gross_amount' => $grossAmount,
            ],
            'item_details'     => $itemDetails,
            'customer_details' => [
                'first_name' => 'Konsumen Telaga Rizqy 21',
                'email'      => 'pelanggan@telagarizqy21.com',
                'phone'      => '081234567890',
            ],
            'enabled_payments' => ['qris', 'gopay', 'shopeepay', 'bca_va', 'bni_va', 'bri_va', 'mandiri_bill'],
            'expiry' => [
                'unit'     => 'minute',
                'duration' => $this->expiryDuration,
            ],
        ];

        // Jika server key masih placeholder dummy, sediakan token sandbox mock untuk pengujian sistem
        if (str_contains($this->serverKey, 'YYYYYY') || empty($this->serverKey)) {
            return [
                'token'        => 'sandbox-token-' . strtoupper(substr(md5($transaksi->kode_invoice), 0, 16)),
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $transaksi->kode_invoice,
            ];
        }

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->post($this->snapApiUrl, $payload);

        if ($response->failed() || !isset($response['token'])) {
            $err = $response->json()['error_messages'][0] ?? $response->body();
            throw new Exception("Gagal membuat token Midtrans: " . $err);
        }

        return [
            'token'        => $response['token'],
            'redirect_url' => $response['redirect_url'] ?? '',
        ];
    }

    /**
     * Memeriksa status transaksi langsung ke Midtrans API
     */
    public function checkStatus(string $orderId): array
    {
        if (str_contains($this->serverKey, 'YYYYYY') || empty($this->serverKey)) {
            return [];
        }

        $url = rtrim($this->apiUrl, '/') . '/' . urlencode($orderId) . '/status';
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->timeout(5)->get($url);

        return $response->json() ?? [];
    }

    /**
     * Validasi Signature Key Webhook
     * Formula: SHA512(order_id + status_code + gross_amount + ServerKey)
     */
    public function verifyWebhookSignature(array $payload): bool
    {
        // Izinkan pengujian simulasi webhook di mode lokal jika menggunakan server key mock
        if (app()->environment('local') && (isset($payload['mock_test']) || str_contains($this->serverKey, 'YYYYYY'))) {
            return true;
        }

        if (!isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
            return false;
        }

        $rawString = $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $this->serverKey;
        $calculatedSig = hash('sha512', $rawString);

        return hash_equals($calculatedSig, $payload['signature_key']);
    }
}

