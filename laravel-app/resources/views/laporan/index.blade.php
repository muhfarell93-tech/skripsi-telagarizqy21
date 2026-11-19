@extends('layouts.app')

@section('title', 'Laporan Keuangan & Penjualan')

@section('content')
<div class="space-y-6">

  <!-- Header & Date Filter Bar -->
  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h2 class="text-lg font-black text-slate-900">Laporan Keuangan &amp; Arus Kas</h2>
      <p class="text-xs text-slate-500">Rekapitulasi transaksi penjualan dan pengeluaran operasional Telaga Rizqy 21</p>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('laporan.index') }}" data-table-loading-target="report-sales" class="flex flex-wrap items-center gap-2">
      <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs">
        <span class="text-slate-400">Periode:</span>
        <select name="periode" class="bg-transparent font-bold text-slate-800 focus:outline-none" onchange="if(this.value !== 'kustom'){this.form.start_date.disabled=true;this.form.end_date.disabled=true}else{this.form.start_date.disabled=false;this.form.end_date.disabled=false}">
          <option value="hari_ini" @selected(($filters['periode'] ?? 'bulan_ini') === 'hari_ini')>Harian</option>
          <option value="minggu_ini" @selected(($filters['periode'] ?? '') === 'minggu_ini')>Mingguan</option>
          <option value="bulan_ini" @selected(($filters['periode'] ?? 'bulan_ini') === 'bulan_ini')>Bulanan</option>
          <option value="tahun_ini" @selected(($filters['periode'] ?? '') === 'tahun_ini')>Tahunan</option>
          <option value="kustom" @selected(($filters['periode'] ?? '') === 'kustom')>Tanggal khusus</option>
        </select>
      </div>
      <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs">
        <span class="text-slate-400">Dari:</span>
        <input type="date" name="start_date" value="{{ $startDate }}" @disabled(($filters['periode'] ?? 'bulan_ini') !== 'kustom') class="bg-transparent font-bold text-slate-800 focus:outline-none">
      </div>
      <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs">
        <span class="text-slate-400">Sampai:</span>
        <input type="date" name="end_date" value="{{ $endDate }}" @disabled(($filters['periode'] ?? 'bulan_ini') !== 'kustom') class="bg-transparent font-bold text-slate-800 focus:outline-none">
      </div>
      <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari invoice / catatan" class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs">
      <button type="submit" class="btn-interactive btn-ripple px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
        <i class="fa-solid fa-filter"></i> Filter
      </button>
      <a href="{{ route('laporan.export', request()->query()) }}" class="btn-interactive px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 no-print shadow-sm"><i class="fa-solid fa-file-excel"></i> Export Excel (CSV)</a>
      <button type="button" onclick="window.print()" class="btn-interactive btn-ripple px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 no-print shadow-sm">
        <i class="fa-solid fa-print"></i> Cetak PDF
      </button>
    </form>
  </div>

  @if($adaDataDemo)
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900"><strong>Mode DEMO:</strong> baris yang berlabel DEMO adalah ilustrasi dan tidak mewakili pembayaran nyata.</div>
  @endif

  <!-- Financial Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="stat-card stat-card-emerald bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pemasukan</span>
      <span class="text-xl font-black text-emerald-600 mt-1 block">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
      <span class="text-[11px] text-slate-500 mt-1 block">{{ $jumlahTransaksi }} Transaksi Lunas + {{ $jumlahPemasukanLain }} pemasukan lain</span>
    </div>

    <div class="stat-card stat-card-rose bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Beban (Kas Keluar)</span>
      <span class="text-xl font-black text-red-600 mt-1 block">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
      <span class="text-[11px] text-slate-500 mt-1 block">{{ $jumlahPengeluaran }} Entri Pengeluaran</span>
    </div>

    <div class="stat-card stat-card-blue bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Estimasi Arus Kas Bersih</span>
      <span class="text-xl font-black text-blue-600 mt-1 block">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
      <span class="text-[11px] text-slate-500 mt-1 block">Pendapatan Bersih Tersedia</span>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <section class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100"><h3 class="text-sm font-bold text-slate-900">Pemasukan lain</h3><p class="text-xs text-slate-500">Penjualan tidak diduplikasi di daftar ini.</p></div>
      <div class="overflow-x-auto"><table class="table-interactive w-full text-left text-xs"><thead class="bg-slate-50 text-slate-500"><tr><th class="p-3">Tanggal</th><th class="p-3">Kategori / keterangan</th><th class="p-3">Nominal</th></tr></thead><tbody class="divide-y divide-slate-100">
        @forelse($pemasukans as $item)<tr><td class="p-3 whitespace-nowrap">{{ $item->tanggal_pemasukan->format('d/m/Y H:i') }}</td><td class="p-3">{{ $item->kategori_pemasukan }} @if($item->is_demo)<span class="badge text-bg-warning">DEMO</span>@endif<div class="text-slate-500">{{ $item->keterangan }}</div></td><td class="p-3 font-bold text-emerald-700">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td></tr>
        @empty<tr class="empty-state-row"><td colspan="3" class="p-6 text-center text-slate-500">Tidak ada pemasukan lain pada periode ini.</td></tr>@endforelse
      </tbody></table></div><div class="p-4">{{ $pemasukans->links() }}</div>
    </section>
    <section class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100"><h3 class="text-sm font-bold text-slate-900">Pengeluaran</h3><p class="text-xs text-slate-500">Biaya operasional yang tercatat pada periode ini.</p></div>
      <div class="overflow-x-auto"><table class="table-interactive w-full text-left text-xs"><thead class="bg-slate-50 text-slate-500"><tr><th class="p-3">Tanggal</th><th class="p-3">Kategori / keterangan</th><th class="p-3">Nominal</th></tr></thead><tbody class="divide-y divide-slate-100">
        @forelse($pengeluarans as $item)<tr><td class="p-3 whitespace-nowrap">{{ $item->tanggal_pengeluaran->format('d/m/Y H:i') }}</td><td class="p-3">{{ $item->kategori_pengeluaran }} @if($item->is_demo)<span class="badge text-bg-warning">DEMO</span>@endif<div class="text-slate-500">{{ $item->keterangan }}</div></td><td class="p-3 font-bold text-red-700">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td></tr>
        @empty<tr class="empty-state-row"><td colspan="3" class="p-6 text-center text-slate-500">Tidak ada pengeluaran pada periode ini.</td></tr>@endforelse
      </tbody></table></div><div class="p-4">{{ $pengeluarans->links() }}</div>
    </section>
  </div>

  <!-- Tabel Riwayat Penjualan -->
  <div id="report-sales" class="table-loading-host relative bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden" data-table-loading-host aria-busy="false">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Rincian Transaksi Penjualan Terverifikasi Lunas</h3>
      <span class="text-xs text-slate-400">{{ $transaksis->count() }} Data Ditemukan</span>
    </div>
    <div class="overflow-x-auto">
      <table class="table-interactive w-full text-left text-xs text-slate-700">
        <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
          <tr>
            <th class="py-3 px-6">Tanggal</th>
            <th class="py-3 px-4">No. Invoice</th>
            <th class="py-3 px-4">Kasir</th>
            <th class="py-3 px-4">Metode Bayar</th>
            <th class="py-3 px-4">Rincian Barang</th>
            <th class="py-3 px-6 text-right">Total Transaksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($transaksis as $trx)
            <tr class="hover:bg-slate-50/50">
              <td class="py-3 px-6 font-medium text-slate-600">{{ $trx->tanggal_transaksi->format('d/m/Y H:i') }}</td>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">{{ $trx->kode_invoice }} @if($trx->is_demo)<span class="badge text-bg-warning">DEMO</span>@endif</div>
                @if($trx->catatan)
                  <div class="text-[10px] text-blue-600 font-semibold flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-user text-[9px]"></i> {{ $trx->catatan }}
                  </div>
                @endif
              </td>
              <td class="py-3 px-4 text-slate-600">{{ $trx->user->name ?? '-' }}</td>
              <td class="py-3 px-4">
                <span class="uppercase font-bold px-2 py-0.5 rounded text-[10px] {{ $trx->metode_pembayaran === 'qris' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-800' }}">
                  {{ $trx->metode_pembayaran }}
                </span>
              </td>
              <td class="py-3 px-4 text-slate-600">
                @foreach($trx->details as $d)
                  <div>&bull; {{ $d->produk->nama_produk ?? 'Susu' }} ({{ $d->kuantitas }}x)</div>
                @endforeach
              </td>
              <td class="py-3 px-6 text-right font-black text-slate-900">
                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr class="empty-state-row">
              <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada transaksi lunas pada rentang tanggal ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @include('components.table-skeleton')
  </div>

</div>
@endsection

