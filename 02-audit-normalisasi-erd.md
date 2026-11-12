# Audit Normalisasi ERD — Sistem Informasi Telaga Rizqy 21

> Basis analisis: foto ERD + daftar kolom pada halaman perancangan (WhatsApp Image
> 2026-11-12 21.04.00, 21.04.00 (1), 21.04.00 (2)). Sebagian nama kolom terbaca
> tidak sempurna dari OCR, jadi struktur di bawah adalah rekonstruksi terbaik.
> Kalau ada kolom yang berbeda di dokumen asli, kirim screenshot yang lebih tajam.

---

## 1. Rekonstruksi Struktur Tabel

Database: `telaganzqy21`

### `tb_outlet`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_outlet | INT(11) | PK |
| nama_outlet | VARCHAR(100) | |
| no_telp | VARCHAR(20) | |
| alamat_outlet | TEXT | |

### `tb_user`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_user | INT(11) | PK |
| nama_user | VARCHAR(100) | |
| username | VARCHAR(100) | belum tentu UNIQUE |
| password | VARCHAR(255) | |
| jabatan | ENUM('administrator','kasir') | |
| id_outlet | INT(11) | FK → tb_outlet |

### `tb_menu`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_menu | INT(11) | PK |
| nama_menu | VARCHAR(100) | |
| harga_menu | INT(11) | |
| id_outlet | INT(11) | FK → tb_outlet |

### `tb_transaksi`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_transaksi | INT(11) | PK |
| kode_invoice | VARCHAR(100) | juga ada di tb_pembayaran |
| kuantitas | INT(11) | jumlah item |
| status_bayar | (enum/varchar) | status pembayaran |
| tgl_transaksi | INT(11) | tanggal disimpan sebagai integer |
| keterangan | TEXT | |
| id_menu | INT(11) | FK → tb_menu (1 menu per transaksi) |
| id_user | INT(11) | FK → tb_user |
| id_outlet | INT(11) | FK → tb_outlet (redundan) |

### `tb_pembayaran`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_pembayaran | INT(11) | PK |
| id_transaksi | INT(11) | FK → tb_transaksi |
| total_pembayaran | INT(11) | nilai total |
| jumlah_uang_dibayar | INT(11) | uang diterima |
| kembalian | INT(11) | hasil hitung |
| tgl_pembayaran | INT(11) | tanggal sebagai integer |
| kode_invoice | VARCHAR(100) | duplikat dari tb_transaksi |
| id_user | INT(11) | FK → tb_user |
| id_outlet | INT(11) | FK → tb_outlet (redundan) |

### `tb_pengeluaran`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_pengeluaran | INT(11) | PK |
| jumlah_pengeluaran | INT(11) | |
| keterangan_pengeluaran | TEXT | |
| tanggal_pengeluaran | INT(11) | tanggal sebagai integer |
| id_user | INT(11) | FK → tb_user |

### `tb_log`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id_log | INT(11) | PK |
| keterangan | TEXT | |
| isi_log | TEXT | |
| tanggal_log | INT(11) | tanggal sebagai integer |
| id_user | INT(11) | FK → tb_user |

### Relasi
```
tb_outlet 1 ── N tb_user        (user milik satu outlet)
tb_outlet 1 ── N tb_menu        (menu milik satu outlet)
tb_menu   1 ── N tb_transaksi   (satu transaksi = satu menu)
tb_transaksi 1 ── 1/N tb_pembayaran
tb_user   1 ── N tb_transaksi
tb_user   1 ── N tb_pembayaran
tb_user   1 ── N tb_pengeluaran
tb_user   1 ── N tb_log
```

---

## 2. Uji 1NF (First Normal Form)

Syarat: nilai tiap kolom **atomik**, tidak ada **repeating group**, tiap baris unik.

