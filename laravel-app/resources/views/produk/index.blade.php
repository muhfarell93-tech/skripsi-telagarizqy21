@extends('layouts.app')

@section('title', 'Katalog Produk & Persediaan Stok')

@section('content')
<div class="space-y-6">

  <!-- Header & Add Button -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shadow-sm">
          <i class="fa-solid fa-boxes-stacked"></i>
        </span>
        <h1 class="text-xl font-black text-slate-900">Katalog Produk Susu &amp; Persediaan Stok</h1>
      </div>
      <p class="text-xs text-slate-500 mt-1">Kelola data varian susu kambing Telaga Rizqy 21, penetapan harga modal (HPP) vs harga jual, dan batas ambang minimum.</p>
    </div>
    
    <button type="button" data-modal-open="modalTambah" class="btn-interactive btn-ripple px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-2 shadow-md shadow-blue-500/20 flex-shrink-0">
      <i class="fa-solid fa-plus"></i> Tambah Produk Baru
    </button>
  </div>

  @if(isset($errors) && $errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-semibold text-red-700 flex items-center gap-2">
      <i class="fa-solid fa-circle-exclamation text-red-500"></i>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <!-- Table Container -->
  <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h2 class="text-sm font-black text-slate-900">Daftar Katalog Susu Kambing</h2>
        <p class="text-[11px] text-slate-400">Total persediaan siap transaksi di kasir POS</p>
      </div>
      <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
        {{ $produks->count() }} Varian Produk
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="table-interactive w-full text-left text-xs text-slate-700">
        <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
          <tr>
            <th class="py-3.5 px-6">Kode / SKU</th>
            <th class="py-3.5 px-4">Nama Produk Susu</th>
            <th class="py-3.5 px-4">Kategori</th>
            <th class="py-3.5 px-4 text-right">Harga Modal</th>
            <th class="py-3.5 px-4 text-right">Harga Jual</th>
            <th class="py-3.5 px-4 text-center">Stok Fisik</th>
            <th class="py-3.5 px-4 text-center">Status Stok</th>
            <th class="py-3.5 px-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($produks as $p)
            <tr>
              <td class="py-3.5 px-6 font-mono font-bold text-slate-800">
                <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 font-bold text-[11px]">{{ $p->kode_produk }}</span>
              </td>
              <td class="py-3.5 px-4 font-bold text-slate-900">
                {{ $p->nama_produk }}
              </td>
              <td class="py-3.5 px-4 text-slate-600 font-medium">
                {{ $p->kategori->nama_kategori ?? 'Susu Kambing' }}
              </td>
              <td class="py-3.5 px-4 text-right text-slate-500 font-medium">
                Rp {{ number_format($p->harga_modal, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">
                Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                <span class="text-sm font-black">{{ $p->stok }}</span> <span class="text-[10px] text-slate-400 font-semibold">{{ $p->satuan }}</span>
              </td>
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
                <div class="flex items-center justify-end gap-1.5">
                  <button type="button" data-modal-open="modalEditProduk{{ $p->id }}" class="btn-interactive p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Ubah Produk">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <form action="{{ route('produk.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-interactive p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Produk">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="empty-state-row">
              <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-300 block"></i>
                <div class="font-bold text-slate-700">Belum ada produk susu terdaftar</div>
                <div class="text-[11px] mt-1 text-slate-400">Klik tombol "Tambah Produk Baru" di atas untuk menambahkan katalog.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Edit Produk per Item -->
  @foreach($produks as $p)
    <div id="modalEditProduk{{ $p->id }}" data-modal-overlay data-modal-dismiss-outside="true" role="dialog" aria-modal="true" aria-hidden="true" class="modal-fade fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
      <div data-modal-panel tabindex="-1" class="modal-scale bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
              <i class="fa-solid fa-pen"></i>
            </div>
            <h3 class="font-black text-slate-900 text-sm">Ubah Data Produk</h3>
          </div>
          <button type="button" data-modal-close aria-label="Tutup modal" class="text-slate-400 hover:text-slate-700 text-lg">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <form method="POST" action="{{ route('produk.update', $p) }}" class="space-y-3.5 text-xs">
          @csrf
          @method('PUT')
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Kode / Barcode</label>
              <input name="kode_produk" value="{{ $p->kode_produk }}" maxlength="30" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900" required>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Kategori</label>
              <select name="kategori_id" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" required>
                @foreach($kategoris as $kat)
                  <option value="{{ $kat->id }}" @selected($p->kategori_id === $kat->id)>{{ $kat->nama_kategori }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div>
            <label class="block font-bold text-slate-700 mb-1">Nama Produk Susu</label>
            <input name="nama_produk" value="{{ $p->nama_produk }}" maxlength="100" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900" required>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Harga Modal (Rp)</label>
              <input name="harga_modal" type="number" min="0" step="0.01" value="{{ $p->harga_modal }}" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold" required>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Harga Jual (Rp)</label>
              <input name="harga_jual" type="number" min="0" step="0.01" value="{{ $p->harga_jual }}" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-black text-blue-600" required>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-bold text-slate-700 mb-1">Stok Fisik</label>
              <input name="stok" type="number" min="0" value="{{ $p->stok }}" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900" required>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Batas Minimum</label>
              <input name="stok_minimum" type="number" min="0" value="{{ $p->stok_minimum }}" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-amber-700" required>
            </div>
            <div>
              <label class="block font-bold text-slate-700 mb-1">Satuan</label>
              <input name="satuan" value="{{ $p->satuan }}" maxlength="20" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800" required>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
            <button type="button" data-modal-close class="btn-interactive px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50">Batal</button>
            <button type="submit" class="btn-interactive btn-ripple px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-500/20">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach

</div>

<!-- Modal Tambah Produk Baru -->
<div id="modalTambah" data-modal-overlay data-modal-dismiss-outside="true" role="dialog" aria-modal="true" aria-labelledby="modalTambahTitle" aria-hidden="true" class="modal-fade fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div data-modal-panel tabindex="-1" class="modal-scale bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
          <i class="fa-solid fa-plus"></i>
        </div>
        <h3 id="modalTambahTitle" class="text-sm font-black text-slate-900 uppercase tracking-wider">Tambah Produk Susu Baru</h3>
      </div>
      <button type="button" data-modal-close class="text-slate-400 hover:text-slate-700 text-lg" aria-label="Tutup modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('produk.store') }}" class="space-y-3.5 text-xs">
      @csrf
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Kode / SKU</label>
          <input type="text" name="kode_produk" required placeholder="PRD-010" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Kategori</label>
          <select name="kategori_id" required class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800">
            @foreach($kategoris as $kat)
              <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div>
        <label class="block font-bold text-slate-700 mb-1">Nama Produk Susu</label>
        <input type="text" name="nama_produk" required placeholder="Contoh: Susu Kambing Bubuk Madu (250gr)" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Harga Modal (Rp)</label>
          <input type="number" name="harga_modal" required placeholder="25000" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Harga Jual (Rp)</label>
          <input type="number" name="harga_jual" required placeholder="35000" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-black text-blue-600">
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Stok Awal</label>
          <input type="number" name="stok" required value="30" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Batas Minimum</label>
          <input type="number" name="stok_minimum" required value="5" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-amber-700">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Satuan</label>
          <input type="text" name="satuan" required value="kotak" class="input-interactive w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800">
        </div>
      </div>

      <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
        <button type="button" data-modal-close class="btn-interactive px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50">Batal</button>
        <button type="submit" class="btn-interactive btn-ripple px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-500/20">Simpan Produk</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  window.openModalTambah = function() {
    const el = document.getElementById('modalTambah');
    if (el) {
      el.classList.remove('hidden');
      el.classList.add('flex');
      requestAnimationFrame(() => el.classList.add('is-open'));
    }
  };
</script>
@endpush
@endsection
