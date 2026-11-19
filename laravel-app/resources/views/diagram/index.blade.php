@extends('layouts.app')

@section('title', 'Desain Diagram Sistem & Skripsi')

@push('styles')
<style>
  @media print {
    .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-full { width: 100% !important; margin: 0 !important; padding: 0 !important; }
  }
  .swimlane-col {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
  }
  .flow-node {
    transition: all 0.2s ease;
  }
  .flow-node:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
  }
  .table-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
    overflow: hidden;
  }
  .table-box-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: #ffffff;
    padding: 8px 12px;
    font-size: 11px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .table-field-pk {
    background: #fffbeb;
    color: #92400e;
    font-weight: 700;
  }
  .table-field-fk {
    background: #eff6ff;
    color: #1e40af;
    font-weight: 600;
  }
</style>
@endpush

@section('content')
<div class="space-y-6 print-full">

  <!-- Header Banner -->
  <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-200 border border-blue-400/30 mb-2">
          <i class="fa-solid fa-graduation-cap"></i> Dokumentasi Skripsi &amp; Arsitektur Sistem
        </div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight">Desain Diagram Sistem &amp; Tabel Relasi Database</h1>
        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
          Visualisasi lengkap alur sistem informasi Telaga Rizqy 21 sesuai dokumen skripsi: Flowchart Penjualan (Swimlane 3 Entitas), Tabel Relasi Database (8 Tabel), ERD, dan DFD Level 1.
        </p>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0 no-print">
        <button onclick="window.print()" class="btn-interactive px-4 py-2.5 rounded-2xl bg-white text-slate-900 font-bold text-xs shadow-md hover:bg-slate-100 transition flex items-center gap-2">
          <i class="fa-solid fa-print text-blue-600"></i> Cetak / Simpan PDF
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto no-print pt-6 border-t border-white/10 mt-6 text-xs font-bold">
      <button onclick="switchTab('flowchart', this)" class="tab-btn px-4 py-2 rounded-xl transition bg-blue-500 text-white shadow-sm flex items-center gap-2">
        <i class="fa-solid fa-diagram-project"></i> Gambar 23: Flowchart Penjualan
      </button>
      <button onclick="switchTab('relasi', this)" class="tab-btn px-4 py-2 rounded-xl transition bg-white/10 text-slate-200 hover:bg-white/20 flex items-center gap-2">
        <i class="fa-solid fa-table-cells"></i> Gambar 28: Desain Tabel Relasi
      </button>
      <button onclick="switchTab('erd', this)" class="tab-btn px-4 py-2 rounded-xl transition bg-white/10 text-slate-200 hover:bg-white/20 flex items-center gap-2">
        <i class="fa-solid fa-network-wired"></i> Gambar 27: Desain ERD
      </button>
      <button onclick="switchTab('dfd', this)" class="tab-btn px-4 py-2 rounded-xl transition bg-white/10 text-slate-200 hover:bg-white/20 flex items-center gap-2">
        <i class="fa-solid fa-arrows-split-up-and-left"></i> Gambar 25: Desain DFD Level 1
      </button>
    </div>
  </div>

  <!-- =========================================================================
       SECTION 1: FLOWCHART PENJUALAN (SWIMLANE 3 AKTOR) - GAMBAR 23
       ========================================================================= -->
  <div id="tab-flowchart" class="space-y-6">
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-6">
        <div>
          <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Gambar 23. Desain Flowchart Penjualan</span>
          <h2 class="text-base font-black text-slate-900 mt-0.5">Flowchart Swimlane Penjualan (Admin, Kasir, Konsumen)</h2>
          <p class="text-xs text-slate-500 mt-0.5">Sumber: Penulis (2024) - Sistem Informasi Pengolahan Data Transaksi Keuangan Telaga Rizqy 21</p>
        </div>
        <div class="flex items-center gap-2 text-[11px] text-slate-500">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 3 Entitas Terhubung
          </span>
        </div>
      </div>

      <!-- Swimlane Grid Container -->
      <div class="overflow-x-auto pb-4">
        <div class="min-w-[850px] grid grid-cols-3 gap-4 border-2 border-slate-300 rounded-3xl p-4 bg-slate-50/70">
          
          <!-- ==================== KOLOM 1: ADMIN ==================== -->
          <div class="swimlane-col p-4 flex flex-col">
            <!-- Header Swimlane -->
            <div class="text-center pb-3 border-b-2 border-slate-200">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-black text-xs mb-1">
                <i class="fa-solid fa-crown"></i>
              </span>
              <h3 class="text-sm font-black text-slate-900 tracking-wide">Admin</h3>
              <p class="text-[10px] text-slate-400 font-semibold">Pengelola Data &amp; Master Menu</p>
            </div>

            <!-- Flowchart Nodes Admin -->
            <div class="py-6 space-y-7 flex flex-col items-center">
              
              <!-- 1. Mulai (Terminator) -->
              <div class="flow-node w-36 py-2 px-4 rounded-full bg-slate-900 text-white text-xs font-black text-center shadow-md border-2 border-slate-800">
                Mulai
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 2. Login Ke Halaman Admin (Process) -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-white border-2 border-indigo-200 text-center shadow-sm">
                <span class="text-[10px] font-bold text-indigo-600 block uppercase mb-0.5">Proses Login</span>
                <span class="text-xs font-bold text-slate-800 leading-snug">Login Ke Halaman Admin</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 3. Input Management Data Menu dan User (Data I/O Parallelogram) -->
              <div class="flow-node w-52 p-3.5 rounded-2xl bg-indigo-50/80 border-2 border-dashed border-indigo-400 text-center shadow-sm relative">
                <span class="text-[10px] font-bold text-indigo-700 block uppercase mb-0.5"><i class="fa-solid fa-keyboard mr-1"></i> Input / Output Data</span>
                <span class="text-xs font-black text-indigo-950 leading-snug">
                  Input Management Data Menu dan User
                </span>
                <span class="text-[10px] text-indigo-600 block mt-1 font-semibold">(Master Produk &amp; Akun)</span>
              </div>

              <!-- Connector Line to Kasir Catalog -->
              <div class="mt-4 pt-4 border-t border-dashed border-slate-300 w-full text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                  <i class="fa-solid fa-arrow-right text-indigo-600"></i> Data Menu Tersedia di POS
                </div>
              </div>

            </div>
          </div>

          <!-- ==================== KOLOM 2: KASIR ==================== -->
          <div class="swimlane-col p-4 flex flex-col">
            <!-- Header Swimlane -->
            <div class="text-center pb-3 border-b-2 border-slate-200">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-black text-xs mb-1">
                <i class="fa-solid fa-cash-register"></i>
              </span>
              <h3 class="text-sm font-black text-slate-900 tracking-wide">Kasir</h3>
              <p class="text-[10px] text-slate-400 font-semibold">Petugas Mesin POS &amp; Transaksi</p>
            </div>

            <!-- Flowchart Nodes Kasir -->
            <div class="py-6 space-y-5 flex flex-col items-center">
              
              <!-- 1. Login Ke Halaman Kasir (Process) -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-white border-2 border-blue-200 text-center shadow-sm">
                <span class="text-[10px] font-bold text-blue-600 block uppercase mb-0.5">Proses Login</span>
                <span class="text-xs font-bold text-slate-800 leading-snug">Login Ke Halaman Kasir</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 2. Input Pesanan (Data I/O) -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-blue-50/80 border-2 border-dashed border-blue-300 text-center shadow-sm">
                <span class="text-[10px] font-bold text-blue-700 block uppercase mb-0.5">Input Data</span>
                <span class="text-xs font-black text-blue-950">Input Pesanan</span>
                <div class="text-[9px] text-blue-600 mt-0.5">&larr; Memilih dari Konsumen</div>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 3. Input Uang yang Dibayarkan (Data I/O) -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-blue-50/80 border-2 border-dashed border-blue-300 text-center shadow-sm">
                <span class="text-[10px] font-bold text-blue-700 block uppercase mb-0.5">Input Pembayaran</span>
                <span class="text-xs font-black text-blue-950">Input Uang yang Dibayarkan</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 4. Decision: Apakah Ada Kembalian? -->
              <div class="flow-node w-44 p-3 rounded-2xl bg-amber-50 border-2 border-amber-300 text-center shadow-sm relative">
                <span class="text-[10px] font-extrabold text-amber-800 block uppercase">Keputusan</span>
                <span class="text-xs font-black text-amber-950">Apakah ada kembalian?</span>
              </div>

              <!-- Cabang Decision: Ya / Tidak -->
              <div class="w-full grid grid-cols-2 gap-2 text-center text-[10px] font-bold">
                <div class="text-emerald-600">
                  <span>&darr; Ya</span>
                </div>
                <div class="text-slate-500">
                  <span>&darr; Tidak</span>
                </div>
              </div>

              <!-- 5. Memberi Kembalian (Process jika Ya) -->
              <div class="flow-node w-48 p-2.5 rounded-xl bg-emerald-50 border-2 border-emerald-300 text-center text-xs font-bold text-emerald-950 shadow-sm">
                <i class="fa-solid fa-hand-holding-dollar text-emerald-600 mr-1"></i> Memberi Kembalian
                <div class="text-[9px] text-emerald-600 font-semibold">&rarr; Konsumen Menerima</div>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 6. Konfirmasi Pembayaran Transaksi -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-blue-600 text-white text-center shadow-md border-2 border-blue-700">
                <span class="text-[10px] font-bold text-blue-200 block uppercase mb-0.5">Validasi Server</span>
                <span class="text-xs font-black">Konfirmasi Pembayaran Transaksi</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 7. Cetak Struk Pembayaran (Document Shape) -->
              <div class="flow-node w-48 p-2.5 rounded-xl bg-slate-100 border-2 border-slate-300 text-center shadow-sm">
                <i class="fa-solid fa-receipt text-slate-600 mr-1"></i>
                <span class="text-xs font-black text-slate-800">Struk Pembayaran</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 8. Memberi Produk yang Dipesan -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-emerald-600 text-white text-center shadow-md border-2 border-emerald-700">
                <i class="fa-solid fa-box-open mr-1"></i>
                <span class="text-xs font-black">Memberi Produk Yang Dipesan</span>
              </div>

            </div>
          </div>

          <!-- ==================== KOLOM 3: KONSUMEN ==================== -->
          <div class="swimlane-col p-4 flex flex-col">
            <!-- Header Swimlane -->
            <div class="text-center pb-3 border-b-2 border-slate-200">
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-black text-xs mb-1">
                <i class="fa-solid fa-user"></i>
              </span>
              <h3 class="text-sm font-black text-slate-900 tracking-wide">Konsumen</h3>
              <p class="text-[10px] text-slate-400 font-semibold">Pelanggan Telaga Rizqy 21</p>
            </div>

            <!-- Flowchart Nodes Konsumen -->
            <div class="py-6 space-y-5 flex flex-col items-center">
              
              <!-- 1. Datang ke Telaga Rizqy 21 -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-white border-2 border-emerald-200 text-center shadow-sm">
                <span class="text-[10px] font-bold text-emerald-600 block uppercase mb-0.5">Kunjungan</span>
                <span class="text-xs font-bold text-slate-800 leading-snug">Datang ke Telaga Rizqy 21</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 2. Melihat Daftar Katalog -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-white border-2 border-slate-200 text-center shadow-sm">
                <i class="fa-solid fa-book-open text-slate-400 mr-1"></i>
                <span class="text-xs font-bold text-slate-800">Melihat Daftar Katalog</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 3. Decision: Tertarik? -->
              <div class="flow-node w-44 p-3 rounded-2xl bg-amber-50 border-2 border-amber-300 text-center shadow-sm">
                <span class="text-[10px] font-extrabold text-amber-800 block uppercase">Keputusan</span>
                <span class="text-xs font-black text-amber-950">Tertarik?</span>
              </div>

              <!-- Decision Split -->
              <div class="w-full flex justify-between px-6 text-[10px] font-bold">
                <span class="text-emerald-600">&darr; Ya (Pesan)</span>
                <span class="text-red-500">Tidak &rarr;</span>
              </div>

              <!-- 4. Membayar Pesanan -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-center shadow-sm">
                <span class="text-[10px] font-bold text-emerald-700 block uppercase mb-0.5">Transaksi</span>
                <span class="text-xs font-black text-emerald-950">Membayar Pesanan</span>
                <div class="text-[9px] text-emerald-600 font-semibold mt-0.5">&rarr; Ke Kasir: Input Pesanan</div>
              </div>

              <!-- Spacer matching kasir processing -->
              <div class="h-6"></div>

              <!-- 5. Menerima Kembalian -->
              <div class="flow-node w-48 p-2.5 rounded-xl bg-white border-2 border-slate-200 text-center text-xs font-semibold text-slate-700 shadow-sm">
                Menerima Kembalian
              </div>

              <!-- Spacer matching kasir verification -->
              <div class="h-8"></div>

              <!-- 6. Struk Pembayaran -->
              <div class="flow-node w-48 p-2.5 rounded-xl bg-slate-100 border-2 border-slate-300 text-center text-xs font-semibold text-slate-700 shadow-sm">
                <i class="fa-solid fa-receipt text-slate-500 mr-1"></i> Menerima Struk Pembayaran
              </div>

              <!-- Spacer matching kasir packing -->
              <div class="h-4"></div>

              <!-- 7. Menerima Produk Yang Dipesan -->
              <div class="flow-node w-48 p-3 rounded-2xl bg-emerald-50 border-2 border-emerald-400 text-center shadow-sm">
                <i class="fa-solid fa-bag-shopping text-emerald-600 mr-1"></i>
                <span class="text-xs font-black text-emerald-950">Menerima Produk Yang Dipesan</span>
              </div>

              <!-- Arrow Down -->
              <div class="flex flex-col items-center text-slate-400">
                <i class="fa-solid fa-arrow-down text-sm text-slate-400"></i>
              </div>

              <!-- 8. Selesai (Terminator) -->
              <div class="flow-node w-36 py-2 px-4 rounded-full bg-slate-900 text-white text-xs font-black text-center shadow-md border-2 border-slate-800">
                Selesai
              </div>

            </div>
          </div>

        </div>
      </div>

      <!-- Keterangan Teks Skripsi (Sesuai Naskah Laporan Skripsi Bab III / IV) -->
      <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200/80 mt-6 text-xs text-slate-700 leading-relaxed space-y-2">
        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
          <i class="fa-solid fa-circle-info text-blue-600"></i> Deskripsi Alur Flowchart (Naskah Skripsi):
        </h4>
        <p>
          Berdasarkan <strong>Gambar 23 (Desain Flowchart Penjualan)</strong>, terdapat 3 entitas/aktor utama dalam sistem informasi penjualan Telaga Rizqy 21, yaitu <strong>Admin</strong>, <strong>Kasir</strong>, dan <strong>Konsumen</strong>:
        </p>
        <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
          <li><strong>Alur Admin:</strong> Dimulai dari login ke sistem, kemudian admin mengelola data master berupa manajemen menu/produk olahan susu kambing serta data user (kasir).</li>
          <li><strong>Alur Konsumen:</strong> Konsumen berkunjung ke outlet Telaga Rizqy 21 Metro, melihat katalog varian susu, memutuskan pembelian, membayar pesanan, menerima kembalian (jika ada), menerima struk pembayaran, dan membawa pulang produk yang dipesan hingga alur selesai.</li>
          <li><strong>Alur Kasir:</strong> Kasir login ke mesin kasir (POS), menginput item pesanan yang dipilih konsumen, menginput nominal pembayaran (tunai maupun QRIS), memvalidasi kembalian, mengonfirmasi transaksi lunas di server, mencetak struk faktur resmi, dan menyerahkan produk susu kepada konsumen.</li>
        </ul>
      </div>

    </div>
  </div>

  <!-- =========================================================================
       SECTION 2: DESAIN TABEL RELASI DATABASE (8 TABEL) - GAMBAR 28
       ========================================================================= -->
  <div id="tab-relasi" class="space-y-6 hidden">
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-6">
        <div>
          <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Gambar 28. Desain Tabel Relasi Database</span>
          <h2 class="text-base font-black text-slate-900 mt-0.5">Struktur Relasi Antar Tabel (Relational Schema)</h2>
          <p class="text-xs text-slate-500 mt-0.5">Basis Data: telagarizqy21_db - Terdiri dari 8 Tabel Relasional</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
            Total: 8 Tabel Relasi
          </span>
        </div>
      </div>

      <!-- Visual Layout 8 Tables Grid (phpMyAdmin Designer Style) -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 pb-4">
        
        <!-- Tabel 1: tb_outlet -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-store mr-1.5 text-amber-400"></i> tb_outlet</span>
            <span class="text-[9px] bg-slate-700 px-1.5 py-0.5 rounded">Master</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_outlet</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>nama_outlet</span>
              <span class="text-[10px] text-slate-400 font-mono">varchar(100)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>no_telepon_outlet</span>
              <span class="text-[10px] text-slate-400 font-mono">varchar(20)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>alamat_outlet</span>
              <span class="text-[10px] text-slate-400 font-mono">text</span>
            </div>
          </div>
        </div>

        <!-- Tabel 2: tb_user -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-users mr-1.5 text-blue-400"></i> tb_user</span>
            <span class="text-[9px] bg-slate-700 px-1.5 py-0.5 rounded">Pengguna</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_user</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>nama_user</span>
              <span class="text-[10px] text-slate-400 font-mono">varchar(100)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>username</span>
              <span class="text-[10px] text-slate-400 font-mono">varchar(100)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>password</span>
              <span class="text-[10px] text-slate-400 font-mono">varchar(255)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>jabatan</span>
              <span class="text-[10px] text-slate-400 font-mono">enum('admin','kasir')</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_outlet</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
          </div>
        </div>

        <!-- Tabel 3: tb_menu -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-bottle-droplet mr-1.5 text-emerald-400"></i> tb_menu</span>
            <span class="text-[9px] bg-slate-700 px-1.5 py-0.5 rounded">Produk</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_menu</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>nama_menu</span>
              <span class="text-[10px] text-slate-400 font-mono">varchar(100)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>harga_menu</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_outlet</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
          </div>
        </div>

        <!-- Tabel 4: tb_transaksi -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-receipt mr-1.5 text-purple-400"></i> tb_transaksi</span>
            <span class="text-[9px] bg-purple-900 px-1.5 py-0.5 rounded">Transaksi</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_transaksi</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between font-bold">
              <span>kode_invoice</span>
              <span class="text-[10px] text-slate-500 font-mono">varchar(100)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>kuantitas</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>status_bayar</span>
              <span class="text-[10px] text-slate-400 font-mono">enum(...)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>tgl_transaksi</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>keterangan</span>
              <span class="text-[10px] text-slate-400 font-mono">text</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_menu</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_user</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_outlet</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
          </div>
        </div>

        <!-- Tabel 5: tb_pembayaran -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-money-check-dollar mr-1.5 text-emerald-400"></i> tb_pembayaran</span>
            <span class="text-[9px] bg-emerald-900 px-1.5 py-0.5 rounded">Kas</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_pembayaran</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>total_pembayaran</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>jml_uang_dibayar</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>kembalian</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>tgl_pembayaran</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between font-bold">
              <span>kode_invoice</span>
              <span class="text-[10px] text-slate-500 font-mono">varchar(100)</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_user</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_outlet</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
          </div>
        </div>

        <!-- Tabel 6: tb_pengeluaran -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-file-invoice mr-1.5 text-red-400"></i> tb_pengeluaran</span>
            <span class="text-[9px] bg-red-900 px-1.5 py-0.5 rounded">Biaya</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_pengeluaran</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>jumlah_pengeluaran</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>keterangan_pengeluaran</span>
              <span class="text-[10px] text-slate-400 font-mono">text</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>tanggal_pengeluaran</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_user</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
          </div>
        </div>

        <!-- Tabel 7: tb_log_pengeluaran -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-clock-rotate-left mr-1.5 text-amber-400"></i> tb_log_pengeluaran</span>
            <span class="text-[9px] bg-slate-700 px-1.5 py-0.5 rounded">Audit</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_log_pengeluaran</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>keterangan</span>
              <span class="text-[10px] text-slate-400 font-mono">text</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>tanggal</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_user</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
          </div>
        </div>

        <!-- Tabel 8: tb_log -->
        <div class="table-box">
          <div class="table-box-header">
            <span><i class="fa-solid fa-list-check mr-1.5 text-slate-300"></i> tb_log</span>
            <span class="text-[9px] bg-slate-700 px-1.5 py-0.5 rounded">Aktivitas</span>
          </div>
          <div class="divide-y divide-slate-100 text-[11px]">
            <div class="px-3 py-1.5 table-field-pk flex justify-between">
              <span><i class="fa-solid fa-key text-amber-500 mr-1 text-[10px]"></i> id_log</span>
              <span class="text-[10px] text-amber-700 font-mono">int(11) [PK]</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>isi_log</span>
              <span class="text-[10px] text-slate-400 font-mono">text</span>
            </div>
            <div class="px-3 py-1.5 flex justify-between">
              <span>tanggal_log</span>
              <span class="text-[10px] text-slate-400 font-mono">int(11)</span>
            </div>
            <div class="px-3 py-1.5 table-field-fk flex justify-between">
              <span><i class="fa-solid fa-link text-blue-500 mr-1 text-[10px]"></i> id_user</span>
              <span class="text-[10px] text-blue-700 font-mono">int(11) [FK]</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Keterangan Relasi Antar Tabel (Naskah Skripsi) -->
      <div class="bg-indigo-50/70 p-5 rounded-2xl border border-indigo-200 text-xs text-indigo-950 leading-relaxed space-y-2.5">
        <h4 class="font-bold text-sm text-indigo-900 flex items-center gap-2">
          <i class="fa-solid fa-circle-nodes text-indigo-600"></i> Keterangan Relasi Antar Tabel (Naskah Skripsi Bab III / IV):
        </h4>
        <p>
          Berdasarkan <strong>Gambar 28 (Desain Tabel Relasi)</strong>, skema database sistem informasi Telaga Rizqy 21 memiliki relasi terintegrasi:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-slate-700 pt-1">
          <div class="bg-white p-3 rounded-xl border border-indigo-100 shadow-xs">
            <strong class="text-indigo-900 block mb-1">1. Relasi Outlet &amp; User (1 to Many):</strong>
            Satu outlet (<code class="text-indigo-700">tb_outlet</code>) dapat menaungi banyak user/kasir (<code class="text-indigo-700">tb_user.id_outlet</code>).
          </div>
          <div class="bg-white p-3 rounded-xl border border-indigo-100 shadow-xs">
            <strong class="text-indigo-900 block mb-1">2. Relasi Menu &amp; Transaksi (1 to Many):</strong>
            Setiap transaksi (<code class="text-indigo-700">tb_transaksi</code>) merujuk pada produk menu (<code class="text-indigo-700">tb_menu.id_menu</code>) dan dicatat oleh kasir (<code class="text-indigo-700">tb_user.id_user</code>).
          </div>
          <div class="bg-white p-3 rounded-xl border border-indigo-100 shadow-xs">
            <strong class="text-indigo-900 block mb-1">3. Relasi Transaksi &amp; Pembayaran:</strong>
            Dihubungkan melalui atribut <code class="text-indigo-700 font-bold">kode_invoice</code> unik yang mencatat total nominal, uang bayar, dan kembalian pelanggan.
          </div>
          <div class="bg-white p-3 rounded-xl border border-indigo-100 shadow-xs">
            <strong class="text-indigo-900 block mb-1">4. Relasi Pengeluaran &amp; Audit Log:</strong>
            Setiap pengeluaran kas operasional (<code class="text-indigo-700">tb_pengeluaran</code>) dan log aktivitas (<code class="text-indigo-700">tb_log</code>) mencatat ID user penanggung jawab.
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- =========================================================================
       SECTION 3: DESAIN ERD (ENTITY RELATIONSHIP DIAGRAM) - GAMBAR 27
       ========================================================================= -->
  <div id="tab-erd" class="space-y-6 hidden">
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-6">
        <div>
          <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Gambar 27. Desain ERD (Entity Relationship Diagram)</span>
          <h2 class="text-base font-black text-slate-900 mt-0.5">Diagram Hubungan Entitas &amp; Kardinalitas (Chen Notation)</h2>
          <p class="text-xs text-slate-500 mt-0.5">Sumber: Penulis (2024) - Entitas: Outlet, User, Menu, Transaksi, Pembayaran, Pengeluaran, Log</p>
        </div>
      </div>

      <!-- Visual Chen Notation ERD Canvas / SVG -->
      <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 overflow-x-auto text-center">
        <div class="min-w-[750px] inline-block text-left space-y-6">
          
          <!-- Row 1: Menu & Transaksi -->
          <div class="flex items-center justify-center gap-8">
            <!-- Entitas Menu -->
            <div class="bg-white p-4 rounded-xl border-2 border-slate-800 text-center shadow-md min-w-[140px]">
              <span class="text-xs font-black text-slate-900 block">Menu</span>
              <span class="text-[10px] text-slate-500 font-semibold">(id_menu, nama, harga)</span>
            </div>
            
            <!-- Relationship Diamond: Memiliki -->
            <div class="w-14 h-14 bg-amber-100 border-2 border-amber-500 rotate-45 flex items-center justify-center shadow-xs">
              <span class="-rotate-45 text-[9px] font-bold text-amber-900">1 : M</span>
            </div>

            <!-- Entitas Transaksi -->
            <div class="bg-white p-4 rounded-xl border-2 border-slate-800 text-center shadow-md min-w-[160px]">
              <span class="text-xs font-black text-slate-900 block">Transaksi</span>
              <span class="text-[10px] text-slate-500 font-semibold">(invoice, qty, tgl, status)</span>
            </div>

            <!-- Relationship Diamond: Memiliki -->
            <div class="w-14 h-14 bg-amber-100 border-2 border-amber-500 rotate-45 flex items-center justify-center shadow-xs">
              <span class="-rotate-45 text-[9px] font-bold text-amber-900">1 : 1</span>
            </div>

            <!-- Entitas Pembayaran -->
            <div class="bg-white p-4 rounded-xl border-2 border-slate-800 text-center shadow-md min-w-[140px]">
              <span class="text-xs font-black text-slate-900 block">Pembayaran</span>
              <span class="text-[10px] text-slate-500 font-semibold">(total, bayar, kembali)</span>
            </div>
          </div>

          <!-- Vertical Connector Lines -->
          <div class="flex justify-center items-center gap-24 text-slate-400">
            <i class="fa-solid fa-arrows-up-down"></i>
            <i class="fa-solid fa-arrows-up-down"></i>
          </div>

          <!-- Row 2: Central Outlet -->
          <div class="flex items-center justify-center gap-8">
            <div class="bg-blue-600 text-white p-5 rounded-2xl border-2 border-blue-800 text-center shadow-lg min-w-[180px]">
              <span class="text-sm font-black block">Outlet (Pusat)</span>
              <span class="text-[10px] text-blue-200">id_outlet, nama_outlet, no_telepon, alamat</span>
            </div>
          </div>

          <!-- Vertical Connector Lines -->
          <div class="flex justify-center items-center text-slate-400">
            <i class="fa-solid fa-arrows-up-down"></i>
          </div>

          <!-- Row 3: User & Operasional -->
          <div class="flex items-center justify-center gap-8">
            <!-- Entitas Pengeluaran -->
            <div class="bg-white p-4 rounded-xl border-2 border-slate-800 text-center shadow-md min-w-[140px]">
              <span class="text-xs font-black text-slate-900 block">Pengeluaran</span>
              <span class="text-[10px] text-slate-500 font-semibold">(jumlah, ket, tgl)</span>
            </div>

            <!-- Relationship Diamond -->
            <div class="w-14 h-14 bg-amber-100 border-2 border-amber-500 rotate-45 flex items-center justify-center shadow-xs">
              <span class="-rotate-45 text-[9px] font-bold text-amber-900">M : 1</span>
            </div>

            <!-- Entitas User -->
            <div class="bg-indigo-600 text-white p-5 rounded-2xl border-2 border-indigo-800 text-center shadow-lg min-w-[180px]">
              <span class="text-sm font-black block">User</span>
              <span class="text-[10px] text-indigo-200">id_user, nama, username, jabatan</span>
            </div>

            <!-- Relationship Diamond -->
            <div class="w-14 h-14 bg-amber-100 border-2 border-amber-500 rotate-45 flex items-center justify-center shadow-xs">
              <span class="-rotate-45 text-[9px] font-bold text-amber-900">1 : M</span>
            </div>

            <!-- Entitas Log Aktivitas -->
            <div class="bg-white p-4 rounded-xl border-2 border-slate-800 text-center shadow-md min-w-[140px]">
              <span class="text-xs font-black text-slate-900 block">Log &amp; Audit</span>
              <span class="text-[10px] text-slate-500 font-semibold">(isi_log, tanggal)</span>
            </div>
          </div>

        </div>
      </div>

      <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200 text-xs text-amber-950 leading-relaxed">
        <strong>Penjelasan ERD:</strong> Diagram ERD di atas memodelkan hubungan antar entitas di mana entitas utama <strong>Outlet</strong> dan <strong>User</strong> memiliki kardinalitas <em>one-to-many</em> terhadap entitas operasional transaksi penjualan, pembayaran faktur, pencatatan pengeluaran operasional, dan log aktivitas sistem.
      </div>
    </div>
  </div>

  <!-- =========================================================================
       SECTION 4: DESAIN DFD LEVEL 1 - GAMBAR 25
       ========================================================================= -->
  <div id="tab-dfd" class="space-y-6 hidden">
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-6">
        <div>
          <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Gambar 25. Desain DFD Level 1</span>
          <h2 class="text-base font-black text-slate-900 mt-0.5">Data Flow Diagram Level 1 Sistem Informasi Pengolahan Data Transaksi Keuangan</h2>
          <p class="text-xs text-slate-500 mt-0.5">Sumber: Penulis (2024) - Entitas Luar: Kasir &amp; Admin</p>
        </div>
      </div>

      <!-- DFD Diagram Layout -->
      <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 overflow-x-auto">
        <div class="min-w-[700px] flex flex-col items-center space-y-8">
          
          <!-- Level 1.1: Kasir -> 1.1P Input Data Transaksi -> Tb_transaksi & Tb_pembayaran -->
          <div class="w-full flex items-center justify-between gap-4">
            <!-- External Entity: Kasir -->
            <div class="w-36 p-4 bg-white border-2 border-slate-800 text-center shadow-sm">
              <span class="text-xs font-black text-slate-900 uppercase">KASIR</span>
            </div>

            <!-- Flow Arrow -->
            <div class="flex-1 text-center border-t-2 border-slate-400 relative">
              <span class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 absolute -top-2.5 left-1/4">Data Transaksi &rarr;</span>
            </div>

            <!-- Process 1.1 P -->
            <div class="w-44 h-24 rounded-full bg-white border-2 border-slate-800 flex flex-col items-center justify-center text-center p-2 shadow-sm">
              <span class="text-[10px] font-bold text-slate-400">1.1 P</span>
              <span class="text-xs font-black text-slate-900 leading-tight">Input Data Transaksi</span>
            </div>

            <!-- Flow Arrow -->
            <div class="flex-1 text-center border-t-2 border-slate-400 relative">
              <span class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 absolute -top-2.5 left-1/4">Simpan &rarr;</span>
            </div>

            <!-- Data Store: Tb_pembayaran & Tb_transaksi -->
            <div class="w-48 space-y-1.5">
              <div class="p-2 border-y-2 border-slate-800 bg-white text-xs font-bold text-slate-800 text-center shadow-xs">
                Tb_pembayaran
              </div>
              <div class="p-2 border-y-2 border-slate-800 bg-white text-xs font-bold text-slate-800 text-center shadow-xs">
                Tb_transaksi
              </div>
            </div>
          </div>

          <!-- Level 2.1: Admin -> 2.1P Input Data Pengeluaran -> Tb_pengeluaran -->
          <div class="w-full flex items-center justify-between gap-4">
            <!-- External Entity: Admin -->
            <div class="w-36 p-4 bg-white border-2 border-slate-800 text-center shadow-sm">
              <span class="text-xs font-black text-slate-900 uppercase">ADMIN</span>
            </div>

            <!-- Flow Arrow -->
            <div class="flex-1 text-center border-t-2 border-slate-400 relative">
              <span class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 absolute -top-2.5 left-1/4">Data Pengeluaran &rarr;</span>
            </div>

            <!-- Process 2.1 P -->
            <div class="w-44 h-24 rounded-full bg-white border-2 border-slate-800 flex flex-col items-center justify-center text-center p-2 shadow-sm">
              <span class="text-[10px] font-bold text-slate-400">2.1 P</span>
              <span class="text-xs font-black text-slate-900 leading-tight">Input Data Pengeluaran</span>
            </div>

            <!-- Flow Arrow -->
            <div class="flex-1 text-center border-t-2 border-slate-400 relative">
              <span class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 absolute -top-2.5 left-1/4">Simpan &rarr;</span>
            </div>

            <!-- Data Store: Tb_pengeluaran -->
            <div class="w-48">
              <div class="p-2 border-y-2 border-slate-800 bg-white text-xs font-bold text-slate-800 text-center shadow-xs">
                Tb_pengeluaran
              </div>
            </div>
          </div>

          <!-- Level 3.1: Cetak Data Keuangan -> Laporan ke Admin & Kasir -->
          <div class="w-full flex items-center justify-between gap-4 pt-4 border-t border-slate-200">
            <div class="w-36 text-center text-[11px] font-bold text-slate-500">
              Laporan data keuangan &larr;
            </div>

            <!-- Flow Arrow -->
            <div class="flex-1 text-center border-t-2 border-slate-400"></div>

            <!-- Process 3.1 P -->
            <div class="w-44 h-24 rounded-full bg-blue-50 border-2 border-blue-600 flex flex-col items-center justify-center text-center p-2 shadow-sm">
              <span class="text-[10px] font-bold text-blue-600">3.1 P</span>
              <span class="text-xs font-black text-blue-900 leading-tight">Cetak Data Keuangan</span>
            </div>

            <!-- Flow Arrow -->
            <div class="flex-1 text-center border-t-2 border-slate-400 relative">
              <span class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 absolute -top-2.5 left-1/4">&larr; Ambil Data</span>
            </div>

            <div class="w-48 text-center text-xs font-bold text-slate-600">
              Data Store Keuangan
            </div>
          </div>

        </div>
      </div>

      <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200 text-xs text-emerald-950 leading-relaxed">
        <strong>Penjelasan DFD Level 1:</strong> Menunjukkan aliran data dari entitas luar (Kasir dan Admin). Kasir mengalirkan data transaksi penjualan ke proses 1.1P yang disimpan ke tabel transaksi dan pembayaran. Admin mengalirkan data biaya ke proses 2.1P yang disimpan ke tabel pengeluaran. Proses 3.1P mengolah dan mencetak seluruh data keuangan tersebut menjadi laporan keuangan resmi.
      </div>
    </div>
  </div>

</div>

<!-- Tab Switcher Script -->
<script>
  function switchTab(tabName, btn) {
    document.querySelectorAll('[id^="tab-"]').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + tabName).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(b => {
      b.classList.remove('bg-blue-500', 'text-white', 'shadow-sm');
      b.classList.add('bg-white/10', 'text-slate-200');
    });

    btn.classList.remove('bg-white/10', 'text-slate-200');
    btn.classList.add('bg-blue-500', 'text-white', 'shadow-sm');
  }
</script>
@endsection
