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

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $produks = Produk::with('kategori')
            ->where('outlet_id', Auth::user()->outlet_id)
            ->where('stok', '>', 0)
            ->orderBy('nama_produk', 'asc')
            ->get();

        return view('pos.index', compact('produks'));
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

            $totalItem = 0;
            $totalHarga = 0;
            $detailData = [];

            // 1. Verifikasi Stok & Kunci Harga Satuan
            $quantitiesByProduct = collect($validated['items'])
                ->groupBy('id')
                ->map(fn ($items) => $items->sum('qty'));

            foreach ($quantitiesByProduct as $productId => $quantity) {
                $produk = Produk::where('outlet_id', Auth::user()->outlet_id)
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

            // 5. Integrasi Snap Gateway jika Pembayaran Online / QRIS
            $snapToken = null;
            if (!$isTunai) {
                $transaksi->load('details.produk');
                $snapRes = $this->midtransService->createSnapToken($transaksi);
                $transaksi->update([
                    'snap_token'       => $snapRes['token'],
                    'gateway_order_id' => $invoice,
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
                'success'      => true,
                'message'      => 'Transaksi berhasil diproses.',
                'invoice'      => $transaksi->kode_invoice,
                'transaksi_id' => $transaksi->id,
                'is_tunai'     => $isTunai,
                'snap_token'   => $snapToken,
                'redirect_url' => route('transaksi.show', $transaksi->id),
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

        return response()->json([
            'id'                => $transaksi->id,
            'kode_invoice'      => $transaksi->kode_invoice,
            'status_pembayaran' => $transaksi->status_pembayaran,
            'is_lunas'          => ($transaksi->status_pembayaran === 'lunas'),
            'is_batal'          => ($transaksi->status_pembayaran === 'batal'),
            'metode_pembayaran' => $transaksi->metode_pembayaran,
            'total_harga'       => (float) $transaksi->total_harga,
            'total_bayar'       => (float) $transaksi->total_bayar,
            'kembalian'         => (float) $transaksi->kembalian,
            'waktu_lunas'       => $transaksi->waktu_lunas ? $transaksi->waktu_lunas->format('d/m/Y H:i:s') : null,
            'redirect_url'      => route('transaksi.show', $transaksi->id),
            'message'           => $transaksi->status_pembayaran === 'lunas'
                ? 'Pembayaran berhasil terverifikasi valid di database server.'
                : ($transaksi->status_pembayaran === 'batal'
                    ? 'Transaksi dibatalkan atau pembayaran ditolak oleh gateway.'
                    : 'Menunggu konfirmasi pembayaran dari payment gateway.'),
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

