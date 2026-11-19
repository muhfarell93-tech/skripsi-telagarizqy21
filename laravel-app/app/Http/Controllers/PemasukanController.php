<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\PemasukanManual;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PemasukanController extends Controller
{
    public function index()
    {
        $outletId = Auth::user()->outlet_id ?? 1;
        $pemasukans = PemasukanManual::with('user')->where('outlet_id', $outletId)
            ->when(!app()->environment(['local', 'testing']), fn ($query) => $query->where('is_demo', false))
            ->latest('tanggal_pemasukan')->paginate(20);

        $omzetPenjualan = Transaksi::where('outlet_id', $outletId)
            ->where('status_pembayaran', 'lunas')
            ->when(!app()->environment(['local', 'testing']), fn ($query) => $query->where('is_demo', false))
            ->sum('total_harga');

        $total = PemasukanManual::where('outlet_id', $outletId)
            ->when(!app()->environment(['local', 'testing']), fn ($query) => $query->where('is_demo', false))
            ->sum('nominal');

        $totalPemasukan = $omzetPenjualan + $total;

        return view('pemasukan.index', compact('pemasukans', 'total', 'omzetPenjualan', 'totalPemasukan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_pemasukan' => ['required', 'string', 'max:50'],
            'keterangan' => ['required', 'string', 'max:2000'],
            'nominal' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'tanggal_pemasukan' => ['required', 'date'],
        ]);

        $record = PemasukanManual::create($data + [
            'kode_pemasukan' => 'INC-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
            'outlet_id' => Auth::user()->outlet_id,
            'user_id' => Auth::id(),
        ]);
        LogAktivitas::catat(Auth::id(), 'PEMASUKAN', 'tambah', "Mencatat pemasukan lain {$record->kode_pemasukan}");

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan lain berhasil dicatat. Penjualan sudah dihitung otomatis dari transaksi lunas.');
    }

    public function destroy(PemasukanManual $pemasukan)
    {
        abort_unless($pemasukan->outlet_id === Auth::user()->outlet_id, 404);
        $code = $pemasukan->kode_pemasukan;
        $pemasukan->delete();
        LogAktivitas::catat(Auth::id(), 'PEMASUKAN', 'hapus', "Menghapus pemasukan lain {$code}");

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil dihapus.');
    }
}
