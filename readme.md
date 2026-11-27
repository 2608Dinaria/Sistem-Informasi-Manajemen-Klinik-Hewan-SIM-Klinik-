# Sistem Informasi Manajemen Klinik Hewan (SIM Klinik)

Sistem Informasi Manajemen Klinik Hewan adalah aplikasi berbasis web yang dirancang untuk membantu pengelolaan operasional klinik hewan. Aplikasi ini mencakup manajemen data pasien (hewan), rekam medis, penjadwalan dokter, appointment, serta layanan vaksinasi.

Sistem ini memfasilitasi tiga peran pengguna utama: **Administrator**, **Dokter**, dan **Pemilik Hewan**, masing-masing dengan antarmuka dan fitur yang disesuaikan.

## Fitur Utama

### 1. Administrator
*   **Dashboard Interaktif**: Melihat statistik ringkas (Total Dokter, Pemilik, Pasien, Appointment Pending, dll).
*   **Manajemen Pengguna**: Mengelola akun untuk Admin, Dokter, dan Pemilik hewan.
*   **Manajemen Jadwal Dokter**: Mengatur jadwal praktik dokter dengan sistem kuota (max slot).
*   **Manajemen Vaksinasi**: Mengelola jadwal vaksinasi massal atau reguler.
*   **Laporan**: Melihat laporan pendaftaran dan aktivitas klinik.

### 2. Dokter
*   **Dashboard Dokter**: Akses cepat ke jadwal dan appointment hari ini.
*   **Pemeriksaan Pasien**: Input data pemeriksaan fisik dan keluhan.
*   **Input Rekam Medis**: Mencatat diagnosis, treatment, resep obat, dan catatan medis lainnya untuk setiap pasien.
*   **Manajemen Vaksinasi**: Mencatat pemberian vaksin dan jadwal booster.

### 3. Pemilik Hewan (Pet Owner)
*   **Manajemen Hewan Peliharaan**: Mendaftarkan hewan peliharaan (Nama, Jenis, Ras, dll).
*   **Booking Appointment**: Membuat janji temu dengan dokter berdasarkan jadwal yang tersedia (sistem *First-Come-First-Serve*).
*   **Riwayat Medis**: Melihat riwayat kesehatan dan pengobatan hewan peliharaan.
*   **Pendaftaran Vaksinasi**: Mendaftar untuk jadwal vaksinasi yang tersedia.

## Teknologi yang Digunakan

*   **Bahasa Pemrograman**: PHP (Native/Procedural & OOP Components)
*   **Database**: MySQL
*   **Frontend Framework**: Bootstrap 5
*   **Styling**: Custom CSS dengan desain responsif
*   **Server Environment**: XAMPP (Apache)

## Struktur Folder

```
/lintasdisiplinilmu
├── admin/          # Halaman dan fungsi khusus Administrator
├── dokter/         # Halaman dan fungsi khusus Dokter
├── pemilik/        # Halaman dan fungsi khusus Pemilik Hewan
├── api/            # Endpoint API untuk data JSON (mis: get_jadwal.php)
├── assets/         # File statis (CSS, JS, Images)
├── layouts/        # Komponen template (Header, Sidebar, Footer)
└── ...             # File root (Login, Register, Config)
```

## Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di lokal:

1.  **Clone Repository**
    Download atau clone repository ini ke dalam folder `htdocs` pada instalasi XAMPP Anda.
    ```bash
    git clone https://github.com/username-anda/lintasdisiplinilmu.git
    ```

2.  **Setup Database**
    *   Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
    *   Buat database baru dengan nama `klinik_hewan_dinas1`.
    *   Import file `database.sql` yang terdapat di root folder project.

3.  **Setup Tabel Tambahan (Vaksinasi)**
    Sistem memiliki modul vaksinasi yang membutuhkan tabel tambahan. Jalankan skrip setup dengan membuka URL berikut di browser:
    ```
    http://localhost/lintasdisiplinilmu/setup_vaksinasi_db.php
    ```
    Pastikan muncul pesan sukses bahwa tabel telah dibuat.

