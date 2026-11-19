<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:lunas,belum_lunas,batal'],
            'metode' => ['nullable', 'in:tunai,transfer,qris'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $query = Transaksi::with('user')
            ->where('outlet_id', Auth::user()->outlet_id)
            ->when(!app()->environment(['local', 'testing']), fn ($builder) => $builder->where('is_demo', false))
            ->when(Auth::user()->role === 'kasir', fn ($builder) => $builder->where('user_id', Auth::id()))
            ->when($filters['q'] ?? null, fn ($builder, $term) => $builder->where('kode_invoice', 'like', '%'.$term.'%'))
            ->when($filters['status'] ?? null, fn ($builder, $status) => $builder->where('status_pembayaran', $status))
            ->when($filters['metode'] ?? null, fn ($builder, $method) => $builder->where('metode_pembayaran', $method))
            ->when($filters['tanggal_mulai'] ?? null, fn ($builder, $date) => $builder->whereDate('tanggal_transaksi', '>=', $date))
            ->when($filters['tanggal_selesai'] ?? null, fn ($builder, $date) => $builder->whereDate('tanggal_transaksi', '<=', $date));

        $transaksis = $query->latest('tanggal_transaksi')->paginate(20)->withQueryString();

        return view('transaksi.index', compact('transaksis', 'filters'));
    }
}
