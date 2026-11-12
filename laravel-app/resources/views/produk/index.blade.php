@extends('layouts.app')

@section('title', 'Katalog Produk & Persediaan Stok')

@section('content')
<div class="space-y-6">

  <!-- Header & Add Button -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-black text-slate-900">Katalog Produk Susu Kambing &amp; Stok</h2>
      <p class="text-xs text-slate-500">Kelola persediaan barang, penetapan harga jual, dan batas ambang stok minimum</p>
    </div>
    <button type="button" data-modal-open="modalTambah" class="btn-interactive btn-ripple px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-md shadow-blue-500/20">
      <i class="fa-solid fa-plus"></i> Tambah Produk Baru
    </button>
  </div>

  <!-- Table Container -->
  <div id="products-table" class="table-loading-host bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden" data-table-loading-host aria-busy="false">
    <div class="overflow-x-auto">
      <table class="table-interactive w-full text-left text-xs text-slate-700">
        <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
          <tr>
            <th class="py-3 px-6">Kode / SKU</th>
            <th class="py-3 px-4">Nama Produk Susu</th>
            <th class="py-3 px-4">Kategori</th>
            <th class="py-3 px-4 text-right">Harga Modal</th>
            <th class="py-3 px-4 text-right">Harga Jual</th>
            <th class="py-3 px-4 text-center">Stok Fisik</th>
            <th class="py-3 px-4 text-center">Status Stok</th>
            <th class="py-3 px-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($produks as $p)
            <tr class="hover:bg-slate-50/50">
              <td class="py-3.5 px-6 font-mono font-bold text-slate-800">{{ $p->kode_produk }}</td>
              <td class="py-3.5 px-4 font-bold text-slate-900">{{ $p->nama_produk }}</td>
              <td class="py-3.5 px-4 text-slate-600">{{ $p->kategori->nama_kategori ?? '-' }}</td>
              <td class="py-3.5 px-4 text-right text-slate-500">Rp {{ number_format($p->harga_modal, 0, ',', '.') }}</td>
              <td class="py-3.5 px-4 text-right font-black text-slate-900">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
              <td class="py-3.5 px-4 text-center font-bold text-slate-900">{{ $p->stok }} {{ $p->satuan }}</td>
              <td class="py-3.5 px-4 text-center">
                @if($p->stok <= $p->stok_minimum)
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation"></i> MENIPIS
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    <i class="fa-solid fa-check"></i> AMAN
                  </span>
                @endif
              </td>
              <td class="py-3.5 px-6 text-right">
                <form action="{{ route('produk.destroy', $p->id) }}" method="POST" data-table-loading-target="products-table" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Produk">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr class="empty-state-row"><td colspan="8" class="px-6 py-12 text-center text-slate-500"><div class="font-bold text-slate-700">Belum ada produk</div><div class="mt-1">Tambahkan produk untuk mulai mengelola stok.</div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('components.table-skeleton')
  </div>

</div>

<!-- Modal Tambah Produk -->
<div id="modalTambah" data-modal-overlay data-modal-dismiss-outside="true" role="dialog" aria-modal="true" aria-labelledby="modalTambahTitle" aria-hidden="true" class="modal-fade fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div data-modal-panel tabindex="-1" class="modal-scale bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <h3 id="modalTambahTitle" class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tambah Produk Susu Baru</h3>
      <button type="button" data-modal-close class="text-slate-400 hover:text-slate-600" aria-label="Tutup modal"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>

    <form method="POST" action="{{ route('produk.store') }}" data-table-loading-target="products-table" class="space-y-3 text-xs">
      @csrf
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Kode / Barcode</label>
          <input type="text" name="kode_produk" required placeholder="PRD-007" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Kategori</label>
          <select name="kategori_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
            @foreach($kategoris as $kat)
              <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div>
        <label class="block font-bold text-slate-700 mb-1">Nama Produk</label>
        <input type="text" name="nama_produk" required placeholder="Susu Kambing Bubuk Vanila" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Harga Modal (Rp)</label>
          <input type="number" name="harga_modal" required placeholder="25000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Harga Jual (Rp)</label>
          <input type="number" name="harga_jual" required placeholder="35000" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Stok Awal</label>
          <input type="number" name="stok" required value="50" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Min. Stok</label>
          <input type="number" name="stok_minimum" required value="10" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Satuan</label>
          <input type="text" name="satuan" required value="kotak" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
        <button type="button" data-modal-close class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold">Batal</button>
        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-sm">Simpan Produk</button>
      </div>
    </form>
  </div>
</div>

@endsection

