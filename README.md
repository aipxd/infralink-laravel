# INFRALINK (PHP & Laravel)

> **Sistem Informasi Manajemen Peminjaman Sarana dan Prasarana Kampus (MVP)**  
> Dibangun menggunakan **PHP 8.2+**, **Laravel 11**, **SQLite / MySQL**, **Tailwind CSS**, dan **FullCalendar**.  
> Sesuai spesifikasi PRD & ERD Infralink v1.1.

---

## 📌 Ringkasan Sistem

Infralink memudahkan mahasiswa, organisasi mahasiswa (ormawa), dan unit kampus dalam meminjam fasilitas kampus (aula, ruang seminar, laboratorium, sarana olahraga) secara transparan dan terstruktur:
- **Katalog Fasilitas**: Pencarian dan filter kategori, gedung, kapasitas, dan ketersediaan waktu.
- **Kalender Interaktif**: Visualisasi slot Disetujui (terisi), Blokir Admin (maintenance/libur), dan Diajukan (antrean). Klik slot kosong langsung membuka form peminjaman dengan data terisi otomatis.
- **Aturan Bisnis Anti-Bentrok**: Validasi overlap transaksional dengan row locking (`lockForUpdate()`), batas jam operasional, durasi maksimal, kapasitas, dan rentang tanggal H+1 s.d 90 hari.
- **Satu Level Persetujuan**: Antrean review untuk Approver & Admin dengan deteksi pengajuan overlap yang bersinggungan.
- **Manajemen Lengkap (Admin)**: CRUD fasilitas, kategori, blokir jadwal, dan kelola peran pengguna.

---

## 🧰 Persiapan (Prasyarat)

Pastikan perangkat berikut sudah terpasang **sebelum** menjalankan proyek ini.

| Kebutuhan            | Versi               | Keterangan                                                                                  | Cek dengan      |
| ---------------------|-------------------- | --------------------------------------------------------------------------------------------|-----------------|
| **PHP**              | 8.2 atau lebih baru | Wajib                                                                                       | `php -v`        |
| **Composer**         | 2.x                 | Wajib, untuk memasang library PHP                                                           | `composer -V`   | 
| **Git**              | Terbaru             | Untuk meng-clone repository (opsional jika mengunduh ZIP)                                   | `git --version` |
| **MySQL**            | 8.x / MariaDB 10.4+ | Opsional, hanya jika tidak memakai SQLite (misalnya lewat XAMPP)                            | -               |
| **Koneksi internet** | -                   | Wajib saat dijalankan, karena Tailwind CSS, FullCalendar, dan Font Awesome dimuat lewat CDN | -               |

> **Node.js / npm tidak diperlukan** untuk menjalankan aplikasi ini.

### Ekstensi PHP yang harus aktif

`mbstring`, `openssl`, `fileinfo`, `curl`, `xml`, `ctype`, `tokenizer`, `zip`, serta `pdo_sqlite` dan `sqlite3` (untuk SQLite) atau `pdo_mysql` (untuk MySQL).

Jika memakai **XAMPP (Windows)**, buka `C:\xampp\php\php.ini`, cari baris berikut, lalu hapus tanda `;` di depannya jika ada:

```ini
extension=fileinfo
extension=zip
extension=pdo_sqlite
extension=sqlite3
extension=pdo_mysql
```

### Panduan pasang untuk pemula (Windows)

1. Pasang **XAMPP** (pilih versi dengan PHP 8.2+): https://www.apachefriends.org
2. Tambahkan `C:\xampp\php` ke **PATH** Windows (cari *Edit the system environment variables* → *Environment Variables* → *Path* → *New*).
3. Pasang **Composer** (`Composer-Setup.exe`): https://getcomposer.org/download/
4. Pasang **Git**: https://git-scm.com/download/win
5. **Tutup lalu buka kembali** PowerShell/terminal agar perintah baru dikenali, lalu cek `php -v`, `composer -V`, dan `git --version`.

---

## 🚀 Cara Menjalankan di Laptop Sendiri

### Langkah 1. Ambil proyek

```bash
git clone https://github.com/aipxd/infralink-laravel.git
cd infralink-laravel
```

Tanpa Git: di halaman repository klik **Code → Download ZIP**, ekstrak, lalu buka terminal di dalam folder hasil ekstrak.

### Langkah 2. Pasang library PHP

```bash
composer install
```

Perintah ini membuat folder `vendor/` (tidak ikut di repository, karena memang dibuat ulang di setiap komputer).

