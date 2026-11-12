# 05 — Teknologi Pengembangan (Requirement #6)

Dokumen ini memetakan requirement **Teknologi Pengembangan** ke kondisi nyata aplikasi
`file skripsi` (SPP SMP Muhammadiyah 3 Metro), mencatat temuan, perubahan yang sudah
diterapkan, serta roadmap upgrade yang belum diterapkan. Audit dilakukan pada 12 November 2026.

---

## 1. Ringkasan Eksekutif

| # | Requirement | Status | Keterangan |
|---|-------------|--------|------------|
| 6.1 | Backend Laravel versi stabil terbaru | ⚠️ **Sebagian** | Aplikasi memakai Laravel **10.50.4**, versi stabil terakhir untuk Laravel 10, tetapi bukan versi terbaru. Laravel 10 sudah EOL sejak 4 Februari 2025. |
| 6.2 | Database MySQL | ✅ **Terpenuhi** | MySQL **8.0.30**, database `db_spp_smpm3`, charset `utf8mb4`, strict mode aktif. |
| 6.3 | Frontend Blade, HTML, CSS, JavaScript, Bootstrap | ✅ **Terpenuhi** | Blade + Bootstrap **5.3.2**, Font Awesome 6.5.1, SweetAlert2 11, CSS/JS kustom. Masih memakai CDN dan inline asset. |
| 6.4 | Struktur kode Laravel rapi & mudah dikembangkan | ✅ **Terpenuhi (dengan catatan)** | Pola MVC, Service, Middleware, dan pemisahan view sudah konsisten. Beberapa controller masih gemuk dan belum ada Form Request/Policy. |
| 6.5 | Validasi, autentikasi, otorisasi, keamanan | ✅ **Terpenuhi** | 30 titik validasi, autentikasi ganda (petugas & siswa), middleware role/sesi, CSRF, hashing, rate limit, security headers, validasi upload, verifikasi webhook. |

**Kesimpulan:** seluruh requirement terpenuhi secara fungsional. Satu-satunya ketidaksesuaian
literal adalah frasa **“versi stabil terbaru”**: aplikasi berjalan di Laravel 10/PHP 8.1 yang
sudah melewati masa dukungan keamanan. Upgrade ke Laravel 13/PHP 8.3+ sudah disusun sebagai
roadmap (§10) dan **tidak diterapkan pada iterasi ini** karena berisiko mengganggu demo sidang.

---

## 2. Stack Aktual

| Lapisan | Teknologi | Versi | Bukti |
|---------|-----------|-------|-------|
| Bahasa | PHP | 8.1.10 | `php -v` |
| Framework | Laravel | 10.50.4 | `php artisan --version` |
| Database | MySQL | 8.0.30 | `SELECT VERSION()` |
| Frontend | Blade Template Engine | bawaan Laravel 10 | `resources/views` |
| CSS Framework | Bootstrap | 5.3.2 (CDN) | `layouts/app.blade.php` |
| Ikon | Font Awesome | 6.5.1 (CDN) | `layouts/app.blade.php` |
| Notifikasi | SweetAlert2 | 11 (CDN) | `layouts/app.blade.php` |
| Font | Google Fonts (Inter, Plus Jakarta Sans) | CDN | `layouts/app.blade.php` |
| Build tool | Vite | 4 (scaffold, belum dipakai) | `package.json`, tanpa `vite.config.js` |
| Payment gateway | Midtrans Snap + webhook | library HTTP Laravel | `MidtransService`, `MidtransController` |
| Server lokal | `php artisan serve` / Laragon | — | — |

Catatan: folder `resources/css/app.css`, `resources/js/app.js`, dan `resources/js/bootstrap.js`
merupakan scaffold Laravel, tetapi **belum ada `@vite` di Blade** dan belum ada
`vite.config.js`, sehingga aset yang benar-benar dipakai adalah CDN + `public/js/spa-engine.js`
serta CSS inline di layout.

---

## 3. Peta Requirement → Implementasi

### 6.1 Backend Laravel

| Aspek | Kondisi |
|-------|---------|
| Framework | Laravel 10.50.4 |
| Rilis Laravel 10 | 14 Februari 2023 |
| Bug fix berakhir | 6 Agustus 2024 |
| Security fix berakhir | **4 Februari 2025 (EOL)** |
| Versi terbaru saat audit | **Laravel 13.42.0** (10 November 2026, butuh PHP ≥ 8.3) |
| PHP saat audit | 8.1.10 — **EOL 31 Desember 2025**, tidak lagi menerima patch resmi |

