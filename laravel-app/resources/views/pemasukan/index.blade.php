@extends('layouts.app')

@section('title', 'Data Pemasukan Kas & Usaha')

@section('content')
<div class="space-y-6">

  <!-- Header & Title -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shadow-sm">
          <i class="fa-solid fa-arrow-trend-up"></i>
        </span>
        <h1 class="text-xl font-black text-slate-900">Data Pemasukan Kas &amp; Usaha</h1>
      </div>
      <p class="text-xs text-slate-500 mt-1">Catat penerimaan kas usaha peternakan. Penjualan produk susu dari kasir POS &amp; QRIS terakumulasi otomatis.</p>
    </div>

    <!-- Summary Boxes -->
    <div class="flex flex-wrap items-center gap-3">
      <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl px-4 py-2.5 text-right">
        <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-wider block">Omzet Penjualan (Otomatis)</span>
        <span class="text-sm font-black text-blue-700 block">Rp {{ number_format($omzetPenjualan ?? 0, 0, ',', '.') }}</span>
      </div>
      <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl px-4 py-2.5 text-right">
        <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider block">Pemasukan Lain (Manual)</span>
        <span class="text-sm font-black text-emerald-700 block">Rp {{ number_format($total ?? 0, 0, ',', '.') }}</span>
      </div>
      <div class="bg-slate-900 border border-slate-800 rounded-2xl px-4 py-2.5 text-right shadow-sm">
        <span class="text-[10px] font-extrabold text-slate-300 uppercase tracking-wider block">Total Semua Pemasukan</span>
        <span class="text-base font-black text-emerald-400 block">Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</span>
      </div>
    </div>
  </div>

  @if(isset($errors) && $errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-semibold text-red-700 flex items-center gap-2">
      <i class="fa-solid fa-circle-exclamation text-red-500"></i>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Kolom Kiri: Form Tambah Pemasukan (4 Kolom) -->
    <div class="lg:col-span-4 space-y-4">
      <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
          <i class="fa-solid fa-circle-plus text-blue-600"></i>
          <h2 class="text-sm font-black text-slate-900">Catat Pemasukan Baru</h2>
        </div>

        <form method="POST" action="{{ route('pemasukan.store') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Pemasukan</label>
            <input name="kategori_pemasukan" value="{{ old('kategori_pemasukan') }}" required maxlength="50"
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800"
              placeholder="Contoh: Penjualan Bibit / Pupuk / Edukasi">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Pemasukan (Rp)</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
              <input name="nominal" type="number" min="1" step="0.01" value="{{ old('nominal') }}" required
                class="input-interactive w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-black text-slate-900"
                placeholder="0">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal &amp; Waktu</label>
            <input name="tanggal_pemasukan" type="datetime-local" value="{{ old('tanggal_pemasukan', now()->format('Y-m-d\TH:i')) }}" required
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-800">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Rincian</label>
            <textarea name="keterangan" rows="3" required maxlength="2000"
              class="input-interactive w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800"
              placeholder="Rincian sumber pemasukan kas...">{{ old('keterangan') }}</textarea>
          </div>

          <button type="submit" class="btn-interactive btn-ripple w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-500/20 flex items-center justify-center gap-1.5 transition">
            <i class="fa-solid fa-plus"></i> Simpan Pemasukan Kas
          </button>
        </form>
      </section>
    </div>

    <!-- Kolom Kanan: Tabel Riwayat Pemasukan (8 Kolom) -->
    <div class="lg:col-span-8">
      <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
          <div>
            <h2 class="text-sm font-black text-slate-900">Riwayat Pemasukan Tambahan</h2>
            <p class="text-[11px] text-slate-400">Daftar kas masuk non-produk yang telah diverifikasi</p>
          </div>
          <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
            {{ $pemasukans->total() }} Data
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="table-interactive min-w-full divide-y divide-slate-100 text-xs text-slate-700">
            <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
              <tr>
                <th class="px-5 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Kode &amp; Kategori</th>
                <th class="px-4 py-3 text-left">Pencatat</th>
                <th class="px-4 py-3 text-right">Nominal</th>
                <th class="px-5 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($pemasukans as $item)
                <tr>
                  <td class="whitespace-nowrap px-5 py-3.5 text-slate-500 font-medium">
                    {{ $item->tanggal_pemasukan->format('d/m/Y H:i') }}
                  </td>
                  <td class="px-4 py-3.5">
                    <div class="font-bold text-slate-900">{{ $item->kategori_pemasukan }}</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $item->keterangan }}</div>
                    <span class="text-[10px] font-mono text-slate-400">{{ $item->kode_pemasukan }}</span>
                  </td>
                  <td class="px-4 py-3.5 text-slate-600 font-medium">
                    {{ $item->user->name ?? '—' }}
                  </td>
                  <td class="whitespace-nowrap px-4 py-3.5 text-right font-black text-emerald-600 text-sm">
                    +Rp {{ number_format($item->nominal, 0, ',', '.') }}
                  </td>
                  <td class="whitespace-nowrap px-5 py-3.5 text-right">
                    <form method="POST" action="{{ route('pemasukan.destroy', $item) }}" onsubmit="return confirm('Hapus catatan pemasukan ini?')">
                      @csrf
                      @method('DELETE')
                      <button class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr class="empty-state-row">
                  <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                    <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300 block"></i>
                    Belum ada data pemasukan tambahan yang dicatat.<br>
                    <span class="text-[11px] text-slate-400">Penjualan susu dari kasir POS &amp; QRIS otomatis tercatat pada Riwayat Transaksi &amp; Laporan.</span>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($pemasukans->hasPages())
          <div class="border-t border-slate-100 p-4">{{ $pemasukans->links() }}</div>
        @endif
      </section>
    </div>

  </div>

</div>
@endsection
