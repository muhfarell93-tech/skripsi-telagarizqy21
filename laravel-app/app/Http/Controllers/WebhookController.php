<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\PembayaranGateway;
use App\Models\LogAktivitas;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Exception;

class WebhookController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function handle(Request $request)
    {
        $payload = $request->all();

        // 1. Verifikasi Keamanan SHA-512 Signature Key
        if (!$this->midtransService->verifyWebhookSignature($payload)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid signature key'
            ], 403);
        }

        $orderId           = $payload['order_id'] ?? '';
        $transactionId     = $payload['transaction_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';
        $paymentType       = $payload['payment_type'] ?? 'qris';
        $grossAmount       = (float) ($payload['gross_amount'] ?? 0);
        $statusCode        = $payload['status_code'] ?? '';
        $signatureKey      = $payload['signature_key'] ?? '';
        $settlementTime    = $payload['settlement_time'] ?? now();

        try {
            DB::beginTransaction();

            $transaksi = Transaksi::where('kode_invoice', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$transaksi) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan'], 404);
            }

            // Replayed or delayed events must never downgrade a paid transaction.
            if ($transaksi->status_pembayaran === 'lunas') {
                DB::rollBack();
                return response()->json(['status' => 'ok', 'message' => 'Sudah diproses sebelumnya'], 200);
            }

            // A late non-payment event must not reopen an already cancelled order.
            if ($transaksi->status_pembayaran === 'batal' && !in_array($transactionStatus, ['settlement', 'capture'], true)) {
                DB::rollBack();
                return response()->json(['status' => 'ok', 'message' => 'Transaksi sudah terminal'], 200);
            }

            $newStatus = $transaksi->status_pembayaran;

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $newStatus = 'lunas';
                }
            } elseif ($transactionStatus === 'settlement') {
                $newStatus = 'lunas';
            } elseif ($transactionStatus === 'pending') {
                $newStatus = 'belum_lunas';
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $newStatus = 'batal';
            }

            // Apply inventory changes once. A cancelled order can still receive a
            // late successful payment; in that case inventory is reduced again.
            if ($newStatus === 'lunas' && !$transaksi->stok_dikurangi) {
                foreach ($transaksi->details as $detail) {
                    $produk = $detail->produk()->lockForUpdate()->first();
                    if ($produk) {
                        $produk->decrement('stok', $detail->kuantitas);
                    }
                }
                $transaksi->stok_dikurangi = true;
            } elseif ($newStatus === 'batal' && $transaksi->stok_dikurangi) {
                foreach ($transaksi->details as $detail) {
                    $produk = $detail->produk()->lockForUpdate()->first();
                    if ($produk) {
                        $produk->increment('stok', $detail->kuantitas);
                    }
                }
                $transaksi->stok_dikurangi = false;
            }

            // Update transaction status and inventory marker atomically.
            $transaksi->update([
                'status_pembayaran'    => $newStatus,
                'total_bayar'          => ($newStatus === 'lunas') ? $grossAmount : $transaksi->total_bayar,
                'kembalian'            => 0,
                'gateway_order_id'     => $orderId,
                'gateway_trans_id'     => $transactionId,
                'gateway_payment_type' => $paymentType,
                'waktu_lunas'          => ($newStatus === 'lunas') ? $settlementTime : null,
                'stok_dikurangi'       => $transaksi->stok_dikurangi,
            ]);

            // 5. Catat Log Audit Webhook
            PembayaranGateway::create([
                'transaksi_id'       => $transaksi->id,
                'order_id'           => $orderId,
                'transaction_id'     => $transactionId,
                'payment_type'       => $paymentType,
                'gross_amount'       => $grossAmount,
                'transaction_status' => $transactionStatus,
                'fraud_status'       => $fraudStatus,
                'status_code'        => $statusCode,
                'signature_key'      => $signatureKey,
                'raw_notification'   => json_encode($payload),
                'waktu_notifikasi'   => now(),
            ]);

            LogAktivitas::catat(
                $transaksi->user_id,
                'WEBHOOK_GATEWAY',
                'ubah',
                "Notifikasi online [{$orderId}] Status: {$newStatus}, Metode: {$paymentType}, Rp " . number_format($grossAmount, 0, ',', '.')
            );

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status pembayaran berhasil disinkronkan',
                'order_id'=> $orderId,
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

