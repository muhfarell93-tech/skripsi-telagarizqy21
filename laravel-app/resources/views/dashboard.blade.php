@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

  <!-- Welcome Banner -->
  <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10 relative overflow-hidden transition-all duration-300">
    <div class="relative z-10 max-w-2xl">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-md mb-3 border border-white/20">
        <i class="fa-solid fa-store"></i> Peternakan &amp; Olahan Susu Telaga Rizqy 21 Metro
      </span>
      <h1 class="text-2xl sm:text-3xl font-black tracking-tight leading-tight">
        Selamat Datang, {{ auth()->user()->name ?? 'Administrator' }}!
      </h1>
      <p class="text-sm text-blue-100 mt-2 leading-relaxed">
        Sistem Informasi Transaksi Keuangan siap digunakan untuk transaksi kasir (POS), pembayaran QRIS Midtrans, pemantauan stok susu, dan rekapitulasi laba rugi.
      </p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('pos.index') }}" class="px-5 py-2.5 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-xl text-xs transition-all shadow-md hover:shadow-lg flex items-center gap-2 btn-interactive">
          <i class="fa-solid fa-cash-register"></i> Buka Mesin Kasir (POS)
        </a>
        <a href="{{ route('laporan.index') }}" class="px-5 py-2.5 bg-blue-500/30 hover:bg-blue-500/50 text-white font-bold rounded-xl text-xs backdrop-blur-md transition-all flex items-center gap-2 border border-white/20 btn-interactive">
          <i class="fa-solid fa-file-lines"></i> Rekap Laporan Keuangan
        </a>
        <a href="{{ route('produk.index') }}" class="px-5 py-2.5 bg-indigo-900/40 hover:bg-indigo-900/60 text-white font-bold rounded-xl text-xs backdrop-blur-md transition-all flex items-center gap-2 border border-white/20 btn-interactive">
          <i class="fa-solid fa-boxes-stacked"></i> Kelola Stok Produk
        </a>
      </div>
    </div>
    <div class="absolute -right-8 -bottom-12 text-blue-400/15 text-9xl pointer-events-none select-none">
      <i class="fa-solid fa-bottle-droplet"></i>
    </div>
  </div>

  <!-- Metric Statistics Cards with Micro-interactions & Counting Animation -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- 1. Omzet Hari Ini -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between stat-card stat-card-blue">
      <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Omzet Hari Ini</span>
        <span class="text-xl sm:text-2xl font-black text-slate-900 mt-1 block tracking-tight"
              data-counter-target="{{ (int)$omzetHariIni }}"
              data-counter-type="rupiah">
          Rp {{ number_format($omzetHariIni, 0, ',', '.') }}
        </span>
        <span class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1 font-semibold">
          <i class="fa-solid fa-receipt text-blue-500 text-[10px]"></i>
          <span data-counter-target="{{ (int)$transaksiHariIni }}" data-counter-type="number" data-counter-suffix=" Transaksi Lunas">{{ $transaksiHariIni }} Transaksi Lunas</span>
        </span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl stat-icon shadow-sm">
        <i class="fa-solid fa-wallet"></i>
      </div>
    </div>

    <!-- 2. Penjualan Bulan Ini -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between stat-card stat-card-emerald">
      <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Penjualan Bulan Ini</span>
        <span class="text-xl sm:text-2xl font-black text-emerald-600 mt-1 block tracking-tight"
              data-counter-target="{{ (int)$omzetBulanIni }}"
              data-counter-type="rupiah">
          Rp {{ number_format($omzetBulanIni, 0, ',', '.') }}
        </span>
        <span class="text-[11px] text-emerald-700 mt-1.5 flex items-center gap-1 font-semibold">
          <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i>
          <span>Total Akumulasi Masuk</span>
        </span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl stat-icon shadow-sm">
        <i class="fa-solid fa-chart-line"></i>
      </div>
    </div>

    <!-- 3. Pengeluaran Kas -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between stat-card stat-card-red">
      <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pengeluaran Kas</span>
        <span class="text-xl sm:text-2xl font-black text-red-600 mt-1 block tracking-tight"
              data-counter-target="{{ (int)$pengeluaranBulanIni }}"
              data-counter-type="rupiah">
          Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}
        </span>
        <span class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1 font-semibold">
          <i class="fa-solid fa-arrow-down-long text-red-500 text-[10px]"></i>
          <span>Beban Operasional Kandang</span>
        </span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl stat-icon shadow-sm">
        <i class="fa-solid fa-arrow-trend-down"></i>
      </div>
    </div>

    <!-- 4. Estimasi Arus Kas / Laba Kotor -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between stat-card stat-card-purple">
      <div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Estimasi Arus Kas</span>
        <span class="text-xl sm:text-2xl font-black text-purple-600 mt-1 block tracking-tight"
              data-counter-target="{{ (int)$labaKotorBulanIni }}"
              data-counter-type="rupiah">
          Rp {{ number_format($labaKotorBulanIni, 0, ',', '.') }}
        </span>
        <span class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1 font-semibold">
          <i class="fa-solid fa-scale-balanced text-purple-500 text-[10px]"></i>
          <span>Margin Penjualan Bersih</span>
        </span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl stat-icon shadow-sm">
        <i class="fa-solid fa-coins"></i>
      </div>
    </div>

  </div>

  <!-- Animated Interactive Charts Section -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- 1. Tren Finansial 7 Hari Terakhir (8 Kolom) -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 transition-all duration-300 hover:shadow-md">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-chart-area text-blue-600"></i>
            Tren Penjualan &amp; Pengeluaran (7 Hari Terakhir)
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">Visualisasi perbandingan omzet penjualan produk susu dan arus kas keluar</p>
        </div>
        <div class="flex items-center gap-3 text-xs font-semibold">
          <span class="flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Penjualan
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

    <!-- 2. Proporsi Metode Pembayaran (4 Kolom) -->
    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 transition-all duration-300 hover:shadow-md flex flex-col justify-between">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-chart-pie text-indigo-600"></i>
          Metode Pembayaran
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">Distribusi transaksi Tunai, QRIS, &amp; Transfer</p>
      </div>
      
      <div class="relative w-full h-[200px] flex items-center justify-center my-auto">
        <canvas id="chartMetode"></canvas>
      </div>

      <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100 text-center text-xs">
        <div class="p-2 bg-slate-50 rounded-xl">
          <span class="text-[10px] text-slate-400 font-bold block uppercase">Tunai</span>
          <span class="font-black text-slate-800 text-sm" data-counter-target="{{ $metodeTunai }}" data-counter-type="number">{{ $metodeTunai }}</span>
        </div>
        <div class="p-2 bg-purple-50 rounded-xl">
          <span class="text-[10px] text-purple-600 font-bold block uppercase">QRIS</span>
          <span class="font-black text-purple-700 text-sm" data-counter-target="{{ $metodeQris }}" data-counter-type="number">{{ $metodeQris }}</span>
        </div>
        <div class="p-2 bg-blue-50 rounded-xl">
          <span class="text-[10px] text-blue-600 font-bold block uppercase">Transfer</span>
          <span class="font-black text-blue-700 text-sm" data-counter-target="{{ $metodeTransfer }}" data-counter-type="number">{{ $metodeTransfer }}</span>
        </div>
      </div>
    </div>

  </div>

  <!-- Grid: Aktivitas Transaksi Terbaru & Peringatan Stok Menipis -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Transaksi Terbaru (8 Kolom) -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
            <i class="fa-solid fa-clock-rotate-left"></i>
          </div>
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aktivitas Transaksi Kasir Terbaru</h3>
        </div>
        <a href="{{ route('laporan.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 transition">
          Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
            <tr>
              <th class="px-6 py-3">No. Invoice</th>
              <th class="px-4 py-3">Kasir</th>
              <th class="px-4 py-3">Metode</th>
              <th class="px-4 py-3">Total Belanja</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($transaksiTerbaru as $trx)
              <tr class="hover:bg-blue-50/40 transition-colors">
                <td class="px-6 py-3.5 font-bold text-slate-900 flex items-center gap-2">
                  <i class="fa-solid fa-receipt text-slate-400 text-xs"></i>
                  {{ $trx->kode_invoice }}
                </td>
                <td class="px-4 py-3.5 text-slate-600 font-medium">{{ $trx->user->name ?? 'Kasir' }}</td>
                <td class="px-4 py-3.5">
                  <span class="uppercase font-bold px-2 py-0.5 rounded text-[10px] tracking-wider {{ $trx->metode_pembayaran === 'qris' ? 'bg-purple-100 text-purple-800 border border-purple-200' : ($trx->metode_pembayaran === 'transfer' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                    {{ $trx->metode_pembayaran }}
                  </span>
                </td>
                <td class="px-4 py-3.5 font-extrabold text-slate-900">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                <td class="px-4 py-3.5">
                  @if($trx->status_pembayaran === 'lunas')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                      <i class="fa-solid fa-circle-check text-emerald-600"></i> LUNAS
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                      <i class="fa-regular fa-clock text-amber-600"></i> {{ strtoupper($trx->status_pembayaran) }}
                    </span>
                  @endif
                </td>
                <td class="px-6 py-3.5 text-right">
                  <a href="{{ route('transaksi.show', $trx->id) }}" class="p-1.5 px-2.5 text-blue-600 hover:bg-blue-100 rounded-lg transition font-bold text-xs inline-flex items-center gap-1 btn-interactive" title="Lihat Struk">
                    <i class="fa-solid fa-receipt"></i> Struk
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada transaksi tercatat hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Peringatan Stok Susu Menipis (4 Kolom) -->
    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 transition-all duration-300 hover:shadow-md flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2 text-amber-600">
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Peringatan Stok</h3>
          </div>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $stokMenipis->count() > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800' }}">
            {{ $stokMenipis->count() }} Produk
          </span>
        </div>

        <div class="space-y-3 mt-4">
          @forelse($stokMenipis as $item)
            <div class="p-3 bg-amber-50/60 border border-amber-200/80 rounded-xl flex items-center justify-between transition-transform hover:scale-[1.02]">
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
            <div class="py-8 text-center text-xs text-slate-400">
              <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl mx-auto mb-2">
                <i class="fa-solid fa-boxes-stacked"></i>
              </div>
              <span class="font-bold text-slate-600 block">Stok Aman</span>
              Seluruh stok produk susu kambing dalam kondisi aman di atas batas minimum.
            </div>
          @endforelse
        </div>
      </div>

      <a href="{{ route('produk.index') }}" class="block text-center w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all btn-interactive mt-4">
        <i class="fa-solid fa-boxes-stacked mr-1"></i> Kelola Persediaan Produk
      </a>
    </div>

  </div>

