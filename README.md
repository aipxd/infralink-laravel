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

## 👥 Akun Bawaan (Default Seeder)

Semua akun menggunakan kata sandi: `password123`

| Peran | Email | Keterangan |
| --- | --- | --- |
| **ADMIN** | `admin@infralink.ac.id` | Akses penuh: dashboard, kelola fasilitas, kategori, blokir jadwal, kelola pengguna & peran |
| **APPROVER** | `approver@infralink.ac.id` | Peninjau peminjaman: antrean pengajuan, setujui/tolak dengan catatan |
| **PEMOHON** | `mahasiswa@infralink.ac.id` | Pengguna: mengajukan peminjaman, memantau riwayat di "Peminjaman Saya", membatalkan |
| **PEMOHON 2** | `hima@infralink.ac.id` | Akun ormawa HIMASISFO |

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Jalankan Server Pengembangan Laravel

Buka terminal di folder project ini (`d:\Coding\Infralink PHP`), lalu jalankan:

```bash
php artisan serve
```

Aplikasi dapat langsung diakses di browser pada:  
👉 **`http://127.0.0.1:8000`**

### 2. Reset / Migrasi & Seed Ulang (Opsional)

Jika ingin me-reset database kembali ke kondisi awal contoh:

```bash
php artisan migrate:fresh --seed
```

---

## 🗄️ Pilihan Database (SQLite & MySQL)

### Default: SQLite (Siap Pakai Tanpa Setup)
Aplikasi sudah terkonfigurasi dengan SQLite (`database/database.sqlite`). Anda tidak perlu menyalakan service database eksternal apa pun.

### Opsi: Menggunakan MySQL (misalnya XAMPP MySQL)
Jika ingin menggunakan MySQL:
1. Pastikan service MySQL di XAMPP telah berjalan (Port 3306).
2. Buat database baru bernama `infralink` di phpMyAdmin atau terminal MySQL:
   ```sql
   CREATE DATABASE infralink;
   ```
3. Buka file `.env` dan sesuaikan:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=infralink
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```

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
Infralink PHP/
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
│   ├── seeders/                         # Seeder akun, kategori, fasilitas, & booking
│   └── database.sqlite
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