Sumber: `endoflife.date/laravel`, `php.net/supported-versions`, diakses 12 November 2026.

**Implikasi:** aplikasi tetap stabil dan aman untuk demo lokal, tetapi tidak lagi menerima
patch keamanan resmi. Untuk produksi, upgrade wajib dilakukan.

### 6.2 Database MySQL

- Driver default: `mysql` (`config/database.php`).
- Charset `utf8mb4`, collation `utf8mb4_unicode_ci`, `strict => true`.
- 15 migration, seluruh tabel inti berada di database yang sama.
- Relasi memakai foreign key fisik (`constrained`, `cascadeOnDelete`, `nullOnDelete`).
- Payment gateway memakai transaksi database (`DB::beginTransaction` / `commit` / `rollBack`)
  pada proses pembayaran multi-bulan.

### 6.3 Frontend

| Komponen | Implementasi |
|----------|--------------|
| Templating | Blade (`@extends`, `@section`, `@yield`, komponen) |
| Layout utama | `resources/views/layouts/app.blade.php` |
| Halaman auth | `resources/views/auth/login.blade.php` |
| Portal siswa | `resources/views/portal/*` |
| Struktur view | 11 folder: auth, components, dashboard, diagram, kelas, laporan, master, pembayaran, portal, siswa, layouts |
| CSS | Bootstrap CDN + CSS kustom inline di layout |
| JavaScript | Bootstrap JS, SweetAlert2, `public/js/spa-engine.js` (26 KB), inline script per halaman |
| Responsif | Grid Bootstrap, sidebar collapse, tabel responsif |

Kekurangan yang dicatat: belum ada Subresource Integrity (SRI) pada CDN, belum ada Content
Security Policy (CSP), dan aset belum dibundel Vite. Ini aman untuk demo, tetapi menjadi
temuan untuk produksi (§9).

### 6.4 Struktur Kode

```
app/
├── Console/
│   ├── Kernel.php
│   └── Commands/DummyGeneratorCommand.php
├── Exceptions/Handler.php
├── Http/
│   ├── Controllers/          (15 controller)
│   ├── Middleware/           (SecurityHeaders, CheckRole, CheckSiswaSession, dll.)
│   └── Kernel.php
├── Models/                   (16 model)
└── Services/                 (MidtransService, DummyDataGeneratorService)
bootstrap/
config/                       (konfigurasi Laravel standar)
database/
├── migrations/               (15 migration)
└── seeders/
resources/
├── css/app.css
├── js/{app.js, bootstrap.js}
└── views/                    (11 folder)
routes/
└── web.php                   (83 route)
public/
└── js/spa-engine.js
tests/
├── Feature/ExampleTest.php
└── Unit/ExampleTest.php
```

Kekuatan:

1. **MVC konsisten** — controller tidak berisi query mentah; model menangani relasi dan scope.
2. **Service layer** — logika Midtrans dan generator data dummy dipisahkan dari controller.
3. **Middleware khusus** — `CheckRole`, `CheckSiswaSession`, `SecurityHeaders`.
4. **Scope Eloquent** — `scopeFilterKanal`, `scopeAktif`, dan accessor kanal pembayaran.
5. **Migration & seeder** — struktur database dapat dibangun ulang.
6. **Route terpusat** — 83 route dengan pengelompokan middleware yang jelas.

Catatan struktur (belum diperbaiki):

- Controller terbesar: `DashboardController` 379 baris, `MasterDataController` 377 baris,
  `PortalSiswaController` 365 baris. Belum dipecah ke Form Request/Service.
- Belum ada `app/Http/Requests`, `app/Policies`, atau `app/ViewModels`.
- Model legacy yang hampir tidak dipakai: `RiwayatKelas` (0 referensi),
  `RiwayatKenaikanKelas` (0), `DetailPembayaran` (1), `LogPaymentGateway` (1),
  `TransaksiPembayaran` (3), `Petugas` (7 referensi tetapi masih ada `User`).
- Hanya ada 2 test contoh bawaan Laravel; belum ada test fitur untuk auth, role, atau pembayaran.

### 6.5 Validasi, Autentikasi, Otorisasi, Keamanan

#### a. Inventaris validasi (30 titik)

