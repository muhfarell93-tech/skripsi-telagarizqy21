<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:lunas,belum_lunas,batal'],
            'q' => ['nullable', 'string', 'max:50'],
        ]);

        $outletId = Auth::user()->outlet_id ?? 1;

        $baseQuery = Transaksi::where('outlet_id', $outletId)
            ->where('metode_pembayaran', 'qris')
            ->when(!app()->environment(['local', 'testing']), fn ($query) => $query->where('is_demo', false));

        $totalQris = (clone $baseQuery)->count();
        $totalLunas = (clone $baseQuery)->where('status_pembayaran', 'lunas')->count();
        $totalMenunggu = (clone $baseQuery)->where('status_pembayaran', 'belum_lunas')->count();
        $nominalLunas = (clone $baseQuery)->where('status_pembayaran', 'lunas')->sum('total_harga');

        $payments = (clone $baseQuery)
            ->with(['user', 'gatewayLogs' => fn ($query) => $query->latest('waktu_notifikasi')])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status_pembayaran', $status))
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where('kode_invoice', 'like', '%'.$term.'%'))
            ->latest('tanggal_transaksi')->paginate(20)->withQueryString();

        return view('pembayaran.index', compact('payments', 'filters', 'totalQris', 'totalLunas', 'totalMenunggu', 'nominalLunas'));
    }
}
