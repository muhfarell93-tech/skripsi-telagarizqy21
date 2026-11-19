@extends('layouts.app')

@section('title', 'Pemantauan Pembayaran QRIS Midtrans')

@section('content')
<div class="space-y-6">

  <!-- Header & Title -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shadow-sm">
          <i class="fa-solid fa-qrcode"></i>
        </span>
        <h1 class="text-xl font-black text-slate-900">Pemantauan Transaksi QRIS Midtrans</h1>
      </div>
      <p class="text-xs text-slate-500 mt-1">
        Pantau status pembayaran digital secara real-time. Status transaksi diperbarui otomatis hanya setelah webhook Midtrans dengan tanda tangan SHA-512 terverifikasi server.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 text-xs font-bold">
        <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
        Gateway: {{ config('midtrans.is_production') ? 'Production' : 'Sandbox Active' }}
      </span>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Total Transaksi QRIS</span>
        <span class="text-2xl font-black text-slate-900 mt-0.5 block">{{ $totalQris ?? 0 }}</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-list-check"></i>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Lunas Terverifikasi</span>
        <span class="text-2xl font-black text-emerald-600 mt-0.5 block">{{ $totalLunas ?? 0 }}</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-circle-check"></i>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Menunggu Bayar</span>
        <span class="text-2xl font-black text-amber-600 mt-0.5 block">{{ $totalMenunggu ?? 0 }}</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-clock"></i>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Nominal Dana Masuk</span>
        <span class="text-xl font-black text-blue-600 mt-0.5 block">Rp {{ number_format($nominalLunas ?? 0, 0, ',', '.') }}</span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
        <i class="fa-solid fa-money-bill-wave"></i>
      </div>
    </div>
  </div>

  <!-- Filter Bar -->
  <form method="GET" action="{{ route('pembayaran.index') }}" data-table-loading-target="qris-table" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
      <div class="relative w-full sm:w-64">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
          <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </span>
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kode invoice..."
          class="input-interactive w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
      </div>

      <div class="w-full sm:w-48">
        <select name="status" class="input-interactive w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
          <option value="">Semua Status</option>
          <option value="lunas" @selected(($filters['status'] ?? '') === 'lunas')>Lunas</option>
          <option value="belum_lunas" @selected(($filters['status'] ?? '') === 'belum_lunas')>Menunggu Pembayaran</option>
          <option value="batal" @selected(($filters['status'] ?? '') === 'batal')>Dibatalkan / Kadaluarsa</option>
        </select>
      </div>
    </div>

    <div class="flex items-center gap-2 w-full md:w-auto justify-end">
      <button type="submit" class="btn-interactive btn-ripple px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs shadow-sm flex items-center gap-1.5 transition">
        <i class="fa-solid fa-filter"></i> Terapkan Filter
      </button>
      <a href="{{ route('pembayaran.index') }}" class="btn-interactive px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
        Reset
      </a>
    </div>
  </form>

  <!-- Table QRIS Transactions -->
  <section id="qris-table" class="table-loading-host bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden" data-table-loading-host aria-busy="false">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-sm font-black text-slate-900">Riwayat Transaksi QRIS</h2>
      <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
        {{ $payments->total() }} Transaksi
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="table-interactive min-w-full divide-y divide-slate-100 text-xs text-slate-700">
        <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
          <tr>
            <th class="px-5 py-3 text-left">Invoice &amp; Waktu</th>
            <th class="px-5 py-3 text-left">Kasir</th>
            <th class="px-5 py-3 text-right">Nominal</th>
            <th class="px-5 py-3 text-center">Status Pembayaran</th>
            <th class="px-5 py-3 text-left">Log Gateway Terakhir</th>
            <th class="px-5 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($payments as $payment)
            <tr class="hover:bg-slate-50/70 transition">
              <td class="px-5 py-3.5 whitespace-nowrap">
                <div class="font-bold text-slate-900">{{ $payment->kode_invoice }}</div>
                <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                  <i class="fa-regular fa-clock text-[10px]"></i>
                  {{ $payment->tanggal_transaksi->format('d/m/Y H:i') }} WIB
                </div>
              </td>
              <td class="px-5 py-3.5 font-medium text-slate-800">
                {{ $payment->user->name ?? 'Kasir' }}
              </td>
              <td class="px-5 py-3.5 whitespace-nowrap text-right font-black text-purple-700">
                Rp {{ number_format($payment->total_harga, 0, ',', '.') }}
              </td>
              <td class="px-5 py-3.5 whitespace-nowrap text-center">
                @if($payment->status_pembayaran === 'lunas')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <i class="fa-solid fa-circle-check text-[10px] text-emerald-600"></i> Lunas
                  </span>
                @elseif($payment->status_pembayaran === 'belum_lunas')
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <i class="fa-solid fa-clock text-[10px] text-amber-600"></i> Menunggu Bayar
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                    <i class="fa-solid fa-circle-xmark text-[10px] text-red-600"></i> Batal / Expired
                  </span>
                @endif
              </td>
              <td class="px-5 py-3.5">
                @if($payment->gatewayLogs->first())
                  <div class="font-bold text-slate-800">{{ $payment->gatewayLogs->first()->transaction_status }}</div>
                  <div class="text-[10px] text-slate-400">{{ $payment->gatewayLogs->first()->waktu_notifikasi->format('d/m/Y H:i:s') }}</div>
                @else
                  <span class="text-slate-400 italic text-[11px]">Belum ada webhook callback</span>
                @endif
              </td>
              <td class="px-5 py-3.5 text-center whitespace-nowrap">
                <a href="{{ route('transaksi.show', $payment) }}" class="btn-interactive inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition shadow-sm">
                  <i class="fa-solid fa-eye text-[10px]"></i> Detail Faktur
                </a>
              </td>
            </tr>
          @empty
            <tr class="empty-state-row">
              <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                <i class="fa-solid fa-qrcode text-3xl mb-2 text-slate-300 block"></i>
                Belum ada transaksi QRIS yang sesuai dengan filter pencarian.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @include('components.table-skeleton')

    <div class="px-6 py-4 border-t border-slate-100 bg-white">
      {{ $payments->links() }}
    </div>
  </section>

</div>
@endsection
