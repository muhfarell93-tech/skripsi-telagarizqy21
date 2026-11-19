@extends('layouts.app')

@section('title', 'Mesin Kasir (POS)')

@push('styles')
<!-- Midtrans Snap JS selected from environment configuration. -->
<script type="text/javascript"
  src="{{ config('midtrans.snap_url') }}"
  data-client-key="{{ config('midtrans.client_key') }}"></script>

<!-- Google Fonts for Modern Cafe / Kopi Kenangan Aesthetics -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
  .font-cafe-title {
    font-family: 'Playfair Display', Georgia, serif;
    letter-spacing: -0.015em;
  }

  .font-cafe-price {
    font-family: 'Playfair Display', Georgia, serif;
    font-style: italic;
  }

  .cafe-card {
    background: #ffffff;
    border: 1px solid #ebe4d8;
    border-radius: 26px;
    box-shadow: 0 4px 14px rgba(70, 40, 25, 0.04);
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .cafe-card:hover {
    transform: translateY(-5px);
    border-color: #d6c6b2;
    box-shadow: 0 16px 30px rgba(70, 40, 25, 0.1);
  }

  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }

  .no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  /* Animasi Halus & Partikel Pembayaran Berhasil */
  @keyframes bounce-subtle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
  }
  .animate-bounce-subtle {
    animation: bounce-subtle 1.8s ease-in-out infinite;
  }
  @keyframes sparkle-float {
    0% { transform: scale(0) rotate(0deg); opacity: 0; }
    50% { transform: scale(1.2) rotate(180deg); opacity: 1; }
    100% { transform: scale(0.8) rotate(360deg); opacity: 0; }
  }
  .sparkle-particle {
    position: absolute;
    animation: sparkle-float 1.8s ease-out infinite;
    pointer-events: none;
  }
  .sparkle-1 { top: -6px; left: 10px; animation-delay: 0.1s; }
  .sparkle-2 { top: -8px; right: 12px; animation-delay: 0.4s; }
  .sparkle-3 { bottom: -6px; left: 14px; animation-delay: 0.7s; }
  .sparkle-4 { bottom: -8px; right: 12px; animation-delay: 0.9s; }
</style>
@endpush

@php
// Metadata khusus varian olahan susu kambing Telaga Rizqy 21
$productMeta = [
'PRD-001' => ['desc' => 'Susu bubuk kambing dengan ekstrak jahe merah asli penghangat tubuh dan penjaga imunitas.', 'badge' => 'HANGAT SEHAT'],
'PRD-002' => ['desc' => 'Susu bubuk rasa original gurih lembut tanpa bau prengus, kaya kalsium alami.', 'badge' => 'BEST SELLER'],
'PRD-003' => ['desc' => 'Manis legit alami gula aren khas Lampung berpadu dengan gurihnya susu kambing.', 'badge' => 'FAVORIT'],
'PRD-004' => ['desc' => 'Cokelat kaya rasa disukai anak-anak dan keluarga, nikmat diseduh hangat maupun dingin.', 'badge' => 'KIDS FAVORITE'],
'PRD-005' => ['desc' => '100% susu kambing murni segar tanpa pengawet dari peternakan Telaga Rizqy Metro.', 'badge' => 'MURNI 100%'],
'PRD-006' => ['desc' => 'Susu kambing pasteurisasi dingin dengan aroma vanila lembut nan creamy yang menyegarkan.', 'badge' => 'DINGIN SEGAR'],
'PRD-007' => ['desc' => 'Kombinasi rasa stroberi manis asam segar yang memanjakan lidah saat disajikan dingin.', 'badge' => 'BEST SELLER'],
'PRD-008' => ['desc' => 'Cita rasa cokelat kaya berpadu dengan kelembutan susu kambing pasteurisasi dingin.', 'badge' => 'FAVORIT'],
'PRD-009' => ['desc' => 'Susu kambing kemasan kotak UHT praktis siap minum kapan saja dan di mana saja.', 'badge' => 'SIAP MINUM'],
'PRD-010' => ['desc' => 'Khasiat kurma pilihan dan madu lebah murni bersatu dalam kelembutan susu kambing dingin.', 'badge' => 'SUPERFOOD'],
'PRD-011' => ['desc' => 'Sensasi otentik teh hijau matcha dengan susu kambing kaya antioksidan alami.', 'badge' => 'NEW FLAVOR'],
'PRD-012' => ['desc' => 'Kopi susu kekinian khas gula aren berpadu nikmatnya susu kambing rendah laktosa.', 'badge' => 'CAFE SIGNATURE'],
'PRD-013' => ['desc' => 'Kesegaran aroma buah melon manis alami dipadukan dalam susu kambing pasteurisasi.', 'badge' => 'NEW FLAVOR'],
'PRD-014' => ['desc' => 'Minuman probiotik alami hasil fermentasi susu kambing untuk kesehatan saluran cerna.', 'badge' => 'PROBIOTIK ALAMI'],
'PRD-015' => ['desc' => 'Formula istimewa madu lebah dan propolis alami untuk stamina dan daya tahan maksimal.', 'badge' => 'PREMIUM IMMUNE'],
'PRD-016' => ['desc' => 'Kandungan kolostrum susu kambing hari pertama dengan imunoglobulin tinggi untuk vitalitas.', 'badge' => 'KOLOSTRUM GOLD'],
'PRD-017' => ['desc' => 'Beli 3 Botol Lebih Hemat: Cokelat, Stroberi, dan Vanila sekaligus!', 'badge' => 'PROMO BUNDLE'],
'PRD-018' => ['desc' => 'Paket komplit hemat: 2 kotak Susu Bubuk Rizqiku + 1 Liter Susu Murni Segar.', 'badge' => 'PAKET KELUARGA'],
];
@endphp

@section('content')
<!-- HTML Audio Element untuk Notifikasi Suara Pembayaran Berhasil (Desktop & Mobile) -->
<audio id="audioPaymentSuccess" preload="auto" playsinline>
  <source src="{{ asset('audio/payment-success.wav') }}" type="audio/wav">
</audio>

