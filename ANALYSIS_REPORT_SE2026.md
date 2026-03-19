# LAPORAN ANALISIS KODE & FUNGSI SISE2026 BPS KABUPATEN JEMBER

## **1. Daftar Bug & Isu Teknis**

| ID | Lokasi Error / File | Kondisi Pemicu | Dampak | Prioritas |
|:---|:---|:---|:---|:---|
| B01 | `sql/schema.sql` vs `src/functions.php` | Penggunaan tabel `pendaftaran` vs `pendaftaran_petugas` | Kebingungan saat maintenance & integrasi data | **Tinggi** |
| B02 | `src/functions.php` | Kegagalan koneksi PDO (misal: password salah) | Fungsi mengembalikan data mock secara diam-diam, menyulitkan debugging | **Tinggi** |
| B03 | `index.php` (POST Handler) | Submisi form di `anomaly.php`, `materi.php`, dll. | Data tidak tersimpan karena handler POST belum lengkap | **Tinggi** |
| B04 | `index.php` (Line 72) | Upload file tanpa pengecekan folder `uploads/` | Jika folder tidak ada, pendaftaran gagal disimpan tanpa pesan error jelas | **Sedang** |
| B05 | `src/auth.php` (Line 18) | Username case-sensitive | Potensi kesulitan login jika user salah ketik kapitalisasi | **Rendah** |

---

## **2. Fungsi Placeholder & Incomplete**

| Fitur / Modul | File Terkait | Status Saat Ini | Dampak |
|:---|:---|:---|:---|
| Alokasi Wilayah | `views/rekrutmen/alokasi.php` | Menggunakan `get_mock_wilayah()`. Peta hanya ilustrasi. | Data distribusi petugas tidak real-time |
| Materi Pelatihan | `views/pelatihan/materi.php` | List materi di-hardcode dalam array PHP. | Tidak bisa update materi via admin panel |
| Monitoring Progres | `views/pengolahan/monitoring.php` | Belum terintegrasi dengan tabel `monitoring_progress`. | Progres sensus masih statis |
| Pengumuman | `views/rekrutmen/pengumuman.php` | Menggunakan `get_mock_pengumuman()`. | Pengumuman baru tidak tampil otomatis |
| E-Buku Saku KBLI | `views/dashboard.php` | Link ada di Home tapi fitur pencarian KBLI belum ada. | User bingung mencari referensi kode |

---

## **3. Isu Integrasi**

- **Database Unification**: Terdapat redundansi antara tabel `pendaftaran` (di schema) dan `pendaftaran_petugas` (di SQL dump). Harus dipilih salah satu.
- **Role-Based Access Control (RBAC)**: Beberapa aksi POST belum mengecek `require_role` secara konsisten (misal: pelaporan anomali).
- **File System**: Upload directory (`uploads/`) belum memiliki proteksi `.htaccess` untuk mencegah akses publik langsung ke file sensitif (misal: KTP).

---

## **4. Rencana Implementasi Perbaikan**

### **Tahap 1: Stabilisasi & Database (High Priority)**
1. Sinkronisasi `schema.sql` dengan `src/functions.php` (Gunakan `pendaftaran_petugas`).
2. Implementasi handler POST yang lengkap di `index.php` untuk Anomali, Materi, dan SK.
3. Tambahkan validasi `is_dir` dan `mkdir` yang lebih robust pada fungsi upload.

### **Tahap 2: Integrasi Data Real (Medium Priority)**
1. Ubah fungsi `get_all_*` di `src/functions.php` untuk mengambil data dari DB terlebih dahulu, baru fallback ke mock jika DB benar-benar kosong.
2. Implementasi fitur CRUD di sisi admin untuk Pengumuman dan Materi Pelatihan.

### **Tahap 3: Penyempurnaan Fitur (Low Priority)**
1. Bangun fitur pencarian KBLI/KBKI pada dashboard.
2. Integrasikan peta interaktif (Leaflet.js) dengan data dari tabel `wilayah_kerja`.
