# DOKUMENTASI DESAIN UI/UX & SPESIFIKASI MICRO-INTERACTIONS
**Sistem Informasi Pengolahan Data Transaksi Keuangan Berbasis Web**  
**Studi Kasus:** Telaga Rizqy 21 (Peternakan & Pengolahan Susu Kambing, Metro)

---

## 1. Pendahuluan & Filosofi Desain

Desain antarmuka (UI) dan pengalaman pengguna (UX) pada sistem informasi keuangan Telaga Rizqy 21 dirancang menggunakan pendekatan **Modern Clean, Responsive, and Tactile**. 

Dalam aplikasi kasir (POS) dan manajemen keuangan, pengguna membutuhkan umpan balik visual (*visual feedback*) yang seketika (*instantaneous*) untuk setiap aksi yang dilakukan guna menghindari keraguan, salah klik ganda (*double click*), atau persepsi bahwa sistem sedang macet (*freezing*). 

Penerapan **micro-interactions** yang halus dan konsisten menjadi jembatan antara tindakan fisik pengguna (klik kursor atau sentuhan layar) dan respons sistem komputer.

---

## 2. Spesifikasi Teknis: 4 Kondisi Interaksi Tombol (*Button Interaction*)

Seluruh tombol pada antarmuka sistem (mulai dari tombol login, kasir POS, simpan transaksi, hingga cetak laporan) mematuhi 4 kondisi interaksi standar berikut:

```
[Normal State] ──(Hover)──► [Hover Lift & Shadow]
      │
   (Click)
      ▼
[Active / Pressed: Scale 0.97]
      │
(Proses Asinkronus)
      ▼
[Loading Spinner State (CLS = 0)] ──(Selesai)──► [Sukses / Disabled]
```

### 2.1 Efek Hover yang Halus (*Smooth Hover Lift*)
* **Tujuan UX:** Memberikan sinyal affordance bahwa elemen tersebut dapat diklik (*clickable*).
* **Spesifikasi CSS:**
  * Transformasi: `transform: translateY(-2px);`
  * Peningkatan Bayangan: `box-shadow: 0 10px 20px -3px rgba(37, 99, 235, 0.25), 0 4px 6px -2px rgba(0, 0, 0, 0.05);`
  * Timing Function: `cubic-bezier(0.4, 0, 0.2, 1)` dengan durasi transisi `0.2s`.
* **Karakteristik:** Transisi berlangsung mulus tanpa patah-patah (*anti-aliased motion*).

### 2.2 Animasi Saat Tombol Ditekan (*Tactile Active Press*)
* **Tujuan UX:** Memberikan sensasi taktil mekanik seperti menekan tombol tuts kasir fisik (*tactile feedback*).
* **Spesifikasi CSS:**
  * Transformasi: `transform: translateY(1px) scale(0.97);`
  * Timing Function: `all 0.1s cubic-bezier(0.4, 0, 0.2, 1);`
  * Efek Riak Sentuh (*Touch Ripple*): Menghasilkan lingkaran gelombang semitransparan yang membesar dari titik koordinat klik kursor mouse (`ripple-effect`).

### 2.3 Loading Spinner Saat Proses Berlangsung (*Asynchronous Loading State*)
* **Tujuan UX:** Menginformasikan bahwa sistem sedang memproses data (misal: verifikasi pembayaran atau request token Midtrans) sehingga pengguna tidak panik atau melakukan klik berulang kali.
* **Spesifikasi CSS & HTML:**
  * Tombol diberi kelas `.is-loading` dan atribut aksesibilitas `aria-busy="true"`.
  * Pointer event dikunci otomatis (`pointer-events: none; cursor: wait;`).
  * Teks tombol disembunyikan menggunakan `visibility: hidden;` (bukan `display: none`) untuk **mencegah pergeseran tata letak tombol (*Cumulative Layout Shift - CLS = 0*)**.
  * Pseudo-element `::after` memunculkan spinner lingkaran berputar 360 derajat dengan durasi putar `0.7s infinite linear`.