| Controller | Jumlah | Cakupan |
|------------|--------|---------|
| `MasterDataController` | 10 | tahun ajaran, tarif SPP, kategori non-budget, pengaturan sekolah/Midtrans, user |
| `PortalSiswaController` | 3 | bayar online, bayar QRIS, ubah PIN |
| `SiswaController` | 3 | simpan, ubah, import CSV |
| `PembayaranSppController` | 3 | simpan pembayaran, siswa per kelas, cek tunggakan |
| `AuthController` | 2 | login petugas, login siswa |
| `AjaxSearchController` | 2 | pencarian siswa, kelas per tahun ajaran |
| `KelasController` | 2 | simpan, ubah |
| `KenaikanKelasController` | 2 | preview, proses |
| `LaporanController` | 1 | filter laporan (dipakai semua laporan & export) |
| `HomeController` | 1 | cek siswa publik |
| `NonBudgetController` | 1 | simpan pembayaran non-budget |

Aturan penting yang sudah ada:

- `exists:` untuk seluruh foreign key (`siswa`, `tahun_ajaran`, `kelas`, `kategori_non_budget`).
- `in:` untuk enum terkontrol (`bulan`, `metode_pembayaran`, `status`, `role`, `tingkat`).
- `max:` untuk seluruh input teks/email/NIS/PIN dan upload 5 MB.
- `mimes:csv,txt` untuk import siswa (sesuai parser CSV yang benar-benar dipakai).
- `after_or_equal`/`date` untuk filter tanggal laporan.
- `confirmed` untuk PIN baru.

#### b. Autentikasi

| Aktor | Kredensial | Mekanisme |
|-------|-----------|-----------|
| Admin / Bendahara / Kepsek | email + password | Guard `web`, `Auth::attempt`, bcrypt |
| Siswa / Orang tua | NIS + PIN Wali | Session `is_siswa`, dicek manual + `Hash::check` |
| Webhook Midtrans | signature SHA-512 | `MidtransService::verifySignature` |

- Login dibatasi **5 percobaan / 60 detik** per kombinasi identitas + IP.
- Sesi diregenerasi setelah login (`session()->regenerate()`).
- Logout memakai `invalidate()` + `regenerateToken()`.
- PIN lama plaintext dimigrasikan otomatis ke bcrypt saat login berhasil.
- Webhook ditolak bila server key kosong atau signature tidak valid.

#### c. Otorisasi

| Middleware | Fungsi | Contoh route |
|------------|--------|--------------|
| `auth` | Wajib login petugas | `/dashboard`, `/siswa`, `/pembayaran/*`, `/laporan/*` |
| `role:admin` | Hanya admin | `/master/*`, hapus transaksi, generator data dummy |
| `role:admin,bendahara` | Admin & bendahara | verifikasi pembayaran QRIS |
| `siswa.session` | Wajib sesi siswa | `/portal/*` |

Matriks hak akses lengkap ada di `04-hak-akses-pengguna.md` §3.

#### d. Keamanan aplikasi

| Kontrol | Status | Implementasi |
|---------|--------|--------------|
| CSRF | ✅ | `VerifyCsrfToken`, seluruh form memakai `@csrf`; hanya webhook Midtrans yang dikecualikan |
| XSS | ✅ | Blade auto-escape; tidak ada `{!! !!}` di view |
| SQL Injection | ✅ | Eloquent/Query Builder; tidak ada `DB::raw`/`whereRaw`/`selectRaw` |
| Password hashing | ✅ | bcrypt, rounds 10 (`config/hashing.php`) |
| PIN hashing | ✅ | bcrypt + fallback transparan untuk data lama |
| Rate limiting login | ✅ | 5 percobaan / 60 detik |
| Rate limiting endpoint publik | ✅ | `throttle:20,1` pada cek siswa publik |
| Session fixation | ✅ | Regenerasi sesi setelah login |
| Cookie | ✅ | `http_only = true`, `same_site = lax`, `secure` mengikuti `SESSION_SECURE_COOKIE` |
| Security headers | ✅ | `SecurityHeaders`: nosniff, SAMEORIGIN, referrer-policy, permissions-policy, HSTS saat HTTPS |
| Mass assignment | ✅ | `$fillable` eksplisit pada seluruh model |
| Upload | ✅ | `file`, `mimes:csv,txt`, `max:5120`; label UI disesuaikan |
| Webhook | ✅ | Signature wajib; server key kosong → tolak |
| Error disclosure | ⚠️ | `APP_DEBUG=true` di `.env` lokal; wajib `false` di produksi |
| CSP / SRI | ⚠️ | Belum ada; aset masih CDN + inline |
| Audit log | ⚠️ | Belum ada untuk hapus/verifikasi transaksi |

