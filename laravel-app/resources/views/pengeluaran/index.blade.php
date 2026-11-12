@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-extrabold text-slate-900">Pengeluaran</h1>
    <p class="mt-1 text-sm text-slate-500">Catat biaya operasional outlet untuk laporan keuangan.</p>
  </div>

  @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
      <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
  @endif

  <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="mb-4 font-bold text-slate-900">Catat pengeluaran baru</h2>
    <form method="POST" action="{{ route('pengeluaran.store') }}" data-table-loading-target="expenses-table" class="grid gap-4 md:grid-cols-2">
      @csrf
      <label class="text-sm font-semibold text-slate-700">Kategori
        <input name="kategori_pengeluaran" value="{{ old('kategori_pengeluaran') }}" required maxlength="50" class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Contoh: Operasional">
      </label>
      <label class="text-sm font-semibold text-slate-700">Nominal (Rp)
        <input name="nominal" type="number" min="1" step="0.01" value="{{ old('nominal') }}" required class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
      </label>
      <label class="text-sm font-semibold text-slate-700">Tanggal
        <input name="tanggal_pengeluaran" type="datetime-local" value="{{ old('tanggal_pengeluaran', now()->format('Y-m-d\TH:i')) }}" required class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
      </label>
      <label class="text-sm font-semibold text-slate-700 md:row-span-2">Keterangan
        <textarea name="keterangan" rows="3" required maxlength="2000" class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('keterangan') }}</textarea>
      </label>
      <div><button class="btn-interactive btn-ripple rounded-lg bg-blue-600 px-4 py-2 font-bold text-white shadow-sm hover:bg-blue-700">Simpan pengeluaran</button></div>
    </form>
  </section>

  <section id="expenses-table" class="table-loading-host relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-table-loading-host aria-busy="false">
    <div class="overflow-x-auto">
      <table class="table-interactive min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Kategori / Keterangan</th><th class="px-4 py-3">Pencatat</th><th class="px-4 py-3 text-right">Nominal</th><th class="px-4 py-3">Aksi</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($pengeluarans as $item)
            <tr>
              <td class="whitespace-nowrap px-4 py-3">{{ $item->tanggal_pengeluaran->format('d/m/Y H:i') }}</td>
              <td class="px-4 py-3"><strong>{{ $item->kategori_pengeluaran }}</strong><div class="text-xs text-slate-500">{{ $item->keterangan }}</div></td>
              <td class="px-4 py-3">{{ $item->user->name ?? '—' }}</td>
              <td class="whitespace-nowrap px-4 py-3 text-right font-bold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
              <td class="px-4 py-3"><form method="POST" action="{{ route('pengeluaran.destroy', $item) }}" data-table-loading-target="expenses-table" onsubmit="return confirm('Hapus catatan pengeluaran ini?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Hapus</button></form></td>
            </tr>
          @empty
            <tr class="empty-state-row"><td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada pengeluaran. Catat pengeluaran pertama menggunakan formulir di atas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('components.table-skeleton')
    <div class="border-t border-slate-100 p-4">{{ $pengeluarans->links() }}</div>
  </section>
</div>
@endsection
