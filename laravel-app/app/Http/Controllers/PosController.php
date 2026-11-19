<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\LogAktivitas;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class PosController extends Controller
{
    protected MidtransService $midtransService;
    protected \App\Services\BuatQrisService $buatQrisService;

    public function __construct(MidtransService $midtransService, \App\Services\BuatQrisService $buatQrisService)
    {
        $this->midtransService = $midtransService;
        $this->buatQrisService = $buatQrisService;
    }

    public function index()
    {
        $outletId = Auth::user()->outlet_id ?: 1;
        $produks = Produk::with('kategori')
            ->where('outlet_id', $outletId)
            ->where('stok', '>', 0)
            ->orderBy('nama_produk', 'asc')
            ->get();

        $kategoris = \App\Models\KategoriProduk::whereHas('produks', function ($q) use ($outletId) {
            $q->where('outlet_id', $outletId)->where('stok', '>', 0);
        })->withCount(['produks' => function ($q) use ($outletId) {
            $q->where('outlet_id', $outletId)->where('stok', '>', 0);
        }])->orderBy('nama_kategori', 'asc')->get();

        return view('pos.index', compact('produks', 'kategoris'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.id'        => 'required|exists:produks,id',
            'items.*.qty'       => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:tunai,qris,transfer',
            'total_bayar'       => 'nullable|numeric|min:0',
            'catatan'           => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $outletId = Auth::user()->outlet_id ?: 1;
            $totalItem = 0;
            $totalHarga = 0;
            $detailData = [];

            // 1. Verifikasi Stok & Kunci Harga Satuan
            $quantitiesByProduct = collect($validated['items'])
                ->groupBy('id')
                ->map(fn ($items) => $items->sum('qty'));

            foreach ($quantitiesByProduct as $productId => $quantity) {
                $produk = Produk::where('outlet_id', $outletId)
                    ->lockForUpdate()
                    ->findOrFail($productId);
                if ($produk->stok < $quantity) {
                    throw new Exception("Stok untuk {$produk->nama_produk} tidak mencukupi (Tersedia: {$produk->stok}).");
                }

                $subtotal = $produk->harga_jual * $quantity;
                $totalItem += $quantity;
                $totalHarga += $subtotal;

                $detailData[] = [
                    'produk'       => $produk,
                    'produk_id'    => $produk->id,
                    'harga_satuan' => $produk->harga_jual,
                    'kuantitas'    => $quantity,
                    'subtotal'     => $subtotal,
                ];
            }

            // 2. Generate Nomor Invoice Unik
            $invoice = 'INV/' . date('Ymd') . '/TR-' . strtoupper(substr(uniqid(), -5));
            $metode = $validated['metode_pembayaran'];
            $isTunai = ($metode === 'tunai');

            $statusPembayaran = $isTunai ? 'lunas' : 'belum_lunas';
            $totalBayar = $isTunai ? (float) ($validated['total_bayar'] ?? $totalHarga) : 0;
            $kembalian = $isTunai ? max(0, $totalBayar - $totalHarga) : 0;

            if ($isTunai && $totalBayar < $totalHarga) {
                throw new Exception("Jumlah uang tunai yang dibayarkan kurang dari total belanja.");
            }

            // 3. Simpan Header Transaksi
            $transaksi = Transaksi::create([
                'kode_invoice'      => $invoice,
                'outlet_id'         => Auth::user()->outlet_id ?? 1,
                'user_id'           => Auth::id(),
                'tanggal_transaksi' => now(),
                'total_item'        => $totalItem,
                'total_harga'       => $totalHarga,
                'total_bayar'       => $totalBayar,
                'kembalian'         => $kembalian,
                'metode_pembayaran' => $metode,
                'status_pembayaran' => $statusPembayaran,
                'stok_dikurangi'    => true,
                'waktu_kadaluarsa'  => $isTunai ? null : now()->addMinutes((int) config('midtrans.expiry_duration', 60)),
                'catatan'           => $validated['catatan'] ?? null,
                'waktu_lunas'       => $isTunai ? now() : null,
            ]);

            // 4. Simpan Detail Transaksi & Kurangi Stok (Jika Tunai Lunas)
            foreach ($detailData as $data) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id'    => $data['produk_id'],
                    'harga_satuan' => $data['harga_satuan'],
                    'kuantitas'    => $data['kuantitas'],
                    'subtotal'     => $data['subtotal'],
                ]);

                // Reserve inventory while an online payment is pending, and commit
                // the same stock reduction immediately for cash payments.
                $data['produk']->decrement('stok', $data['kuantitas']);
            }

            // 5. Integrasi Snap Gateway & BuatQRIS jika Pembayaran Online / QRIS
            $snapToken = null;
            $qrisData = null;

            if (!$isTunai) {
                $transaksi->load('details.produk');

                // Generate BuatQRIS Code
                $qrisData = $this->buatQrisService->createQris($transaksi);
                $buatQrisTrxId = $qrisData['transaction_id'] ?? null;

                // Generate Snap Token Midtrans
                $snapRes = $this->midtransService->createSnapToken($transaksi);
                $transaksi->update([
                    'snap_token'       => $snapRes['token'],
                    'gateway_order_id' => $invoice,
                    'gateway_trans_id' => $buatQrisTrxId,
                ]);
                $snapToken = $snapRes['token'];
            }

            LogAktivitas::catat(
                Auth::id(),
                'TRANSAKSI',
                'tambah',
                "Membuat transaksi [{$invoice}] Metode: {$metode}, Total: Rp " . number_format($totalHarga, 0, ',', '.')
            );

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Transaksi berhasil diproses.',
                'invoice'        => $transaksi->kode_invoice,
                'transaksi_id'   => $transaksi->id,
                'is_tunai'       => $isTunai,
                'snap_token'     => $snapToken,
                'qris_image'     => $qrisData['qris_image'] ?? null,
                'qris_content'   => $qrisData['qris_content'] ?? null,
                'total_amount'   => $qrisData['total_amount'] ?? $totalHarga,
                'redirect_url'   => route('transaksi.show', $transaksi->id),
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Transaksi $transaksi)
    {
        abort_unless(Auth::user()->role === 'admin' || $transaksi->user_id === Auth::id(), 403);

        $transaksi->load(['details.produk', 'user', 'outlet', 'gatewayLogs']);
        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Cek status pembayaran transaksi secara real-time dari server (API Polling)
     * Digunakan oleh antarmuka POS & detail transaksi untuk memastikan status valid dari server.
     */
    public function checkStatus(Transaksi $transaksi)
    {
        abort_unless(Auth::user()->role === 'admin' || $transaksi->user_id === Auth::id(), 403);

        // Jika masih belum lunas dan metode QRIS, cek status ke gateway Midtrans / BuatQRIS
        if ($transaksi->status_pembayaran === 'belum_lunas' && $transaksi->metode_pembayaran === 'qris') {
            // 1. Cek status ke Midtrans API (jika ada snap_token / gateway_order_id)
            if ($transaksi->gateway_order_id || $transaksi->snap_token) {
                try {
                    $midtransStatus = $this->midtransService->checkStatus($transaksi->kode_invoice);
                    $trxStatus = strtolower($midtransStatus['transaction_status'] ?? '');
                    $fraudStatus = strtolower($midtransStatus['fraud_status'] ?? 'accept');

                    if (in_array($trxStatus, ['settlement', 'capture']) && $fraudStatus === 'accept') {
                        DB::transaction(function () use ($transaksi, $midtransStatus) {
                            $transaksi->update([
                                'status_pembayaran'    => 'lunas',
                                'total_bayar'          => (float) ($midtransStatus['gross_amount'] ?? $transaksi->total_harga),
                                'waktu_lunas'          => now(),
                                'gateway_payment_type' => $midtransStatus['payment_type'] ?? 'qris',
                            ]);

                            if (!$transaksi->stok_dikurangi) {
                                foreach ($transaksi->details as $dt) {
                                    $dt->produk()->decrement('stok', $dt->kuantitas);
                                }
                                $transaksi->update(['stok_dikurangi' => true]);
                            }

                            LogAktivitas::catat(
                                $transaksi->user_id,
                                'TRANSAKSI',
                                'ubah',
                                "Pembayaran QRIS Midtrans [{$transaksi->kode_invoice}] terverifikasi LUNAS"
                            );
                        });
                        $transaksi->refresh();
                    } elseif (in_array($trxStatus, ['deny', 'cancel', 'expire'])) {
                        $transaksi->update(['status_pembayaran' => 'batal']);
                        $transaksi->refresh();
                    }
                } catch (Exception $e) {
                    // Abaikan kesalahan koneksi sementara saat polling
                }
            }

            // 2. Cek status ke BuatQRIS API (jika masih belum lunas dan ada transaction id)
            if ($transaksi->status_pembayaran === 'belum_lunas' && $transaksi->gateway_trans_id) {
                try {
                    $qrisCheck = $this->buatQrisService->checkStatus($transaksi->gateway_trans_id, $transaksi->kode_invoice);
                    $apiStatus = strtolower($qrisCheck['data']['status'] ?? $qrisCheck['status'] ?? '');

                    if (in_array($apiStatus, ['paid', 'success', 'settlement', 'lunas'])) {
                        DB::transaction(function () use ($transaksi, $qrisCheck) {
                            $paidAmount = $qrisCheck['data']['total_amount'] ?? $qrisCheck['data']['amount'] ?? $transaksi->total_harga;
                            $transaksi->update([
                                'status_pembayaran' => 'lunas',
                                'total_bayar'       => $paidAmount,
                                'waktu_lunas'       => now(),
                            ]);

                            if (!$transaksi->stok_dikurangi) {
                                foreach ($transaksi->details as $dt) {
                                    $dt->produk()->decrement('stok', $dt->kuantitas);
                                }
                                $transaksi->update(['stok_dikurangi' => true]);
                            }

                            LogAktivitas::catat(
                                $transaksi->user_id,
                                'TRANSAKSI',
                                'ubah',
                                "Pembayaran QRIS [{$transaksi->kode_invoice}] terverifikasi LUNAS via BuatQRIS Gateway"
                            );
                        });
                        $transaksi->refresh();
                    }
                } catch (Exception $e) {
                    // Ignore temporary network check errors during polling
                }
            }
        }

        $isLunas = ($transaksi->status_pembayaran === 'lunas');
        $isBatal = ($transaksi->status_pembayaran === 'batal');
        $isPending = ($transaksi->status_pembayaran === 'belum_lunas');

        return response()->json([
            'id'                   => $transaksi->id,
            'kode_invoice'         => $transaksi->kode_invoice,
            'status_pembayaran'    => $transaksi->status_pembayaran,
            'is_lunas'             => $isLunas,
            'is_pending'           => $isPending,
            'is_batal'             => $isBatal,
            'metode_pembayaran'    => $transaksi->metode_pembayaran,
            'gateway_payment_type' => $transaksi->gateway_payment_type ?? 'qris',
            'total_harga'          => (float) $transaksi->total_harga,
            'total_bayar'          => (float) $transaksi->total_bayar,
            'kembalian'            => (float) $transaksi->kembalian,
            'waktu_lunas'          => $transaksi->waktu_lunas ? $transaksi->waktu_lunas->format('d/m/Y H:i:s') : null,
            'redirect_url'         => route('transaksi.show', $transaksi->id),
            'message'              => $isLunas
                ? 'Pembayaran QRIS berhasil terverifikasi valid (Settlement Webhook Midtrans).'
                : ($isBatal
                    ? 'Transaksi dibatalkan atau pembayaran ditolak oleh gateway.'
                    : 'Menunggu konfirmasi pembayaran dari payment gateway (Pending).'),
        ]);
    }

    /**
     * Endpoint simulasi konfirmasi pembayaran lunas dari server
     * Digunakan untuk pengujian lokal/sandbox tanpa memerlukan webhook external.
     */
    public function simulasiLunas(Transaksi $transaksi)
    {
        abort_unless(Auth::user()->role === 'admin' || $transaksi->user_id === Auth::id(), 403);

        if ($transaksi->status_pembayaran === 'lunas') {
            return response()->json(['success' => true, 'message' => 'Transaksi sudah lunas sebelumnya.']);
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->update([
                'status_pembayaran' => 'lunas',
                'total_bayar'       => $transaksi->total_harga,
                'kembalian'         => 0,
                'waktu_lunas'       => now(),
            ]);

            // Jika stok belum dikurangi, kurangi sekarang
            if (!$transaksi->stok_dikurangi) {
                foreach ($transaksi->details as $dt) {
                    $dt->produk()->decrement('stok', $dt->kuantitas);
                }
                $transaksi->update(['stok_dikurangi' => true]);
            }

            LogAktivitas::catat(
                Auth::id(),
                'TRANSAKSI',
                'ubah',
                "Simulasi pembayaran LUNAS pada transaksi [{$transaksi->kode_invoice}]"
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi pembayaran valid berhasil dicatat di server.',
            'redirect_url' => route('transaksi.show', $transaksi->id),
        ]);
    }
}