</div>
@endsection

@push('scripts')
<script>
  // =========================================================
  // ANIMATED CHARTS FOR DASHBOARD (CHART.JS)
  // =========================================================
  document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Grafik Tren Penjualan & Pengeluaran 7 Hari Terakhir
    const ctxTren = document.getElementById('chartTrenFinansial');
    if (ctxTren) {
      const labels = @json($chartLabels);
      const dataOmzet = @json($chartOmzet);
      const dataPengeluaran = @json($chartPengeluaran);

      // Create gradient for omzet area
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
              label: 'Penjualan (Omzet)',
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
              pointHoverBorderWidth: 2,
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
              pointHoverBorderWidth: 2,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: {
            duration: 1500,
            easing: 'easeOutQuart'
          },
          interaction: {
            mode: 'index',
            intersect: false
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: '#0f172a',
              titleFont: { size: 12, weight: 'bold' },
              bodyFont: { size: 11 },
              padding: 10,
              cornerRadius: 10,
              displayColors: true,
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
              grid: {
                display: false
              },
              ticks: {
                font: { size: 11, weight: '600' },
                color: '#94a3b8'
              }
            },
            y: {
              grid: {
                color: 'rgba(226, 232, 240, 0.6)',
                borderDash: [4, 4]
              },
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
            backgroundColor: [
              '#3b82f6', // Tunai (Blue)
              '#a855f7', // QRIS (Purple)
              '#06b6d4'  // Transfer (Cyan)
            ],
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverOffset: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '72%',
          animation: {
            animateRotate: true,
            animateScale: true,
            duration: 1300,
            easing: 'easeOutQuart'
          },
          plugins: {
            legend: {
              display: false
            },
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

  });
</script>
@endpush