<!-- Floating Banner Notifikasi Visual Pembayaran Berhasil -->
<div id="floatingSuccessBanner" class="fixed top-5 left-1/2 -translate-x-1/2 z-[100] transition-all duration-500 pointer-events-none transform -translate-y-20 opacity-0">
  <div class="bg-white/95 backdrop-blur-md px-5 py-3 rounded-2xl border-2 border-emerald-500 shadow-2xl flex items-center gap-3.5 text-slate-800">
    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-base shadow-inner animate-pulse">
      <i class="fa-solid fa-circle-check"></i>
    </div>
    <div>
      <div class="text-[11px] font-black uppercase tracking-wider text-emerald-600 flex items-center gap-1.5">
        <span>PEMBAYARAN BERHASIL!</span>
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
      </div>
      <div class="text-xs font-bold text-slate-900" id="bannerSuccessInvoice">Faktur Lunas Terverifikasi</div>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  <!-- Kolom Kiri: Katalog Produk Susu (8 Kolom di Layar Lebar) -->
  <div class="lg:col-span-7 xl:col-span-8 space-y-4">

    <!-- Search Bar & Counter Menu Cafe Style -->
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-[#EBE4D8] flex flex-col sm:flex-row gap-3 items-center justify-between">
      <div class="relative w-full sm:w-80">
        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-400">
          <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </span>
        <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="Cari varian susu, rasa, atau kode..."
          class="input-interactive w-full pl-10 pr-4 py-2.5 bg-[#FAF7F2] border border-[#E5DDD0] focus:border-[#8B2635] focus:ring-1 focus:ring-[#8B2635] rounded-2xl text-xs text-stone-800 placeholder-stone-400 transition">
      </div>
      <div class="flex items-center gap-2 self-end sm:self-center text-xs text-stone-500 font-medium">
        <span>Menampilkan:</span>
        <span class="px-2.5 py-1 rounded-xl bg-[#FAF7F2] border border-[#EBE4D8] text-stone-800 font-bold">
          <strong id="visibleCount" class="text-[#8B2635]">{{ $produks->count() }}</strong> / {{ $produks->count() }} Varian
        </span>
      </div>
    </div>

    <!-- Category Pills Navigation (Horizontal Scrollable Tabs) -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
      <button type="button" onclick="filterCategory('all', this)"
        class="cat-pill-btn px-4 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all duration-200 bg-[#5A1C24] text-white shadow-md">
        <i class="fa-solid fa-mug-hot mr-1 text-[10px]"></i> Semua Menu ({{ $produks->count() }})
      </button>
      @foreach($kategoris as $kat)
      <button type="button" onclick="filterCategory('{{ $kat->nama_kategori }}', this)"
        class="cat-pill-btn px-3.5 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all duration-200 bg-white text-stone-700 border border-[#EBE4D8] hover:border-[#D6C6B2] hover:bg-[#FAF7F2]">
        {{ $kat->nama_kategori }} <span class="ml-1 text-[10px] text-stone-400 font-bold">({{ $kat->produks_count }})</span>
      </button>
      @endforeach
    </div>

    <!-- Product Grid with Cafe-Style Aesthetic Cards -->
    <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-4" id="productGrid">
      @foreach($produks as $p)
      @php
      $meta = $productMeta[$p->kode_produk] ?? [
      'desc' => ($p->kategori->deskripsi ?? 'Varian olahan susu kambing berkualitas tinggi Telaga Rizqy 21.'),
      'badge' => ($p->kategori->nama_kategori ?? 'SUSU KAMBING')
      ];
      $isPromo = str_contains(strtolower($p->nama_produk), 'paket') || str_contains(strtolower($p->kategori->nama_kategori ?? ''), 'paket');
      @endphp

      <div class="product-card cafe-card group relative overflow-hidden flex flex-col justify-between cursor-pointer select-none active:scale-[0.98] transition-transform"
        data-name="{{ strtolower($p->nama_produk) }}"
        data-code="{{ strtolower($p->kode_produk) }}"
        data-category="{{ $p->kategori->nama_kategori ?? 'Lainnya' }}"
        onclick="addToCart({{ $p->id }}, '{{ addslashes($p->nama_produk) }}', {{ $p->harga_jual }}, {{ $p->stok }})">

        <!-- Bagian Atas: Gambar Minuman/Produk Aesthetic Cafe -->
        <div class="relative w-full aspect-[4/3] bg-gradient-to-b from-[#FDFBF7] to-[#F3ECE1] p-3 flex items-center justify-center overflow-hidden border-b border-[#EFE8DD]">

          <!-- Badge Promo / Category (seperti pada contoh referensi) -->
          <div class="absolute top-2.5 left-2.5 z-10">
            @if($isPromo)
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold tracking-wider bg-[#5A1C24] text-white uppercase shadow-sm">
              <i class="fa-solid fa-fire text-[8px] text-amber-300"></i> PROMO BUNDLE
            </span>
            @else
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold tracking-wider bg-[#8B2635] text-white uppercase shadow-sm">
              {{ $meta['badge'] }}
            </span>
            @endif
          </div>

          <!-- Badge Stok -->
          <div class="absolute top-2.5 right-2.5 z-10">
            @if($p->stok <= $p->stok_minimum)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-100 text-red-700 border border-red-200">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Sisa {{ $p->stok }}
              </span>
              @else
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-medium bg-white/85 text-stone-600 border border-stone-200/60 backdrop-blur-xs">
                Stok: {{ $p->stok }}
              </span>
              @endif
          </div>

          <!-- Visual Gambar / Ilustrasi Varian Susu -->
          <div class="w-full h-full flex items-center justify-center transform group-hover:scale-105 transition-transform duration-300 ease-out">
            @if($p->foto && file_exists(public_path($p->foto)))
            <img src="{{ asset($p->foto) }}" alt="{{ $p->nama_produk }}" class="max-h-full max-w-full object-contain drop-shadow-md">
            @else
            <div class="w-16 h-16 rounded-2xl bg-amber-100/60 text-[#8B2635] flex items-center justify-center text-3xl">
              <i class="fa-solid fa-mug-hot"></i>
            </div>
            @endif
          </div>
        </div>

        <!-- Bagian Bawah: Informasi Detail Produk (Aesthetic Kopi Kenangan) -->
        <div class="p-4 flex flex-col justify-between flex-1 bg-white">
          <div>
            <h4 class="font-cafe-title font-bold text-slate-900 text-sm md:text-[15px] leading-snug group-hover:text-[#8B2635] transition-colors line-clamp-1">
              {{ $p->nama_produk }}
            </h4>
            <p class="text-[11px] text-[#7A7165] line-clamp-2 leading-relaxed mt-1 min-h-[32px]">
              {{ $meta['desc'] }}
            </p>
          </div>

          <!-- Baris Harga & Tombol Tambah Pesanan -->
          <div class="mt-3 pt-2.5 border-t border-[#F4EFE6] flex items-center justify-between">
            <div class="flex flex-col">
              <span class="font-cafe-price text-[#8B2635] text-xs md:text-sm font-semibold tracking-tight">
                Mulai dari Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
              </span>
            </div>
            <button type="button"
              class="w-7 h-7 rounded-full bg-[#8B2635] group-hover:bg-[#5A1C24] text-white flex items-center justify-center text-xs shadow-sm transition-all group-hover:scale-110 active:scale-90"
              title="Tambah ke keranjang">
              <i class="fa-solid fa-plus text-[10px]"></i>
            </button>
          </div>
        </div>

      </div>
      @endforeach
    </div>

    <!-- Notice jika filter / pencarian tidak menemukan produk -->
    <div id="catalogEmptyNotice" class="hidden bg-white p-12 rounded-3xl border border-[#EBE4D8] text-center">
      <div class="w-16 h-16 rounded-full bg-amber-50 text-[#8B2635] flex items-center justify-center mx-auto text-2xl mb-3">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>
      <h4 class="font-cafe-title font-bold text-slate-800 text-base">Tidak Ada Varian yang Cocok</h4>
      <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
        Silakan ubah kata kunci pencarian atau pilih kategori lain untuk melihat varian susu kambing lainnya.
      </p>
    </div>

  </div>

  <!-- Kolom Kanan: Keranjang & Panel Pembayaran (4 Kolom di Layar Lebar) -->
  <div class="lg:col-span-5 xl:col-span-4 space-y-4">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 flex flex-col justify-between min-h-[580px]">

      <div>
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shadow-sm">
              <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Keranjang Pesanan</h3>
          </div>
          <div class="flex items-center gap-1.5 sm:gap-2">
            <!-- Tombol Pengaturan Suara (Aktif / Nonaktif) -->
            <button type="button" id="btnToggleSound" onclick="toggleSoundSetting()" title="Pengaturan Suara Pembayaran (Klik untuk On/Off)"
              class="px-2.5 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold border border-emerald-200 transition flex items-center gap-1.5 btn-interactive shadow-xs">
              <i id="iconToggleSound" class="fa-solid fa-volume-high text-emerald-600"></i>
              <span id="textToggleSound">Suara: ON</span>
            </button>

            <!-- Tombol Uji Coba Suara -->
            <button type="button" onclick="testPaymentSound()" title="Tes Suara Notifikasi Kasir" 
              class="px-2 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold border border-slate-200 transition flex items-center gap-1 btn-interactive shadow-xs">
              <i class="fa-solid fa-play text-[9px] text-emerald-600"></i>
              <span class="hidden sm:inline">Tes</span>
            </button>

            <!-- Kosongkan Keranjang -->
            <button onclick="clearCart()" class="text-[11px] font-bold text-red-500 hover:text-red-700 hover:underline transition ml-0.5">
              Kosongkan
            </button>
          </div>
        </div>

        <!-- Cart Items List with Smooth Transitions -->
        <div id="cartContainer" class="divide-y divide-slate-100 max-h-72 overflow-y-auto my-3 pr-1">
          <div id="emptyCartMessage" class="py-12 text-center text-xs text-slate-400">
            <i class="fa-solid fa-basket-shopping text-3xl text-slate-200 mb-2 block"></i>
            Keranjang masih kosong. Klik produk di sebelah kiri untuk menambahkan.
          </div>
        </div>
      </div>

      <!-- Checkout & Payment Panel -->
      <div class="space-y-4 pt-4 border-t border-slate-200">

        <!-- Total Hitungan -->
        <div class="space-y-1.5 text-xs text-slate-600">
          <div class="flex justify-between">
            <span>Total Item</span>
            <span id="txtTotalQty" class="font-bold text-slate-900">0</span>
          </div>
          <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-dashed border-slate-200">
            <span>Total Belanja</span>
            <span id="txtGrandTotal" class="text-blue-600 text-lg">Rp 0</span>
          </div>
        </div>

        <!-- Input Nama Pembeli / Pelanggan -->
        <div>
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Pelanggan / Catatan</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs pointer-events-none">
              <i class="fa-solid fa-user"></i>
            </span>
            <input type="text" id="inputCustomer" placeholder="Contoh: Ibu Nurul / dr. Hendra / Pelanggan Umum"
              class="input-interactive w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800">
          </div>
        </div>

        <!-- Pilihan Metode Pembayaran dengan Efek Aktif -->
        <div>
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Metode Pembayaran</label>
          <div class="grid grid-cols-2 gap-2" id="methodGroup">
            <label id="lblTunai" class="btn-interactive border-2 border-blue-600 bg-blue-50/60 rounded-2xl p-3 flex items-center gap-2 text-xs font-bold text-blue-900 transition">
              <input type="radio" name="payment_method" value="tunai" checked onchange="togglePaymentMethod('tunai')" class="text-blue-600 focus:ring-blue-500">
              <i class="fa-solid fa-money-bill-wave text-blue-600"></i> Tunai (Cash)
            </label>
            <label id="lblQris" class="btn-interactive border border-slate-200 hover:border-slate-300 rounded-2xl p-3 flex items-center gap-2 text-xs font-bold text-slate-700 transition">
              <input type="radio" name="payment_method" value="qris" onchange="togglePaymentMethod('qris')" class="text-blue-600 focus:ring-blue-500">
              <i class="fa-solid fa-qrcode text-purple-600"></i> Online (QRIS)
            </label>
          </div>
        </div>

        <!-- Input Bayar Tunai dengan Real-time Validation Micro-Interactions -->
        <div id="cashInputSection" class="space-y-1.5">
          <div class="flex justify-between items-center text-xs">
            <label class="font-bold text-slate-700">Uang Tunai Diterima (Rp):</label>
            <span id="txtKembalian" class="font-bold text-slate-400">Kembalian: Rp 0</span>
          </div>

          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-xs" id="currencyPrefix">
              Rp
            </span>
            <input type="number" id="inputCash" oninput="validateCashPayment()" placeholder="Masukkan nominal uang pembeli..."
              class="input-interactive w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-900">

            <!-- Real-time Status Icon -->
            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
              <i id="cashSuccessIcon" class="fa-solid fa-circle-check text-emerald-500 input-feedback-icon"></i>
              <i id="cashErrorIcon" class="fa-solid fa-circle-exclamation text-red-500 input-feedback-icon absolute"></i>
            </span>
          </div>

          <!-- Tombol Pintas Uang Tunai (Quick Cash Shortcuts) -->
          <div class="flex flex-wrap items-center gap-1.5 pt-1">
            <span class="text-[10px] text-slate-400 font-bold mr-0.5">Pintas:</span>
            <button type="button" onclick="setCashAmount('pas')" class="btn-interactive px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg text-[11px] border border-blue-200 shadow-none">
              ⚡ Uang Pas
            </button>
            <button type="button" onclick="setCashAmount(50000)" class="btn-interactive px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[11px] shadow-none">
              Rp 50.000
            </button>
            <button type="button" onclick="setCashAmount(100000)" class="btn-interactive px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[11px] shadow-none">
              Rp 100.000
            </button>
            <button type="button" onclick="setCashAmount(200000)" class="btn-interactive px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[11px] shadow-none">
              Rp 200.000
            </button>
          </div>

          <!-- Pesan Error / Sukses Real-time -->
          <div id="cashErrorMsg" class="form-feedback-message text-[11px] font-semibold text-red-600 flex items-center gap-1.5 pt-0.5">
            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> <span id="cashErrorText">Nominal uang tunai kurang dari total belanja.</span>
          </div>
          <div id="cashSuccessMsg" class="form-feedback-message text-[11px] font-semibold text-emerald-600 flex items-center gap-1.5 pt-0.5">
            <i class="fa-solid fa-circle-check text-[10px]"></i> <span>Nominal uang mencukupi. Kembalian telah dihitung otomatis.</span>
          </div>
        </div>

        <!-- Tombol Checkout Interaktif -->
        <button id="btnProses" onclick="processCheckout()" disabled
          class="btn-interactive btn-ripple w-full py-4 px-4 bg-blue-600 text-white rounded-2xl text-xs uppercase tracking-wider shadow-lg shadow-blue-500/25">
          <span class="btn-text flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-check"></i> Proses &amp; Selesaikan Transaksi
          </span>
        </button>

      </div>

    </div>
  </div>

</div>

<!-- =========================================================
     MODAL & OVERLAY: PAYMENT INTERACTION & STATUS INDICATORS
     ========================================================= -->
<div id="paymentModalOverlay" data-modal-overlay role="dialog" aria-modal="true" aria-label="Status pembayaran" aria-hidden="true" class="modal-fade fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div id="paymentModalContent" data-modal-panel tabindex="-1" class="modal-scale bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 text-center">

    <!-- State 1: Loading Pembuatan Transaksi -->
    <div id="payStateLoading" class="space-y-4 py-4">
      <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto text-2xl relative shadow-inner">
        <i class="fa-solid fa-circle-notch fa-spin"></i>
      </div>
      <div>
        <h3 class="text-base font-bold text-slate-900" id="payLoadingTitle">Memproses Transaksi Pembayaran...</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed" id="payLoadingDesc">Menghubungkan ke gateway pembayaran dan mengamankan stok susu.</p>
      </div>
      <div class="bg-slate-50 p-3 rounded-2xl text-xs text-slate-600 font-semibold border border-slate-200/60">
        Total Belanja: <span class="font-bold text-blue-600 text-sm" id="payModalTotal">Rp 0</span>
      </div>
    </div>

    <!-- State 2: Menunggu Pembayaran QRIS (Live Indicator & Visual QR Code) -->
    <div id="payStateWaiting" class="space-y-3 py-3 hidden">
      <div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 mb-1">
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Menunggu Pembayaran Pelanggan
        </span>
        <h3 class="text-base font-bold text-slate-900" id="payWaitingInvoice">Invoice: INV-...</h3>
        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
          Pindai kode QRIS di bawah ini dengan aplikasi e-Wallet atau Mobile Banking Anda.
        </p>
      </div>

      <!-- Kotak Tampilan Kode QRIS (BuatQRIS API) -->
      <div id="boxQrisImage" class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 inline-block mx-auto text-center shadow-inner">
        <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1 flex items-center justify-center gap-1.5">
          <i class="fa-solid fa-qrcode text-purple-600"></i> SCAN QRIS UNTUK BAYAR
        </div>
        <img id="imgQrisCode" src="" alt="Kode QRIS" class="w-48 h-48 mx-auto rounded-xl border border-slate-200 shadow-sm bg-white p-2 object-contain">
        <div class="text-[10px] text-slate-500 font-semibold mt-1">
          Mendukung Semua Bank &amp; E-Wallet Indonesia
        </div>
      </div>

      <div class="p-3 bg-purple-50 rounded-2xl border border-purple-200/60 text-xs flex justify-between items-center text-purple-900 font-semibold">
        <span>Total Pembayaran:</span>
        <span class="font-black text-sm text-purple-700" id="payWaitingAmount">Rp 0</span>
      </div>

      <!-- Action buttons -->
      <div class="space-y-2 pt-1">
        <button type="button" id="btnManualCheckStatus" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 btn-interactive shadow-sm">
          <i class="fa-solid fa-rotate"></i> Cek Verifikasi Pembayaran ke Server
        </button>
        <button type="button" id="btnSimulasiLunas" class="w-full py-2 px-4 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 border border-purple-200 btn-interactive">
          <i class="fa-solid fa-bolt"></i> Simulasi Bayar Lunas (Uji Coba Cepat)
        </button>
      </div>
    </div>

    <!-- State 3: Memverifikasi Konfirmasi Server -->
    <div id="payStateVerifying" class="space-y-4 py-4 hidden">
      <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-2xl relative shadow-inner">
        <i class="fa-solid fa-arrows-rotate fa-spin"></i>
      </div>
      <div>
        <h3 class="text-base font-bold text-slate-900">Memverifikasi ke Server...</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          Menerima respons gateway. Memastikan status transaksi telah diverifikasi secara valid oleh database server sebelum menampilkan bukti lunas.
        </p>
      </div>
    </div>

    <!-- State 4: Animasi Sukses Ketika Pembayaran Terverifikasi -->
    <div id="payStateSuccess" class="space-y-4 py-4 hidden pop-in-spring">
      
      <!-- Visual Checkmark dengan Animasi Halus & Sparkles -->
      <div class="relative flex items-center justify-center mx-auto w-24 h-24">
        <!-- Ripple Glow Ring -->
        <div class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping"></div>
        <div class="absolute inset-2 rounded-full bg-emerald-500/25 blur-sm"></div>
        
        <!-- Floating Sparkle Particles -->
        <span class="sparkle-particle sparkle-1 text-amber-400 text-xs font-bold">✨</span>
        <span class="sparkle-particle sparkle-2 text-emerald-500 text-xs font-bold">⭐</span>
        <span class="sparkle-particle sparkle-3 text-teal-400 text-xs font-bold">✨</span>
        <span class="sparkle-particle sparkle-4 text-amber-500 text-xs font-bold">🎉</span>

        <!-- Checkmark SVG -->
        <svg class="checkmark-svg relative z-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
          <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" />
          <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
        </svg>
      </div>

      <div>
        <!-- Notifikasi Visual Utama: PEMBAYARAN BERHASIL! -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 text-white shadow-lg shadow-emerald-600/25 mb-1.5 animate-bounce-subtle">
          <i class="fa-solid fa-circle-check text-sm text-emerald-200"></i>
          <span class="tracking-wider">PEMBAYARAN BERHASIL!</span>
        </div>
        <h3 class="text-base font-black text-slate-900 mt-1" id="paySuccessTitle">Transaksi Lunas Terverifikasi</h3>
        <p class="text-xs text-slate-500 mt-0.5" id="paySuccessInvoice">Faktur Penjualan Siap Dicetak</p>
      </div>

      <div class="bg-gradient-to-b from-emerald-50/90 to-teal-50/50 border border-emerald-200 p-4 rounded-2xl text-xs space-y-1.5 text-slate-700 shadow-inner">
        <div class="flex justify-between">
          <span class="font-semibold text-slate-600">Total Belanja:</span>
          <span class="font-bold text-slate-900 text-sm" id="paySuccessTotal">Rp 0</span>
        </div>
        <div class="flex justify-between" id="rowSuccessKembalian">
          <span class="font-semibold text-slate-600">Uang Kembalian:</span>
          <span class="font-black text-emerald-600 text-sm" id="paySuccessKembalian">Rp 0</span>
        </div>
      </div>

      <div class="pt-2">
        <a id="btnLihatStruk" href="#" class="w-full py-3 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg btn-interactive">
          <i class="fa-solid fa-receipt"></i> Buka &amp; Cetak Struk Penjualan
        </a>
      </div>
    </div>

    <!-- State 5: Gagal / Dibatalkan -->
    <div id="payStateFailed" class="space-y-4 py-4 hidden">
      <div class="w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
        <i class="fa-solid fa-circle-xmark"></i>
      </div>
      <div>
        <h3 class="text-base font-bold text-slate-900">Pembayaran Tidak Berhasil</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed" id="payFailedMsg">
          Pembayaran belum terselesaikan, dibatalkan, atau kadaluarsa.
        </p>
      </div>
      <div class="pt-2 flex gap-2">
        <button type="button" onclick="closePaymentModal()" class="w-full py-2.5 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition">
          Tutup &amp; Coba Lagi
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Script Logika POS & Real-time Validation Micro-Interactions -->
<script>
  let cart = [];
  let currentCategory = 'all';

  function filterCategory(catName, btnElement) {
    currentCategory = catName;
    document.querySelectorAll('.cat-pill-btn').forEach(b => {
      b.classList.remove('bg-[#5A1C24]', 'text-white', 'shadow-md');
      b.classList.add('bg-white', 'text-stone-700', 'border-[#EBE4D8]');
    });
    if (btnElement) {
      btnElement.classList.remove('bg-white', 'text-stone-700', 'border-[#EBE4D8]');
      btnElement.classList.add('bg-[#5A1C24]', 'text-white', 'shadow-md');
    }
    applyFilters();
  }

  function filterProducts() {
    applyFilters();
  }

  function applyFilters() {
    const q = (document.getElementById('searchInput').value || '').toLowerCase().trim();
    let visibleCount = 0;

    document.querySelectorAll('.product-card').forEach(card => {
      const name = (card.getAttribute('data-name') || '').toLowerCase();
      const code = (card.getAttribute('data-code') || '').toLowerCase();
      const cat = card.getAttribute('data-category') || '';

      const matchSearch = !q || name.includes(q) || code.includes(q) || cat.toLowerCase().includes(q);
      const matchCat = (currentCategory === 'all' || cat === currentCategory);

      if (matchSearch && matchCat) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    const emptyNotice = document.getElementById('catalogEmptyNotice');
    if (emptyNotice) {
      emptyNotice.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    const countElem = document.getElementById('visibleCount');
    if (countElem) {
      countElem.textContent = visibleCount;
    }
  }

  function addToCart(id, name, price, maxStock) {
    const existing = cart.find(i => i.id === id);
    if (existing) {
      if (existing.qty >= maxStock) {
        alert(`Batas stok tercapai (${maxStock} ${name}).`);
        return;
      }
      existing.qty++;
    } else {
      cart.push({
        id,
        name,
        price,
        qty: 1,
        maxStock
      });
    }
    renderCart();
  }

  function updateQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
      cart = cart.filter(i => i.id !== id);
    } else if (item.qty > item.maxStock) {
      alert(`Stok hanya tersedia ${item.maxStock}.`);
      item.qty = item.maxStock;
    }
    renderCart();
  }

  function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
  }

  function clearCart() {
    cart = [];
    renderCart();
  }

  function renderCart() {
    const container = document.getElementById('cartContainer');

    if (cart.length === 0) {
      container.innerHTML = `
        <div id="emptyCartMessage" class="py-12 text-center text-xs text-slate-400">
          <i class="fa-solid fa-basket-shopping text-3xl text-slate-200 mb-2 block"></i>
          Keranjang masih kosong. Klik produk di sebelah kiri untuk menambahkan.
        </div>
      `;
      document.getElementById('txtTotalQty').textContent = '0';
      document.getElementById('txtGrandTotal').textContent = 'Rp 0';
      validateCashPayment();
      return;
    }

    let html = '';
    let totalQty = 0;
    let grandTotal = 0;

    cart.forEach(item => {
      const subtotal = item.price * item.qty;
      totalQty += item.qty;
      grandTotal += subtotal;

      html += `
        <div class="py-2.5 flex items-center justify-between text-xs animate-fade-in">
          <div class="flex-1 pr-2">
            <div class="font-bold text-slate-800">${item.name}</div>
            <div class="text-[10px] text-slate-400">@ Rp ${item.price.toLocaleString('id-ID')}</div>
          </div>
          <div class="flex items-center gap-1">
            <button type="button" onclick="updateQty(${item.id}, -1)" title="Kurangi" class="btn-interactive w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black shadow-none">-</button>
            <span class="w-6 text-center font-bold text-slate-900">${item.qty}</span>
            <button type="button" onclick="updateQty(${item.id}, 1)" title="Tambah" class="btn-interactive w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black shadow-none">+</button>
            <button type="button" onclick="removeFromCart(${item.id})" title="Hapus produk" class="btn-interactive w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-bold shadow-none ml-1">
              <i class="fa-solid fa-trash-can text-[10px]"></i>
            </button>
          </div>
          <div class="w-20 text-right font-black text-slate-800">
            Rp ${subtotal.toLocaleString('id-ID')}
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
    document.getElementById('txtTotalQty').textContent = totalQty;
    document.getElementById('txtGrandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

    validateCashPayment();
  }

  function togglePaymentMethod(method) {
    const cashSection = document.getElementById('cashInputSection');
    const lblTunai = document.getElementById('lblTunai');
    const lblQris = document.getElementById('lblQris');

    if (method === 'tunai') {
      cashSection.style.display = 'block';
      lblTunai.className = "btn-interactive border-2 border-blue-600 bg-blue-50/60 rounded-2xl p-3 flex items-center gap-2 text-xs font-bold text-blue-900 transition";
      lblQris.className = "btn-interactive border border-slate-200 hover:border-slate-300 rounded-2xl p-3 flex items-center gap-2 text-xs font-bold text-slate-700 transition";
    } else {
      cashSection.style.display = 'none';
      lblTunai.className = "btn-interactive border border-slate-200 hover:border-slate-300 rounded-2xl p-3 flex items-center gap-2 text-xs font-bold text-slate-700 transition";
      lblQris.className = "btn-interactive border-2 border-purple-600 bg-purple-50/60 rounded-2xl p-3 flex items-center gap-2 text-xs font-bold text-purple-900 transition";
    }
    validateCashPayment();
  }

  function setCashAmount(nominal) {
    const grandTotal = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
    const cashInput = document.getElementById('inputCash');
    if (nominal === 'pas') {
      cashInput.value = grandTotal;
    } else {
      cashInput.value = nominal;
    }
    validateCashPayment();
  }

  // Real-time Form Validation for Cash Payment
  function validateCashPayment() {
    const grandTotal = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
    const method = document.querySelector('input[name="payment_method"]:checked').value;
    const btnProses = document.getElementById('btnProses');
    const cashInput = document.getElementById('inputCash');
    const successIcon = document.getElementById('cashSuccessIcon');
    const errorIcon = document.getElementById('cashErrorIcon');
    const errorMsg = document.getElementById('cashErrorMsg');
    const successMsg = document.getElementById('cashSuccessMsg');
    const errorText = document.getElementById('cashErrorText');
    const txtKembalian = document.getElementById('txtKembalian');
    const rawVal = parseFloat(cashInput.value);

    // Jika keranjang kosong
    if (cart.length === 0) {
      btnProses.disabled = true;
      cashInput.classList.remove('is-valid', 'is-invalid');
      successIcon.classList.remove('show-feedback');
      errorIcon.classList.remove('show-feedback');
      errorMsg.classList.remove('show-message');
      successMsg.classList.remove('show-message');
      txtKembalian.textContent = 'Kembalian: Rp 0';
      txtKembalian.className = "font-bold text-slate-400";
      return;
    }

    // Jika metode QRIS
    if (method === 'qris') {
      btnProses.disabled = false;
      return;
    }

    // Metode Tunai (Cash)
    if (isNaN(rawVal) || rawVal === 0) {
      cashInput.classList.remove('is-valid', 'is-invalid');
      successIcon.classList.remove('show-feedback');
      errorIcon.classList.remove('show-feedback');
      errorMsg.classList.remove('show-message');
      successMsg.classList.remove('show-message');
      txtKembalian.textContent = 'Kembalian: Rp 0';
      txtKembalian.className = "font-bold text-slate-400";
      btnProses.disabled = true;
    } else if (rawVal < grandTotal) {
      // Invalid (Uang Kurang)
      const kekurangan = grandTotal - rawVal;
      cashInput.classList.remove('is-valid');
      cashInput.classList.add('is-invalid');
      successIcon.classList.remove('show-feedback');
      errorIcon.classList.add('show-feedback');
      errorText.textContent = `Nominal tunai kurang Rp ${kekurangan.toLocaleString('id-ID')} dari total belanja.`;
      errorMsg.classList.add('show-message');
      successMsg.classList.remove('show-message');
      txtKembalian.textContent = `Kurang Rp ${kekurangan.toLocaleString('id-ID')}`;
      txtKembalian.className = "font-bold text-red-600";
      btnProses.disabled = true;
    } else {
      // Valid (Uang Cukup / Lebih)
      const kembalian = rawVal - grandTotal;
      cashInput.classList.remove('is-invalid');
      cashInput.classList.add('is-valid');
      errorIcon.classList.remove('show-feedback');
      successIcon.classList.add('show-feedback');
      errorMsg.classList.remove('show-message');
      successMsg.classList.add('show-message');
      txtKembalian.textContent = `Kembalian: Rp ${kembalian.toLocaleString('id-ID')}`;
      txtKembalian.className = "font-bold text-emerald-600";
      btnProses.disabled = false;
    }
  }

  // =========================================================
  // 5. PAYMENT INTERACTION & SERVER-VERIFIED STATUS LOGIC
  // =========================================================
  let currentTx = null;
  let currentSnapToken = null;
  let statusPollTimer = null;

  function openPaymentModal() {
    openModalById('paymentModalOverlay', document.getElementById('btnProses'));
  }

  function closePaymentModal() {
    if (statusPollTimer) clearInterval(statusPollTimer);
    const overlay = document.getElementById('paymentModalOverlay');
    closeModal(overlay);
    setTimeout(() => {
      const btn = document.getElementById('btnProses');
      btn.classList.remove('is-loading');
      btn.removeAttribute('aria-busy');
    }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 210);
  }

  function setPaymentModalState(state) {
    const states = ['payStateLoading', 'payStateWaiting', 'payStateVerifying', 'payStateSuccess', 'payStateFailed'];
    states.forEach(s => {
      const el = document.getElementById(s);
      if (el) el.classList.add('hidden');
    });

    const activeEl = document.getElementById('payState' + state.charAt(0).toUpperCase() + state.slice(1));
    if (activeEl) activeEl.classList.remove('hidden');
  }

  function processCheckout() {
    if (cart.length === 0) return;

    const method = document.querySelector('input[name="payment_method"]:checked').value;
    const cashInput = parseFloat(document.getElementById('inputCash').value) || 0;
    const grandTotal = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);

    if (method === 'tunai' && cashInput < grandTotal) {
      validateCashPayment();
      return;
    }

    const btn = document.getElementById('btnProses');
    btn.classList.add('is-loading');
    btn.setAttribute('aria-busy', 'true');

    // 1. Tampilkan Loading Pembuatan Transaksi
    document.getElementById('payModalTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    setPaymentModalState('loading');
    openPaymentModal();

    const customerName = document.getElementById('inputCustomer')?.value?.trim() || null;

    fetch("{{ route('pos.checkout') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
          items: cart,
          metode_pembayaran: method,
          total_bayar: cashInput,
          catatan: customerName,
        })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          setPaymentModalState('failed');
          document.getElementById('payFailedMsg').textContent = data.message || 'Gagal memproses transaksi.';
          window.showToast("Gagal Memproses Transaksi", data.message, "error");
          btn.classList.remove('is-loading');
          btn.removeAttribute('aria-busy');
          return;
        }

        currentTx = data;
        currentSnapToken = data.snap_token;

        // Kasus A: Pembayaran Tunai (Langsung Lunas Server)
        if (data.is_tunai) {
          showVerifiedSuccessState({
            kode_invoice: data.invoice,
            total_harga: grandTotal,
            kembalian: Math.max(0, cashInput - grandTotal),
            redirect_url: data.redirect_url,
            is_tunai: true
          });
          return;
        }

        // Kasus B: Pembayaran Online / QRIS (Menunggu Verifikasi Server)
        document.getElementById('payWaitingInvoice').textContent = 'Invoice: ' + data.invoice;
        const displayAmount = data.total_amount ? Number(data.total_amount) : grandTotal;
        document.getElementById('payWaitingAmount').textContent = 'Rp ' + displayAmount.toLocaleString('id-ID');

        if (data.qris_image) {
          document.getElementById('imgQrisCode').src = data.qris_image;
        }

        setPaymentModalState('waiting');

        // Setup event tombol interaktif di modal
        const btnSimulasi = document.getElementById('btnSimulasiLunas');
        if (btnSimulasi) {
          btnSimulasi.onclick = function() {
            simulasikanLunas(data.transaksi_id);
          };
        }
        const btnManual = document.getElementById('btnManualCheckStatus');
        if (btnManual) {
          btnManual.onclick = function() {
            checkServerPaymentStatus(data.transaksi_id, true);
          };
        }

        // Mulai polling verifikasi ke server setiap 2.5 detik
        startServerStatusPolling(data.transaksi_id);
      })
      .catch(err => {
        setPaymentModalState('failed');
        document.getElementById('payFailedMsg').textContent = "Terjadi gangguan koneksi: " + err;
        window.showToast("Gangguan Jaringan", "Tidak dapat menghubungi server.", "error");
        btn.classList.remove('is-loading');
        btn.removeAttribute('aria-busy');
      });
  }

  // Simulasi Bayar Lunas (untuk testing cepat tanpa scanning QR fisik)
  function simulasikanLunas(transaksiId) {
    setPaymentModalState('verifying');
    fetch(`/transaksi/${transaksiId}/simulasi-lunas`, {
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
          checkServerPaymentStatus(transaksiId, false);
        } else {
          window.showToast("Gagal Simulasi", res.message || "Gagal memproses simulasi.", "error");
          setPaymentModalState('waiting');
        }
      })
      .catch(err => {
        window.showToast("Error", "Gagal menghubungi server: " + err, "error");
        setPaymentModalState('waiting');
      });
  }

  // Meluncurkan Popup Midtrans Snap (hanya jika snap token real)
  function launchSnapPayment(data) {
    if (!data.snap_token || !window.snap || data.snap_token.startsWith('sandbox-token-')) {
      return;
    }

    window.snap.pay(data.snap_token, {
      onSuccess: function(result) {
        // PENTING: Jangan langsung tampilkan sukses! Verifikasi dahulu ke database server!
        setPaymentModalState('verifying');
        checkServerPaymentStatus(data.transaksi_id, false);
      },
      onPending: function(result) {
        setPaymentModalState('waiting');
        window.showToast("Menunggu Pembayaran", "Menunggu pelanggan menyelesaikan transaksi di payment gateway.", "warning");
      },
      onError: function(result) {
        window.showToast("Pembayaran Gagal", "Pembayaran ditolak atau terjadi kendala gateway.", "error");
        setPaymentModalState('failed');
      },
      onClose: function() {
        setPaymentModalState('waiting');
      }
    });
  }

  // Polling Pemeriksaan Status ke Server
  function startServerStatusPolling(transaksiId) {
    if (statusPollTimer) clearInterval(statusPollTimer);
    // Polling setiap 2 detik ke server untuk mendeteksi konfirmasi webhook Midtrans secepatnya
    statusPollTimer = setInterval(() => {
      checkServerPaymentStatus(transaksiId, false);
    }, 2000);
  }

  // Cek Status Pembayaran Valid ke Server (Terintegrasi Webhook Midtrans / Gateway)
  function checkServerPaymentStatus(transaksiId, isManualClick = false) {
    fetch(`/transaksi/${transaksiId}/status`, {
        headers: {
          "Accept": "application/json"
        }
      })
      .then(res => res.json())
      .then(res => {
        // 1. STATUS LUNAS (Konfirmasi Settlement Webhook Midtrans diterima server)
        if (res.is_lunas) {
          if (statusPollTimer) clearInterval(statusPollTimer);
          showVerifiedSuccessState({
            kode_invoice: res.kode_invoice,
            total_harga: res.total_harga,
            kembalian: res.kembalian,
            redirect_url: res.redirect_url,
            is_tunai: false
          });
        } 
        // 2. STATUS BATAL / GAGAL (Deny, Cancel, Expire) -> DILARANG MEMUTAR SUARA
        else if (res.is_batal) {
          if (statusPollTimer) clearInterval(statusPollTimer);
          setPaymentModalState('failed');
          document.getElementById('payFailedMsg').textContent = "Transaksi telah dibatalkan atau waktu pembayaran telah kadaluarsa.";
          window.showToast("Pembayaran Dibatalkan", "Transaksi berstatus batal atau ditolak oleh gateway.", "error");
          // TIDAK MEMUTAR SUARA KARENA TRANSAKSI GAGAL/BATAL
        } 
        // 3. STATUS PENDING (Belum Lunas) -> DILARANG MEMUTAR SUARA
        else {
          if (isManualClick) {
            window.showToast("Status: Menunggu Pembayaran", "Menunggu konfirmasi pembayaran QRIS dari gateway Midtrans (Status: Pending).", "warning");
          }
          // TIDAK MEMUTAR SUARA KARENA TRANSAKSI MASIH PENDING
        }
      })
      .catch(err => {
        if (isManualClick) {
          window.showToast("Cek Status", "Gagal memeriksa status ke server.", "error");
        }
      });
  }

  // =========================================================================
  // SISTEM NOTIFIKASI SUARA PEMBAYARAN BERHASIL (TEKNIS AUDIO & AUTOPLAY COMPLIANT)
  // =========================================================================

  // 1. REGISTRY FAKTUR SUDAH BERSUARA (Anti-Duplikasi & Anti-Replay saat Refresh)
  const playedPaymentAudioInvoices = new Set();
  const STORAGE_KEY_PLAYED_AUDIOS = 'telaga_played_payment_audios';

  function isAudioAlreadyPlayed(invoiceCode) {
    if (!invoiceCode) return true;
    if (playedPaymentAudioInvoices.has(invoiceCode)) return true;
    try {
      const stored = JSON.parse(sessionStorage.getItem(STORAGE_KEY_PLAYED_AUDIOS) || '[]');
      if (Array.isArray(stored) && stored.includes(String(invoiceCode))) {
        playedPaymentAudioInvoices.add(invoiceCode);
        return true;
      }
    } catch (e) {}
    return false;
  }

  function markAudioAsPlayed(invoiceCode) {
    if (!invoiceCode) return;
    playedPaymentAudioInvoices.add(invoiceCode);
    try {
      const stored = JSON.parse(sessionStorage.getItem(STORAGE_KEY_PLAYED_AUDIOS) || '[]');
      if (!stored.includes(String(invoiceCode))) {
        stored.push(String(invoiceCode));
        if (stored.length > 50) stored.shift(); // Batasi 50 faktur terakhir dalam session
        sessionStorage.setItem(STORAGE_KEY_PLAYED_AUDIOS, JSON.stringify(stored));
      }
    } catch (e) {}
  }

  // 2. PENGATURAN SUARA (AKTIF / NONAKTIF - PERSISTEN LOCALSTORAGE)
  const STORAGE_KEY_SOUND_ENABLED = 'telaga_pos_sound_enabled';

  function isSoundEnabled() {
    const val = localStorage.getItem(STORAGE_KEY_SOUND_ENABLED);
    return val === null ? true : val === 'true';
  }

  function updateSoundToggleUI() {
    const enabled = isSoundEnabled();
    const btn = document.getElementById('btnToggleSound');
    const icon = document.getElementById('iconToggleSound');
    const txt = document.getElementById('textToggleSound');
    if (!btn || !icon || !txt) return;

    if (enabled) {
      btn.className = "px-2.5 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold border border-emerald-200 transition flex items-center gap-1.5 btn-interactive shadow-xs";
      icon.className = "fa-solid fa-volume-high text-emerald-600";
      txt.textContent = "Suara: ON";
      btn.title = "Notifikasi Suara AKTIF. Klik untuk menonaktifkan (mode hening).";
    } else {
      btn.className = "px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 text-[11px] font-bold border border-slate-300 transition flex items-center gap-1.5 btn-interactive shadow-xs";
      icon.className = "fa-solid fa-volume-xmark text-slate-400";
      txt.textContent = "Suara: OFF";
      btn.title = "Notifikasi Suara NONAKTIF (Hening). Klik untuk mengaktifkan.";
    }
  }

  function toggleSoundSetting() {
    const currentlyEnabled = isSoundEnabled();
    const newState = !currentlyEnabled;
    localStorage.setItem(STORAGE_KEY_SOUND_ENABLED, String(newState));
    updateSoundToggleUI();

    if (newState) {
      // Buka kunci audio sekaligus uji coba bunyi ringkas
      unlockAudioSubsystem();
      playChimeAudio(true);
      window.showToast("Suara Kasir Aktif", "Notifikasi audio pembayaran berhasil telah DIAKTIFKAN.", "success");
    } else {
      window.showToast("Mode Hening (Mute)", "Notifikasi suara kasir dinonaktifkan. Visual banner tetap tampil.", "info");
    }
  }

  // 3. KEBIJAKAN AUTOPLAY BROWSER & DUKUNGAN DESKTOP + MOBILE (iOS & Android)
  let isAudioUnlocked = false;
  let sharedAudioContext = null;

  function getSharedAudioContext() {
    if (!sharedAudioContext) {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (AudioCtx) {
        sharedAudioContext = new AudioCtx();
      }
    }
    return sharedAudioContext;
  }

  // Membuka kunci audio pada interaksi pertama pengguna di desktop maupun mobile
  function unlockAudioSubsystem() {
    if (isAudioUnlocked) return;

    // 1. Resume Web Audio API context
    try {
      const ctx = getSharedAudioContext();
      if (ctx && ctx.state === 'suspended') {
        ctx.resume();
      }
    } catch (e) {}

    // 2. Pre-prime HTML Audio element
    try {
      const audioEl = document.getElementById('audioPaymentSuccess');
      if (audioEl) {
        audioEl.muted = true;
        const p = audioEl.play();
        if (p !== undefined) {
          p.then(() => {
            audioEl.pause();
            audioEl.currentTime = 0;
            audioEl.muted = false;
            isAudioUnlocked = true;
          }).catch(() => {});
        }
      } else {
        isAudioUnlocked = true;
      }
    } catch (e) {}

    isAudioUnlocked = true;
  }

  // Pasang listener interaksi pertama pengguna untuk memenuhi kebijakan autoplay
  ['pointerdown', 'touchstart', 'touchend', 'click', 'keydown'].forEach(evtName => {
    document.addEventListener(evtName, function onFirstUserGesture() {
      unlockAudioSubsystem();
      ['pointerdown', 'touchstart', 'touchend', 'click', 'keydown'].forEach(e => {
        document.removeEventListener(e, onFirstUserGesture, true);
      });
    }, { capture: true, once: true });
  });

  // 4. PEMICU NOTIFIKASI SUARA (Cash & QRIS)
  function triggerPaymentSuccessAudio(invoiceCode, isTunai = false) {
    if (!invoiceCode) return;

    // Ketentuan Teknis: Suara HANYA DIPUTAR SATU KALI per transaksi (kebal refresh & multi-event)
    if (isAudioAlreadyPlayed(invoiceCode)) {
      return;
    }
    markAudioAsPlayed(invoiceCode);

    // Periksa pengaturan suara apakah aktif atau nonaktif
    if (!isSoundEnabled()) {
      console.log("Notifikasi suara kasir di-mute oleh pengaturan pengguna.");
      return;
    }

    // Pastikan audio subsistem dalam keadaan aktif
    unlockAudioSubsystem();

    // 1. Putar efek audio register chime (HTML Audio Element + Web Audio API Fallback)
    playChimeAudio();

    // 2. Putar suara pengucap Bahasa Indonesia (Speech Synthesis)
    speakPaymentVoice(isTunai);
  }

  // 5. PEMUTAR NADA BEL REGISTER KASIR ("Ting!" + "Cha-Ching!")
  function playChimeAudio(isShortPreview = false) {
    try {
      const audioEl = document.getElementById('audioPaymentSuccess');
      if (audioEl) {
        audioEl.currentTime = 0;
        audioEl.volume = 0.9;
        const playPromise = audioEl.play();
        if (playPromise !== undefined) {
          playPromise.catch(err => {
            // Jika HTML Audio dicegah oleh browser, gunakan synthesizer Web Audio API
            synthWebAudioChime(isShortPreview);
          });
          return;
        }
      }
      synthWebAudioChime(isShortPreview);
    } catch (e) {
      synthWebAudioChime(isShortPreview);
    }
  }

  // Web Audio API Synth Generator: Digital POS "Ting!" + "Cha-Ching!"
  function synthWebAudioChime(isShortPreview = false) {
    try {
      const ctx = getSharedAudioContext();
      if (!ctx) return;
      if (ctx.state === 'suspended') {
        ctx.resume();
      }

      const t0 = ctx.currentTime;

      // BAGIAN 1: "Ting!" (Crystalline POS Bell Strike di C7 + E7)
      const tingNotes = [
        { freq: 2093.00, time: 0.0, dur: isShortPreview ? 0.35 : 0.8, gain: 0.35, type: 'sine' },
        { freq: 2637.02, time: 0.0, dur: isShortPreview ? 0.30 : 0.7, gain: 0.20, type: 'sine' },
        { freq: 3135.96, time: 0.0, dur: isShortPreview ? 0.25 : 0.5, gain: 0.12, type: 'sine' },
        { freq: 1046.50, time: 0.01, dur: isShortPreview ? 0.35 : 0.9, gain: 0.28, type: 'triangle' }
      ];

      tingNotes.forEach(n => {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = n.type;
        osc.frequency.setValueAtTime(n.freq, t0 + n.time);

        gain.gain.setValueAtTime(0, t0 + n.time);
        gain.gain.linearRampToValueAtTime(n.gain, t0 + n.time + 0.015);
        gain.gain.exponentialRampToValueAtTime(0.0001, t0 + n.time + n.dur);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start(t0 + n.time);
        osc.stop(t0 + n.time + n.dur);
      });

      if (isShortPreview) return;

      // BAGIAN 2: "Cha-Ching!" (Laci Kasir Digital + Denting Koin Emas)
      const chachingNotes = [
        { freq: 820, time: 0.14, dur: 0.15, gain: 0.18, type: 'triangle' },
        { freq: 3850, time: 0.18, dur: 0.35, gain: 0.22, type: 'sine' },
        { freq: 4920, time: 0.19, dur: 0.30, gain: 0.15, type: 'sine' },
        { freq: 4200, time: 0.25, dur: 0.40, gain: 0.25, type: 'sine' },
        { freq: 5600, time: 0.26, dur: 0.35, gain: 0.18, type: 'sine' },
        { freq: 6400, time: 0.34, dur: 0.45, gain: 0.14, type: 'sine' }
      ];

      chachingNotes.forEach(n => {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = n.type;
        osc.frequency.setValueAtTime(n.freq, t0 + n.time);

        gain.gain.setValueAtTime(0, t0 + n.time);
        gain.gain.linearRampToValueAtTime(n.gain, t0 + n.time + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.0001, t0 + n.time + n.dur);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start(t0 + n.time);
        osc.stop(t0 + n.time + n.dur);
      });

    } catch (err) {
      console.warn("Web Audio API warning:", err);
    }
  }

  // Notifikasi Suara Pengucap Bahasa Indonesia (Text-to-Speech)
  function speakPaymentVoice(isTunai) {
    if (!('speechSynthesis' in window)) return;
    try {
      window.speechSynthesis.cancel();
      const text = isTunai ?
        "Pembayaran tunai berhasil diterima." :
        "Pembayaran QRIS berhasil diverifikasi.";
      const utter = new SpeechSynthesisUtterance(text);
      utter.lang = 'id-ID';
      utter.rate = 1.05;
      utter.pitch = 1.1;

      const voices = window.speechSynthesis.getVoices();
      const idVoice = voices.find(v => v.lang.includes('id') || v.lang.includes('ID') || v.name.toLowerCase().includes('indonesian'));
      if (idVoice) {
        utter.voice = idVoice;
      }

      setTimeout(() => {
        window.speechSynthesis.speak(utter);
      }, 350);
    } catch (err) {
      console.warn("Speech Synthesis warning:", err);
    }
  }

  // Fungsi pengujian suara untuk kasir
  function testPaymentSound() {
    unlockAudioSubsystem();
    playChimeAudio();
    speakPaymentVoice(true);

    const isEnabled = isSoundEnabled();
    const banner = document.getElementById('floatingSuccessBanner');
    if (banner) {
      document.getElementById('bannerSuccessInvoice').textContent = 'Tes Suara Berhasil - Audio Digital Siap' + (isEnabled ? '' : ' (Mode Hening Aktif)');
      banner.classList.remove('-translate-y-20', 'opacity-0');
      banner.classList.add('translate-y-0', 'opacity-100');
      setTimeout(() => {
        banner.classList.remove('translate-y-0', 'opacity-100');
        banner.classList.add('-translate-y-20', 'opacity-0');
      }, 2500);
    }

    if (isEnabled) {
      window.showToast("Tes Suara Berhasil", "Audio kasir digital 'Ting!' & 'Cha-Ching!' berfungsi normal.", "success");
    } else {
      window.showToast("Tes Suara (Status: OFF)", "Suara terdengar di sesi tes ini, namun status saat ini Nonaktif (Mute).", "warning");
    }
  }

  // Menampilkan Animasi Sukses & Notifikasi Hanya Setelah Terverifikasi Server
  function showVerifiedSuccessState(details) {
    if (statusPollTimer) clearInterval(statusPollTimer);

    // 1. PUTAR NOTIFIKASI SUARA MODERN ("Ting!" & "Cha-Ching!" - Otomatis, Sekali, Bebas Replay)
    triggerPaymentSuccessAudio(details.kode_invoice, details.is_tunai);

    // 2. TAMPILKAN BANNER VISUAL PEMBAYARAN BERHASIL MELAYANG (FLOATING CELEBRATION TOAST)
    const banner = document.getElementById('floatingSuccessBanner');
    if (banner) {
      document.getElementById('bannerSuccessInvoice').textContent = 'Faktur ' + details.kode_invoice + ' - Rp ' + Number(details.total_harga).toLocaleString('id-ID');
      banner.classList.remove('-translate-y-20', 'opacity-0');
      banner.classList.add('translate-y-0', 'opacity-100');
      setTimeout(() => {
        banner.classList.remove('translate-y-0', 'opacity-100');
        banner.classList.add('-translate-y-20', 'opacity-0');
      }, 3500);
    }

    document.getElementById('paySuccessInvoice').textContent = 'Faktur: ' + details.kode_invoice;
    document.getElementById('paySuccessTotal').textContent = 'Rp ' + Number(details.total_harga).toLocaleString('id-ID');
    document.getElementById('paySuccessKembalian').textContent = 'Rp ' + Number(details.kembalian || 0).toLocaleString('id-ID');
    document.getElementById('btnLihatStruk').href = details.redirect_url;

    // 3. AKTIFKAN MODAL DENGAN ANIMASI HALUS & RIPPLE
    setPaymentModalState('success');

    // 4. NOTIFIKASI TOAST SISTEM
    const toastTitle = details.is_tunai 
      ? "Pembayaran Tunai Berhasil!" 
      : "Pembayaran QRIS Berhasil Diverifikasi!";
    const toastDesc = `Faktur ${details.kode_invoice} telah lunas terverifikasi oleh server.`;
    window.showToast(toastTitle, toastDesc, "success", 6000);

    // 5. KOSONGKAN KERANJANG KASIR
    clearCart();

    // Auto-redirect ke halaman struk setelah 3.2 detik agar kasir sempat melihat animasi sukses
    setTimeout(() => {
      window.location.href = details.redirect_url;
    }, 3200);
  }

  // Inisialisasi Pengaturan Suara saat halaman siap
  document.addEventListener('DOMContentLoaded', function() {
    updateSoundToggleUI();
  });
  // Fallback inisialisasi langsung jika DOMContentLoaded sudah lewat
  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    updateSoundToggleUI();
  }
</script>
@endsection