4.  **Konfigurasi Koneksi (Opsional)**
    Jika Anda mengubah konfigurasi default MySQL (user/password), sesuaikan file `config.php` atau `koneksi.php`.

5.  **Jalankan Aplikasi**
    Buka browser dan akses:
    ```
    http://localhost/lintasdisiplinilmu
    ```

## Akun Default (Untuk Testing)

Gunakan akun berikut untuk masuk ke dalam sistem:

*   **Administrator**
    *   Email: `admin@dinaspeternakan.go.id`
    *   Username: `admin`
    *   Password: `password`

*   **Dokter**
    *   Email: `dokter1@dinaspeternakan.go.id`
    *   Username: `dokter1`
    *   Password: `password`

*   **Pemilik**
    *   Anda dapat mendaftar akun baru melalui halaman Register Pemilik.

---

## 📸 Tampilan Antarmuka (Screenshot)

### Halaman Publik

| Landing Page | Login | Register Pemilik |
|:---:|:---:|:---:|
| ![Landing Page](screenshots/01_landing_page.png) | ![Login](screenshots/03_login.png) | ![Register](screenshots/04_register_pemilik.png) |

| Layanan Vaksinasi |
|:---:|
| ![Vaksinasi](screenshots/02_layanan_vaksinasi.png) |

---

### Panel Administrator

| Dashboard Admin | Jadwal Dokter |
|:---:|:---:|
| ![Dashboard Admin](screenshots/05_admin_dashboard.png) | ![Jadwal Dokter](screenshots/06_admin_jadwal_dokter.png) |

| Jadwal Vaksinasi | Data Pasien |
|:---:|:---:|
| ![Jadwal Vaksinasi](screenshots/07_admin_jadwal_vaksinasi.png) | ![Data Pasien](screenshots/08_admin_pasien.png) |

| Laporan Pasien |
|:---:|
| ![Laporan Pasien](screenshots/09_admin_laporan_pasien.png) |

---

### Panel Dokter

| Dashboard Dokter | Jadwal Praktik |
|:---:|:---:|
| ![Dashboard Dokter](screenshots/10_dokter_dashboard.png) | ![Jadwal Praktik](screenshots/11_dokter_jadwal_praktik.png) |

| Appointment | Rekam Medis |
|:---:|:---:|
| ![Appointment](screenshots/12_dokter_appointment.png) | ![Rekam Medis](screenshots/13_dokter_rekam_medis.png) |

| Riwayat Vaksinasi |
|:---:|
| ![Vaksinasi Dokter](screenshots/14_dokter_vaksinasi.png) |

---

### Panel Pemilik Hewan

| Dashboard Pemilik | Data Hewan Saya |
|:---:|:---:|
| ![Dashboard Pemilik](screenshots/15_pemilik_dashboard.png) | ![Data Hewan](screenshots/16_pemilik_data_hewan.png) |

| Appointment | Riwayat Vaksinasi |
|:---:|:---:|
| ![Appointment Pemilik](screenshots/17_pemilik_appointment.png) | ![Riwayat Vaksinasi](screenshots/18_pemilik_riwayat_vaksinasi.png) |

---

## 👩‍💻 Pengembang

| | |
|---|---|
| **Nama** | Dinaria Simatupang |
| **Role** | Full-Stack Developer |
| **GitHub** | [@2608Dinaria](https://github.com/2608Dinaria) |
| **Email** | dinariasimatupang@gmail.com |

> Proyek ini dikembangkan sebagai bagian dari sistem informasi manajemen klinik hewan berbasis web menggunakan PHP, MySQL, dan Bootstrap 5.

---

<p align="center">
  Dibuat dengan ❤️ oleh <strong>Dinaria Simatupang</strong>
</p>