### 2.4 Kondisi Disabled Ketika Proses Belum Selesai (*Graceful Disabled State*)
* **Tujuan UX:** Mencegah terjadinya eksekusi aksi yang tidak valid (contoh: kasir menekan tombol checkout saat keranjang masih kosong atau uang tunai kurang).
* **Spesifikasi CSS:**
  * Selector: `:disabled`, `[disabled]`, `.is-disabled`.
  * Opasitas: `opacity: 0.55;`
  * Kursor: `cursor: not-allowed !important;`
  * Proteksi Klik: `pointer-events: none;`
  * Efek Transformasi: Dinonaktifkan (`transform: none !important; box-shadow: none !important;`).

---

## 3. Spesifikasi Teknis: Interaksi Formulir (*Form Interaction*)

Formulir adalah gerbang utama pertukaran data antara pengguna dan sistem. Sistem Telaga Rizqy 21 mengimplementasikan 4 standar interaksi formulir berikut:

### 3.1 Animasi Fokus pada Input (*Focus State Micro-Animation*)
* **Tujuan UX:** Memberikan penegasan visual yang jelas mengenai bidang formulir mana yang sedang aktif diisi (*active input indicator*).
* **Spesifikasi CSS:**
  ```css
  .input-interactive:focus {
    background-color: #ffffff !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15), 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  ```
* **Karakteristik:** Terjadi perluasan cincin cahaya (*ring glow expansion*) berwarna biru safir lembut dan elevasi mikro setinggi 1 piksel.

### 3.2 Validasi Secara Real-Time (*Instant Validation on Type*)
* **Tujuan UX:** Membantu pengguna membetulkan kesalahan seketika saat mengetik tanpa harus menunggu tombol submit ditekan.
* **Mekanisme:** Menggunakan event listener `oninput` dan `onblur`:
  * Validasi format teks (panjang minimal karakter dan ketiadaan spasi).
  * Validasi numerik kasir (apakah uang tunai yang diinput $\ge$ total belanjaan).
  * Validasi integritas harga jual ($\ge$ harga pokok produksi/HPP).

### 3.3 Pesan Kesalahan yang Informatif (*Informative Slide-Down Error*)
* **Tujuan UX:** Tidak hanya menyatakan "Error", tetapi menjelaskan **mengapa salah** dan **berapa selisih yang dibutuhkan**.
* **Spesifikasi:**
  * Animasi Getar Mikro (*Subtle Shake*): Mengarahkan fokus mata pengguna ke kolom yang keliru:
    ```css
    @keyframes shake-error {
      10%, 90% { transform: translate3d(-1px, 0, 0); }
      30%, 70% { transform: translate3d(-3px, 0, 0); }
      50% { transform: translate3d(3px, 0, 0); }
    }
    ```
  * Slide-down Text: Pesan teks muncul meluncur ke bawah secara halus (`max-height` transition dari `0` ke `60px`).
  * Teks Contoh: *"Nominal uang tunai kurang Rp 16.000 dari total belanja (Rp 96.000)"*.

### 3.4 Indikator Keberhasilan Setelah Input Valid (*Success Pop-in Checkmark*)
* **Tujuan UX:** Memberikan rasa kepastian dan kepuasan (*positive reinforcement*) bahwa data yang dimasukkan sudah benar.
* **Spesifikasi:**
  * Input border berubah menjadi hijau emerald (`border-color: #10b981; background-color: #f0fdf4;`).
  * Ikon centang hijau (`fa-circle-check`) muncul dengan animasi membesar (*pop-in scale animation*):
    ```css
    .input-feedback-icon {
      transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
      transform: scale(0);
      opacity: 0;
    }
    .input-feedback-icon.show-feedback {
      transform: scale(1);
      opacity: 1;
    }
    ```
  * Memberikan informasi tambahan yang berguna (misal: *"Uang mencukupi. Kembalian: Rp 4.000"* atau *"Margin laba bersih: +26%"*).

---

## 4. Spesifikasi Teknis: Interaksi Dasbor (*Dashboard Interaction*)

Pada antarmuka Dasbor Utama (Executive Dashboard), sistem dirancang untuk menyajikan data analitik finansial secara hidup, interaktif, dan responsif melalui 4 pilar interaksi:

