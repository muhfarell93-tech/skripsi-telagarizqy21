@extends('layouts.app')

@section('title', 'Detail Transaksi ' . $transaksi->kode_invoice)

@push('styles')
<style>
  @media print {
    body * { visibility: hidden; }
    #printableReceipt, #printableReceipt * { visibility: visible; }
    #printableReceipt { position: absolute; left: 0; top: 0; width: 100%; display: block !important; }
    .no-print { display: none !important; }
  }
</style>
@if($transaksi->status_pembayaran === 'belum_lunas' && $transaksi->snap_token)
<script type="text/javascript"
  src="https://app.sandbox.midtrans.com/snap/snap.js"
  data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6 no-print">
  
  <!-- Action Bar -->
  <div class="flex items-center justify-between">
    <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs transition flex items-center gap-1.5">
      <i class="fa-solid fa-arrow-left"></i> Kembali ke Kasir (POS)
    </a>
    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm">
        <i class="fa-solid fa-print"></i> Cetak Struk (Thermal / PDF)
      </button>
      <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition">
        Dashboard
      </a>
    </div>
  </div>

  <!-- Detail Invoice Card -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
      <div>
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">Faktur Penjualan</span>
        <h2 class="text-xl font-black text-slate-900">{{ $transaksi->kode_invoice }}</h2>
        <p class="text-xs text-slate-500 mt-0.5">Waktu: {{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }} WIB</p>
      </div>

      <div class="text-left sm:text-right" id="statusBadgeContainer">
        @if($transaksi->status_pembayaran === 'lunas')
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm pop-in-spring">
            <i class="fa-solid fa-circle-check text-emerald-600"></i> LUNAS TERVERIFIKASI SERVER
          </div>
          <div class="text-[10px] text-slate-400 mt-1">Waktu: {{ $transaksi->waktu_lunas ? $transaksi->waktu_lunas->format('d/m/Y H:i') : '-' }} WIB</div>
        @elseif($transaksi->status_pembayaran === 'belum_lunas')
          <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 status-pulse-amber">
              <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> MENUNGGU PEMBAYARAN
            </span>
            <div class="flex flex-wrap sm:justify-end gap-2 pt-1">
              @if($transaksi->snap_token)
                <button type="button" onclick="window.snap.pay('{{ $transaksi->snap_token }}')" class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-sm btn-interactive flex items-center gap-1.5">
                  <i class="fa-solid fa-qrcode"></i> Bayar Sekarang (QRIS)
                </button>
              @endif
              <button type="button" onclick="checkStatusManual()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-300 btn-interactive flex items-center gap-1">
                <i class="fa-solid fa-rotate"></i> Cek Server
              </button>
              <button type="button" onclick="simulateLunas()" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold rounded-xl border border-emerald-200 flex items-center gap-1">
                <i class="fa-solid fa-flask"></i> Uji Coba Lunas (Sandbox)
              </button>
            </div>
            <p class="text-[10px] text-slate-400">Sistem memantau konfirmasi valid server secara real-time...</p>
          </div>
        @else
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-800 border border-red-200">
            <i class="fa-solid fa-circle-xmark text-red-600"></i> DIBATALKAN / KADALUARSA
          </span>
        @endif
      </div>
    </div>

    <!-- Metadata Kasir & Outlet -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl text-xs border border-slate-200/60">
      <div>
        <span class="text-slate-400 block text-[10px] uppercase font-bold">Unit Usaha</span>
        <span class="font-bold text-slate-800">{{ $transaksi->outlet->nama_outlet ?? 'Telaga Rizqy 21' }}</span>
      </div>
      <div>
        <span class="text-slate-400 block text-[10px] uppercase font-bold">Kasir Petugas</span>
        <span class="font-bold text-slate-800">{{ $transaksi->user->name ?? 'Kasir' }}</span>
      </div>
      <div>
        <span class="text-slate-400 block text-[10px] uppercase font-bold">Metode Pembayaran</span>
        <span class="font-bold text-slate-800 uppercase">{{ $transaksi->metode_pembayaran }}</span>
      </div>
    </div>

    <!-- Tabel Rincian Item Barang -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-slate-700">
        <thead class="bg-slate-50 uppercase text-[10px] text-slate-400 font-bold border-b border-slate-100">
          <tr>
            <th class="py-3 px-4">Nama Produk Susu</th>
            <th class="py-3 px-4 text-center">Harga Satuan (Snapshot)</th>
            <th class="py-3 px-4 text-center">Qty</th>
            <th class="py-3 px-4 text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($transaksi->details as $item)
            <tr>
              <td class="py-3.5 px-4 font-bold text-slate-900">{{ $item->produk->nama_produk ?? 'Produk' }}</td>
              <td class="py-3.5 px-4 text-center text-slate-600">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
              <td class="py-3.5 px-4 text-center font-bold text-slate-800">{{ $item->kuantitas }}</td>
              <td class="py-3.5 px-4 text-right font-black text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Ringkasan Angka Finansial -->
    <div class="border-t border-slate-200 pt-4 flex justify-end">
      <div class="w-full sm:w-72 space-y-2 text-xs">
        <div class="flex justify-between text-slate-600">
          <span>Subtotal Tagihan:</span>
          <span class="font-bold text-slate-900">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Nominal Dibayar:</span>
          <span class="font-bold text-slate-900">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Uang Kembalian:</span>
          <span class="font-bold text-emerald-600">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-dashed border-slate-300">
          <span>Total Akhir:</span>
          <span class="text-blue-600 text-base">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>

  </div>

