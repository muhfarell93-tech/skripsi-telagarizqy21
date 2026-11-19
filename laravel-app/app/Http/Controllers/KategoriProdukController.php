<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriProdukController extends Controller
{
    public function index()
    {
        $kategoris = KategoriProduk::withCount('produks')->orderBy('nama_kategori')->paginate(20);
        return view('kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama_kategori' => ['required', 'string', 'max:50', 'unique:kategori_produks,nama_kategori'], 'deskripsi' => ['nullable', 'string', 'max:255']]);
        $kategori = KategoriProduk::create($data);
        LogAktivitas::catat(Auth::id(), 'KATEGORI', 'tambah', "Menambah kategori {$kategori->nama_kategori}");
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriProduk $kategori)
    {
        $data = $request->validate(['nama_kategori' => ['required', 'string', 'max:50', 'unique:kategori_produks,nama_kategori,'.$kategori->id], 'deskripsi' => ['nullable', 'string', 'max:255']]);
        $kategori->update($data);
        LogAktivitas::catat(Auth::id(), 'KATEGORI', 'ubah', "Memperbarui kategori {$kategori->nama_kategori}");
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriProduk $kategori)
    {
        if ($kategori->produks()->exists()) {
            return back()->with('error', 'Kategori masih digunakan produk. Ubah kategori produk terlebih dahulu.');
        }
        $name = $kategori->nama_kategori;
        $kategori->delete();
        LogAktivitas::catat(Auth::id(), 'KATEGORI', 'hapus', "Menghapus kategori {$name}");
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