### 4.1 Animasi Angka Statistik (*Counting-Up Animation*)
* **Tujuan UX:** Memberikan daya tarik visual saat halaman pertama kali dimuat (*delight factor*), menarik perhatian pengguna ke metrik utama (omzet, transaksi, pengeluaran, laba), serta mencerminkan dinamika data riil.
* **Algoritma & Easing:**
  * Menggunakan `window.requestAnimationFrame()` dengan fungsi kurva perlambatan kubik (*cubic ease-out*):
    $$\text{easeOut}(p) = 1 - (1 - p)^3$$
  * Format mata uang disesuaikan otomatis dengan standar Rupiah Indonesia (`Rp` dengan pemisah titik ribuan, contoh: `Rp 2.450.000`).
  * Atribut deklaratif HTML:
    ```html
    <span data-counter-target="2450000" data-counter-type="rupiah">Rp 0</span>
    <span data-counter-target="19" data-counter-type="number" data-counter-suffix=" Transaksi">0</span>
    ```

### 4.2 Efek Hover pada Kartu Statistik (*Stat Cards Micro-Motion*)
* **Tujuan UX:** Memberikan fokus visual pada metrik yang sedang diperiksa oleh pengguna dengan memberikan kedalaman elevasi 3D dan kilauan halus.
* **Spesifikasi CSS:**
  * **Card Elevation Lift:** `transform: translateY(-5px);` dengan `transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);`.
  * **Pendaran Warna Sesuai Konteks (*Ambient Glow Shadow*):**
    * Kartu Omzet (Biru): Bayangan bernuansa biru `rgba(37, 99, 235, 0.22)`.
    * Kartu Penjualan Bulan Ini (Hijau): Bayangan bernuansa zamrud `rgba(16, 185, 129, 0.22)`.
    * Kartu Pengeluaran Kas (Merah): Bayangan bernuansa mawar `rgba(239, 68, 68, 0.22)`.
    * Kartu Arus Kas / Laba Kotor (Ungu): Bayangan bernuansa ungu `rgba(168, 85, 247, 0.22)`.
  * **Animasi Ikon Metrik:** Ikon di dalam kartu mengalami perbesaran dan kemiringan dinamis (`transform: scale(1.15) rotate(8deg);`).
  * **Kilauan Kaca Bergerak (*Sheen Reflection Effect*):** Pseudo-elemen `::after` menyapu permukaan kartu dari kiri ke kanan dengan gradien semitransparan saat kursor diarahkan ke kartu.

### 4.3 Grafik dengan Animasi Saat Ditampilkan (*Animated Interactive Charts*)
* **Library:** Chart.js 4.4 dengan integrasi HTML5 Canvas.
* **Spesifikasi Animasi Visual:**
  1. **Grafik Tren Finansial 7 Hari Terakhir (Dual Area & Line):**
     * Menampilkan perbandingan dinamis antara Omzet Penjualan Produk Susu vs Pengeluaran Operasional.
     * Animasi pembentukan kurva bertahap (*progressive draw-in*) dengan durasi `1500ms` dan kurva perlambatan `easeOutQuart`.
     * Area kurva menggunakan gradien lembut semi-transparan (*emerald & rose gradient*).
     * Tooltip interaktif bergaya dark glassmorphism yang mengikuti pergerakan kursor mouse dengan format nilai mata uang otomatis.
  2. **Grafik Donat Proporsi Pembayaran (*Payment Method Doughnut*):**
     * Menampilkan proporsi transaksi Tunai, QRIS, dan Transfer Bank.
     * Animasi pemutaran dan penskalaan terkoordinasi (*animateRotate: true*, *animateScale: true*) dengan durasi `1300ms`.
     * Efek pemisahan irisan (*hoverOffset: 6px*) saat pengguna mengarahkan mouse ke bagian diagram tertentu.

### 4.4 Transisi Antarhalaman yang Nyaman (*Smooth Page Transition*)
* **Tujuan UX:** Menghilangkan efek kedipan putih (*white flash*) saat berpindah halaman dan memberikan umpan balik instan bahwa permintaan navigasi sedang dikerjakan.
* **Spesifikasi Teknis:**
  * **Bilah Kemajuan Atas (*Top Navigation Progress Bar*):** Komponen `#page-nav-progress` terletak di puncak viewport (`fixed top-0 left-0 h-[3px]`) dengan gradien multi-warna (`blue -> indigo -> emerald`). Saat pengguna mengeklik tautan navigasi (Dashboard, POS, Laporan, Produk), bilah bergerak secara halus dari 0% ke 75%, lalu langsung tuntas 100% dan memudar (*fade-out*) saat halaman tujuan selesai dimuat.
  * **Animasi Masuk Halaman (*Page Enter Fade & Slide*):** Elemen `<main>` diberi kelas `.page-enter` dengan animasi keyframe:
    ```css
    @keyframes pageEnter {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }
    ```
    Durasi animasi adalah `0.35s` menggunakan kurva percepatan Apple WebKit `cubic-bezier(0.16, 1, 0.3, 1)`.