---

## 4. Temuan Audit

| ID | Temuan | Tingkat | Status |
|----|--------|---------|--------|
| F-01 | Laravel 10 dan PHP 8.1 sudah EOL, tidak menerima patch keamanan resmi | **Tinggi** | Terbuka — roadmap §10 |
| F-02 | Schema drift: `tanggal_bayar` ada di database live tetapi tidak di migration; `metode_pembayaran` live `varchar(50)`, migration `enum('tunai','midtrans')` | Tinggi | **Diperbaiki** |
| F-03 | Pencarian transaksi memakai kolom `kode_transaksi` yang tidak ada di database (`PembayaranSppController`, `NonBudgetController`) | Tinggi | **Diperbaiki** |
| F-04 | `NonBudgetController@store` menulis kolom tidak ada (`kode_transaksi`, `user_id`, `keterangan`) dan tidak menulis `kategori_non_budget_id`/`petugas_id` | Tinggi | **Diperbaiki** |
| F-05 | Belum ada security headers pada response | Sedang | **Diperbaiki** |
| F-06 | Filter laporan, endpoint AJAX, dan cek siswa publik belum divalidasi | Sedang | **Diperbaiki** |
| F-07 | Form import menerima `.xlsx/.xls`, tetapi parser hanya CSV | Sedang | **Diperbaiki** |
| F-08 | `env()` dipanggil saat runtime di `MidtransService` (gagal saat `config:cache`) | Sedang | **Diperbaiki** |
| F-09 | Webhook melewati verifikasi signature bila server key kosong | **Tinggi** | **Diperbaiki** |
| F-10 | Controller gemuk, belum ada Form Request/Policy | Sedang | Terbuka |
| F-11 | Test hanya contoh bawaan Laravel | Sedang | Terbuka |
| F-12 | Model legacy tidak terpakai | Rendah | Terbuka |
| F-13 | CDN tanpa SRI, JS/CSS inline, belum ada CSP, Vite belum dipakai | Sedang | Terbuka |
| F-14 | `APP_DEBUG=true` dan kredensial demo | Sedang | Dimigrasi: demo hanya tampil di `local`; produksi wajib `APP_DEBUG=false` |
| F-15 | Endpoint cek siswa publik memungkinkan enumerasi NIS | Rendah | Dimigrasi: validasi + throttle 20/menit; tetap publik by design |
| F-16 | Route closure di `web.php` | Rendah | **Diperbaiki** — dipindah ke `HomeController` |

---

## 5. Perubahan yang Diterapkan

### 5.0 Berkas yang Diubah/Ditambah

| Berkas | Perubahan |
|--------|-----------|
| `app/Http/Middleware/SecurityHeaders.php` | **Baru** — header keamanan dasar |
| `app/Http/Kernel.php` | Daftarkan `SecurityHeaders` di akhir grup `web` |
| `app/Http/Controllers/HomeController.php` | **Baru** — landing page + cek siswa publik |
| `routes/web.php` | Closure dipindah ke `HomeController`; throttle endpoint publik; validasi NIS |
| `app/Http/Controllers/PembayaranSppController.php` | Perbaikan pencarian; validasi `getSiswaByKelas` & `cekTunggakan` |
| `app/Http/Controllers/NonBudgetController.php` | Perbaikan pencarian & pemetaan kolom saat simpan |
| `app/Http/Controllers/LaporanController.php` | `validateReportFilters()` untuk semua laporan & export |
| `app/Http/Controllers/AjaxSearchController.php` | Validasi `q` dan `tahun_ajaran_id` |
| `app/Http/Controllers/SiswaController.php` | Upload hanya `csv,txt`, maksimal 5 MB |
| `app/Services/MidtransService.php` | `config('midtrans.*')` menggantikan `env()` |
| `app/Http/Controllers/MidtransController.php` | Tolak webhook bila server key kosong |
| `app/Models/PembayaranSpp.php` | `tanggal_bayar` masuk `$fillable`; accessor membaca kolom asli |
| `app/Models/PembayaranNonBudget.php` | Sama seperti di atas |
| `database/migrations/2026_11_12_000000_add_tanggal_bayar_to_payment_tables.php` | **Baru** — guard `Schema::hasColumn` |
| `database/migrations/2026_10_29_170240_create_pembayaran_spp_table.php` | Tambah `tanggal_bayar`; `metode_pembayaran` menjadi `string(50)` |
| `database/migrations/2026_10_29_170260_create_pembayaran_non_budget_table.php` | Sama seperti di atas |
| `resources/views/siswa/index.blade.php` | Label import menjadi CSV, `accept=".csv,.txt"` |

