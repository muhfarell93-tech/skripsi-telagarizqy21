<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\LogAktivitas;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with(['kategori', 'outlet'])
            ->where('outlet_id', Auth::user()->outlet_id)
            ->orderBy('nama_produk', 'asc')
            ->get();
        $kategoris = KategoriProduk::all();

        return view('produk.index', compact('produks', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk'  => 'required|string|max:30|unique:produks,kode_produk',
            'kategori_id'  => 'required|exists:kategori_produks,id',
            'nama_produk'  => 'required|string|max:100',
            'harga_modal'  => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan'       => 'required|string|max:20',
        ]);

        $validated['outlet_id'] = Auth::user()->outlet_id ?? 1;

        $produk = Produk::create($validated);

        LogAktivitas::catat(Auth::id(), 'PRODUK', 'tambah', "Menambahkan produk baru: {$produk->nama_produk}");

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Produk $produk)
    {
        abort_unless($produk->outlet_id === Auth::user()->outlet_id, 404);
        $validated = $request->validate([
            'kode_produk'  => 'required|string|max:30|unique:produks,kode_produk,' . $produk->id,
            'kategori_id'  => 'required|exists:kategori_produks,id',
            'nama_produk'  => 'required|string|max:100',
            'harga_modal'  => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan'       => 'required|string|max:20',
        ]);

        $produk->update($validated);

        LogAktivitas::catat(Auth::id(), 'PRODUK', 'ubah', "Memperbarui data produk: {$produk->nama_produk}");

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        abort_unless($produk->outlet_id === Auth::user()->outlet_id, 404);
        if (DetailTransaksi::where('produk_id', $produk->id)->exists()) {
            return back()->with('error', 'Produk sudah tercatat dalam transaksi dan tidak dapat dihapus. Atur stok menjadi nol untuk menghentikan penjualan.');
        }
        $nama = $produk->nama_produk;
        $produk->delete();

        LogAktivitas::catat(Auth::id(), 'PRODUK', 'hapus', "Menghapus produk: {$nama}");

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}

