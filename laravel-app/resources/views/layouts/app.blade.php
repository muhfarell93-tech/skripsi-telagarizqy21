<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Sistem Pengolahan Data Transaksi Keuangan Berbasis Web') - Telaga Rizqy 21</title>

  <!-- Progressive Web App (PWA - Berfungsi seperti APK di Android & Desktop) -->
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Telaga Rizqy 21">
  <link rel="apple-touch-icon" href="/images/products/susu_murni.svg">
  <!-- Bootstrap 5, existing Tailwind utilities, icons, and Chart.js -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.6.1/dist/chart.umd.min.js"></script>
  
  <style>
    /* =========================================================
       MICRO-INTERACTIONS DESIGN SYSTEM - TELAGA RIZQY 21
       ========================================================= */
    
    :root {
      --primary-color: #2563eb;
      --primary-hover: #1d4ed8;
      --primary-shadow: rgba(37, 99, 235, 0.25);
      --transition-standard: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      --transition-fast: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .app-sidebar { --bs-offcanvas-width: 270px; background: #fff; border-right: 1px solid #dbe4ef; }
    .sidebar-brand-mark { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 14px; background: #e7f1fb; color: #153b66; }
    .sidebar-link { display: flex; align-items: center; gap: .75rem; padding: .7rem .85rem; margin: .12rem 0; border-radius: .7rem; color: #52647a; text-decoration: none; font-size: .875rem; font-weight: 600; transition: background-color .16s ease,color .16s ease; }
    .sidebar-link:hover, .sidebar-link.active { color: #153b66; background: #edf5fc; }
    .app-topbar { min-height: 64px; }
    @media (min-width: 992px) {
      .app-sidebar { position: fixed !important; inset: 0 auto 0 0 !important; z-index: 45 !important; width: 270px !important; visibility: visible !important; transform: none !important; }
      .app-topbar, .app-flash-wrap, main, footer { margin-left: 270px !important; }
      main { max-width: calc(100% - 270px) !important; }
    }
    @media (max-width: 991.98px) {
      .app-sidebar .offcanvas-header { border-bottom: 1px solid #e7edf4; }
      .app-sidebar .sidebar-nav { padding: 1rem; }
    }

    /* ---------------------------------------------------------
       1. BUTTON INTERACTIONS
       --------------------------------------------------------- */
    .btn-interactive {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      font-weight: 700;
      border-radius: 0.75rem;
      transition: var(--transition-standard);
      cursor: pointer;
      user-select: none;
      outline: none;
      overflow: hidden;
      -webkit-tap-highlight-color: transparent;
    }

    .btn-interactive:hover:not(:disabled):not(.is-loading) {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px -3px var(--primary-shadow), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .btn-interactive:active:not(:disabled):not(.is-loading) {
      transform: translateY(1px) scale(0.97);
      transition: var(--transition-fast);
      box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
    }

    .btn-interactive:focus-visible {
      outline: 2px solid var(--primary-color);
      outline-offset: 3px;
    }

    .btn-interactive:disabled,
    .btn-interactive[disabled],
    .btn-interactive.is-disabled {
      opacity: 0.55;
      cursor: not-allowed !important;
      pointer-events: none;
      transform: none !important;
      box-shadow: none !important;
      filter: grayscale(20%);
    }

    .btn-interactive.is-loading {
      cursor: wait !important;
      pointer-events: none;
      position: relative;
    }

    .btn-interactive.is-loading .btn-text {
      visibility: hidden;
      opacity: 0;
    }

    .btn-interactive.is-loading::after {
      content: "";
      position: absolute;
      width: 1.125rem;
      height: 1.125rem;
      top: 50%;
      left: 50%;
      margin-top: -0.5625rem;
      margin-left: -0.5625rem;
      border: 2.5px solid rgba(255, 255, 255, 0.35);
      border-radius: 50%;
      border-top-color: #ffffff;
      animation: btn-spinner 0.7s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    }

    @keyframes btn-spinner {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* ---------------------------------------------------------
       2. FORM & INPUT INTERACTIONS (REAL-TIME VALIDATION)
       --------------------------------------------------------- */
    .input-interactive {
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      outline: none;
    }

    /* Requirement 1: Animasi Fokus Pada Input */
    .input-interactive:focus {
      background-color: #ffffff !important;
      border-color: #2563eb !important;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15), 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      transform: translateY(-1px);
    }

    /* State Validasi Real-time: Sukses */
    .input-interactive.is-valid {
      border-color: #10b981 !important;
      background-color: #f0fdf4 !important;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
    }

    /* State Validasi Real-time: Error */
    .input-interactive.is-invalid {
      border-color: #ef4444 !important;
      background-color: #fef2f2 !important;
      box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
      animation: shake-error 0.35s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }

    @keyframes shake-error {
      10%, 90% { transform: translate3d(-1px, 0, 0); }
      20%, 80% { transform: translate3d(2px, 0, 0); }
      30%, 50%, 70% { transform: translate3d(-3px, 0, 0); }
      40%, 60% { transform: translate3d(3px, 0, 0); }
    }

    /* Indikator Ikon Valid / Invalid Animasi */
    .input-feedback-icon {
      transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
      transform: scale(0);
      opacity: 0;
    }
    .input-feedback-icon.show-feedback {
      transform: scale(1);
      opacity: 1;
    }

    /* Pesan Error & Sukses Animasi Slide Down */
    .form-feedback-message {
      overflow: hidden;
      max-height: 0;
      opacity: 0;
      transform: translateY(-4px);
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .form-feedback-message.show-message {
      max-height: 60px;
      opacity: 1;
      transform: translateY(0);
    }

    /* Ripple Wave Effect */
    .btn-ripple {
      position: relative;
      overflow: hidden;
    }
    .ripple-effect {
      position: absolute;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.45);
      transform: scale(0);
      animation: ripple-animation 0.55s ease-out;
      pointer-events: none;
    }
    @keyframes ripple-animation {
      to { transform: scale(4); opacity: 0; }
    }

    /* Card Interactive */
    .card-interactive {
      transition: var(--transition-standard);
    }
    .card-interactive:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.04);
    }

    /* Table Interactive & Row Micro-interactions */
    .table-interactive tbody tr {
      transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .table-interactive tbody tr:hover {
      background-color: #f1f5f9;
      transform: translateY(-1px);
      box-shadow: inset 3px 0 0 #2563eb;
    }

    /* ---------------------------------------------------------
       3. DASHBOARD STATISTIC CARDS & HOVER EFFECTS
       --------------------------------------------------------- */
    .stat-card {
      position: relative;
      overflow: hidden;
      border-radius: 1rem;
      transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      cursor: default;
    }
    .stat-card:hover {
      transform: translateY(-5px);
    }
    .stat-card-blue:hover {
      box-shadow: 0 16px 30px -8px rgba(37, 99, 235, 0.22), 0 4px 10px -2px rgba(37, 99, 235, 0.08);
      border-color: rgba(59, 130, 246, 0.45) !important;
    }
    .stat-card-emerald:hover {
      box-shadow: 0 16px 30px -8px rgba(16, 185, 129, 0.22), 0 4px 10px -2px rgba(16, 185, 129, 0.08);
      border-color: rgba(16, 185, 129, 0.45) !important;
    }
    .stat-card-red:hover {
      box-shadow: 0 16px 30px -8px rgba(239, 68, 68, 0.22), 0 4px 10px -2px rgba(239, 68, 68, 0.08);
      border-color: rgba(239, 68, 68, 0.45) !important;
    }
    .stat-card-purple:hover {
      box-shadow: 0 16px 30px -8px rgba(168, 85, 247, 0.22), 0 4px 10px -2px rgba(168, 85, 247, 0.08);
      border-color: rgba(168, 85, 247, 0.45) !important;
    }
    .stat-card::after {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 50%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
      transform: skewX(-20deg);
      transition: 0.65s ease;
      pointer-events: none;
    }
    .stat-card:hover::after {
      left: 150%;
    }
    .stat-icon {
      transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .stat-card:hover .stat-icon {
      transform: scale(1.15) rotate(8deg);
    }

    /* ---------------------------------------------------------
       4. PAGE TRANSITIONS (TRANSISI ANTARHALAMAN)
       --------------------------------------------------------- */
    @keyframes pageEnter {
      from {
        opacity: 0;
        transform: translateY(8px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .page-enter {
      animation: pageEnter 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Top Progress Bar for Page Navigation */
    #page-nav-progress {
      position: fixed;
      top: 0;
      left: 0;
      height: 3px;
      width: 0%;
      background: linear-gradient(90deg, #2563eb, #6366f1, #10b981);
      z-index: 9999;
      pointer-events: none;
      transition: width 0.3s ease, opacity 0.2s ease;
      opacity: 0;
    }

    /* ---------------------------------------------------------
       5. PAYMENT INTERACTIONS & SUCCESS ANIMATIONS
       --------------------------------------------------------- */
    .pop-in-spring {
      animation: pop-in-spring 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    @keyframes pop-in-spring {
      0% { transform: scale(0.3); opacity: 0; }
      70% { transform: scale(1.1); opacity: 1; }
      100% { transform: scale(1); opacity: 1; }
    }

    /* SVG Checkmark Draw Animation */
    .checkmark-svg {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      display: block;
      stroke-width: 3;
      stroke: #10b981;
      stroke-miterlimit: 10;
      box-shadow: inset 0 0 0 #10b981;
      animation: fill-check 0.4s ease-in-out 0.4s forwards, scale-check 0.3s ease-in-out 0.9s both;
    }
    .checkmark-circle {
      stroke-dasharray: 166;
      stroke-dashoffset: 166;
      stroke-width: 3;
      stroke-miterlimit: 10;
      stroke: #10b981;
      fill: none;
      animation: stroke-circle 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .checkmark-check {
      transform-origin: 50% 50%;
      stroke-dasharray: 48;
      stroke-dashoffset: 48;
      animation: stroke-check 0.35s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
    }
    @keyframes stroke-circle {
      100% { stroke-dashoffset: 0; }
    }
    @keyframes stroke-check {
      100% { stroke-dashoffset: 0; }
    }
    @keyframes fill-check {
      100% { box-shadow: inset 0 0 0 30px rgba(16, 185, 129, 0.12); }
    }
    @keyframes scale-check {
      0%, 100% { transform: none; }
      50% { transform: scale3d(1.1, 1.1, 1); }
    }

    /* ---------------------------------------------------------
       6. TOAST NOTIFICATION MICRO-INTERACTIONS & STATES
       --------------------------------------------------------- */
    .toast-item {
      pointer-events: auto;
      transform: translateX(125%) scale(0.95);
      opacity: 0;
      max-height: 180px;
      overflow: hidden;
      transition: transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1),
                  opacity 0.25s ease,
                  max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                  margin 0.25s ease,
                  padding 0.25s ease;
    }
    .toast-item.show {
      transform: translateX(0) scale(1);
      opacity: 1;
    }
    .toast-item.hide {
      transform: translateX(125%) scale(0.95);
      opacity: 0;
      max-height: 0 !important;
      margin: 0 !important;
      padding-top: 0 !important;
      padding-bottom: 0 !important;
      border-width: 0 !important;
    }
    .toast-progress {
      height: 3px;
      width: 100%;
      position: absolute;
      bottom: 0;
      left: 0;
      transform-origin: left;
      transition: width linear;
    }

    /* Status Pulse Badges */
    .status-pulse-amber {
      box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
      animation: status-pulse-amber-kf 2s infinite;
    }
    @keyframes status-pulse-amber-kf {
      0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
      70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
      100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    .status-pulse-emerald {
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: status-pulse-emerald-kf 2s infinite;
    }
    @keyframes status-pulse-emerald-kf {
      0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
      100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Table feedback: row hover, pagination, and loading skeleton */
    .table-interactive tbody tr:not(.empty-state-row) { transition: background-color .16s ease; }
    .table-interactive tbody tr:not(.empty-state-row):hover { background-color: #f8fafc; }
    .table-loading-host { position: relative; }
    .table-skeleton-overlay { display: none; position: absolute; inset: 0; z-index: 20; padding: 64px 20px 20px; background: rgba(255,255,255,.94); }
    .table-loading-host[aria-busy="true"] .table-skeleton-overlay { display: block; }
    .table-skeleton-rows { display: grid; gap: 12px; }
    .table-skeleton-row { display: grid; grid-template-columns: 1.1fr 1.4fr 1fr .8fr .65fr; gap: 16px; align-items: center; min-height: 38px; }
    .table-skeleton-row span { height: 12px; border-radius: 999px; background: linear-gradient(100deg,#edf2f4 20%,#f8fafb 40%,#edf2f4 60%); background-size: 220% 100%; animation: table-shimmer 1.35s ease-in-out infinite; }
    @keyframes table-shimmer { to { background-position-x: -220%; } }
    [data-table-loading-host] nav[role="navigation"] a { transition: color .16s ease, background-color .16s ease, transform .16s ease; }
    [data-table-loading-host] nav[role="navigation"] a:hover { transform: translateY(-1px); }

    /* Reusable modal motion: short opacity and scale transitions. */
    .modal-fade { opacity: 0; transition: opacity 200ms ease; }
    .modal-fade.is-open { opacity: 1; }
    .modal-scale { transform: scale(.96); transition: transform 200ms ease; }
    .modal-scale.is-open { transform: scale(1); }

    @media (prefers-reduced-motion: reduce) {
      .btn-interactive, .input-interactive, .card-interactive, .stat-card, .page-enter, .stat-icon, .checkmark-svg, .checkmark-circle, .checkmark-check, .pop-in-spring {
        transform: none !important;
        transition: none !important;
        animation: none !important;
      }
      .table-interactive tbody tr, .table-skeleton-row span, [data-table-loading-host] nav[role="navigation"] a { transition: none !important; animation: none !important; }
      .modal-fade, .modal-scale { transition: none !important; }
    }

    @media print {
      .no-print { display: none !important; }
    }
  </style>
  @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col font-sans antialiased">
  <!-- Top Navigation Progress Indicator (Smooth Page Transition) -->
  <div id="page-nav-progress"></div>
  
  <!-- Global Toast Notification Container (Payment & System Alerts) -->
  <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2.5 pointer-events-none max-w-sm w-full no-print"></div>
  @php
    $currentUser = auth()->user();
    $homeRoute = ($currentUser && $currentUser->role === 'admin') ? 'dashboard' : 'pos.index';
  @endphp

  <!-- Topbar Navigation -->
  <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
      
      <!-- Brand Logo -->
      <a href="{{ route($homeRoute) }}" class="flex items-center space-x-3 group flex-shrink-0">
        <div class="w-10 h-10 rounded-xl bg-blue-600 group-hover:bg-blue-700 flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/20 group-hover:scale-105 transition">
          <i class="fa-solid fa-bottle-droplet text-lg"></i>
        </div>
        <div>
          <span class="text-base font-extrabold text-slate-900 tracking-tight block leading-tight group-hover:text-blue-600 transition">TELAGA RIZQY 21</span>
          <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider block">Susu Kambing Metro</span>
        </div>
      </a>

      <!-- Navigation Links (Desktop) -->
      @if($currentUser)
        <div class="hidden lg:flex items-center space-x-1 py-1">
          @if($currentUser->role === 'admin')
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="{{ route('pos.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('pos.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-cash-register"></i> Kasir POS
            </a>
            <a href="{{ route('pemasukan.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('pemasukan.*') ? 'bg-emerald-50 text-emerald-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-arrow-trend-up text-emerald-600"></i> Pemasukan
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('pengeluaran.*') ? 'bg-red-50 text-red-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-arrow-trend-down text-red-600"></i> Pengeluaran
            </a>
            <a href="{{ route('laporan.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('laporan.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-file-invoice-dollar"></i> Laporan
            </a>
            <a href="{{ route('produk.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('produk.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-boxes-stacked"></i> Produk
            </a>
            <a href="{{ route('transaksi.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('transaksi.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-clock-rotate-left"></i> Transaksi
            </a>
            <a href="{{ route('pembayaran.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('pembayaran.*') ? 'bg-purple-50 text-purple-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-qrcode text-purple-600"></i> QRIS
            </a>
            <a href="{{ route('kasir.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('kasir.*') ? 'bg-indigo-50 text-indigo-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-users-gear text-indigo-600"></i> Kasir
            </a>
            <a href="{{ route('diagram.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('diagram.*') ? 'bg-amber-50 text-amber-900 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-diagram-project text-amber-600"></i> Diagram
            </a>
          @else
            <!-- Kasir Navigation -->
            <a href="{{ route('pos.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ request()->routeIs('pos.*') ? 'bg-blue-600 text-white font-black shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-cash-register"></i> Mesin Kasir (POS)
            </a>
            <a href="{{ route('transaksi.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ request()->routeIs('transaksi.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Transaksi Saya
            </a>
            <a href="{{ route('diagram.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ request()->routeIs('diagram.*') ? 'bg-amber-50 text-amber-900 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
              <i class="fa-solid fa-diagram-project text-amber-600"></i> Diagram
            </a>
          @endif
        </div>
      @endif

      <!-- User Profile, Shortcut Ganti Peran, Mobile Hamburger & Logout -->
      <div class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
        @if($currentUser)
          <!-- Shortcut Cepat Ganti Peran (Admin <-> Kasir) -->
          @if($currentUser->role === 'kasir')
            <a href="{{ route('demo.switch-role', 'admin') }}" title="Klik untuk beralih cepat ke akun ADMIN (Buka Laporan & Dashboard)"
              class="btn-interactive inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 text-xs font-bold transition shadow-sm">
              <i class="fa-solid fa-crown text-indigo-600"></i>
              <span>Ke Admin</span>
            </a>
          @else
            <a href="{{ route('demo.switch-role', 'kasir') }}" title="Klik untuk beralih cepat ke akun KASIR (Buka Mesin POS)"
              class="btn-interactive inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-bold transition shadow-sm">
              <i class="fa-solid fa-cash-register text-amber-600"></i>
              <span>Ke Kasir</span>
            </a>
          @endif

          <div class="text-right hidden sm:block">
            <div class="text-xs font-bold text-slate-900">{{ $currentUser->name }}</div>
            <div class="text-[10px] font-semibold text-blue-600 uppercase">{{ $currentUser->role }}</div>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition btn-interactive" title="Logout">
              <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
          </form>

          <!-- Mobile Hamburger Toggle -->
          <button type="button" onclick="document.getElementById('mobileNavMenu').classList.toggle('hidden')" class="lg:hidden p-2 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-xl transition" title="Menu">
            <i class="fa-solid fa-bars text-base"></i>
          </button>
        @else
          <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition">
            Masuk
          </a>
        @endif
      </div>

    </div>

    <!-- Mobile Navigation Drawer -->
    @if($currentUser)
      <div id="mobileNavMenu" class="hidden lg:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1 shadow-lg">
        <!-- Mobile Switch Shortcut -->
        <div class="pb-2 mb-1 border-b border-slate-100">
          @if($currentUser->role === 'kasir')
            <a href="{{ route('demo.switch-role', 'admin') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-indigo-50 text-indigo-800 text-xs font-bold">
              <span><i class="fa-solid fa-crown mr-2 text-indigo-600"></i> Beralih ke Admin (Laporan &amp; Data)</span>
              <span class="text-[10px] bg-indigo-200 px-2 py-0.5 rounded-md">1 Klik</span>
            </a>
          @else
            <a href="{{ route('demo.switch-role', 'kasir') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50 text-amber-800 text-xs font-bold">
              <span><i class="fa-solid fa-cash-register mr-2 text-amber-600"></i> Beralih ke Kasir (Mesin POS)</span>
              <span class="text-[10px] bg-amber-200 px-2 py-0.5 rounded-md">1 Klik</span>
            </a>
          @endif
        </div>
        @if($currentUser->role === 'admin')
          <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-chart-pie mr-2"></i> Dashboard
          </a>
          <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('pos.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-cash-register mr-2"></i> Kasir POS
          </a>
          <a href="{{ route('pemasukan.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('pemasukan.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-arrow-trend-up text-emerald-600 mr-2"></i> Pemasukan
          </a>
          <a href="{{ route('pengeluaran.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('pengeluaran.*') ? 'bg-red-50 text-red-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-arrow-trend-down text-red-600 mr-2"></i> Pengeluaran
          </a>
          <a href="{{ route('laporan.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('laporan.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Laporan Keuangan
          </a>
          <a href="{{ route('produk.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('produk.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-boxes-stacked mr-2"></i> Produk &amp; Stok
          </a>
          <a href="{{ route('transaksi.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('transaksi.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat Transaksi
          </a>
          <a href="{{ route('pembayaran.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('pembayaran.*') ? 'bg-purple-50 text-purple-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-qrcode text-purple-600 mr-2"></i> Pemantauan QRIS
          </a>
          <a href="{{ route('kasir.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('kasir.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-users-gear text-indigo-600 mr-2"></i> Kelola Akun Kasir
          </a>
          <a href="{{ route('diagram.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('diagram.*') ? 'bg-amber-50 text-amber-900' : 'text-slate-600' }}">
            <i class="fa-solid fa-diagram-project text-amber-600 mr-2"></i> Diagram Sistem &amp; Relasi
          </a>
        @else
          <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('pos.*') ? 'bg-blue-600 text-white' : 'text-slate-600' }}">
            <i class="fa-solid fa-cash-register mr-2"></i> Mesin Kasir (POS)
          </a>
          <a href="{{ route('transaksi.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('transaksi.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600' }}">
            <i class="fa-solid fa-clock-rotate-left mr-2"></i> Riwayat Transaksi Saya
          </a>
          <a href="{{ route('diagram.index') }}" class="block px-3 py-2 rounded-xl text-xs font-bold {{ request()->routeIs('diagram.*') ? 'bg-amber-50 text-amber-900' : 'text-slate-600' }}">
            <i class="fa-solid fa-diagram-project text-amber-600 mr-2"></i> Diagram Sistem &amp; Relasi
          </a>
        @endif
      </div>
    @endif
  </nav>

  <!-- Flash Notification -->
  <div class="app-flash-wrap max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full no-print">
    @if(session('success'))
      <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> {{ session('success') }}</div>
      </div>
    @endif
    @if(session('error'))
      <div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2"><i class="fa-solid fa-circle-xmark text-red-600"></i> {{ session('error') }}</div>
      </div>
    @endif
  </div>

  <!-- Content Body -->
  <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full page-enter">
    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 no-print">
    &copy; {{ date('Y') }} Sistem Pengolahan Data Transaksi Keuangan Berbasis Web pada Telaga Rizqy 21.
  </footer>

  <!-- Micro-interactions & Page Transition Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js" integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu" crossorigin="anonymous"></script>
  <script>
    // 1. Global Ripple Effect
    document.addEventListener('click', function(e) {
      const target = e.target.closest('.btn-ripple');
      if (!target || target.disabled || target.classList.contains('is-loading')) return;

      const circle = document.createElement('span');
      const diameter = Math.max(target.clientWidth, target.clientHeight);
      const radius = diameter / 2;
      const rect = target.getBoundingClientRect();

      circle.style.width = circle.style.height = `${diameter}px`;
      circle.style.left = `${e.clientX - rect.left - radius}px`;
      circle.style.top = `${e.clientY - rect.top - radius}px`;
      circle.classList.add('ripple-effect');

      const existingRipple = target.querySelector('.ripple-effect');
      if (existingRipple) existingRipple.remove();

      target.appendChild(circle);
      setTimeout(() => circle.remove(), 600);
    });

    // 2. Transisi Antarhalaman yang Nyaman (Top Progress Bar)
    (function initPageTransition() {
      const progressBar = document.getElementById('page-nav-progress');
      if (!progressBar) return;

      document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank' || e.ctrlKey || e.metaKey) {
          return;
        }

        // Trigger smooth progress bar on page transition
        progressBar.style.opacity = '1';
        progressBar.style.width = '30%';
        setTimeout(() => { progressBar.style.width = '75%'; }, 120);
      });

      // Complete progress bar when DOM ready
      window.addEventListener('load', () => {
        progressBar.style.width = '100%';
        setTimeout(() => {
          progressBar.style.opacity = '0';
          setTimeout(() => { progressBar.style.width = '0%'; }, 200);
        }, 150);
      });
    })();

    // Table skeleton on filter submission and paginator navigation.
    function showTableSkeleton(host) {
      if (!host) return;
      host.setAttribute('aria-busy', 'true');
    }

    document.addEventListener('submit', function(e) {
      const form = e.target.closest('form[data-table-loading-target]');
      if (!form) return;
      showTableSkeleton(document.getElementById(form.dataset.tableLoadingTarget));
    });

    document.addEventListener('click', function(e) {
      const modalOpenButton = e.target.closest('[data-modal-open]');
      if (modalOpenButton) {
        openModalById(modalOpenButton.dataset.modalOpen, modalOpenButton);
        return;
      }

      const modalCloseButton = e.target.closest('[data-modal-close]');
      if (modalCloseButton) {
        closeModal(modalCloseButton.closest('[data-modal-overlay]'));
        return;
      }

      const modalOverlay = e.target.closest('[data-modal-overlay]');
      if (modalOverlay && e.target === modalOverlay && canDismissModal(modalOverlay)) {
        dismissModal(modalOverlay);
        return;
      }

      const link = e.target.closest('a');
      if (!link || e.defaultPrevented || e.ctrlKey || e.metaKey || link.target === '_blank') return;
      const host = link.closest('[data-table-loading-host]');
      if (!host || !link.closest('nav[role="navigation"]')) return;
      showTableSkeleton(host);
    });

    function openModalById(id, trigger = document.activeElement) {
      const overlay = document.getElementById(id);
      if (!overlay) return;
      window.clearTimeout(overlay._modalCloseTimer);
      overlay._modalTrigger = trigger;
      overlay.classList.remove('hidden');
      overlay.classList.add('flex');
      overlay.setAttribute('aria-hidden', 'false');
      requestAnimationFrame(() => {
        if (overlay.classList.contains('hidden')) return;
        overlay.classList.add('is-open');
        const panel = overlay.querySelector('[data-modal-panel]');
        if (panel) panel.classList.add('is-open');
        const firstControl = Array.from(overlay.querySelectorAll('input:not([disabled]), select:not([disabled]), button:not([disabled]), a[href]'))
          .find(control => control.getClientRects().length > 0);
        if (firstControl) firstControl.focus();
        else if (panel) panel.focus();
      });
    }

    function closeModal(overlay) {
      if (!overlay || overlay.classList.contains('hidden')) return;
      overlay.classList.remove('is-open');
      const panel = overlay.querySelector('[data-modal-panel]');
      if (panel) panel.classList.remove('is-open');
      overlay.setAttribute('aria-hidden', 'true');
      const duration = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 210;
      overlay._modalCloseTimer = window.setTimeout(() => {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        if (overlay._modalTrigger && document.contains(overlay._modalTrigger)) overlay._modalTrigger.focus();
      }, duration);
    }

    function dismissModal(overlay) {
      if (overlay && overlay.id === 'paymentModalOverlay') closePaymentModal();
      else closeModal(overlay);
    }

    function canDismissModal(overlay) {
      if (overlay.dataset.modalDismissOutside === 'true') return true;
      if (overlay.id === 'paymentModalOverlay') {
        const failedState = document.getElementById('payStateFailed');
        return failedState && !failedState.classList.contains('hidden');
      }
      return false;
    }

    document.addEventListener('keydown', function(e) {
      const activeOverlay = Array.from(document.querySelectorAll('[data-modal-overlay]:not(.hidden)')).pop();
      if (e.key === 'Tab' && activeOverlay) {
        const controls = Array.from(activeOverlay.querySelectorAll('input:not([disabled]), select:not([disabled]), button:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])'))
          .filter(control => control.getClientRects().length > 0);
        if (controls.length) {
          const first = controls[0];
          const last = controls[controls.length - 1];
          if (e.shiftKey && (document.activeElement === first || document.activeElement === activeOverlay.querySelector('[data-modal-panel]'))) { e.preventDefault(); last.focus(); }
          else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
        return;
      }
      if (e.key !== 'Escape') return;
      const overlays = Array.from(document.querySelectorAll('[data-modal-overlay]:not(.hidden)'));
      const overlay = overlays[overlays.length - 1];
      if (!overlay || !canDismissModal(overlay)) return;
      e.preventDefault();
      dismissModal(overlay);
    });

    // 3. Animasi Angka Statistik (Counter Up Animation dengan Easing)
    function animateCounters() {
      const counters = document.querySelectorAll('[data-counter-target]');
      if (!counters.length) return;

      const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-counter-target')) || 0;
        const type = counter.getAttribute('data-counter-type') || 'number'; // 'rupiah' or 'number'
        const prefix = counter.getAttribute('data-counter-prefix') || '';
        const suffix = counter.getAttribute('data-counter-suffix') || '';
        const duration = parseInt(counter.getAttribute('data-counter-duration')) || 1200;

        if (prefersReducedMotion) {
          counter.textContent = formatValue(target, type, prefix, suffix);
          return;
        }

        let startTime = null;

        function step(timestamp) {
          if (!startTime) startTime = timestamp;
          const progress = Math.min((timestamp - startTime) / duration, 1);
          // Ease-out cubic easing function
          const easeOut = 1 - Math.pow(1 - progress, 3);
          const current = Math.floor(easeOut * target);

          counter.textContent = formatValue(current, type, prefix, suffix);

          if (progress < 1) {
            window.requestAnimationFrame(step);
          } else {
            counter.textContent = formatValue(target, type, prefix, suffix);
          }
        }

        function formatValue(val, formatType, pfx, sfx) {
          let formatted = '';
          if (formatType === 'rupiah') {
            formatted = 'Rp ' + val.toLocaleString('id-ID');
          } else {
            formatted = val.toLocaleString('id-ID');
          }
          return `${pfx}${formatted}${sfx}`;
        }

        window.requestAnimationFrame(step);
      });
    }

    // 4. Global Toast Notification System (Micro-interactions: Enter, Exit, Dismiss, Pause)
    window.showToast = function(title, message, type = 'success', duration = 5000) {
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      toast.className = 'toast-item relative bg-white border rounded-2xl p-4 shadow-xl flex items-start gap-3 pointer-events-auto transition-all text-xs overflow-hidden';
      
      let iconHtml = '';
      let borderClass = '';
      let progressColor = '';
      
      if (type === 'success') {
        borderClass = 'border-emerald-200 text-slate-800 shadow-emerald-500/10';
        progressColor = 'bg-emerald-500';
        iconHtml = '<div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 text-base font-bold shadow-sm pop-in-spring"><i class="fa-solid fa-circle-check"></i></div>';
      } else if (type === 'error') {
        borderClass = 'border-red-200 text-slate-800 shadow-red-500/10';
        progressColor = 'bg-red-500';
        iconHtml = '<div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0 text-base font-bold shadow-sm pop-in-spring"><i class="fa-solid fa-circle-xmark"></i></div>';
      } else if (type === 'warning') {
        borderClass = 'border-amber-200 text-slate-800 shadow-amber-500/10';
        progressColor = 'bg-amber-500';
        iconHtml = '<div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 text-base font-bold shadow-sm pop-in-spring"><i class="fa-solid fa-triangle-exclamation"></i></div>';
      } else {
        borderClass = 'border-blue-200 text-slate-800 shadow-blue-500/10';
        progressColor = 'bg-blue-500';
        iconHtml = '<div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 text-base font-bold shadow-sm pop-in-spring"><i class="fa-solid fa-circle-info"></i></div>';
      }

      toast.classList.add(...borderClass.split(' '));
      toast.innerHTML = `
        ${iconHtml}
        <div class="flex-1 pr-1">
          <h4 class="font-bold text-slate-900 text-xs">${title}</h4>
          <p class="text-slate-500 text-[11px] mt-0.5 leading-relaxed">${message}</p>
        </div>
        <button type="button" class="btn-interactive text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg p-1 transition" title="Tutup Notifikasi">
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
        <div class="toast-progress ${progressColor}"></div>
      `;

      container.appendChild(toast);

      // Trigger smooth slide in
      requestAnimationFrame(() => {
        toast.classList.add('show');
      });

      const progressBar = toast.querySelector('.toast-progress');
      const closeBtn = toast.querySelector('button');

      let timerId = null;
      let remaining = duration;
      let startTime = Date.now();

      function closeToast() {
        if (timerId) clearTimeout(timerId);
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
      }

      closeBtn.addEventListener('click', closeToast);

      if (duration > 0) {
        // Progress bar smooth animation
        progressBar.style.transition = `width ${duration}ms linear`;
        requestAnimationFrame(() => {
          progressBar.style.width = '0%';
        });

        // Pause on hover
        toast.addEventListener('mouseenter', () => {
          if (timerId) clearTimeout(timerId);
          remaining -= Date.now() - startTime;
          const currentWidth = (remaining / duration) * 100;
          progressBar.style.transition = 'none';
          progressBar.style.width = `${Math.max(0, currentWidth)}%`;
        });

        toast.addEventListener('mouseleave', () => {
          if (remaining > 0) {
            startTime = Date.now();
            progressBar.style.transition = `width ${remaining}ms linear`;
            progressBar.style.width = '0%';
            timerId = setTimeout(closeToast, remaining);
          } else {
            closeToast();
          }
        });

        timerId = setTimeout(closeToast, duration);
      }
    };

    // Auto-trigger Laravel Session Flash Messages via Toast
    document.addEventListener('DOMContentLoaded', function() {
      @if(session('success'))
        window.showToast('Operasi Berhasil', "{{ addslashes(session('success')) }}", 'success');
      @endif
      @if(session('error'))
        window.showToast('Terjadi Kesalahan', "{{ addslashes(session('error')) }}", 'error');
      @endif
      @if(session('warning'))
        window.showToast('Peringatan', "{{ addslashes(session('warning')) }}", 'warning');
      @endif
      @if(session('info'))
        window.showToast('Informasi', "{{ addslashes(session('info')) }}", 'info');
      @endif
      @if(isset($errors) && $errors->any())
        window.showToast('Periksa Input', "{{ addslashes($errors->first()) }}", 'error');
      @endif
    });

    // Run counters when DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', animateCounters);
    } else {
      animateCounters();
    }
    // PWA Service Worker Registration (APK Mode)
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').catch(function() {});
      });
    }
  </script>

  @stack('scripts')
</body>
</html>