---

## 5. Spesifikasi Teknis: Interaksi Pembayaran (*Payment Interaction*)

Proses pembayaran merupakan titik paling krusial dalam sistem informasi keuangan Telaga Rizqy 21. Diperlukan transparansi status, kepastian transaksi, dan pencegahan penipuan (*anti-fraud*) melalui 5 standar interaksi pembayaran:

### 5.1 Loading Saat Pembuatan Transaksi (*Payment Creation Loading*)
* **Tujuan UX:** Mengunci antarmuka seketika kasir menekan tombol *"Proses & Selesaikan Transaksi"* guna mencegah pengiriman ganda (*duplicate submission*) dan menginformasikan bahwa sistem sedang mencadangkan stok susu kambing.
* **Spesifikasi:**
  * Tombol utama beralih ke kondisi `.is-loading` (`aria-busy="true"`).
  * Modal overlay transparan (`bg-slate-900/60 backdrop-blur-sm`) dimunculkan dengan pemutar putar (*spinner*) terpusat.
  * Teks informatif: *"Memproses Transaksi Pembayaran... Menghubungkan ke gateway pembayaran dan mengamankan stok susu."*

### 5.2 Indikator Status Pembayaran (*Live Payment Status Indicator*)
Sistem menyediakan indikator status multi-kondisi dengan warna dan animasi yang jelas:
1. **Menunggu Pembayaran (*Pending Payment*):**
   * Pendaran cincin kuning-kuningan (*amber pulse ring*) berulang (`status-pulse-amber`).
   * Titik ping dinamis (`animate-ping`) mengindikasikan koneksi aktif menunggu scan QRIS dari pelanggan.
2. **Memverifikasi Konfirmasi Server (*Server Verifying*):**
   * Ikon rotasi panah ganda berputar (`fa-arrows-rotate fa-spin`) dengan latar biru nila.
   * Menginformasikan kasir bahwa respons Midtrans sedang dicocokkan dengan basis data server.
3. **Lunas Terverifikasi (*Verified Paid*):**
   * Lencana hijau zamrud dengan ikon centang tebal (`fa-circle-check`).
4. **Dibatalkan / Kadaluarsa (*Cancelled / Expired*):**
   * Lencana merah mawar dengan efek getar (*shake animation*).

### 5.3 Animasi Sukses Ketika Pembayaran Terverifikasi (*Verified Success Animation*)
* **Tujuan UX:** Memberikan kepastian visual yang memuaskan dan melegakan (*reassurance & delight*) setelah transaksi selesai dan terverifikasi sah.
* **Spesifikasi SVG Vector Animation:**
  * **Gambar Lingkaran Luar:** Jalur kurva SVG lingkaran digambar dari 0% ke 100% menggunakan properti `stroke-dashoffset` berdurasi `0.6s`.
  * **Gambar Centang Dalam:** Jalur centang digambar bertahap segera setelah lingkaran tertutup (`stroke-dashoffset: 0` berdurasi `0.35s`).
  * **Efek Pegas Pop-In (*Spring Bounce*):** Seluruh kontainer ikon mengalami lonjakan skala pegas:
    ```css
    @keyframes pop-in-spring {
      0% { transform: scale(0.3); opacity: 0; }
      70% { transform: scale(1.1); opacity: 1; }
      100% { transform: scale(1); opacity: 1; }
    }
    ```

### 5.4 Sistem Notifikasi Dinamis (*Toast Notification System*)
* **Tujuan UX:** Menyampaikan pemberitahuan melayang (*floating toast alert*) tanpa memblokir seluruh interaksi layar pengguna.
* **Karakteristik:**
  * Muncul di pojok kanan atas layar (`fixed top-5 right-5 z-50`).
  * Bergerak masuk (*slide-in*) dengan kurva perlambatan elastis (`cubic-bezier(0.34, 1.56, 0.64, 1)`).
  * Auto-dismiss setelah 5-6 detik atau dapat ditutup manual.
  * **Notifikasi Berhasil:** Menampilkan nomor invoice, total uang dibayarkan, dan status verifikasi server.
  * **Notifikasi Gagal:** Memberikan penjelasan penyebab (contoh: batas waktu pembayaran habis).