### Langkah 3. Siapkan file konfigurasi `.env`

File `.env` **tidak ikut di repository** (berisi pengaturan pribadi). Buat dari contoh yang tersedia:

```bash
# Windows (PowerShell / CMD)
copy .env.example .env

# macOS / Linux
cp .env.example .env
```

### Langkah 4. Buat kunci aplikasi

```bash
php artisan key:generate
```

Perintah ini mengisi `APP_KEY` di `.env` secara otomatis. Setiap komputer memiliki kunci sendiri dan **kunci ini tidak boleh dibagikan**.

### Langkah 5. Siapkan database

**Opsi A: SQLite (paling mudah, tanpa instalasi database)**

File database SQLite tidak ikut di repository, jadi buat dulu file kosongnya:

```bash
# Windows (PowerShell)
New-Item database/database.sqlite -ItemType File

# macOS / Linux
touch database/database.sqlite
```

Pengaturan bawaan `.env.example` sudah memakai `DB_CONNECTION=sqlite`, tidak perlu diubah.

**Opsi B: MySQL (misalnya XAMPP)**

1. Nyalakan **MySQL** di XAMPP Control Panel (port 3306).
2. Buka http://localhost/phpmyadmin, buat database baru bernama `infralink` (collation `utf8mb4_unicode_ci`).
3. Ubah bagian database di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=infralink
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Pastikan tidak ada tanda `#` di depan baris-baris tersebut. Kosongkan `DB_PASSWORD` hanya jika MySQL lokal Anda memang tanpa kata sandi (bawaan XAMPP). Untuk pemakaian lain, gunakan kata sandi yang kuat.

### Langkah 6. Buat tabel dan isi data contoh

```bash
php artisan migrate --seed
```

Perintah ini membuat seluruh tabel dan mengisi akun, kategori, fasilitas, serta booking contoh.

### Langkah 7. (Opsional) Hubungkan folder upload

```bash
php artisan storage:link
```

### Langkah 8. Jalankan server

```bash
php artisan serve
```

Buka di browser: 👉 **http://127.0.0.1:8000**

Untuk menghentikan server, tekan `Ctrl + C` di terminal.

---

## 👥 Akun Demo (dari Seeder)

Setelah `php artisan migrate --seed`, akun berikut tersedia untuk pengujian lokal:

| Peran | Email | Keterangan |
| --- | --- | --- |
| **ADMIN** | `admin@infralink.ac.id` | Akses penuh: dashboard, kelola fasilitas, kategori, blokir jadwal, kelola pengguna & peran |
| **APPROVER** | `approver@infralink.ac.id` | Peninjau peminjaman: antrean pengajuan, setujui/tolak dengan catatan |
| **PEMOHON** | `mahasiswa@infralink.ac.id` | Pengguna: mengajukan peminjaman, memantau riwayat di "Peminjaman Saya", membatalkan |
| **PEMOHON 2** | `hima@infralink.ac.id` | Akun ormawa HIMASISFO |

Kata sandi akun demo ditentukan di `database/seeders/DatabaseSeeder.php`. Akun ini **hanya untuk pengembangan/demo lokal**. Jangan gunakan data ini pada server yang dapat diakses publik, atau ganti kata sandinya terlebih dahulu.

---

## 🔄 Reset Data

Untuk mengembalikan database ke kondisi awal contoh:

```bash
php artisan migrate:fresh --seed
```

> ⚠️ Perintah ini **menghapus seluruh tabel dan datanya**, lalu membuatnya ulang. Jangan jalankan pada database yang berisi data penting.

---

## 🛠️ Pemecahan Masalah

| Masalah | Penyebab & Solusi |
| --- | --- |
| `'php' / 'composer' / 'git' is not recognized` | Program belum terpasang atau belum masuk PATH. Pasang lalu **buka ulang terminal**. |
| `Your PHP version does not satisfy the requirement` | Versi PHP di bawah 8.2. Perbarui PHP. |
| `The requested PHP extension ext-xxx is missing` | Aktifkan ekstensi tersebut di `php.ini` (hapus tanda `;`), lalu ulangi `composer install`. |
| `No application encryption key has been specified` | Jalankan `php artisan key:generate`. |
| `Database file ... does not exist` | Buat file `database/database.sqlite` (Langkah 5 Opsi A). |
| `Access denied for user` / `Connection refused` / `Unknown database` | Periksa MySQL sudah menyala, nama database sudah dibuat, dan isi `.env` sudah benar. Lalu jalankan `php artisan config:clear`. |
| Tampilan berantakan / kalender tidak muncul | Periksa koneksi internet (aset dimuat dari CDN). |
| Perubahan `.env` tidak terbaca | Jalankan `php artisan config:clear`. |
| Proyek berada di folder OneDrive dan instalasi lambat/gagal | Pindahkan proyek ke folder biasa, misalnya `C:\proyek\infralink-laravel`. |

