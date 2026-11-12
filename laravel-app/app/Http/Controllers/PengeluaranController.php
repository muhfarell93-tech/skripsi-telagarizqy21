<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluarans = Pengeluaran::with('user')
            ->where('outlet_id', Auth::user()->outlet_id)
            ->latest('tanggal_pengeluaran')
            ->paginate(20);

        return view('pengeluaran.index', compact('pengeluarans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_pengeluaran' => ['required', 'string', 'max:50'],
            'keterangan' => ['required', 'string', 'max:2000'],
            'nominal' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'tanggal_pengeluaran' => ['required', 'date'],
        ]);

        $pengeluaran = Pengeluaran::create($data + [
            'kode_pengeluaran' => 'EXP-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8)),
            'outlet_id' => Auth::user()->outlet_id,
            'user_id' => Auth::id(),
        ]);

        LogAktivitas::catat(Auth::id(), 'PENGELUARAN', 'tambah', "Mencatat pengeluaran {$pengeluaran->kode_pengeluaran}");

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        abort_unless($pengeluaran->outlet_id === Auth::user()->outlet_id, 404);

        $kode = $pengeluaran->kode_pengeluaran;
        $pengeluaran->delete();
        LogAktivitas::catat(Auth::id(), 'PENGELUARAN', 'hapus', "Menghapus pengeluaran {$kode}");

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