### 5.1 Perbaikan bug runtime

1. **Pencarian transaksi** — `where('kode_transaksi', ...)` diganti menjadi
   `where('midtrans_order_id', ...)` + `orWhere('id', ...)` untuk input numerik.
   Kode tampilan tetap memakai accessor `kode_transaksi` (tidak ada perubahan UI).
2. **Simpan pembayaran non-budget** — kolom disesuaikan dengan skema live:
   `kategori_non_budget_id`, `midtrans_order_id`, `petugas_id`, `catatan`,
   `tanggal_bayar`.
3. **`tanggal_bayar`** — accessor kini mengembalikan nilai kolom asli, dan fallback ke
   `created_at` hanya bila kolom kosong. Sebelumnya semua tampilan/laporan memakai
   `created_at`, sehingga tanggal pembayaran manual tidak pernah muncul.
4. **Migration drift** — migration baru memakai guard agar aman dijalankan pada database
   yang sudah memiliki kolom; migration awal diselaraskan untuk instalasi baru.

### 5.2 Pengerasan keamanan

1. **Security headers** pada seluruh response web:
   `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`,
   `Permissions-Policy`, `X-Permitted-Cross-Domain-Policies`, dan HSTS saat HTTPS.
   CSP sengaja belum diaktifkan karena masih banyak inline script/CDN.
2. **Validasi filter laporan** — mencegah `tahun_ajaran_id`, `kelas_id`, `kategori_id`,
   `tanggal_*`, `metode`, dan `sampai_bulan` berisi nilai sembarang.
3. **Validasi AJAX** — pencarian siswa, kelas per tahun ajaran, siswa per kelas, dan cek
   tunggakan kini memakai `exists`/`integer`/`max`.
4. **Endpoint publik** — `/api/public/cek-siswa` divalidasi dan dibatasi 20 request/menit.
5. **Upload** — hanya CSV/TXT yang diterima, sesuai kemampuan parser.
6. **Midtrans** — konfigurasi dibaca dari config (bukan `env()` runtime) dan webhook
   ditolak bila server key belum diisi.
7. **Struktur** — route closure dipindah ke `HomeController`, sehingga `web.php` hanya
   berisi definisi route dan seluruh logika berada di controller.

---

## 6. Hasil Uji

### 6.1 Pemeriksaan statis

| Perintah | Hasil |
|----------|-------|
| `php -l` pada seluruh berkas yang diubah | ✅ Tidak ada syntax error |
| `php artisan route:list` | ✅ 83 route terbaca |
| `php artisan route:cache` lalu `route:clear` | ✅ Berhasil |
| `php artisan view:cache` lalu `view:clear` | ✅ Berhasil |
| `php artisan migrate --force` | ✅ Migration `add_tanggal_bayar` berjalan (guard tidak mengubah kolom yang sudah ada) |
| `php artisan migrate:fresh --force` pada database uji terpisah | ✅ Seluruh 15 migration berjalan dari nol; kolom `tanggal_bayar` ada di kedua tabel (database uji dihapus setelah pengujian) |

### 6.2 Smoke test HTTP

Server: `php artisan serve` (PHP 8.1.10, MySQL 8.0.30).

