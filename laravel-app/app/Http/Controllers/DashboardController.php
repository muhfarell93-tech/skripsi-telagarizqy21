<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Pengeluaran;
use App\Models\PemasukanManual;
use App\Models\LogAktivitas;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'kasir') {
            return redirect()->route('pos.index');
        }

        $outletId = auth()->user()->outlet_id ?? 1;
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();
        $includeDemo = app()->environment(['local', 'testing']);

        $sales = Transaksi::query()->where('outlet_id', $outletId)->where('status_pembayaran', 'lunas')
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false));
        $manualIncome = PemasukanManual::query()->where('outlet_id', $outletId)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false));
        $expenses = Pengeluaran::query()->where('outlet_id', $outletId)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false));
        $transactions = Transaksi::query()->where('outlet_id', $outletId)
            ->when(!$includeDemo, fn ($query) => $query->where('is_demo', false));

        $pendapatanPenjualan = (clone $sales)->sum('total_harga');
        $pendapatanLain = (clone $manualIncome)->sum('nominal');
        $totalPemasukan = $pendapatanPenjualan + $pendapatanLain;
        $totalPengeluaran = (clone $expenses)->sum('nominal');
        $saldoKeuangan = $totalPemasukan - $totalPengeluaran;

        $omzetHariIni = (clone $sales)->whereDate('tanggal_transaksi', $today)->sum('total_harga');
        $transaksiHariIni = (clone $sales)->whereDate('tanggal_transaksi', $today)->count();
        $omzetBulanIni = (clone $sales)->where('tanggal_transaksi', '>=', $monthStart)->sum('total_harga');
        $pemasukanLainBulanIni = (clone $manualIncome)->where('tanggal_pemasukan', '>=', $monthStart)->sum('nominal');
        $pengeluaranBulanIni = (clone $expenses)->where('tanggal_pengeluaran', '>=', $monthStart)->sum('nominal');
        $labaKotorBulanIni = $omzetBulanIni + $pemasukanLainBulanIni - $pengeluaranBulanIni;

        $totalTransaksi = (clone $transactions)->count();
        $totalTransaksiLunas = (clone $transactions)->where('status_pembayaran', 'lunas')->count();
        $totalPending = (clone $transactions)->where('status_pembayaran', 'belum_lunas')->count();
        $totalProduk = Produk::where('outlet_id', $outletId)->count();
        $stokMenipis = Produk::where('outlet_id', $outletId)->whereColumn('stok', '<=', 'stok_minimum')->orderBy('stok')->take(8)->get();
        $transaksiTerbaru = (clone $transactions)->with(['user', 'details.produk'])
            ->latest('tanggal_transaksi')->take(8)->get();
        $aktivitasTerbaru = LogAktivitas::with('user')->whereHas('user', fn ($query) => $query->where('outlet_id', $outletId))
            ->latest('waktu')->take(8)->get();

        $chartLabels = [];
        $chartOmzet = [];
        $chartPengeluaran = [];
        $dailyStart = Carbon::today()->subDays(6)->startOfDay();
        $dailyEnd = Carbon::today()->endOfDay();
        $dailySales = (clone $sales)->whereBetween('tanggal_transaksi', [$dailyStart, $dailyEnd])->get(['tanggal_transaksi', 'total_harga'])
            ->groupBy(fn ($row) => $row->tanggal_transaksi->format('Y-m-d'))->map(fn ($rows) => (float) $rows->sum('total_harga'));
        $dailyOtherIncome = (clone $manualIncome)->whereBetween('tanggal_pemasukan', [$dailyStart, $dailyEnd])->get(['tanggal_pemasukan', 'nominal'])
            ->groupBy(fn ($row) => $row->tanggal_pemasukan->format('Y-m-d'))->map(fn ($rows) => (float) $rows->sum('nominal'));
        $dailyExpenses = (clone $expenses)->whereBetween('tanggal_pengeluaran', [$dailyStart, $dailyEnd])->get(['tanggal_pengeluaran', 'nominal'])
            ->groupBy(fn ($row) => $row->tanggal_pengeluaran->format('Y-m-d'))->map(fn ($rows) => (float) $rows->sum('nominal'));
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $key = $date->toDateString();
            $chartOmzet[] = ($dailySales[$key] ?? 0) + ($dailyOtherIncome[$key] ?? 0);
            $chartPengeluaran[] = $dailyExpenses[$key] ?? 0;
        }

        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBulananLabels = $namaBulan;
        $monthlySales = (clone $sales)->whereBetween('tanggal_transaksi', [$yearStart, $yearStart->copy()->endOfYear()])->get(['tanggal_transaksi', 'total_harga'])
            ->groupBy(fn ($row) => $row->tanggal_transaksi->month)->map(fn ($rows) => (float) $rows->sum('total_harga'));
        $monthlyOtherIncome = (clone $manualIncome)->whereBetween('tanggal_pemasukan', [$yearStart, $yearStart->copy()->endOfYear()])->get(['tanggal_pemasukan', 'nominal'])
            ->groupBy(fn ($row) => $row->tanggal_pemasukan->month)->map(fn ($rows) => (float) $rows->sum('nominal'));
        $monthlyExpenses = (clone $expenses)->whereBetween('tanggal_pengeluaran', [$yearStart, $yearStart->copy()->endOfYear()])->get(['tanggal_pengeluaran', 'nominal'])
            ->groupBy(fn ($row) => $row->tanggal_pengeluaran->month)->map(fn ($rows) => (float) $rows->sum('nominal'));
        $chartBulananPemasukan = $chartBulananPengeluaran = [];
        for ($month = 1; $month <= 12; $month++) {
            $chartBulananPemasukan[] = ($monthlySales[$month] ?? 0) + ($monthlyOtherIncome[$month] ?? 0);
            $chartBulananPengeluaran[] = $monthlyExpenses[$month] ?? 0;
        }

        $metodeTunai = (clone $sales)->where('metode_pembayaran', 'tunai')->count();
        $metodeQris = (clone $sales)->where('metode_pembayaran', 'qris')->count();
        $metodeTransfer = (clone $sales)->where('metode_pembayaran', 'transfer')->count();
        $adaDataDemo = (clone $transactions)->where('is_demo', true)->exists()
            || (clone $manualIncome)->where('is_demo', true)->exists()
            || (clone $expenses)->where('is_demo', true)->exists();

        return view('dashboard', compact(
            'omzetHariIni',
            'transaksiHariIni',
            'omzetBulanIni',
            'pengeluaranBulanIni',
            'labaKotorBulanIni',
            'totalPemasukan', 'pendapatanPenjualan', 'pendapatanLain', 'totalPengeluaran', 'saldoKeuangan',
            'totalTransaksi', 'totalPending', 'aktivitasTerbaru', 'adaDataDemo',
            'stokMenipis',
            'transaksiTerbaru',
            'chartLabels',
            'chartOmzet',
            'chartPengeluaran',
            'chartBulananLabels', 'chartBulananPemasukan', 'chartBulananPengeluaran',
            'metodeTunai',
            'metodeQris',
            'metodeTransfer',
            'totalProduk',
            'totalTransaksiLunas'
        ));
    }
}
