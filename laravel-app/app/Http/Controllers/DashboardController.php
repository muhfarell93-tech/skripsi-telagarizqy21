<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // 1. Ringkasan Finansial Hari Ini
        $omzetHariIni = Transaksi::where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_transaksi', $today)
            ->sum('total_harga');

        $transaksiHariIni = Transaksi::where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_transaksi', $today)
            ->count();

        // 2. Ringkasan Finansial Bulan Ini
        $omzetBulanIni = Transaksi::where('status_pembayaran', 'lunas')
            ->where('tanggal_transaksi', '>=', $thisMonth)
            ->sum('total_harga');

        $pengeluaranBulanIni = Pengeluaran::where('tanggal_pengeluaran', '>=', $thisMonth)
            ->sum('nominal');

        $labaKotorBulanIni = $omzetBulanIni - $pengeluaranBulanIni;

        // 3. Peringatan Stok Produk Menipis (<= stok_minimum)
        $stokMenipis = Produk::whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok', 'asc')
            ->get();

        // 4. Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with(['user', 'details.produk'])
            ->latest('tanggal_transaksi')
            ->take(6)
            ->get();

        // 5. Tren 7 Hari Terakhir (Grafik Interaktif Animasi)
        $chartLabels = [];
        $chartOmzet = [];
        $chartPengeluaran = [];

        for ($i = 6; $i >= 0; $i--) {
            $loopDate = Carbon::today()->subDays($i);
            $chartLabels[] = $loopDate->translatedFormat('d M');

            $dailyOmzet = Transaksi::where('status_pembayaran', 'lunas')
                ->whereDate('tanggal_transaksi', $loopDate)
                ->sum('total_harga');
            $chartOmzet[] = (int) $dailyOmzet;

            $dailyExpense = Pengeluaran::whereDate('tanggal_pengeluaran', $loopDate)
                ->sum('nominal');
            $chartPengeluaran[] = (int) $dailyExpense;
        }

        // 6. Proporsi Metode Pembayaran (Grafik Donut Animasi)
        $metodeTunai = Transaksi::where('status_pembayaran', 'lunas')
            ->where('metode_pembayaran', 'tunai')
            ->count();
        $metodeQris = Transaksi::where('status_pembayaran', 'lunas')
            ->where('metode_pembayaran', 'qris')
            ->count();
        $metodeTransfer = Transaksi::where('status_pembayaran', 'lunas')
            ->where('metode_pembayaran', 'transfer')
            ->count();

        // 7. Metrik Pendukung
        $totalProduk = Produk::count();
        $totalTransaksiLunas = Transaksi::where('status_pembayaran', 'lunas')->count();

        return view('dashboard', compact(
            'omzetHariIni',
            'transaksiHariIni',
            'omzetBulanIni',
            'pengeluaranBulanIni',
            'labaKotorBulanIni',
            'stokMenipis',
            'transaksiTerbaru',
            'chartLabels',
            'chartOmzet',
            'chartPengeluaran',
            'metodeTunai',
            'metodeQris',
            'metodeTransfer',
            'totalProduk',
            'totalTransaksiLunas'
        ));
    }
}