| No | Temuan | Status |
|---|---|---|
| 1 | Semua tabel punya PK dan kolom skalar | ✅ |
| 2 | `tb_transaksi` mencampur **header transaksi** dan **satu item** (`id_menu`, `kuantitas`) | ⚠️ **Belum 1NF** untuk kasus satu nota berisi banyak menu. Ini "repeating group yang diratakan". |
| 3 | `kode_invoice` disimpan di **dua tabel** (`tb_transaksi` & `tb_pembayaran`) | ⚠️ Bukan pelanggaran atomik, tapi duplikasi nilai (redundansi). |
| 4 | Tanggal disimpan `INT(11)` (`tgl_transaksi`, `tgl_pembayaran`, `tanggal_pengeluaran`, `tanggal_log`) | ⚠️ Domain/tipe data salah. Bukan pelanggaran 1NF murni, tapi bikin parsing & query rawan salah. Pakai `DATE`/`DATETIME`. |
| 5 | `tb_log.isi_log` / `keterangan` berupa TEXT bebas | ⚠️ Berpotensi menyimpan banyak nilai sekaligus (JSON/serialized). Kalau di dalamnya ada beberapa atribut, melanggar atomik. |
| 6 | `jabatan` ENUM, `status_bayar` | ✅ atomik |

**Kesimpulan 1NF:** secara teknis sebagian besar lolos, **tapi model transaksi belum 1NF**
karena belum memisahkan item transaksi (detail). Untuk sistem POS, ini yang paling penting.

---

## 3. Uji 2NF (Second Normal Form)

Syarat: sudah 1NF **dan** tidak ada **ketergantungan parsial** pada kunci komposit.

| No | Temuan | Status |
|---|---|---|
| 1 | Semua tabel memakai **PK tunggal** (`id_...` auto increment) | ✅ formal 2NF |
| 2 | `tb_transaksi` memakai `id_transaksi` sebagai PK, tetapi `id_menu` + `kuantitas` adalah atribut item. Kalau kunci alaminya `(id_transaksi, id_menu)`, maka `id_outlet` hanya bergantung pada `id_menu` → **parsial** | ⚠️ |
| 3 | `tb_pembayaran` menyimpan `kode_invoice` yang hanya bergantung pada `id_transaksi`, bukan pada `id_pembayaran` | ⚠️ redundan (mirip partial dependency) |
| 4 | `tb_menu.harga_menu` bergantung penuh pada `id_menu` | ✅ |

**Kesimpulan 2NF:** lolos secara formal karena semua tabel pakai surrogate key,
**tetapi** desainnya rapuh: begitu kunci natural dipakai, muncul ketergantungan parsial.
Solusinya tetap: pisahkan `tb_detail_transaksi`.

---

## 4. Uji 3NF (Third Normal Form)

Syarat: sudah 2NF **dan** tidak ada **ketergantungan transitif** (atribut non-kunci
hanya boleh bergantung pada kunci primer, bukan pada atribut non-kunci lain).

### Pelanggaran yang ditemukan

| No | Kolom | Ketergantungan transitif | Dampak | Perbaikan |
|---|---|---|---|---|
| 1 | `tb_transaksi.id_outlet` | `id_transaksi → id_user → id_outlet` dan `id_transaksi → id_menu → id_outlet` | Outlet bisa beda antara transaksi, menu, dan user | **Hapus** `id_outlet` dari transaksi; ambil lewat JOIN |
| 2 | `tb_pembayaran.id_outlet` | `id_pembayaran → id_transaksi → id_outlet` | Sama seperti di atas | **Hapus** |
| 3 | `tb_pembayaran.kode_invoice` | `id_pembayaran → id_transaksi → kode_invoice` | Invoice ganda/tidak sinkron | **Hapus**; JOIN ke transaksi |
| 4 | `tb_pembayaran.kembalian` | `kembalian = jumlah_uang_dibayar − total_pembayaran` | Nilai turunan bisa salah | Hitung di aplikasi / generated column, jangan input manual |
| 5 | `tb_pembayaran.total_pembayaran` | turunan dari `Σ(kuantitas × harga_menu)` | Total bisa tidak cocok dengan detail | Simpan hanya di level invoice sebagai snapshot (didokumentasikan) atau hitung dari detail |
| 6 | `tb_transaksi.status_bayar` | turunan dari ada/tidaknya `tb_pembayaran` dan statusnya | Status bisa dobel & tidak konsisten | Simpan status di **satu tempat saja** (di transaksi) dan update dari pembayaran, atau sebaliknya |
| 7 | `tb_menu.harga_menu` dipakai langsung untuk transaksi lama | Harga berubah → nota lama ikut berubah | Bukan transitif murni, tapi **temporal integrity** | Simpan `harga_satuan` snapshot di `tb_detail_transaksi` |
| 8 | `tb_pengeluaran` tidak punya `id_outlet` | Pengeluaran tidak bisa diatribusikan per outlet | Laporan per outlet tidak akurat | Tambah `id_outlet` (jika multi-outlet) |

