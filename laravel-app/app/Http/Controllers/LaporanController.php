<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // 1. Data Penjualan Terverifikasi Lunas
        $transaksis = Transaksi::with(['user', 'details.produk'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween(Transaksi::raw('DATE(tanggal_transaksi)'), [$startDate, $endDate])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $totalPenjualan = $transaksis->sum('total_harga');

        // 2. Data Pengeluaran Kas
        $pengeluarans = Pengeluaran::with('user')
            ->whereBetween(Pengeluaran::raw('DATE(tanggal_pengeluaran)'), [$startDate, $endDate])
            ->orderBy('tanggal_pengeluaran', 'desc')
            ->get();

        $totalPengeluaran = $pengeluarans->sum('nominal');

        // 3. Estimasi Laba Bersih
        $labaBersih = $totalPenjualan - $totalPengeluaran;

        return view('laporan.index', compact(
            'transaksis',
            'pengeluarans',
            'totalPenjualan',
            'totalPengeluaran',
            'labaBersih',
            'startDate',
            'endDate'
        ));
    }
}