### 5.5 Prinsip Ketat: "Jangan Menampilkan Status Berhasil Sebelum Ada Konfirmasi Pembayaran Valid dari Server"
* **Latar Belakang Keamanan:** Dalam pembayaran online pihak ketiga (seperti Midtrans Snap), penutupan jendela popup atau pengembalian callback JavaScript di browser (`onSuccess`) **TIDAK BISA** dipercaya secara langsung sebagai bukti lunas karena rentan terhadap manipulasi sisi klien (*client-side tampering*) atau jaringan terputus sebelum webhook tiba di server.
* **Arsitektur Verifikasi Sistem:**
  ```
  [Pelanggan Bayar QRIS] ──► [Midtrans Gateway]
                                  │ (Webhook Asinkron)
                                  ▼
  [Client POS Browser] ──(Poll)──► [Server API: /transaksi/{id}/status]
           │                                 │
   (Status != 'lunas')                       ▼
           ▼                         [Verifikasi SHA-512 & DB Status]
   [Tetap di State Verifying]                │
           │                                 │
           └──────(Status == 'lunas')◄───────┘
                           │
                           ▼
          [Trigger Animasi Sukses & Notifikasi Berhasil]
  ```
* **Aturan Eksekusi:** Antarmuka kasir secara tegas menahan status "Berhasil" dan tetap berada pada kondisi *Memverifikasi/Menunggu* hingga panggilan API ke endpoint server `GET /transaksi/{id}/status` mengembalikan respons JSON resmi `{ is_lunas: true, status_pembayaran: 'lunas' }`.

---

## 6. Spesifikasi Teknis: Interaksi Notifikasi (*Notification Interaction*)

Sistem mengadopsi pola notifikasi mengambang (*Toast Notification System*) yang modern, responsif, dan tidak menginterupsi alur kerja kasir maupun administrator.

### 6.1 Arsitektur 4 Kondisi Status (*State-Driven Design*)
Setiap notifikasi menyajikan indikasi visual yang tegas dan kontras sesuai dengan tingkat kepentingannya:

| State | Aksen Warna | Ikon Visual | Peruntukan / Skenario Penggunaan |
| :--- | :--- | :--- | :--- |
| **Success** | Emerald (`#10b981`) | `<i class="fa-solid fa-circle-check">` | Transaksi berhasil, pembayaran QRIS lunas, data produk/pengeluaran tersimpan. |
| **Error** | Rose/Red (`#ef4444`) | `<i class="fa-solid fa-circle-xmark">` | Validasi gagal, pembayaran kadaluarsa, transaksi dibatalkan, atau gagal koneksi server. |
| **Warning** | Amber (`#f59e0b`) | `<i class="fa-solid fa-triangle-exclamation">` | Peringatan stok susu menipis di bawah ambang minimum atau uang tunai kurang. |
| **Information** | Sky/Blue (`#2563eb`) | `<i class="fa-solid fa-circle-info">` | Informasi status sinkronisasi Midtrans API atau panduan penggunaan fitur baru. |

### 6.2 Animasi Masuk dan Keluar (*Entry & Exit Micro-Animations*)
1. **Animasi Masuk (*Slide-In with Spring Bounce*):**
   * Transformasi: `transform: translateX(125%) scale(0.95)` $\longrightarrow$ `transform: translateX(0) scale(1.0)`.
   * Opasitas: `opacity: 0` $\longrightarrow$ `opacity: 1`.
   * Timing Function: `cubic-bezier(0.34, 1.56, 0.64, 1)` dengan durasi `0.38s`. Menghasilkan lonjakan elastis taktil saat kartu notifikasi meluncur dari sisi kanan layar.