---

## 🗄️ Pilihan Database (SQLite & MySQL)

- **SQLite (default)**: satu file (`database/database.sqlite`), tanpa server database terpisah. Cocok untuk demo dan pengembangan.
- **MySQL**: cocok untuk penggunaan nyata. Kode aplikasi tidak perlu diubah, cukup mengganti pengaturan di `.env`.

Catatan: penguncian baris `lockForUpdate()` bekerja penuh di MySQL. Pada SQLite, penguncian dilakukan pada seluruh file database.

---

## 📋 Implementasi Aturan Bisnis (PRD Section 6)

| ID Aturan | Implementasi di Sistem |
| --- | --- |
| **BR-01** | Hanya booking berstatus `DISETUJUI` yang memblokir kalender. |
| **BR-02** | Booking berstatus `DIAJUKAN` tampil di kalender sebagai penanda kuning (antrean). |
| **BR-03** | Pengajuan otomatis ditolak jika rentang waktu overlap dengan booking Disetujui atau Blokir Admin pada fasilitas & tanggal yang sama. |
| **BR-04** | Saat Approver menyetujui, sistem memvalidasi ulang overlap di dalam transaksi database dengan row locking (`lockForUpdate()`). |
| **BR-05** | Setiap pengajuan hanya untuk satu fasilitas dan satu rentang waktu pada satu tanggal. |
| **BR-06** | Waktu peminjaman harus berada di dalam jam operasional fasilitas (`open_time` - `close_time`). |
| **BR-07** | Durasi peminjaman tidak melebihi durasi maksimal fasilitas (`max_duration_minutes`). |
| **BR-08** | Jumlah peserta tidak melebihi kapasitas fasilitas. |
| **BR-09** | Tanggal pengajuan minimal H+1 dan maksimal 90 hari ke depan. |
| **BR-10** | Pemohon hanya bisa membatalkan pengajuan berstatus `DIAJUKAN`. |
| **BR-11** | Penolakan wajib disertai catatan alasan; persetujuan boleh disertai catatan. |
| **BR-12** | Approval satu level dan manual. |
| **BR-13** | Status akhir (`DISETUJUI`, `DITOLAK`, `DIBATALKAN`) tidak dapat diubah kembali. |

---

## 🏗️ Struktur Arsitektur

```
infralink-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Login, Register, Profile, Password
│   │   │   ├── FacilityController.php   # Katalog, Filter FR-13, Kalender JSON FR-20..24
│   │   │   ├── BookingController.php    # Form Pengajuan, Peminjaman Saya, Pembatalan
│   │   │   ├── ApprovalController.php   # Antrean Persetujuan & Deteksi Overlap
│   │   │   └── AdminController.php      # Fasilitas, Kategori, Blokir, Kelola Users
│   │   └── Middleware/
│   │       └── EnsureUserRole.php       # Proteksi RBAC (Pemohon, Approver, Admin)
│   ├── Models/
│   │   ├── User.php
│   │   ├── FacilityCategory.php
│   │   ├── Facility.php
│   │   ├── FacilityPhoto.php
│   │   ├── Booking.php
│   │   ├── BookingStatusLog.php
│   │   ├── BlockedSlot.php
│   │   └── Attachment.php
│   └── Services/
│       └── BookingRuleService.php       # Sumber kebenaran validasi bisnis BR-03..BR-09
├── database/
│   ├── migrations/                      # Skema tabel lengkap
│   └── seeders/                         # Seeder akun, kategori, fasilitas, & booking
├── resources/
│   └── views/
│       ├── layouts/app.blade.php        # Layout utama (Tailwind CSS, FullCalendar, Flash alert)
│       ├── auth/                        # Login & Register
│       ├── profile/                     # Pengaturan profil & kata sandi
│       ├── facilities/                  # Katalog & Detail Kalender
│       ├── bookings/                    # Form Pengajuan, Peminjaman Saya, Detail & Riwayat
│       ├── approvals/                   # Antrean & Review Persetujuan
│       └── admin/                       # Dashboard, Fasilitas, Kategori, Blokir, Pengguna
└── routes/
    └── web.php                          # 42 rute web terstruktur
```
