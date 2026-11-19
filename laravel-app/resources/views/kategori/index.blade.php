@extends('layouts.app')

@section('title', 'Kategori Produk Susu')

@section('content')
<div class="space-y-6">

  <!-- Header & Title -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shadow-sm">
          <i class="fa-solid fa-tags"></i>
        </span>
        <h1 class="text-xl font-black text-slate-900">Kategori Produk Susu</h1>
      </div>
      <p class="text-xs text-slate-500 mt-1">Kelompokkan varian produk susu kambing agar katalog kasir lebih mudah dikelola dan difilter.</p>
    </div>
    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl px-5 py-3 text-right">
      <span class="text-[10px] font-extrabold text-amber-700 uppercase tracking-wider block">Total Kategori</span>
      <span class="text-lg font-black text-amber-700 block">{{ $kategoris->total() }}</span>
    </div>
  </div>

  @if(isset($errors) && $errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-semibold text-red-700 flex items-center gap-2">
      <i class="fa-solid fa-circle-exclamation text-red-500"></i>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Kolom Kiri: Form Tambah Kategori -->
    <div class="lg:col-span-4 space-y-4">
      <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
          <i class="fa-solid fa-circle-plus text-blue-600"></i>
          <h2 class="text-sm font-black text-slate-900">Tambah Kategori Baru</h2>
        </div>

        <form method="POST" action="{{ route('kategori.store') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kategori</label>
            <input name="nama_kategori" maxlength="50" required
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800"
              placeholder="Contoh: Susu Bubuk / Susu Pasteurisasi">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi (Opsional)</label>
            <textarea name="deskripsi" maxlength="255" rows="3"
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800"
              placeholder="Keterangan singkat tentang kategori produk ini..."></textarea>
          </div>

          <button type="submit" class="btn-interactive btn-ripple w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 flex items-center justify-center gap-1.5 transition">
            <i class="fa-solid fa-plus"></i> Simpan Kategori
          </button>
        </form>
      </section>
    </div>

    <!-- Kolom Kanan: Tabel Daftar Kategori -->
    <div class="lg:col-span-8">
      <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-black text-slate-900">Daftar Kategori</h2>
            <p class="text-[11px] text-slate-400">Edit langsung di tabel, atau hapus jika tidak ada produk terhubung</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="table-interactive min-w-full divide-y divide-slate-100 text-xs text-slate-700">
            <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
              <tr>
                <th class="px-5 py-3 text-left">Nama Kategori</th>
                <th class="px-5 py-3 text-left">Deskripsi</th>
                <th class="px-5 py-3 text-center">Produk</th>
                <th class="px-5 py-3 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($kategoris as $kategori)
                <tr class="hover:bg-slate-50/70 transition">
                  <td class="px-5 py-3.5" colspan="4">
                    <form method="POST" action="{{ route('kategori.update', $kategori) }}" class="flex flex-wrap items-center gap-2">
                      @csrf @method('PUT')
                      <input name="nama_kategori" value="{{ $kategori->nama_kategori }}" maxlength="50" required
                        class="input-interactive rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs font-bold text-slate-900 w-40">
                      <input name="deskripsi" value="{{ $kategori->deskripsi }}" maxlength="255"
                        class="input-interactive flex-1 min-w-[120px] rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs text-slate-700"
                        placeholder="Deskripsi...">
                      <span class="text-[11px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">
                        {{ $kategori->produks_count }} produk
                      </span>
                      <button type="submit" class="btn-interactive px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-[11px] transition shadow-sm">
                        <i class="fa-solid fa-floppy-disk text-[10px] mr-0.5"></i> Simpan
                      </button>
                    </form>
                    <form method="POST" action="{{ route('kategori.destroy', $kategori) }}" class="inline mt-1"
                      onsubmit="return confirm('Hapus kategori ini? Kategori yang masih dipakai produk tidak dapat dihapus.')">
                      @csrf @method('DELETE')
                      <button type="submit" @disabled($kategori->produks_count > 0)
                        class="btn-interactive px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-lg text-[11px] border border-red-200 transition mt-1.5 disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-trash-can text-[10px] mr-0.5"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr class="empty-state-row">
                  <td colspan="4" class="px-5 py-12 text-center text-slate-400">
                    <i class="fa-solid fa-tags text-3xl mb-2 text-slate-300 block"></i>
                    Belum ada kategori produk. Buat kategori pertama di panel sebelah kiri.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-white">
          {{ $kategoris->links() }}
        </div>
      </section>
    </div>

  </div>
</div>
@endsection
