# Sistem Aplikasi SE2026 BPS Kabupaten Jember — Implementation Plan

Membangun sistem manajemen Sensus Ekonomi 2026 yang komprehensif di atas existing codebase Native PHP (front controller pattern). Aplikasi ini memiliki **4 menu utama** dengan submenu, role-based access control, dan fitur cross-cutting (search, filter, export, notifikasi, log).

## User Review Required

> [!IMPORTANT]
> **Migrasi Database ke MySQL:** Berdasarkan error yang muncul, sistem nampaknya menggunakan MySQL/MariaDB (Laragon default) bukan PostgreSQL. Saya akan mengubah konfigurasi koneksi dan sintaks SQL.
> 
> **Perubahan Port:** Default port MySQL adalah `3306`. Saya akan menyesuaikan [config.php](file:///c:/laragon/www/se2026-jember/config/config.php).

> [!IMPORTANT]
> **Skala proyek ini sangat besar** (setara enterprise application). Saya akan mengerjakannya secara **bertahap/incremental per fase**. Fase 1 (Foundation) dikerjakan terlebih dahulu, lalu setiap fase selanjutnya setelah fase sebelumnya selesai dan di-review.

> [!WARNING]
> **Database**: Codebase saat ini menggunakan PostgreSQL tapi koneksi bersifat optional (fallback ke mock data). Implementasi ini akan tetap menggunakan PostgreSQL sesuai [config.php](file:///c:/laragon/www/se2026-jember/config/config.php). Pastikan PostgreSQL server aktif dan database `bps_jember_se2026` sudah dibuat.

> [!IMPORTANT]
> **Pendekatan bertahap — Fase 1 akan mencakup:**
> 1. Database schema lengkap
> 2. Auth system (login, session, roles: admin, operator, pcl, pml, public)
> 3. Enhanced routing untuk nested menu/submenu
> 4. Sidebar + mega-menu navigation responsive
> 5. Implementasi **seluruh halaman dari 4 menu utama** sebagai working UI dengan mock data
> 6. Shared components: search bar, date filter, export buttons, notification placeholders
>
> Setelah Fase 1 selesai, fitur-fitur yang lebih kompleks (API Zoom, email/SMS integration, Google Calendar sync, QR-Code, tanda tangan digital, dll) akan dikerjakan di fase berikutnya.

---

## Proposed Changes

### 1. Database Schema

#### [NEW] [schema.sql](file:///c:/laragon/www/se2026-jember/sql/schema.sql)
Full PostgreSQL schema meliputi tabel-tabel:
- `users` — autentikasi & role (admin, operator, pcl, pml)
- `pendaftaran` — rekrutmen petugas
- `dokumen_persyaratan` — upload dokumen
- `jadwal_seleksi` — jadwal seleksi
- `wilayah_kerja` — alokasi petugas per wilayah
- [pengumuman](file:///c:/laragon/www/se2026-jember/src/functions.php#102-109) — pengumuman hasil seleksi
- `surat_keputusan` — SK management
- [surat_masuk](file:///c:/laragon/www/se2026-jember/src/functions.php#118-125), [surat_keluar](file:///c:/laragon/www/se2026-jember/src/functions.php#126-132) — surat tracking
- `memorandum` — memo & undangan
- `laporan_kegiatan` — laporan + lampiran
- `notulen_rapat` — notulen
- `pelatihan_online`, `pelatihan_offline` — pelatihan management
- `materi_bahan` — repository materi
- `surat_tugas`, `visum`, `jadwal_pertemuan` — pelaksanaan
- [anomaly](file:///c:/laragon/www/se2026-jember/src/functions.php#141-148), `monitoring` — pengolahan data
- `dokumentasi` — dokumentasi pelatihan & pelaksanaan
- `activity_logs` — log aktivitas
- `notifications` — notifikasi

---

### 2. Core Infrastructure

#### [MODIFY] [config.php](file:///c:/laragon/www/se2026-jember/config/config.php)
- Tambah session management
- Tambah CSRF token generation
- Tambah role constants

#### [NEW] [auth.php](file:///c:/laragon/www/se2026-jember/src/auth.php)
- Login/logout handler
- Session validation
- Role checking (admin, operator, pcl, pml)
- Password hashing (bcrypt)
- CSRF token validation

#### [MODIFY] [functions.php](file:///c:/laragon/www/se2026-jember/src/functions.php)
- Tambah utility functions: [generate_csrf_token()](file:///c:/laragon/www/se2026-jember/src/auth.php#110-119), [validate_csrf()](file:///c:/laragon/www/se2026-jember/src/auth.php#120-126), `check_role()`, `redirect()`
- Tambah pagination helper
- Tambah export helper (PDF/Excel)
- Tambah notification helper

#### [MODIFY] [index.php](file:///c:/laragon/www/se2026-jember/index.php)
- Expand routing switch untuk semua page/submenu baru
- Tambah auth check middleware per route
- Tambah CSRF validation untuk POST requests

---

### 3. Navigation & Layout

#### [MODIFY] [header.php](file:///c:/laragon/www/se2026-jember/views/partials/header.php)
- Redesign navbar menjadi mega-menu dropdown dengan 4 menu utama
- Setiap menu memiliki submenu yang bisa di-expand
- Mobile responsive hamburger menu dengan accordion submenu
- Tampilkan status login/user info di navbar
- Login/Logout button

#### [MODIFY] [footer.php](file:///c:/laragon/www/se2026-jember/views/partials/footer.php)
- Minor update, tambah link ke menu baru

#### [MODIFY] [style.css](file:///c:/laragon/www/se2026-jember/assets/css/style.css)
- Tambah styles untuk mega-menu, sidebar, form components, tables, modals
- Tambah styles untuk dashboard cards, badges, status indicators
- Tambah print-friendly styles

### [MySQL Migration]

#### [MODIFY] [schema.sql](file:///c:/laragon/www/se2026-jember/sql/schema.sql)
- Konversi sintaks PostgreSQL (`SERIAL`, `ON CONFLICT`, `CHECK`) ke MySQL.
- Perbaikan error 1101 (TEXT default value).

#### [MODIFY] [config.php](file:///c:/laragon/www/se2026-jember/config/config.php)
- Ubah DSN dari `pgsql` ke `mysql`.
- Update `DB_PORT` ke `3306`.

#### [MODIFY] [app.js](file:///c:/laragon/www/se2026-jember/assets/js/app.js)
- Tambah mega-menu toggle logic
- Tambah modal, tab, accordion components
- Tambah client-side form validation
- Tambah AJAX handlers untuk dynamic content

---

### 4. Menu Rekrutmen Petugas (Public Access)

#### [MODIFY] [rekrutmen.php](file:///c:/laragon/www/se2026-jember/views/rekrutmen.php)
- Refactor menjadi landing page menu rekrutmen dengan 3 submenu tabs

#### [NEW] [rekrutmen_administrasi.php](file:///c:/laragon/www/se2026-jember/views/rekrutmen/administrasi.php)
- Form pendaftaran petugas (PCL/PML)
- Upload dokumen persyaratan (KTP, ijazah, CV)
- Status kelengkapan berkas (progress indicator)
- Tabel jadwal seleksi

#### [NEW] [rekrutmen_alokasi.php](file:///c:/laragon/www/se2026-jember/views/rekrutmen/alokasi.php)
- Peta interaktif penugasan (Leaflet.js / OpenStreetMap)
- Daftar wilayah kerja (31 kecamatan Jember)
- Jumlah kebutuhan petugas per wilayah
- Status ketersediaan petugas

#### [NEW] [rekrutmen_pengumuman.php](file:///c:/laragon/www/se2026-jember/views/rekrutmen/pengumuman.php)
- Daftar pengumuman hasil seleksi
- Jadwal kegiatan (timeline)
- Download dokumen PDF

---

### 5. Menu Teknis & Administrasi (Login: operator, admin)

#### [NEW] views/teknis/ folder
- [sk.php](file:///c:/laragon/www/se2026-jember/views/teknis/sk.php) — Daftar SK, upload, preview, cetak
- [surat_masuk.php](file:///c:/laragon/www/se2026-jember/views/teknis/surat_masuk.php) — Form entri, nomor otomatis, disposisi, tracking
- [surat_keluar.php](file:///c:/laragon/www/se2026-jember/views/teknis/surat_keluar.php) — Template, log pengiriman, arsip
- [memorandum.php](file:///c:/laragon/www/se2026-jember/views/teknis/memorandum.php) — Template cepat, distribusi
- [laporan_kegiatan.php](file:///c:/laragon/www/se2026-jember/views/teknis/laporan_kegiatan.php) — Form isian, lampiran foto, approval
- [notulen_rapat.php](file:///c:/laragon/www/se2026-jember/views/teknis/notulen_rapat.php) — Editor, penugasan, distribusi

#### [NEW] views/pelatihan/ folder (Login: pcl, pml, operator, admin)
- [online.php](file:///c:/laragon/www/se2026-jember/views/pelatihan/online.php) — Notulen & Rekaman, Undangan, Zoom, Daftar Hadir, QnA
- [offline.php](file:///c:/laragon/www/se2026-jember/views/pelatihan/offline.php) — Daftar hadir manual, evaluasi, sertifikat
- [materi.php](file:///c:/laragon/www/se2026-jember/views/pelatihan/materi.php) — Repository file, versi PDF & PPT
- [pelaksanaan.php](file:///c:/laragon/www/se2026-jember/views/pelatihan/pelaksanaan.php) — Surat Tugas, Visum, KBLI/KBKI, Jadwal, Laporan, Username FASIH, Monitoring

---

### 6. Menu Pengolahan (Login Required)

#### [NEW] views/pengolahan/ folder
- [anomaly.php](file:///c:/laragon/www/se2026-jember/views/pengolahan/anomaly.php) — Form pelaporan, upload bukti, workflow approval, dashboard
- [monitoring.php](file:///c:/laragon/www/se2026-jember/views/pengolahan/monitoring.php) — Peta sebaran, progress capaian, alert, ekspor

---

### 7. Menu Dokumentasi (Login Required)

#### [NEW] views/dokumentasi/ folder
- [pelatihan_online.php](file:///c:/laragon/www/se2026-jember/views/dokumentasi/pelatihan_online.php) — Galeri video, thumbnail, filter
- [pelatihan_offline.php](file:///c:/laragon/www/se2026-jember/views/dokumentasi/pelatihan_offline.php) — Upload foto, tagging, album
- `pelaksanaan_rapat.php` — Notulen, daftar hadir, foto
- [foto_kegiatan.php](file:///c:/laragon/www/se2026-jember/views/dokumentasi/foto_kegiatan.php) — Upload foto, watermark, penamaan standar

---

### 8. Model Layer (Backend)

#### [NEW] [src/models/](file:///c:/laragon/www/se2026-jember/src/models/) folder
Setiap modul memiliki model class sendiri:
- `UserModel.php` — CRUD users, authentication queries
- `RekrutmenModel.php` — pendaftaran, dokumen, jadwal
- `SuratModel.php` — SK, surat masuk/keluar, memo
- `PelatihanModel.php` — pelatihan online/offline, materi
- `PelaksanaanModel.php` — surat tugas, visum, jadwal
- `PengolahanModel.php` — anomaly, monitoring
- `DokumentasiModel.php` — dokumentasi, foto
- `ActivityLogModel.php` — log semua aksi
- `NotificationModel.php` — notifikasi

---

## Verification Plan

### Browser Testing
1. **Buka** `http://localhost/se2026-jember/` — verifikasi beranda tampil dengan mega-menu baru
2. **Klik** setiap menu di navbar — verifikasi dropdown submenu muncul
3. **Akses** `?page=rekrutmen` — verifikasi 3 submenu tabs (Administrasi, Alokasi, Pengumuman)
4. **Akses** `?page=teknis` tanpa login — verifikasi redirect ke halaman login
5. **Login** sebagai admin — verifikasi akses ke semua menu
6. **Login** sebagai PCL — verifikasi hanya bisa akses Pelatihan, bukan Administrasi SK/Surat
7. **Test mobile responsive** — resize browser, verifikasi hamburger menu + accordion submenu
8. **Test form submission** — isi form pendaftaran, verifikasi CSRF token dan validasi

### Manual Testing (User)
1. Buka browser ke `http://localhost/se2026-jember/`
2. Navigasi ke setiap menu dan submenu — pastikan semua tampil tanpa error PHP
3. Test login flow — coba login dengan role admin/operator/pcl/pml
4. Test akses role-based — verifikasi halaman terproteksi tidak bisa diakses tanpa role yang sesuai
5. Test responsive di mobile device atau Chrome DevTools (toggle device mode)

### Security Check
- Verifikasi CSRF token ada di setiap form
- Test SQL injection pada input fields (single quotes, union select)
- Test XSS pada input fields (script tags)
- Verifikasi password tersimpan ter-hash (bcrypt)
