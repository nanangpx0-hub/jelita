# JELITA - Sistem Informasi Sensus Ekonomi 2026 BPS Kabupaten Jember  
  
## Deskripsi  
  
JELITA adalah sistem informasi untuk pengelolaan data Sensus Ekonomi 2026 di BPS Kabupaten Jember. 
  
## Teknologi yang Digunakan  
  
  
## Fitur Utama  
  
- Sistem Autentikasi dan Autorisasi (Admin, Petugas, Respondent)  
- Dashboard dan Monitoring  
- Manajemen Rekrutmen  
- Modul Pelatihan (Online/Offline)  
- Manajemen Dokumentasi  
- Pengolahan Data dan Deteksi Anomali  
- Dokumen Teknis (Surat, SK, Notulen) 
  
## Prasyarat  
  
- PHP  atau PHP  
- MySQL  
- Composer  
- Web Server (Apache/Nginx) atau Laragon/XAMPP 
  
## Langkah Setup Awal  
  
### 1. Clone Repository  
~~~bash  
git clone https://github.com/nanangpx0-hub/jelita.git  
cd jelita  
~~~ 
  
### 2. Install Dependencies  
~~~bash  
composer install  
~~~  
  
### 3. Setup Database  
  
Buat database MySQL (bps_jember_se2026) dan import file SQL:  
~~~bash  
mysql -u username -p bps_jember_se2026 < sql/schema.sql  
mysql -u username -p bps_jember_se2026 < sql/seed_dummy_data.sql  
~~~ 
  
### 4. Konfigurasi Environment  
  
Salin .env.example menjadi .env dan sesuaikan konfigurasi database.  
  
### 5. Jalankan Aplikasi  
  
Akses via web server: http://localhost/jelita  
  
## Default Login  
  
- Admin: admin / admin123  
- Petugas: petugas / petugas123  
- Respondent: respondent / respondent123 
  
  
## Lisensi  
  
MIT License 
