# 📄 RINGKASAN TEKNIS KONFIGURASI HOSTING SISE2026
## Unit: BPS Kabupaten Jember | Provider: Jagoan Hosting

Ringkasan ini disusun berdasarkan ekstraksi data dari dokumentasi server Jagoan Hosting untuk keperluan deployment aplikasi Sistem Informasi Sensus Ekonomi 2026.

---

## 🔐 1. INFORMASI AKUN & SERVER
- **Nama Server:** `brave`
- **Alamat IP Shared:** `103.163.138.166`
- **Username cPanel:** `bpsjembe`
- **Domain Utama:** `bpsjembe.com`
- **Production URL:** `https://se2026.bpsjember.my.id` (Endpoint Utama - Verified ✅)
- **Status Endpoint:** Online (HTTP 200), SSL/HTTPS Active, LiteSpeed Server.
- **Home Directory:** `/home/bpsjembe`
- **Versi cPanel:** `132.0 (build 19)`

---

## 🖥️ 2. SPESIFIKASI RESOURCE (LIMITS)
| Komponen | Kapasitas / Batasan |
| :--- | :--- |
| **CPU Cores** | 12 Cores |
| **RAM (Physical Memory)** | 4 GB |
| **Storage (Disk Space)** | 20 GB (SSD/NVMe) |
| **I/O Throughput** | 97.66 MB/s |
| **IOPS** | 1,024 |
| **Entry Processes** | 50 |
| **Number of Processes** | 100 |

---

## 🐘 3. KONFIGURASI PHP & DATABASE
- **Versi PHP Default:** `8.2` (Configured for SE2026)
- **Versi MariaDB:** `10.6.24-MariaDB`
- **Ekstensi PHP Kritis:** `pdo_mysql`, `mysqli`, `gd`, `mbstring`, `curl`, `openssl`, `zip`, `intl`, `soap`.
- **Batas PHP:**
  - `memory_limit`: `256M`
  - `upload_max_filesize`: `2G`
  - `post_max_size`: `2G`
  - `max_execution_time`: `300s`
  - `display_errors`: `OFF` (Production Standard)

---

## 🌐 4. NAMESERVER & AKSES
- **Nameservers:** Biasanya mengikuti format Jagoan Hosting (`ns1.jagoanhosting.com` s/d `ns4.jagoanhosting.com`). *Harap verifikasi di menu "Server Information" cPanel jika terjadi kendala propagasi.*
- **Akses cPanel:** `https://bpsjembe.com:2083` atau `https://103.163.138.166:2083`
- **Akses WHM:** (Hanya jika memiliki akses reseller/VPS - tidak tertera dalam shared info).

---

## 🛡️ 5. CATATAN KEAMANAN & SSL
- **SSL Certificate:** Aktif (Let's Encrypt / AutoSSL) untuk `bpsjembe.com`.
- **Firewall:** ModSecurity Aktif.
- **Proteksi Akun:** cPHulk Brute Force Protection & IP Blocker tersedia.
- **Backup:** JetBackup 5 tersedia untuk pemulihan data berkala.
- **Rekomendasi:** 
  - Aktifkan Two-Factor Authentication (2FA) pada akun cPanel.
  - Gunakan `https` secara paksa via `.htaccess`.
  - Pastikan folder `uploads/` memiliki proteksi akses publik yang ketat.

---

## 📂 6. DIREKTORI DEPLOYMENT
- **Document Root:** `/home/bpsjembe/public_html`
- **Lokasi Logs:** `/home/bpsjembe/logs`
- **Lokasi Session:** `/opt/alt/php82/var/lib/php/session`

---
**Status Deployment:** ✅ Konfigurasi Sesuai Kebutuhan Aplikasi.
