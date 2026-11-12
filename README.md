# Sistem Pengolahan Data Transaksi Keuangan Berbasis Web pada Telaga Rizqy 21

Kumpulan catatan dari TA "Sistem Informasi Pengolahan Data Transaksi Keuangan
Berbasis Web pada Telaga Rizqy 21" (Alda Nadia Putri, 2024).

| File | Isi |
|---|---|
| `01-catatan-bab-1-telaga-rizqy-21.md` | Ringkasan Bab I, teknologi, rancangan sistem, ide fitur project baru. |
| `02-audit-normalisasi-erd.md` | Audit singkat ERD lama + ERD perbaikan. |
| `03-analisis-database.md` | **Dokumen analisis database formal**: PK, FK, 1NF–3NF, redundansi, anomali, kekurangan, perbaikan. |
| `04-hak-akses-pengguna.md` | **Requirement #5 Hak Akses Pengguna**: audit login/autentikasi, role admin/kasir, pembayaran online, temuan keamanan, dan perbaikan yang diterapkan. |
| `05-teknologi-pengembangan.md` | **Requirement #6 Teknologi Pengembangan**: stack aktual, struktur kode, validasi/autentikasi/otorisasi/keamanan, temuan, perbaikan, dan roadmap upgrade Laravel 10 → 13. |
| `database_telaga_rizqy21_fixed.sql` | DDL MariaDB siap jalan + view + contoh data. |

## Status normalisasi

| Aspek | Sebelum | Sesudah |
|---|---|---|
| 1NF | ❌ header + item menyatu | ✅ |
| 2NF | ⚠️ formal lolos | ✅ |
| 3NF | ❌ ada dependensi transitif | ✅ |
| PK | ✅ | ✅ + UNIQUE |
| FK | ⚠️ logis saja | ✅ fisik |

## Cara pakai SQL

1. Backup database lama.
2. Buka phpMyAdmin → Import → pilih `database_telaga_rizqy21_fixed.sql`.
3. Sesuaikan nama database di bagian atas file jika perlu.
4. Ganti hash password contoh dengan hasil `password_hash()` dari aplikasi.
