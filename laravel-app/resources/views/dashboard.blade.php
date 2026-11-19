@extends('layouts.app')

@section('title', 'Dashboard Eksekutif')

@section('content')
<div class="space-y-6">

  <!-- Welcome Banner -->
  <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10 relative overflow-hidden transition-all duration-300">
    <div class="relative z-10 max-w-3xl">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md mb-3 border border-white/20">
        <i class="fa-solid fa-store"></i> Peternakan &amp; Olahan Susu Telaga Rizqy 21 Metro
      </span>
      <h1 class="text-2xl sm:text-3xl font-black tracking-tight leading-tight">
        Selamat Datang di Dasbor Keuangan, {{ auth()->user()->name ?? 'Administrator' }}!
      </h1>
      <p class="text-xs sm:text-sm text-blue-100 mt-2 leading-relaxed">
        Sistem Informasi Pengolahan Data Transaksi Keuangan berjalan aktif secara otomatis menghubungkan kasir POS, pembayaran QRIS online, pencatatan beban operasional, serta monitoring laba rugi.
      </p>
      
      <!-- Quick Action Buttons -->
      <div class="mt-6 flex flex-wrap gap-2.5">
        <a href="{{ route('pos.index') }}" class="btn-interactive btn-ripple px-4 py-2.5 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-xl text-xs transition-all shadow-md flex items-center gap-2">
          <i class="fa-solid fa-cash-register"></i> Buka Kasir POS
        </a>
        <a href="{{ route('pemasukan.index') }}" class="btn-interactive btn-ripple px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-xs transition-all shadow-md flex items-center gap-2">
          <i class="fa-solid fa-plus"></i> Catat Pemasukan
        </a>
        <a href="{{ route('pengeluaran.index') }}" class="btn-interactive btn-ripple px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl text-xs transition-all shadow-md flex items-center gap-2">
          <i class="fa-solid fa-arrow-down"></i> Catat Pengeluaran
        </a>
        <a href="{{ route('laporan.index') }}" class="btn-interactive btn-ripple px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-bold rounded-xl text-xs backdrop-blur-md transition-all flex items-center gap-2 border border-white/20">
          <i class="fa-solid fa-file-invoice-dollar"></i> Laporan Keuangan
        </a>
      </div>
    </div>
    <div class="absolute -right-8 -bottom-12 text-blue-400/15 text-9xl pointer-events-none select-none">
      <i class="fa-solid fa-bottle-droplet"></i>
    </div>
  </div>

  <!-- SECTION 1: RINGKASAN METRIK KEUANGAN UTAMA (7 METRIK OTOMATIS DARI DATABASE) -->
  <div>
    <div class="flex items-center justify-between mb-3 px-1">
      <h2 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 flex items-center gap-1.5">
        <i class="fa-solid fa-chart-simple text-blue-600"></i> Ringkasan Finansial Otomatis
      </h2>
      <span class="text-[11px] text-slate-400 font-semibold">
        Sinkronisasi Real-Time Basis Data
      </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      
      <!-- 1. Total Pemasukan -->
      <div class="stat-card stat-card-emerald bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pemasukan</span>
          <span class="text-xl sm:text-2xl font-black text-emerald-600 mt-1 block tracking-tight"
                data-counter-target="{{ (int)$totalPemasukan }}"
                data-counter-type="rupiah">
            Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
          </span>
          <span class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1 font-semibold">
            <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i>
            <span>Penjualan Kasir + Kas Masuk</span>
          </span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl stat-icon shadow-sm">
          <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
      </div>

      <!-- 2. Total Pengeluaran -->
      <div class="stat-card stat-card-red bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pengeluaran</span>
          <span class="text-xl sm:text-2xl font-black text-red-600 mt-1 block tracking-tight"
                data-counter-target="{{ (int)$totalPengeluaran }}"
                data-counter-type="rupiah">
            Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
          </span>
          <span class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1 font-semibold">
            <i class="fa-solid fa-arrow-trend-down text-red-500 text-[10px]"></i>
            <span>Beban Operasional Peternakan</span>
          </span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl stat-icon shadow-sm">
          <i class="fa-solid fa-wallet"></i>
        </div>
      </div>

      <!-- 3. Total Saldo Keuangan -->
      <div class="stat-card stat-card-blue bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Saldo Keuangan</span>
          <span class="text-xl sm:text-2xl font-black text-blue-600 mt-1 block tracking-tight"
                data-counter-target="{{ (int)$saldoKeuangan }}"
                data-counter-type="rupiah">
            Rp {{ number_format($saldoKeuangan, 0, ',', '.') }}
          </span>
          <span class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1 font-semibold">
            <i class="fa-solid fa-scale-balanced text-blue-500 text-[10px]"></i>
            <span>Pemasukan &minus; Pengeluaran Kas</span>
          </span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl stat-icon shadow-sm">
          <i class="fa-solid fa-coins"></i>
        </div>
      </div>

      <!-- 4. Total Transaksi & Status Lunas/Pending -->
      <div class="stat-card stat-card-purple bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Transaksi</span>
          <span class="text-xl sm:text-2xl font-black text-purple-700 mt-1 block tracking-tight"
                data-counter-target="{{ (int)$totalTransaksi }}"
                data-counter-type="number"
                data-counter-suffix=" Transaksi">
            {{ $totalTransaksi }} Transaksi
          </span>
          <div class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-2 font-semibold">
            <span class="text-emerald-600 font-bold"><i class="fa-solid fa-check"></i> {{ $totalTransaksiLunas }} Lunas</span>
            <span>&bull;</span>
            <span class="text-amber-600 font-bold"><i class="fa-regular fa-clock"></i> {{ $totalPending }} Pending</span>
          </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl stat-icon shadow-sm">
          <i class="fa-solid fa-receipt"></i>
        </div>
      </div>

    </div>
  </div>

  <!-- SECTION 2: METRIK PRODUK & STATUS TRANSAKSI CEPAT -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    
    <!-- Jumlah Produk Aktif -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base font-bold">
          <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div>
          <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Jumlah Produk Aktif</span>
          <h3 class="text-base font-black text-slate-900 mt-0.5">{{ $totalProduk }} Varian Susu</h3>
        </div>
      </div>
      <a href="{{ route('produk.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Kelola &rarr;</a>
    </div>

    <!-- Transaksi Berhasil -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
          <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Transaksi Berhasil</span>
          <h3 class="text-base font-black text-emerald-600 mt-0.5">{{ $totalTransaksiLunas }} Lunas Sah</h3>
        </div>
      </div>
      <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">100% Valid</span>
    </div>

    <!-- Transaksi Pending (Menunggu) -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold">
          <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div>
          <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Transaksi Pending</span>
          <h3 class="text-base font-black text-amber-600 mt-0.5">{{ $totalPending }} Menunggu</h3>
        </div>
      </div>
      <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">Menunggu QRIS</span>
    </div>

  </div>

  <!-- SECTION 3: GRAFIK PEMASUKAN & PENGELUARAN (HARIAN, BULANAN, & METODE PEMBAYARAN) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- 1. Grafik Tren Penjualan & Pengeluaran 7 Hari Terakhir (Harian) -->
    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-chart-area text-blue-600"></i>
            Grafik Penjualan &amp; Pengeluaran Harian (7 Hari Terakhir)
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">Perbandingan fluktuasi omzet masuk vs beban pengeluaran harian</p>
        </div>
        <div class="flex items-center gap-2.5 text-xs font-semibold">
          <span class="flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Pemasukan
          </span>
          <span class="flex items-center gap-1.5 text-red-700 bg-red-50 px-2.5 py-1 rounded-lg">
            <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> Pengeluaran
          </span>
        </div>
      </div>
      <div class="relative w-full h-[280px]">
        <canvas id="chartTrenFinansial"></canvas>
      </div>
    </div>

    <!-- 2. Proporsi Metode Pembayaran (Donut Chart) -->
    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4 flex flex-col justify-between">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-chart-pie text-indigo-600"></i>
          Distribusi Metode Pembayaran
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">Proporsi transaksi Tunai, QRIS, &amp; Transfer Bank</p>
      </div>
      
      <div class="relative w-full h-[200px] flex items-center justify-center my-auto">
        <canvas id="chartMetode"></canvas>
      </div>

      <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100 text-center text-xs">
        <div class="p-2 bg-blue-50/70 rounded-xl">
          <span class="text-[10px] text-blue-700 font-bold block uppercase">Tunai</span>
          <span class="font-black text-slate-900 text-sm" data-counter-target="{{ $metodeTunai }}" data-counter-type="number">{{ $metodeTunai }}</span>
        </div>
        <div class="p-2 bg-purple-50/70 rounded-xl">
          <span class="text-[10px] text-purple-700 font-bold block uppercase">QRIS</span>
          <span class="font-black text-purple-700 text-sm" data-counter-target="{{ $metodeQris }}" data-counter-type="number">{{ $metodeQris }}</span>
        </div>
        <div class="p-2 bg-cyan-50/70 rounded-xl">
          <span class="text-[10px] text-cyan-700 font-bold block uppercase">Transfer</span>
          <span class="font-black text-cyan-700 text-sm" data-counter-target="{{ $metodeTransfer }}" data-counter-type="number">{{ $metodeTransfer }}</span>
        </div>
      </div>
    </div>

  </div>

  <!-- SECTION 4: GRAFIK PENJUALAN & KEUANGAN BULANAN (TAHUN INI) -->
  <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
      <div>
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-chart-column text-indigo-600"></i>
          Grafik Arus Keuangan Bulanan (Tahun {{ now()->year }})
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">Rekapitulasi komparasi pemasukan dan beban kas keluar sepanjang tahun berjalan</p>
      </div>
      <a href="{{ route('laporan.index', ['periode' => 'tahun_ini']) }}" class="btn-interactive px-3.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-bold transition flex items-center gap-1">
        Buka Laporan Tahunan <i class="fa-solid fa-arrow-right text-[10px]"></i>
      </a>
    </div>
    <div class="relative w-full h-[280px]">
      <canvas id="chartBulananKeuangan"></canvas>
    </div>
  </section>

  <!-- SECTION 5: DAFTAR TRANSAKSI TERBARU & INFORMASI AKTIVITAS PENGGUNA -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Daftar Transaksi Terbaru (8 Kolom) -->
    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shadow-sm">
            <i class="fa-solid fa-clock-rotate-left"></i>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Daftar Transaksi Terbaru</h3>
            <p class="text-[11px] text-slate-400">Data penjualan riil terbaru dari kasir</p>
          </div>
        </div>
        <a href="{{ route('transaksi.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 transition">
          Lihat Semua ({{ $totalTransaksi }}) &rarr;
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="table-interactive w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
            <tr>
              <th class="px-5 py-3">No. Invoice &amp; Pembeli</th>
              <th class="px-4 py-3">Kasir</th>
              <th class="px-4 py-3">Metode Bayar</th>
              <th class="px-4 py-3 text-right">Total Belanja</th>
              <th class="px-4 py-3 text-center">Status</th>
              <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($transaksiTerbaru as $trx)
              <tr>
                <td class="px-5 py-3.5">
                  <div class="font-bold text-slate-900">{{ $trx->kode_invoice }}</div>
                  @if($trx->catatan)
                    <div class="text-[11px] text-blue-600 font-medium flex items-center gap-1 mt-0.5">
                      <i class="fa-solid fa-user text-[10px]"></i> {{ $trx->catatan }}
                    </div>
                  @endif
                </td>
                <td class="px-4 py-3.5 text-slate-600 font-medium">{{ $trx->user->name ?? 'Kasir' }}</td>
                <td class="px-4 py-3.5">
                  <span class="inline-flex items-center gap-1 uppercase font-bold text-[10px] px-2 py-0.5 rounded {{ $trx->metode_pembayaran === 'qris' ? 'bg-purple-100 text-purple-800' : ($trx->metode_pembayaran === 'transfer' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800') }}">
                    @if($trx->metode_pembayaran === 'qris')
                      <i class="fa-solid fa-qrcode text-[9px]"></i>
                    @elseif($trx->metode_pembayaran === 'transfer')
                      <i class="fa-solid fa-building-columns text-[9px]"></i>
                    @else
                      <i class="fa-solid fa-money-bill text-[9px]"></i>
                    @endif
                    {{ $trx->metode_pembayaran }}
                  </span>
                </td>
                <td class="px-4 py-3.5 text-right font-black text-slate-900">
                  Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                </td>
                <td class="px-4 py-3.5 text-center">
                  @if($trx->status_pembayaran === 'lunas')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                      <i class="fa-solid fa-circle-check"></i> LUNAS
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                      <i class="fa-regular fa-clock"></i> PENDING
                    </span>
                  @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                  <a href="{{ route('transaksi.show', $trx->id) }}" class="btn-interactive px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs inline-flex items-center gap-1 transition">
                    <i class="fa-solid fa-receipt text-[11px]"></i> Struk
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada transaksi tercatat.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Informasi Aktivitas Pengguna & Stok (4 Kolom) -->
    <div class="lg:col-span-4 space-y-6">

      <!-- Informasi Aktivitas Pengguna (Audit Trail) -->
      <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shadow-sm">
              <i class="fa-solid fa-user-clock"></i>
            </div>
            <div>
              <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Aktivitas Pengguna</h3>
              <p class="text-[11px] text-slate-400">Jejak audit staf &amp; kasir</p>
            </div>
          </div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Live Log</span>
        </div>

        <div class="space-y-3">
          @forelse($aktivitasTerbaru as $activity)
            <div class="flex items-start gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
              <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 text-xs font-bold mt-0.5">
                <i class="fa-solid fa-bolt text-[11px]"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="font-bold text-slate-900 truncate">{{ $activity->user->name ?? 'Pengguna' }}</div>
                <div class="text-[11px] text-slate-600 leading-snug">{{ $activity->deskripsi }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                  <span>{{ $activity->modul }}</span>
                  <span>&bull;</span>
                  <span>{{ $activity->waktu->diffForHumans() }}</span>
                </div>
              </div>
            </div>
          @empty
            <div class="p-4 text-center text-xs text-slate-400">
              Belum ada aktivitas tercatat.
            </div>
          @endforelse
        </div>
      </section>

      <!-- Peringatan Stok Menipis -->
      <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 text-amber-600">
            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shadow-sm">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Peringatan Stok</h3>
          </div>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $stokMenipis->count() > 0 ? 'bg-red-100 text-red-800 animate-pulse' : 'bg-emerald-100 text-emerald-800' }}">
            {{ $stokMenipis->count() }} Menipis
          </span>
        </div>

        <div class="space-y-2.5">
          @forelse($stokMenipis as $item)
            <div class="p-3 bg-red-50/60 border border-red-200/80 rounded-2xl flex items-center justify-between">
              <div>
                <div class="text-xs font-bold text-slate-900">{{ $item->nama_produk }}</div>
                <div class="text-[10px] text-slate-500">Batas Min: {{ $item->stok_minimum }} {{ $item->satuan }}</div>
              </div>
              <div class="text-right">
                <span class="text-sm font-black text-red-600">{{ $item->stok }}</span>
                <span class="text-[10px] text-slate-400 block font-semibold">{{ $item->satuan }}</span>
              </div>
            </div>
          @empty
            <div class="py-4 text-center text-xs text-emerald-600 font-semibold">
              <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Semua stok produk dalam batas aman.
            </div>
          @endforelse
        </div>

        <a href="{{ route('produk.index') }}" class="btn-interactive block text-center w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
          <i class="fa-solid fa-boxes-stacked mr-1"></i> Buka Katalog &amp; Tambah Stok
        </a>
      </section>

    </div>

  </div>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Grafik Tren Penjualan & Pengeluaran 7 Hari Terakhir (Line & Area)
    const ctxTren = document.getElementById('chartTrenFinansial');
    if (ctxTren) {
      const labels = @json($chartLabels);
      const dataOmzet = @json($chartOmzet);
      const dataPengeluaran = @json($chartPengeluaran);

      const ctx = ctxTren.getContext('2d');
      const gradientOmzet = ctx.createLinearGradient(0, 0, 0, 260);
      gradientOmzet.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
      gradientOmzet.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

      const gradientExp = ctx.createLinearGradient(0, 0, 0, 260);
      gradientExp.addColorStop(0, 'rgba(239, 68, 68, 0.20)');
      gradientExp.addColorStop(1, 'rgba(239, 68, 68, 0.01)');

      new Chart(ctxTren, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Pemasukan (Omzet)',
              data: dataOmzet,
              borderColor: '#10b981',
              backgroundColor: gradientOmzet,
              borderWidth: 2.5,
              fill: true,
              tension: 0.35,
              pointBackgroundColor: '#ffffff',
              pointBorderColor: '#10b981',
              pointBorderWidth: 2,
              pointRadius: 4,
              pointHoverRadius: 6,
              pointHoverBackgroundColor: '#10b981',
              pointHoverBorderColor: '#ffffff',
            },
            {
              label: 'Pengeluaran',
              data: dataPengeluaran,
              borderColor: '#ef4444',
              backgroundColor: gradientExp,
              borderWidth: 2,
              borderDash: [5, 5],
              fill: true,
              tension: 0.35,
              pointBackgroundColor: '#ffffff',
              pointBorderColor: '#ef4444',
              pointBorderWidth: 2,
              pointRadius: 3,
              pointHoverRadius: 5,
              pointHoverBackgroundColor: '#ef4444',
              pointHoverBorderColor: '#ffffff',
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: { duration: 1500, easing: 'easeOutQuart' },
          interaction: { mode: 'index', intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#0f172a',
              titleFont: { size: 12, weight: 'bold' },
              bodyFont: { size: 11 },
              padding: 10,
              cornerRadius: 10,
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  if (label) label += ': ';
                  label += 'Rp ' + (context.parsed.y || 0).toLocaleString('id-ID');
                  return label;
                }
              }
            }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8' }
            },
            y: {
              grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [4, 4] },
              ticks: {
                font: { size: 10 },
                color: '#94a3b8',
                callback: function(value) {
                  if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                  if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                  return 'Rp ' + value;
                }
              }
            }
          }
        }
      });
    }

    // 2. Grafik Donut Metode Pembayaran
    const ctxMetode = document.getElementById('chartMetode');
    if (ctxMetode) {
      const tunai = {{ $metodeTunai }};
      const qris = {{ $metodeQris }};
      const transfer = {{ $metodeTransfer }};
      const total = tunai + qris + transfer;

      new Chart(ctxMetode, {
        type: 'doughnut',
        data: {
          labels: ['Tunai', 'QRIS', 'Transfer Bank'],
          datasets: [{
            data: total > 0 ? [tunai, qris, transfer] : [1, 0, 0],
            backgroundColor: ['#3b82f6', '#a855f7', '#06b6d4'],
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverOffset: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '72%',
          animation: { animateRotate: true, animateScale: true, duration: 1300, easing: 'easeOutQuart' },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#0f172a',
              titleFont: { size: 11, weight: 'bold' },
              bodyFont: { size: 11 },
              padding: 10,
              cornerRadius: 10,
              callbacks: {
                label: function(context) {
                  const val = context.raw || 0;
                  const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                  return ` ${context.label}: ${val} transaksi (${pct}%)`;
                }
              }
            }
          }
        }
      });
    }

    // 3. Grafik Batang Bulanan (Tahun Berjalan)
    const monthlyCanvas = document.getElementById('chartBulananKeuangan');
    if (monthlyCanvas) {
      new Chart(monthlyCanvas, {
        type: 'bar',
        data: {
          labels: @json($chartBulananLabels),
          datasets: [
            {
              label: 'Total Pemasukan (Rp)',
              data: @json($chartBulananPemasukan),
              backgroundColor: '#2563eb',
              borderRadius: 6
            },
            {
              label: 'Total Pengeluaran (Rp)',
              data: @json($chartBulananPengeluaran),
              backgroundColor: '#ef4444',
              borderRadius: 6
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: { duration: 1400, easing: 'easeOutQuart' },
          plugins: {
            legend: { position: 'top', labels: { font: { size: 11, weight: 'bold' } } },
            tooltip: {
              callbacks: {
                label: context => `${context.dataset.label}: Rp ${Number(context.raw || 0).toLocaleString('id-ID')}`
              }
            }
          },
          scales: {
            x: { grid: { display: false } },
            y: {
              beginAtZero: true,
              ticks: {
                callback: value => {
                  if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                  if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                  return 'Rp ' + value;
                }
              }
            }
          }
        }
      });
    }

  });
</script>
@endpush
