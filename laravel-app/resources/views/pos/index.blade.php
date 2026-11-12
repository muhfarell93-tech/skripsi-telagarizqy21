@extends('layouts.app')

@section('title', 'Mesin Kasir (POS)')

@push('styles')
<!-- Midtrans Snap JS (Sandbox) -->
<script type="text/javascript"
  src="https://app.sandbox.midtrans.com/snap/snap.js"
  data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  <!-- Kolom Kiri: Katalog Produk Susu (7 Kolom) -->
  <div class="lg:col-span-7 space-y-4">
    
    <!-- Search & Filter Bar with Focus Micro-Interaction -->
    <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col sm:flex-row gap-3 items-center justify-between">
      <div class="relative w-full sm:w-72">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
          <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </span>
        <input type="text" id="searchInput" onkeyup="filterProducts()" placeholder="Cari nama produk / kode..."
          class="input-interactive w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
      </div>
      <span class="text-xs text-slate-500 self-end sm:self-center font-medium">
        Katalog: <strong class="text-slate-800 font-bold">{{ $produks->count() }}</strong> Varian Susu
      </span>
    </div>

    <!-- Product Grid with Micro-Interactions -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5" id="productGrid">
      @foreach($produks as $p)
        <div class="product-card card-interactive bg-white p-4 rounded-3xl border border-slate-200/80 shadow-sm hover:border-blue-500 cursor-pointer flex flex-col justify-between group active:scale-[0.98] transition-transform"
             data-name="{{ strtolower($p->nama_produk) }}"
             data-code="{{ strtolower($p->kode_produk) }}"
             onclick="addToCart({{ $p->id }}, '{{ addslashes($p->nama_produk) }}', {{ $p->harga_jual }}, {{ $p->stok }})">
          <div>
            <div class="flex justify-between items-start mb-2">
              <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-[10px] font-bold group-hover:bg-blue-600 group-hover:text-white transition">
                {{ $p->kode_produk }}
              </span>
              <span class="text-[10px] font-semibold {{ $p->stok <= $p->stok_minimum ? 'text-red-500 font-bold' : 'text-slate-400' }}">
                Stok: {{ $p->stok }}
              </span>
            </div>
            <h4 class="text-xs font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-blue-600 transition">{{ $p->nama_produk }}</h4>
            <div class="text-[10px] text-slate-400 mt-1">{{ $p->kategori->nama_kategori ?? 'Susu Kambing' }}</div>
          </div>
          <div class="mt-4 pt-2 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs font-black text-blue-600">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</span>
            <button type="button" class="btn-interactive w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-blue-600 group-hover:text-white text-slate-600 text-xs shadow-none group-hover:shadow-md transition">
              <i class="fa-solid fa-plus text-[10px]"></i>
            </button>
          </div>
        </div>
      @endforeach
    </div>

  </div>

  <!-- Kolom Kanan: Keranjang & Panel Pembayaran (5 Kolom) -->
  <div class="lg:col-span-5 space-y-4">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 flex flex-col justify-between min-h-[580px]">
      
      <div>
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shadow-sm">
              <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Keranjang Pesanan</h3>
          </div>
          <button onclick="clearCart()" class="text-[11px] font-bold text-red-500 hover:text-red-700 hover:underline transition">
            Kosongkan
          </button>
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

    <!-- State 2: Menunggu Pembayaran QRIS (Live Indicator) -->
    <div id="payStateWaiting" class="space-y-4 py-4 hidden">
      <div class="relative w-16 h-16 mx-auto">
        <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-2xl status-pulse-amber">
          <i class="fa-solid fa-qrcode"></i>
        </div>
      </div>
      <div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 mb-2">
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Menunggu Pembayaran Pelanggan
        </span>
        <h3 class="text-base font-bold text-slate-900" id="payWaitingInvoice">Invoice: INV-...</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
          Silakan selesaikan pembayaran melalui popup Midtrans QRIS. Sistem sedang menunggu konfirmasi valid dari server...
        </p>
      </div>

      <div class="p-3 bg-purple-50 rounded-2xl border border-purple-200/60 text-xs flex justify-between items-center text-purple-900 font-semibold">
        <span>Total Pembayaran:</span>
        <span class="font-black text-sm text-purple-700" id="payWaitingAmount">Rp 0</span>
      </div>

      <!-- Action buttons -->
      <div class="space-y-2 pt-2">
        <button type="button" id="btnReopenSnap" class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 btn-interactive">
          <i class="fa-solid fa-up-right-from-square"></i> Buka Ulang Popup Pembayaran QRIS
        </button>
        <button type="button" id="btnManualCheckStatus" class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 btn-interactive">
          <i class="fa-solid fa-rotate"></i> Cek Status Pembayaran ke Server
        </button>
        <button type="button" id="btnSimulasiLunas" class="w-full py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-[11px] font-bold transition flex items-center justify-center gap-1.5">
          <i class="fa-solid fa-flask"></i> Uji Coba: Simulasikan Pembayaran Lunas (Sandbox)
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
      <div class="flex items-center justify-center mx-auto">
        <svg class="checkmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
          <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
          <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
      </div>
      <div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 mb-1 border border-emerald-200">
          <i class="fa-solid fa-circle-check text-emerald-600"></i> LUNAS TERVERIFIKASI SERVER
        </span>
        <h3 class="text-lg font-black text-slate-900 mt-1" id="paySuccessTitle">Pembayaran Berhasil!</h3>
        <p class="text-xs text-slate-500 mt-0.5" id="paySuccessInvoice">Faktur Penjualan Siap Dicetak</p>
      </div>

      <div class="bg-emerald-50/70 border border-emerald-200 p-4 rounded-2xl text-xs space-y-1.5 text-slate-700">
        <div class="flex justify-between">
          <span>Total Belanja:</span>
          <span class="font-bold text-slate-900" id="paySuccessTotal">Rp 0</span>
        </div>
        <div class="flex justify-between" id="rowSuccessKembalian">
          <span>Uang Kembalian:</span>
          <span class="font-black text-emerald-600" id="paySuccessKembalian">Rp 0</span>
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

  function filterProducts() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      const name = card.getAttribute('data-name');
      const code = card.getAttribute('data-code');
      card.style.display = (name.includes(q) || code.includes(q)) ? 'flex' : 'none';
    });
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
      cart.push({ id, name, price, qty: 1, maxStock });
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

  function clearCart() {
    cart = [];
    renderCart();
  }

  function renderCart() {
    const container = document.getElementById('cartContainer');
    const emptyMsg = document.getElementById('emptyCartMessage');

    if (cart.length === 0) {
      container.innerHTML = '';
      container.appendChild(emptyMsg);
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
          <div class="flex items-center gap-1.5">
            <button onclick="updateQty(${item.id}, -1)" class="btn-interactive w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black shadow-none">-</button>
            <span class="w-6 text-center font-bold text-slate-900">${item.qty}</span>
            <button onclick="updateQty(${item.id}, 1)" class="btn-interactive w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black shadow-none">+</button>
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
          redirect_url: data.redirect_url
        });
        return;
      }

      // Kasus B: Pembayaran Online / QRIS (Menunggu Verifikasi Server)
      document.getElementById('payWaitingInvoice').textContent = 'Invoice: ' + data.invoice;
      document.getElementById('payWaitingAmount').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
      setPaymentModalState('waiting');

      // Setup event tombol interaktif di modal
      document.getElementById('btnReopenSnap').onclick = function() {
        launchSnapPayment(data);
      };
      document.getElementById('btnManualCheckStatus').onclick = function() {
        checkServerPaymentStatus(data.transaksi_id, true);
      };
      document.getElementById('btnSimulasiLunas').onclick = function() {
        simulatePaymentSuccess(data.transaksi_id);
      };

      // Luncurkan popup Snap Midtrans
      launchSnapPayment(data);

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

  // Meluncurkan Popup Midtrans Snap
  function launchSnapPayment(data) {
    if (!data.snap_token || !window.snap) {
      window.showToast("Midtrans Mode Sandbox", "Snap token tersedia. Menunggu konfirmasi pembayaran valid dari server.", "info");
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
        // Pengguna menutup popup: tetap di state waiting agar kasir bisa cek status atau buka ulang
        setPaymentModalState('waiting');
      }
    });
  }

  // Polling Pemeriksaan Status ke Server
  function startServerStatusPolling(transaksiId) {
    if (statusPollTimer) clearInterval(statusPollTimer);
    statusPollTimer = setInterval(() => {
      checkServerPaymentStatus(transaksiId, false);
    }, 2500);
  }

  // Cek Status Pembayaran Valid ke Server
  function checkServerPaymentStatus(transaksiId, isManualClick = false) {
    fetch(`/transaksi/${transaksiId}/status`, {
      headers: { "Accept": "application/json" }
    })
    .then(res => res.json())
    .then(res => {
      if (res.is_lunas) {
        if (statusPollTimer) clearInterval(statusPollTimer);
        showVerifiedSuccessState({
          kode_invoice: res.kode_invoice,
          total_harga: res.total_harga,
          kembalian: res.kembalian,
          redirect_url: res.redirect_url
        });
      } else if (res.is_batal) {
        if (statusPollTimer) clearInterval(statusPollTimer);
        setPaymentModalState('failed');
        document.getElementById('payFailedMsg').textContent = "Transaksi telah dibatalkan atau waktu pembayaran telah kadaluarsa.";
        window.showToast("Pembayaran Dibatalkan", "Transaksi telah berstatus batal.", "error");
      } else if (isManualClick) {
        window.showToast("Status: Menunggu Pembayaran", "Belum ada verifikasi pembayaran masuk dari server.", "warning");
      }
    })
    .catch(err => {
      if (isManualClick) {
        window.showToast("Cek Status", "Gagal memeriksa status ke server.", "error");
      }
    });
  }

  // Simulasi Pembayaran Lunas (Sandbox/Uji Coba Cepat)
  function simulatePaymentSuccess(transaksiId) {
    setPaymentModalState('verifying');
    fetch(`/transaksi/${transaksiId}/simulasi-lunas`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}",
        "Accept": "application/json"
      }
    })
    .then(res => res.json())
    .then(res => {
      if (res.success) {
        checkServerPaymentStatus(transaksiId, false);
      }
    })
    .catch(err => {
      window.showToast("Gagal Simulasi", "Terjadi kesalahan saat mencatat simulasi.", "error");
    });
  }

  // Menampilkan Animasi Sukses & Notifikasi Hanya Setelah Terverifikasi Server
  function showVerifiedSuccessState(details) {
    if (statusPollTimer) clearInterval(statusPollTimer);

    document.getElementById('paySuccessInvoice').textContent = 'Faktur: ' + details.kode_invoice;
    document.getElementById('paySuccessTotal').textContent = 'Rp ' + Number(details.total_harga).toLocaleString('id-ID');
    document.getElementById('paySuccessKembalian').textContent = 'Rp ' + Number(details.kembalian || 0).toLocaleString('id-ID');
    document.getElementById('btnLihatStruk').href = details.redirect_url;

    // Aktifkan State Animasi Sukses
    setPaymentModalState('success');

    // Notifikasi Toast Berhasil
    window.showToast(
      "Pembayaran Berhasil Diverifikasi!",
      `Faktur ${details.kode_invoice} telah lunas terverifikasi oleh server.`,
      "success",
      6000
    );

    // Kosongkan keranjang kasir
    clearCart();

    // Auto-redirect ke halaman struk setelah 3.2 detik agar kasir sempat melihat animasi sukses
    setTimeout(() => {
      window.location.href = details.redirect_url;
    }, 3200);
  }
</script>
@endsection