| Uji | Hasil |
|-----|-------|
| `GET /` (landing) | 200 |
| `GET /api/public/cek-siswa?nis=1` | 200, JSON `success=false` (data uji tidak ada) |
| Login admin `admin@sekolah.com` | 302 → `/dashboard` |
| `GET /dashboard` | 200 |
| `GET /pembayaran/spp?search=SPP` | 200 |
| `GET /pembayaran/nonbudget?search=NB` | 200 |
| `GET /pembayaran/spp?search=1` dan `nonbudget?search=1` | 200 |
| `GET /laporan/spp`, `/laporan/tunggakan`, `/laporan/nonbudget` | 200 |
| `GET /master/users` sebagai admin | 200 |
| Login bendahara, `GET /master/users` | **403** (otorisasi role bekerja) |
| `GET /api/siswa-by-kelas?kelas_id=99999` | 302 (redirect validasi, bukan error 500) |
| `POST /pembayaran/nonbudget` | 302; record tersimpan dengan `kategori_non_budget_id`, `petugas_id`, `tanggal_bayar`, `midtrans_order_id` benar |
| Accessor `tanggal_bayar` | Mengembalikan tanggal kolom asli, bukan `created_at` |
| Data uji | Record SPP dan non-budget uji dihapus kembali; server dihentikan |

---

## 7. Kepatuhan Terhadap Frasa “Versi Stabil Terbaru”

| Tafsiran | Penilaian |
|----------|-----------|
| “Framework stabil, bukan beta/alpha” | ✅ Laravel 10.50.4 adalah rilis stabil |
| “Rilis minor/patch terakhir pada jalur 10.x” | ✅ 10.50.4 adalah patch terakhir Laravel 10 |
| “Major version terbaru yang masih didukung” | ❌ Laravel 13.42.0; Laravel 10 sudah EOL |
| “PHP yang masih didukung” | ❌ PHP 8.1 sudah EOL; Laravel 13 membutuhkan PHP ≥ 8.3 |

Untuk keperluan sidang, pernyataan yang jujur dan dapat dipertanggungjawabkan:

> Aplikasi dikembangkan dengan Laravel 10.50.4, rilis stabil terakhir pada jalur 10.x,
> dan diuji pada PHP 8.1.10 serta MySQL 8.0.30. Karena Laravel 10 dan PHP 8.1 telah
> melewati masa dukungan keamanan, telah disusun roadmap upgrade ke Laravel 13/PHP 8.3+
> beserta langkah dan risikonya.

Jika penguji mensyaratkan secara literal, upgrade dapat dilakukan **setelah** demo/sidang
mengikuti roadmap berikut.

---

## 8. Rekomendasi Lanjutan (belum diterapkan)

| Prioritas | Rekomendasi | Dampak |
|-----------|-------------|--------|
| Tinggi | Upgrade PHP 8.1 → 8.3/8.4, lalu Laravel 10 → 11 → 12 → 13 | Keamanan & dukungan jangka panjang |
| Tinggi | Set `APP_DEBUG=false`, `APP_ENV=production`, HTTPS, `SESSION_SECURE_COOKIE=true` di server produksi | Mencegah kebocoran stack trace & cookie |
| Tinggi | Tambahkan test fitur: login, role, QRIS pending, verifikasi, pencarian, import | Mencegah regresi |
| Sedang | Pecah controller gemuk menjadi Form Request + Service/Action | Keterbacaan & kemudahan pengembangan |
| Sedang | Tambahkan Policy/Gate untuk aksi tulis (kepsek read-only) | Otorisasi lebih eksplisit |
| Sedang | Bundel aset dengan Vite, aktifkan SRI, lalu CSP bertahap | Keamanan frontend |
| Sedang | Hapus/arsipkan model legacy yang tidak dipakai | Mengurangi kebingungan |
| Rendah | Tambahkan audit log untuk hapus/verifikasi transaksi | Akuntabilitas |
| Rendah | Samarkan data pada endpoint cek siswa publik atau pindahkan ke area login | Mengurangi enumerasi NIS |

---

## 9. Roadmap Upgrade Laravel 10 → 13

> **Status: belum dijalankan.** Semua perubahan di bawah ini bersifat rencana.

### Fase 0 — Persiapan (0,5 hari)

1. Backup database dan seluruh folder aplikasi.
2. Buat repository Git (saat ini belum ada) dan commit kondisi stabil.
3. Siapkan branch `upgrade/laravel-13`.
4. Jalankan baseline test/smoke test dan catat hasilnya.

### Fase 1 — PHP 8.1 → 8.3/8.4 (0,5–1 hari)

1. Pasang PHP 8.3 atau 8.4 beserta ekstensi: `pdo_mysql`, `mbstring`, `openssl`,
   `fileinfo`, `curl`, `zip`, `intl`.
2. Ubah `composer.json`: `"php": "^8.3"`.
3. Jalankan `composer update --with-all-dependencies` dan perbaiki deprecation.
4. Uji ulang seluruh halaman dan proses pembayaran.