</div>

<!-- ==================== STRUK THERMAL UNTUK CETAK ==================== -->
<div id="printableReceipt" class="hidden max-w-sm mx-auto bg-white p-6 font-mono text-xs text-slate-900 leading-relaxed border border-slate-300">
  <div class="text-center space-y-1 border-b border-dashed border-slate-400 pb-3">
    <div class="text-sm font-bold uppercase tracking-wider">TELAGA RIZQY 21</div>
    <div class="text-[10px]">Peternakan &amp; Olahan Susu Kambing</div>
    <div class="text-[10px]">Jl. Sepat No. 3 RT/RW 27/11 Yosodadi, Metro Timur</div>
    <div class="text-[10px]">Telp: 0812-3456-7890</div>
  </div>

  <div class="py-2.5 space-y-1 border-b border-dashed border-slate-400 text-[11px]">
    <div class="flex justify-between"><span>No. Faktur:</span><span class="font-bold">{{ $transaksi->kode_invoice }}</span></div>
    <div class="flex justify-between"><span>Waktu:</span><span>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</span></div>
    <div class="flex justify-between"><span>Kasir:</span><span>{{ $transaksi->user->name ?? 'Kasir' }}</span></div>
    <div class="flex justify-between"><span>Metode:</span><span class="font-bold uppercase">{{ $transaksi->metode_pembayaran }}</span></div>
    <div class="flex justify-between"><span>Status:</span><span class="font-bold uppercase text-emerald-700">{{ $transaksi->status_pembayaran }}</span></div>
  </div>

  <div class="py-2.5 space-y-1.5 border-b border-dashed border-slate-400 text-[11px]">
    @foreach($transaksi->details as $item)
      <div>
        <div class="font-bold">{{ $item->produk->nama_produk ?? 'Produk' }}</div>
        <div class="flex justify-between text-slate-600">
          <span>{{ $item->kuantitas }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
          <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
      </div>
    @endforeach
  </div>

  <div class="py-2.5 space-y-1 text-[11px]">
    <div class="flex justify-between"><span>Total Belanja:</span><span class="font-bold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span></div>
    <div class="flex justify-between"><span>Total Bayar:</span><span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span></div>
    <div class="flex justify-between"><span>Kembalian:</span><span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span></div>
  </div>

  <div class="text-center pt-4 space-y-1 text-[10px] text-slate-600 border-t border-dashed border-slate-400">
    <div class="font-bold">Terima Kasih Atas Kunjungan Anda</div>
    <div>Susu Kambing Alami &amp; Sehat Bergizi</div>
    <div class="text-[9px] text-slate-400 pt-1">Simpan struk ini sebagai bukti pembayaran sah.</div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const txId = {{ $transaksi->id }};
  const isLunasInitial = {{ $transaksi->status_pembayaran === 'lunas' ? 'true' : 'false' }};
  let showPollTimer = null;

  function checkStatusManual() {
    fetch(`/transaksi/${txId}/status`, {
      headers: { "Accept": "application/json" }
    })
    .then(r => r.json())
    .then(data => {
      if (data.is_lunas) {
        handleSuccessVerification(data);
      } else {
        window.showToast("Status Pembayaran", data.message, "warning");
      }
    })
    .catch(() => {
      window.showToast("Gagal Cek", "Tidak dapat menghubungi server.", "error");
    });
  }

  function simulateLunas() {
    fetch(`/transaksi/${txId}/simulasi-lunas`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}",
        "Accept": "application/json"
      }
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        checkStatusManual();
      }
    });
  }

  function handleSuccessVerification(data) {
    if (showPollTimer) clearInterval(showPollTimer);

    // Render Animasi Sukses dan Status Baru
    const badgeContainer = document.getElementById('statusBadgeContainer');
    if (badgeContainer) {
      badgeContainer.innerHTML = `
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm pop-in-spring">
          <i class="fa-solid fa-circle-check text-emerald-600"></i> LUNAS TERVERIFIKASI SERVER
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Waktu: ${data.waktu_lunas || 'Baru Saja'} WIB</div>
      `;
    }

    // Tampilkan Notifikasi Toast
    window.showToast(
      "Pembayaran Berhasil Terverifikasi!",
      `Faktur ${data.kode_invoice} telah diverifikasi lunas oleh server.`,
      "success",
      5000
    );

    // Refresh halaman setelah 2.5 detik untuk memperbarui struk cetak
    setTimeout(() => {
      window.location.reload();
    }, 2500);
  }

  // Polling jika status transaksi saat ini masih 'belum_lunas'
  if (!isLunasInitial) {
    showPollTimer = setInterval(() => {
      fetch(`/transaksi/${txId}/status`, {
        headers: { "Accept": "application/json" }
      })
      .then(r => r.json())
      .then(data => {
        if (data.is_lunas) {
          handleSuccessVerification(data);
        }
      })
      .catch(() => {});
    }, 3000);
  }
</script>
@endpush

