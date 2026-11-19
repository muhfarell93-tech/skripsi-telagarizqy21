<?php

namespace App\Services;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BuatQrisService
{
    protected string $apiUrl;
    protected string $accountId;
    protected string $secretToken;
    protected string $method;
    protected string $feeBy;
    protected bool $isSandbox;

    public function __construct()
    {
        $this->apiUrl      = config('buatqris.api_url', 'https://api.buatqris.site');
        $this->accountId   = config('buatqris.account_id', '');
        $this->secretToken = config('buatqris.secret_token', '');
        $this->method      = config('buatqris.qris_method', 'qris_two');
        $this->feeBy       = config('buatqris.fee_by', 'user');
        $this->isSandbox   = (bool) config('buatqris.is_sandbox', true);
    }

    /**
     * Membuat Dynamic QRIS melalui API buatqris.site
     */
    public function createQris(Transaksi $transaksi): array
    {
        $amount = (int) round($transaksi->total_harga);
        $invoice = $transaksi->kode_invoice;

        // Jika kredensial belum diisi, sediakan mode simulasi sandbox visual lengkap
        if (empty($this->accountId) || empty($this->secretToken)) {
            $qrData = "00020101021226600014ID.TELAGARIZQY.WWW01189360099900000000000215{$invoice}520454115303360540" . strlen($amount) . $amount . "5802ID5915TELAGA RIZQY 216005METRO6304";
            $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=15&data=" . urlencode($qrData);

            return [
                'success'      => true,
                'is_simulasi'  => true,
                'qris_image'   => $qrImageUrl,
                'qris_content' => $qrData,
                'amount'       => $amount,
                'invoice'      => $invoice,
                'message'      => 'Mode Sandbox/Simulasi Siap (Masukkan account_id & secret_token di .env untuk live).',
            ];
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            ])->asForm()->post($this->apiUrl, [
                'action'       => 'api_create_qris',
                'account_id'   => $this->accountId,
                'secret_token' => $this->secretToken,
                'amount'       => $amount,
                'description'  => "Pembayaran order #{$invoice}",
                'qris_method'  => $this->method,
                'fee_by'       => $this->feeBy,
                'test'         => $this->isSandbox ? '1' : '0',
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success']) {
                $data = $result['data'] ?? $result;
                $qrImage = $data['qr_url'] ?? $data['qris_image'] ?? $data['qr_image'] ?? $data['image_url'] ?? null;
                $qrContent = $data['qris_code'] ?? $data['content'] ?? $data['qris_content'] ?? null;
                $transactionId = $data['transaction_id'] ?? null;
                $paymentUrl = $data['payment_url'] ?? null;
                $totalAmount = (int) ($data['total_amount'] ?? $amount);

                if (!$qrImage && $qrContent) {
                    $qrImage = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=15&data=" . urlencode($qrContent);
                }

                return [
                    'success'        => true,
                    'is_simulasi'    => false,
                    'qris_image'     => $qrImage,
                    'qris_content'   => $qrContent,
                    'transaction_id' => $transactionId,
                    'payment_url'    => $paymentUrl,
                    'amount'         => $amount,
                    'total_amount'   => $totalAmount,
                    'invoice'        => $invoice,
                    'raw'            => $result,
                ];
            }

            // Jika API merespons dengan pesan error
            $errMsg = $result['message'] ?? 'Gagal membuat QRIS di buatqris.site';
            Log::warning("BuatQris API response: " . json_encode($result));

            // Fallback ke simulasi visual agar antarmuka kasir tidak macet
            $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=15&data=" . urlencode("TELAGA-RIZQY-{$invoice}-{$amount}");
            return [
                'success'      => true,
                'is_simulasi'  => true,
                'qris_image'   => $qrImageUrl,
                'qris_content' => "TELAGA-RIZQY-{$invoice}-{$amount}",
                'amount'       => $amount,
                'invoice'      => $invoice,
                'message'      => "BuatQRIS API Info: {$errMsg}. Menampilkan QRIS Uji Coba.",
            ];

        } catch (Exception $e) {
            Log::error("BuatQris Exception: " . $e->getMessage());

            $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=15&data=" . urlencode("TELAGA-RIZQY-{$invoice}-{$amount}");
            return [
                'success'      => true,
                'is_simulasi'  => true,
                'qris_image'   => $qrImageUrl,
                'qris_content' => "TELAGA-RIZQY-{$invoice}-{$amount}",
                'amount'       => $amount,
                'invoice'      => $invoice,
                'message'      => "Gagal terhubung ke gateway: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Memeriksa status pembayaran QRIS ke buatqris.site
     */
    public function checkStatus(?string $transactionId = null, ?string $invoice = null): array
    {
        if (empty($this->accountId) || empty($this->secretToken)) {
            return [
                'success' => true,
                'status'  => 'pending',
            ];
        }

        try {
            $payload = [
                'action'       => 'api_check_status',
                'account_id'   => $this->accountId,
                'secret_token' => $this->secretToken,
            ];

            if ($transactionId) {
                $payload['transaction_id'] = $transactionId;
            }
            if ($invoice) {
                $payload['order_id'] = $invoice;
            }

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            ])->asForm()->post($this->apiUrl, $payload);

            return $response->json() ?? ['success' => false];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