### Yang sudah benar
- `tb_outlet`, `tb_user`, `tb_menu`, `tb_log` tidak menyimpan nama/atribut entitas lain
  secara berulang (selain FK), jadi **3NF-nya cukup baik**.
- `tb_user.id_outlet` **bukan** pelanggaran selama satu user memang milik satu outlet.
  Kalau user bisa bertugas di banyak outlet, baru perlu tabel relasi many-to-many.

---

## 5. Kesimpulan Audit

| Bentuk Normal | Hasil | Catatan |
|---|---|---|
| **1NF** | ⚠️ Lolos sebagian | Item transaksi belum dipisah; tanggal pakai INT; isi_log berpotensi tidak atomik |
| **2NF** | ✅ Lolos formal | Karena semua PK surrogate, tapi ada ketergantungan parsial tersembunyi |
| **3NF** | ❌ **Belum lolos** | 6–8 atribut redundan/turunan (id_outlet, kode_invoice, kembalian, total, status_bayar, harga snapshot) |

**Verdict:** ERD Telaga Rizqy 21 **belum sepenuhnya 3NF**. Untuk kebutuhan TA tahun 2024
mungkin dianggap cukup, tetapi kalau dipakai sebagai basis project baru, sebaiknya
dirapikan dulu — terutama pemisahan **header–detail transaksi** dan penghapusan
kolom yang bisa di-JOIN.

---

## 6. Usulan ERD Perbaikan (3NF)

```
outlet (id_outlet, nama_outlet, no_telp, alamat_outlet)
   │
   ├── user (id_user, id_outlet, nama_user, username, password, jabatan)
   ├── menu (id_menu, id_outlet, nama_menu, harga_menu)
   │
   └── (opsional) pengeluaran.id_outlet

user ── transaksi (id_transaksi, kode_invoice, id_user, tgl_transaksi, status_bayar, keterangan)
             │
             ├── detail_transaksi (id_detail, id_transaksi, id_menu, kuantitas, harga_satuan)
             └── pembayaran (id_pembayaran, id_transaksi, metode, jumlah_bayar, tgl_bayar, id_user)

user ── pengeluaran (id_pengeluaran, id_outlet, id_user, jumlah, keterangan, tanggal)
user ── log (id_log, id_user, aksi, isi_log, tanggal_log)
```

### DDL MariaDB (contoh)

