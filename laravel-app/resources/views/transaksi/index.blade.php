@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-6">
  <header>
    <h1 class="text-2xl font-extrabold text-slate-900">Riwayat transaksi</h1>
    <p class="mt-1 text-sm text-slate-500">Cari dan tinjau transaksi penjualan berdasarkan invoice, status, metode, atau tanggal.</p>
  </header>

  <form method="GET" action="{{ route('transaksi.index') }}" data-table-loading-target="transactions-table" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-6">
    <label class="text-xs font-semibold text-slate-600 lg:col-span-2">Nomor invoice
      <input name="q" value="{{ $filters['q'] ?? '' }}" class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Cari nomor invoice">
    </label>
    <label class="text-xs font-semibold text-slate-600">Status
      <select name="status" class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Semua status</option>
        @foreach(['lunas' => 'Lunas', 'belum_lunas' => 'Belum lunas', 'batal' => 'Batal'] as $value => $label)
          <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </label>
    <label class="text-xs font-semibold text-slate-600">Metode bayar
      <select name="metode" class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">Semua metode</option>
        @foreach(['tunai' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'] as $value => $label)
          <option value="{{ $value }}" @selected(($filters['metode'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </label>
    <label class="text-xs font-semibold text-slate-600">Dari tanggal
      <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </label>
    <label class="text-xs font-semibold text-slate-600">Sampai tanggal
      <input type="date" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="input-interactive mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
    </label>
    <div class="flex items-end gap-2 lg:col-span-6">
      <button class="btn-interactive btn-ripple rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700">Terapkan filter</button>
      <a href="{{ route('transaksi.index') }}" class="btn-interactive rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
    </div>
  </form>

  <section id="transactions-table" class="table-loading-host relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-table-loading-host aria-busy="false">
    <div class="overflow-x-auto">
      <table class="table-interactive min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Invoice</th><th class="px-4 py-3">Kasir</th><th class="px-4 py-3">Pembayaran</th><th class="px-4 py-3 text-right">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Bukti</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($transaksis as $transaksi)
            <tr>
              <td class="whitespace-nowrap px-4 py-3">{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</td>
              <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-800">{{ $transaksi->kode_invoice }}</td>
              <td class="px-4 py-3">{{ $transaksi->user->name ?? '—' }}</td>
              <td class="px-4 py-3 uppercase">{{ $transaksi->metode_pembayaran }}</td>
              <td class="whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
              <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $transaksi->status_pembayaran === 'lunas' ? 'bg-emerald-50 text-emerald-700' : ($transaksi->status_pembayaran === 'batal' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">{{ str_replace('_', ' ', ucfirst($transaksi->status_pembayaran)) }}</span></td>
              <td class="whitespace-nowrap px-4 py-3"><a href="{{ route('transaksi.show', $transaksi) }}" class="font-semibold text-blue-700 hover:underline">Lihat nota</a></td>
            </tr>
          @empty
            <tr class="empty-state-row"><td colspan="7" class="px-4 py-12 text-center text-slate-500">Tidak ada transaksi yang cocok dengan filter.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('components.table-skeleton')
    <div class="border-t border-slate-100 p-4">{{ $transaksis->links() }}</div>
  </section>
</div>
@endsection