### Fase 2 — Laravel 10 → 11 → 12 → 13 (2–4 hari)

| Tahap | Fokus |
|-------|-------|
| 10 → 11 | Struktur `bootstrap/app.php`, middleware di `app.php`, `casts()` method, `routes/console.php`, pembaruan Sanctum |
| 11 → 12 | Pembaruan dependensi, penyesuaian default framework, cek breaking changes |
| 12 → 13 | PHP ≥ 8.3, penyesuaian default baru, pembaruan seluruh first-party package |

Langkah per tahap:

1. Ubah constraint `laravel/framework` ke versi target.
2. `composer update --with-all-dependencies`.
3. `php artisan upgrade`/baca upgrade guide resmi; perbaiki API yang berubah.
4. `php artisan route:list`, `view:cache`, dan test fitur.
5. Baru lanjut ke tahap berikutnya — **jangan lompat langsung 10 → 13**.

### Fase 3 — Frontend & aset (1–2 hari)

1. Tambahkan `vite.config.js` dan `@vite` di layout.
2. Pindahkan Bootstrap, Font Awesome, SweetAlert2, dan font ke dependency lokal/npm.
3. Aktifkan SRI bila tetap memakai CDN.
4. Aktifkan CSP bertahap setelah inline script dihapus.

### Fase 4 — Test & CI (2–3 hari)

1. Test fitur: login petugas/siswa, role 403, QRIS pending, verifikasi, pencarian,
   import CSV, filter laporan.
2. Gunakan database test terpisah + `RefreshDatabase`.
3. Jalankan `php artisan test` pada CI.

**Estimasi total:** 5–9 hari kerja, dengan risiko utama pada Fase 2 (perubahan struktur
middleware Laravel 11 dan pembaruan Sanctum).

---

## 10. Checklist Pengujian Blackbox (Requirement #6)

| No | Skenario | Hasil diharapkan |
|----|----------|------------------|
| 1 | Buka landing page | 200, data sekolah/tarif tampil |
| 2 | Cek siswa dengan NIS valid | JSON data siswa |
| 3 | Cek siswa dengan NIS kosong/terlalu panjang | Ditolak validasi |
| 4 | Spam cek siswa > 20 kali/menit | HTTP 429 |
| 5 | Login admin | Masuk dashboard |
| 6 | Login bendahara lalu buka `/master/users` | 403 |
| 7 | Akses `/dashboard` tanpa login | Redirect ke login |
| 8 | Akses `/portal` tanpa sesi siswa | Redirect ke login |
| 9 | Simpan pembayaran SPP | Tersimpan, `tanggal_bayar` terisi |
| 10 | Cari transaksi berdasarkan order ID/nomor | Hasil muncul, tidak error |
| 11 | Simpan pembayaran non-budget | Tersimpan dengan kategori & petugas benar |
| 12 | Filter laporan dengan `tahun_ajaran_id=abc` | Ditolak validasi |
| 13 | Import file `.xlsx` | Ditolak; hanya CSV/TXT |
| 14 | Import CSV valid | Data siswa masuk |
| 15 | Webhook Midtrans tanpa signature | 403 |
| 16 | Cek response header | `X-Content-Type-Options`, `X-Frame-Options`, dll. ada |
| 17 | `php artisan route:cache` | Berhasil |
| 18 | `php artisan view:cache` | Berhasil |

---

## 11. Kesimpulan

Requirement **Teknologi Pengembangan** terpenuhi secara fungsional: backend Laravel,
database MySQL, frontend Blade/Bootstrap/JavaScript, struktur MVC yang konsisten, serta
validasi, autentikasi, otorisasi, dan keamanan yang tersedia di seluruh alur utama.

Iterasi ini menemukan dan memperbaiki empat bug berdampak tinggi (pencarian kolom tidak ada,
simpan non-budget yang salah kolom, schema drift `tanggal_bayar`, dan webhook tanpa server key),
serta menambahkan security headers, validasi endpoint, pembatasan upload, dan pemindahan route
closure ke controller. Satu ketidaksesuaian yang tersisa bersifat versi: Laravel 10/PHP 8.1
sudah EOL. Roadmap upgrade ke Laravel 13/PHP 8.3+ telah disusun dan siap dijalankan setelah
demo sidang.