```sql
CREATE TABLE outlet (
  id_outlet     INT AUTO_INCREMENT PRIMARY KEY,
  nama_outlet   VARCHAR(100) NOT NULL,
  no_telp       VARCHAR(20)  NULL,
  alamat_outlet TEXT         NULL
) ENGINE=InnoDB;

CREATE TABLE user (
  id_user   INT AUTO_INCREMENT PRIMARY KEY,
  id_outlet INT NOT NULL,
  nama_user VARCHAR(100) NOT NULL,
  username  VARCHAR(100) NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL,
  jabatan   ENUM('administrator','kasir') NOT NULL DEFAULT 'kasir',
  CONSTRAINT fk_user_outlet FOREIGN KEY (id_outlet)
    REFERENCES outlet(id_outlet) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE menu (
  id_menu    INT AUTO_INCREMENT PRIMARY KEY,
  id_outlet  INT NOT NULL,
  nama_menu  VARCHAR(100) NOT NULL,
  harga_menu DECIMAL(12,2) NOT NULL,
  UNIQUE KEY uq_menu_outlet (id_outlet, nama_menu),
  CONSTRAINT fk_menu_outlet FOREIGN KEY (id_outlet)
    REFERENCES outlet(id_outlet) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transaksi (
  id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
  kode_invoice VARCHAR(50) NOT NULL UNIQUE,
  id_user      INT NOT NULL,                    -- kasir pembuat
  tgl_transaksi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status_bayar ENUM('belum_bayar','lunas','batal') NOT NULL DEFAULT 'belum_bayar',
  keterangan   TEXT NULL,
  CONSTRAINT fk_trx_user FOREIGN KEY (id_user)
    REFERENCES user(id_user) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE detail_transaksi (
  id_detail    INT AUTO_INCREMENT PRIMARY KEY,
  id_transaksi INT NOT NULL,
  id_menu      INT NOT NULL,
  kuantitas    INT NOT NULL DEFAULT 1,
  harga_satuan DECIMAL(12,2) NOT NULL,           -- snapshot harga saat transaksi
  UNIQUE KEY uq_detail (id_transaksi, id_menu),
  CONSTRAINT fk_detail_trx  FOREIGN KEY (id_transaksi)
    REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
  CONSTRAINT fk_detail_menu FOREIGN KEY (id_menu)
    REFERENCES menu(id_menu) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pembayaran (
  id_pembayaran    INT AUTO_INCREMENT PRIMARY KEY,
  id_transaksi     INT NOT NULL UNIQUE,          -- 1 transaksi = 1 pembayaran
  metode           ENUM('tunai','transfer','qris') NOT NULL DEFAULT 'tunai',
  jumlah_bayar     DECIMAL(12,2) NOT NULL,
  tgl_bayar        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_user          INT NOT NULL,                 -- kasir penerima
  CONSTRAINT fk_bayar_trx  FOREIGN KEY (id_transaksi)
    REFERENCES transaksi(id_transaksi) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_bayar_user FOREIGN KEY (id_user)
    REFERENCES user(id_user) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pengeluaran (
  id_pengeluaran INT AUTO_INCREMENT PRIMARY KEY,
  id_outlet      INT NOT NULL,
  id_user        INT NOT NULL,
  jumlah         DECIMAL(12,2) NOT NULL,
  keterangan     TEXT NULL,
  tanggal        DATE NOT NULL,
  CONSTRAINT fk_peng_outlet FOREIGN KEY (id_outlet) REFERENCES outlet(id_outlet),
  CONSTRAINT fk_peng_user   FOREIGN KEY (id_user)   REFERENCES user(id_user)
) ENGINE=InnoDB;

CREATE TABLE log (
  id_log    INT AUTO_INCREMENT PRIMARY KEY,
  id_user   INT NOT NULL,
  aksi      VARCHAR(100) NOT NULL,
  isi_log   TEXT NULL,
  tanggal_log DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_log_user FOREIGN KEY (id_user) REFERENCES user(id_user)
) ENGINE=InnoDB;
```

### Query turunan (tidak perlu disimpan sebagai kolom)
```sql
-- total per transaksi
SELECT d.id_transaksi, SUM(d.kuantitas * d.harga_satuan) AS total
FROM detail_transaksi d
GROUP BY d.id_transaksi;

-- kembalian
-- kembalian = pembayaran.jumlah_bayar - total_transaksi

-- outlet sebuah transaksi
SELECT t.id_transaksi, m.id_outlet
FROM transaksi t
JOIN detail_transaksi d ON d.id_transaksi = t.id_transaksi
JOIN menu m ON m.id_menu = d.id_menu;
```

---

## 7. Checklist Perbaikan Cepat

- [ ] Buat tabel `detail_transaksi` (header–detail).
- [ ] Hapus `tb_transaksi.id_outlet` dan `tb_pembayaran.id_outlet`.
- [ ] Hapus `tb_pembayaran.kode_invoice`.
- [ ] Hapus `kembalian` (hitung di aplikasi).
- [ ] Tentukan status bayar di satu tabel saja.
- [ ] Ganti `INT(11)` tanggal → `DATE`/`DATETIME`.
- [ ] Tambah `UNIQUE(username)`, `UNIQUE(kode_invoice)`, `UNIQUE(id_transaksi)` di pembayaran.
- [ ] Simpan `harga_satuan` di detail transaksi.
- [ ] (Kalau multi-outlet) tambah `id_outlet` di pengeluaran.
- [ ] Dokumentasikan kolom yang sengaja didenormalisasi (mis. total invoice) supaya tidak dianggap salah.