2. **Animasi Keluar (*Slide-Out & Graceful Collapse*):**
   * Transformasi: `transform: translateX(125%) scale(0.95)` dan `opacity: 0`.
   * Pelipatan Dimensi (*Height Collapse*): Properti `max-height` menyusut ke `0` serta `margin` dan `padding` direset ke `0` dalam `0.3s`.
   * **Manfaat UX:** Menghilangkan jeda kosong (*gap*) seketika sebuah notifikasi ditutup, sehingga tumpukan notifikasi lain di bawahnya bergeser naik secara anggun tanpa lompatan (*zero Cumulative Layout Shift - CLS*).

### 6.3 Notifikasi Dapat Ditutup Pengguna (*User Dismissible*)
* **Tombol Tutup Cepat:** Setiap toast dilengkapi tombol silang (`&times;`) di pojok kanan atas dengan efek hover dan transisi membesar saat didekati kursor.
* **Tutup Otomatis (*Auto-Dismiss*) & Progress Bar:**
  * Bilah garis di bagian bawah notifikasi (`.toast-progress`) menyusut dari lebar 100% ke 0% secara linier selama interval aktif (default: `5000ms`).
* **Jeda Saat Kursor Diarahkan (*Pause on Hover*):**
  * Ketika pengguna mengarahkan kursor mouse ke dalam tubuh notifikasi (`mouseenter`), timer tutup otomatis dan animasi *progress bar* dijeda (*paused*).
  * Saat kursor meninggalkan notifikasi (`mouseleave`), sisa waktu berjalan kembali (*resumed*). Mekanisme ini memastikan pengguna dengan kecepatan membaca beragam tetap dapat membaca informasi secara utuh.

---

## 7. Standar Aksesibilitas (*Accessibility & WCAG 2.1*)

1. **Aria Attributes:** Bidang input yang tidak valid diberi atribut `aria-invalid="true"` dan `aria-describedby="errorMsgId"`.
2. **Kepatuhan Preferensi Gerak (*Reduced Motion*):**
   ```css
   @media (prefers-reduced-motion: reduce) {
     .btn-interactive, .input-interactive, .card-interactive {
       transform: none !important;
       transition: none !important;
       animation: none !important;
     }
   }
   ```
   Animasi getar dan pergeseran skala dinonaktifkan otomatis bagi pengguna yang mengaktifkan opsi aksesibilitas gerak berkurang pada sistem operasi.

---

## 8. File Hasil Implementasi & Lab Pengujian Live

1. **Laboratorium Pengujian Interaksi Interaktif (Live Showcase):**  
   [ui_ux_micro_interactions_showcase.html](file:///c:/Users/advan/Documents/Skripsi%20kating/ui_ux_micro_interactions_showcase.html)  
   *(Buka file ini di browser untuk menguji Form Interaction Lab, Button Interaction, Payment Guard Lab, serta Toast Notification Lab 4 State).*
2. **Master Layout Laravel:**  
   [app.blade.php](file:///c:/Users/advan/Documents/Skripsi%20kating/laravel-app/resources/views/layouts/app.blade.php)  
   *(CSS global untuk tombol, form, kartu statistik, transisi antarhalaman, toast container `#toast-container`, progress countdown, dan fungsi `window.showToast`).*
3. **Dasbor Finansial Laravel:**  
   [dashboard.blade.php](file:///c:/Users/advan/Documents/Skripsi%20kating/laravel-app/resources/views/dashboard.blade.php)  
   *(Animasi angka counter-up, efek hover pendaran kartu metrik, kanvas Chart.js tren 7 hari & donut metode pembayaran).*
4. **Antarmuka Kasir POS Laravel:**  
   [index.blade.php (POS)](file:///c:/Users/advan/Documents/Skripsi%20kating/laravel-app/resources/views/pos/index.blade.php)  
   *(Input uang tunai real-time validation, modal loading transaksi, indikator live QRIS, animasi SVG checkmark spring, dan Server Verification Guard).*
5. **Detail Struk Transaksi Laravel:**  
   [show.blade.php (Transaksi)](file:///c:/Users/advan/Documents/Skripsi%20kating/laravel-app/resources/views/transaksi/show.blade.php)  
   *(Live status polling, simulasi lunas, struk thermal, dan toast alert status verifikasi server).*
6. **Halaman Login Laravel:**  
   [login.blade.php](file:///c:/Users/advan/Documents/Skripsi%20kating/laravel-app/resources/views/auth/login.blade.php)  
   *(Input username dan password dengan animasi fokus, validasi real-time, dan ikon centang hijau).*
