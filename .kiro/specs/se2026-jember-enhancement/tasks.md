# Rencana Implementasi: SE2026 Jember Enhancement

## Overview

Implementasi dilakukan secara bertahap menggunakan PHP MVC yang sudah ada. Setiap task membangun di atas task sebelumnya, dimulai dari skema database, lalu backend, lalu frontend, dan diakhiri dengan testing dan dokumentasi.

## Tasks

- [x] 1. Migrasi Skema Database
  - Buat file `sql/migration_se2026_enhancement.sql` dengan semua tabel baru
  - Tambahkan tabel `usaha` dengan semua kolom, foreign key, dan index
  - Tambahkan tabel `anomali_validasi` dengan index pada `kode_anomali` dan `status`
  - Tambahkan tabel `notifikasi_wa` dengan index pada `status` dan `usaha_id`
  - Tambahkan tabel `responden_tokens` dengan unique index pada `token`
  - Tambahkan tabel `oss_sync_log` dengan index pada `usaha_id` dan `synced_at`
  - Tambahkan tabel `offline_sync_log` dengan foreign key ke `users`
  - Pastikan migration idempotent (gunakan `CREATE TABLE IF NOT EXISTS`)
  - _Requirements: 1.1, 3.5, 5.2, 6.1, 10.4_


- [ ] 2. Implementasi Validator dan Model Usaha
  - [x] 2.1 Buat `src/Models/UsahaModel.php`
    - Method `save(array $data): int`, `findById(int $id): ?array`
    - Method `findByKecamatan(int $kecamatanId): array`, `findAll(array $filters): array`
    - Method `updateFromOss(int $id, array $ossData): bool`
    - _Requirements: 3.1, 6.7, 10.2_

  - [x] 2.2 Buat `src/Utils/Validator.php`
    - Method `validate(array $data): ValidationResult`
    - Method `checkDuplicate(array $data): bool` — similarity nama+alamat+telp > 90%
    - Method `validateNpwp(string $npwp): bool` — tepat 15 digit numerik
    - Method `validateAlamat(array $alamat): bool` — cek jalan, nomor, kecamatan
    - Method `checkSkalaConsistency(string $skala, int $tk): bool` — sesuai UU 20/2008
    - Method `generateDailyReport(): array`
    - Simpan anomali ke tabel `anomali_validasi` saat validasi gagal
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8_

  - [-] 2.3 Tulis property test untuk Validator
    - **Property 9: Validator Flags Invalid Data** — `testValidatorFlagsInvalidProperty`
    - **Property 10: Duplicate Detection Threshold** — `testDuplicateDetectionProperty`
    - **Property 11: NPWP Format Validation** — `testNpwpValidationProperty`
    - **Property 12: Address Completeness Validation** — `testAddressValidationProperty`
    - **Property 16: Scale-Workforce Consistency** — `testScaleWorkforceProperty`
    - Gunakan eris/eris, min 100 iterasi per property
    - Simpan di `tests/Property/ValidatorPropertyTest.php`
    - **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.8**

  - [~] 2.4 Tulis property test untuk anomali persistence dan laporan harian
    - **Property 13: Anomali Persistence Round-Trip** — `testAnomaliPersistenceProperty`
    - **Property 14: Daily Anomali Report Accuracy** — `testDailyReportAccuracyProperty`
    - **Property 15: Cross-PCL Duplicate Detection** — `testCrossPclDuplicateProperty`
    - Simpan di `tests/Property/ValidatorPropertyTest.php`
    - **Validates: Requirements 3.5, 3.6, 3.7**

  - [~] 2.5 Buat `src/Controllers/ValidasiController.php`
    - Method `handleIndex()` — tampilkan daftar anomali terbuka
    - Method `handleResolve()` — tandai anomali sebagai resolved
    - Method `handleLaporanHarian()` — tampilkan laporan ringkasan harian
    - Daftarkan route di `index.php`: `case 'validasi'`
    - _Requirements: 3.5, 3.6_


- [~] 3. Checkpoint — Pastikan semua test lulus
  - Jalankan `./vendor/bin/phpunit tests/Property/ValidatorPropertyTest.php`
  - Pastikan tabel baru dapat dibuat dari migration SQL tanpa error
  - Tanyakan kepada user jika ada pertanyaan sebelum melanjutkan

- [ ] 4. Modul Offline-First
  - [~] 4.1 Buat Service Worker `assets/js/sw.js`
    - Strategi cache: Network First untuk API, Cache First untuk assets statis
    - Event listener `sync` dengan tag `sync-offline-queue`
    - Fungsi `syncOfflineQueue()` yang membaca IndexedDB dan POST ke `?page=api&sub=sync`
    - Retry dengan exponential backoff (1s, 2s, 4s), max 3 kali
    - _Requirements: 1.2, 1.3_

  - [~] 4.2 Buat modul IndexedDB `assets/js/offline-queue.js`
    - Fungsi `saveToQueue(data)`, `getQueue()`, `updateStatus(id, status)`, `clearDone()`
    - Fungsi `isStale(entry): bool` — cek timestamp > 7 hari
    - Fungsi `getQueueCount(): int`
    - Store `offline_queue` dengan schema: `{id, data, timestamp, status, retry_count}`
    - _Requirements: 1.1, 1.5, 1.7_

  - [~] 4.3 Tulis property test untuk offline queue
    - **Property 1: Offline Queue Persistence** — `testQueuePersistenceProperty`
    - **Property 4: Stale Data Detection** — `testStaleDataProperty`
    - Simpan di `tests/Property/ValidatorPropertyTest.php` (method tambahan)
    - **Validates: Requirements 1.1, 1.7**

  - [~] 4.4 Buat API endpoint sync `src/Controllers/ApiController.php`
    - Method `handleSync()` — terima `{ entries: UsahaPayload[] }`, proses via `Validator`, simpan ke `usaha`
    - Deteksi konflik duplikat, kembalikan `{ synced, conflicts, conflict_ids }`
    - Catat ke `offline_sync_log` setiap sesi sync
    - Daftarkan route di `index.php`: `case 'api'` dengan `sub === 'sync'`
    - _Requirements: 1.2, 1.4_

  - [~] 4.5 Tulis property test untuk sync order dan conflict detection
    - **Property 2: Sync Chronological Order** — `testSyncOrderProperty`
    - **Property 3: Conflict Detection on Sync** — `testConflictDetectionProperty`
    - Simpan di `tests/Integration/OfflineSyncTest.php`
    - **Validates: Requirements 1.2, 1.4**

  - [~] 4.6 Buat UI indikator offline di `views/partials/header.php`
    - Banner status offline dengan jumlah entri pending
    - Indikator progres sinkronisasi (jumlah berhasil / total)
    - Peringatan data > 7 hari
    - Daftarkan Service Worker di footer
    - _Requirements: 1.3, 1.6, 1.7_


- [ ] 5. Dashboard Analitik Real-Time
  - [~] 5.1 Buat `src/Controllers/DashboardController.php`
    - Method `handleIndex()` — render view dashboard (role: admin, operator)
    - Method `handleApiStats()` — JSON: total usaha per kategori, progres per kecamatan, kinerja enumerator
    - Method `handleApiTrend()` — JSON: tren harian selama periode sensus
    - Method `handleExportCsv()` — download CSV dari data yang sedang ditampilkan
    - Daftarkan route di `index.php`: `case 'dashboard-analitik'`
    - _Requirements: 2.1, 2.2, 2.3, 2.5, 2.6, 2.7_

  - [~] 5.2 Buat `src/Utils/CSVExporter.php`
    - Method `export(array $data, string $filename): void` — set header dan output CSV
    - _Requirements: 2.6_

  - [~] 5.3 Buat `views/dashboard/index.php`
    - Inisialisasi Chart.js: BarChart progres per kecamatan, LineChart tren harian, DoughnutChart UMK/UM/UB, BarChart kinerja enumerator top-10
    - Filter dropdown kecamatan yang memperbarui semua chart
    - Tombol ekspor CSV
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [~] 5.4 Buat `assets/js/dashboard.js`
    - Fungsi `fetchAndUpdateCharts(kecamatanFilter)` — fetch JSON dari `handleApiStats`
    - `setInterval(() => fetchAndUpdateCharts(), 5 * 60 * 1000)` — polling 5 menit
    - Event listener filter kecamatan yang trigger fetch ulang
    - _Requirements: 2.1, 2.4_

  - [ ] 5.5 Tulis property test untuk dashboard
    - **Property 5: Dashboard Grouping Completeness** — `testGroupingCompletenessProperty`
    - **Property 6: Dashboard Filter Correctness** — `testFilterCorrectnessProperty`
    - **Property 7: Enumerator Ranking Order** — `testRankingOrderProperty`
    - **Property 8: CSV Export Round-Trip** — `testCsvExportProperty`
    - Simpan di `tests/Property/DashboardPropertyTest.php`
    - **Validates: Requirements 2.1, 2.4, 2.5, 2.6**


- [ ] 6. Pemetaan Spasial Heatmap
  - [~] 6.1 Buat `src/Controllers/PetaController.php`
    - Method `handleIndex()` — render view peta (role: admin)
    - Method `handleApiHeatmap()` — JSON: array `[lat, lng, weight]` per titik usaha
    - Method `handleApiMarkers()` — JSON: marker per kecamatan dengan `total_usaha`, `pct_capaian`, `per_sektor`
    - Daftarkan route di `index.php`: `case 'peta'`
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [~] 6.2 Buat `views/peta/index.php`
    - Inisialisasi Leaflet.js dengan tile OpenStreetMap
    - Layer heatmap menggunakan `leaflet.heat`
    - Layer marker per kecamatan dengan popup rincian per sektor KBLI
    - Layer titik usaha individual (toggle on/off)
    - Filter sektor dan skala usaha
    - _Requirements: 4.1, 4.3, 4.4, 4.5, 4.6_

  - [ ] 6.3 Tulis property test untuk peta
    - **Property 17: Heatmap Data Validity** — `testHeatmapDataValidityProperty`
    - **Property 18: Marker Data Completeness** — `testMarkerCompletenessProperty`
    - **Property 19: Map Filter Correctness** — `testMapFilterProperty`
    - Simpan di `tests/Property/DashboardPropertyTest.php`
    - **Validates: Requirements 4.2, 4.3, 4.5**

- [ ] 7. Sistem Notifikasi WhatsApp
  - [~] 7.1 Buat `src/Utils/WhatsAppNotifier.php`
    - Constructor `__construct(string $apiToken, string $phoneNumberId)`
    - Method `send(string $to, string $templateName, array $params): NotifResult`
    - Method `sendBatch(array $recipients, string $templateName): BatchResult`
    - Method `scheduleBatch(array $recipients, string $templateName, DateTime $sendAt): int`
    - Method `canSendNotification(int $usahaId): bool` — cek batas 3x per periode
    - Method `validateScheduleTime(DateTime $time): bool` — cek jam 08.00–17.00 WIB
    - Catat setiap pengiriman ke tabel `notifikasi_wa`
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.6_

  - [~] 7.2 Buat `src/Controllers/NotifikasiController.php`
    - Method `handleIndex()` — tampilkan daftar notifikasi dan statistik
    - Method `handleKirim()` — proses pengiriman batch
    - Method `handleJadwal()` — atur jadwal pengiriman
    - Method `handleExportCsv()` — fallback ekspor nomor + template ke CSV
    - Method `handleApiStats()` — JSON: total terkirim, gagal, tingkat respons
    - Daftarkan route di `index.php`: `case 'notifikasi'`
    - _Requirements: 5.1, 5.5, 5.7_

  - [~] 7.3 Buat `views/notifikasi/index.php`
    - Tabel daftar responden belum diwawancarai dengan checkbox multi-select
    - Form jadwal pengiriman dengan time picker
    - Statistik ringkasan: terkirim, gagal, tingkat respons
    - Tombol ekspor CSV fallback
    - _Requirements: 5.1, 5.5, 5.6, 5.7_

  - [ ] 7.4 Tulis property test untuk notifikasi
    - **Property 20: Notification Rate Limiting** — `testRateLimitingProperty`
    - **Property 21: Notification Log and Stats Invariant** — `testLogStatsInvariantProperty`
    - **Property 22: Business Hours Scheduling** — `testBusinessHoursProperty`
    - **Property 23: CSV Fallback Completeness** — `testCsvFallbackProperty`
    - Simpan di `tests/Property/NotifikasiPropertyTest.php`
    - **Validates: Requirements 5.2, 5.4, 5.5, 5.6, 5.7**


- [~] 8. Checkpoint — Pastikan semua test lulus
  - Jalankan `./vendor/bin/phpunit tests/Property/`
  - Verifikasi endpoint API (`?page=api&sub=sync`, `?page=dashboard-analitik`, `?page=peta`) merespons JSON yang valid
  - Tanyakan kepada user jika ada pertanyaan sebelum melanjutkan

- [ ] 9. Pelaporan Mandiri Responden
  - [~] 9.1 Buat `src/Controllers/RespondenController.php`
    - Method `handleForm()` — validasi token, tampilkan form pre-filled dengan data usaha
    - Method `handleSubmit()` — proses submit, jalankan Validator, simpan ke `usaha` dengan `sumber_data = 'mandiri'`, tandai token `used`
    - Method `handleExpired()` — tampilkan halaman info token tidak valid
    - Method `generateToken(int $usahaId, int $createdBy): string` — buat token 32-hex, simpan ke `responden_tokens` dengan expiry 30 hari
    - Daftarkan route di `index.php`: `case 'responden'` (tidak perlu auth)
    - _Requirements: 6.1, 6.2, 6.4, 6.5, 6.7_

  - [~] 9.2 Buat `views/responden/form.php`
    - Form mobile-friendly, lebar minimum 320px, input besar untuk layar sentuh
    - Field pre-filled dari data usaha yang sudah ada
    - Validasi inline per field dengan pesan error spesifik
    - _Requirements: 6.2, 6.3, 6.4_

  - [~] 9.3 Buat `views/responden/expired.php`
    - Halaman informasi token tidak valid/kedaluwarsa
    - Tampilkan nomor kontak BPS Jember
    - _Requirements: 6.5_

  - [ ] 9.4 Tulis property test untuk token responden
    - **Property 24: Token Lookup Round-Trip** — `testTokenLookupProperty`
    - **Property 25: Expired Token Rejection** — `testExpiredTokenProperty`
    - **Property 26: Data Source Tracking** — `testDataSourceTrackingProperty`
    - Simpan di `tests/Property/ValidatorPropertyTest.php`
    - **Validates: Requirements 6.1, 6.5, 6.7**

  - [ ] 9.5 Tulis integration test untuk alur pelaporan mandiri
    - Test alur lengkap: generate token → akses form → submit → verifikasi data tersimpan
    - Simpan di `tests/Integration/RespondenFormTest.php`
    - _Requirements: 6.1, 6.2, 6.4, 6.7_


- [ ] 10. Integrasi OSS
  - [~] 10.1 Buat `src/Utils/OSSClient.php`
    - Constructor `__construct(string $apiKey, string $baseUrl)`
    - Method `lookupByNib(string $nib): OSSResult`
    - Method `lookupByNpwp(string $npwp): OSSResult`
    - Method `batchSync(array $usahaIds): BatchSyncResult` — proses hingga 100 entri
    - Gunakan `curl` untuk HTTP request, timeout 30 detik
    - Catat setiap percobaan ke `oss_sync_log`
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

  - [~] 10.2 Buat `src/Controllers/OSSController.php`
    - Method `handleIndex()` — tampilkan daftar usaha dengan status OSS
    - Method `handleSync()` — sinkronisasi satu entri via NIB/NPWP
    - Method `handleBatchSync()` — sinkronisasi batch dengan progres real-time (SSE atau polling)
    - Method `handleRiwayat()` — tampilkan riwayat sinkronisasi per usaha
    - Daftarkan route di `index.php`: `case 'oss'`
    - _Requirements: 10.1, 10.3, 10.4, 10.5, 10.6_

  - [~] 10.3 Buat `views/oss/index.php`
    - Tabel daftar usaha dengan kolom status OSS dan tombol sync
    - Indikator progres batch sync
    - Filter usaha "belum terverifikasi OSS"
    - _Requirements: 10.5, 10.6_

  - [ ] 10.4 Tulis property test untuk OSS integration
    - **Property 30: OSS Sync Data Update** — `testOssSyncUpdateProperty`
    - **Property 31: OSS Error Immutability** — `testOssErrorImmutabilityProperty`
    - **Property 32: OSS Batch Completeness** — `testOssBatchCompletenessProperty`
    - **Property 33: Missing NIB Status** — `testMissingNibStatusProperty`
    - Simpan di `tests/Property/OSSPropertyTest.php`
    - **Validates: Requirements 10.2, 10.3, 10.4, 10.5, 10.6**


- [ ] 11. Data Dummy Ekstensif
  - [~] 11.1 Buat `src/Utils/DummyGenerator.php`
    - Method `generate(int $count = 50000): array` — hasilkan array data usaha
    - Distribusi sektor: pertanian 15%, perdagangan 40%, jasa 30%, manufaktur 15%
    - Distribusi skala: mikro 70%, kecil 20%, menengah 8%, besar 2%
    - Distribusi per kecamatan proporsional berdasarkan bobot populasi
    - Method `generateEdgeCases(): array` — 500 NPWP invalid, 300 alamat tidak lengkap, 200 duplikat
    - Method `generateHistorical(int $years = 3): array` — data 2023, 2024, 2025 (min 30.000 entri)
    - Method `generateSql(array $data): string` — output SQL INSERT yang valid
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

  - [~] 11.2 Buat `sql/seed_se2026_dummy.sql` menggunakan DummyGenerator
    - Jalankan `DummyGenerator` dan tulis output ke file SQL
    - Pastikan file dapat dieksekusi langsung di MySQL tanpa modifikasi
    - _Requirements: 7.6_

  - [ ] 11.3 Tulis property test untuk DummyGenerator
    - **Property 27: Dummy Data Distribution** — `testDistributionProperty`
    - **Property 28: Dummy Edge Cases Count** — `testEdgeCasesCountProperty`
    - **Property 29: Dummy SQL Validity** — `testSqlValidityProperty`
    - Simpan di `tests/Unit/DummyGeneratorTest.php`
    - **Validates: Requirements 7.2, 7.3, 7.4, 7.6**

- [~] 12. Checkpoint — Pastikan semua test lulus
  - Jalankan `./vendor/bin/phpunit` untuk semua test
  - Verifikasi `sql/seed_se2026_dummy.sql` dapat diimport tanpa error
  - Tanyakan kepada user jika ada pertanyaan sebelum melanjutkan


- [ ] 13. Testing & QA Suite
  - [~] 13.1 Buat unit test untuk semua Controller dan Model baru
    - `tests/Unit/ValidatorTest.php` — test contoh spesifik: NPWP valid/invalid, alamat lengkap/tidak, skala vs TK
    - `tests/Unit/WhatsAppNotifierTest.php` — test rate limiting, validasi jadwal, log status
    - `tests/Unit/OSSClientTest.php` — test lookup, error handling, immutability data
    - `tests/Unit/TokenManagerTest.php` — test generate token, lookup, expiry
    - Target coverage minimal 80% untuk semua Controller dan Model baru
    - _Requirements: 8.3, 8.4_

  - [ ] 13.2 Tulis integration test untuk DashboardController
    - Test endpoint `handleApiStats()` dan `handleApiTrend()` mengembalikan JSON valid
    - Test `handleExportCsv()` menghasilkan CSV dengan jumlah baris yang benar
    - Simpan di `tests/Integration/DashboardApiTest.php`
    - _Requirements: 8.3, 8.4_

  - [ ] 13.3 Tulis integration test untuk OfflineSync
    - Test endpoint `?page=api&sub=sync` menerima batch dan mengembalikan `synced` + `conflicts`
    - Simpan di `tests/Integration/OfflineSyncTest.php`
    - _Requirements: 8.3, 8.4_

  - [~] 13.4 Buat konfigurasi PHPUnit `phpunit.xml`
    - Daftarkan semua test suite: Unit, Integration, Property
    - Konfigurasi code coverage untuk `src/Controllers/` dan `src/Models/`
    - Pastikan semua test dapat dijalankan dengan satu perintah: `./vendor/bin/phpunit`
    - _Requirements: 8.3, 8.8_

  - [~] 13.5 Buat setup stress test `tests/stress/load_test.js`
    - Script k6 untuk 100 VU selama 10 menit
    - Skenario: baca data usaha, submit form, akses dashboard
    - Target: waktu respons rata-rata < 3 detik untuk operasi baca
    - _Requirements: 8.1, 8.2_

  - [~] 13.6 Buat checklist kompatibilitas browser `tests/compatibility/checklist.md`
    - Checklist untuk Chrome, Firefox, Safari, Edge, Opera versi terbaru
    - Checklist untuk Android 10, 11, 12 dengan Chrome Mobile
    - _Requirements: 8.5, 8.6_


- [ ] 14. Dokumentasi Teknis
  - [~] 14.1 Buat `docs/openapi.yaml` — spesifikasi OpenAPI 3.0
    - Dokumentasikan semua endpoint baru: `/api/sync`, `/dashboard-analitik`, `/peta`, `/notifikasi`, `/responden`, `/oss`, `/validasi`
    - Sertakan contoh request dan response untuk setiap endpoint
    - _Requirements: 9.1_

  - [~] 14.2 Buat `docs/erd.md` — ERD teks/Mermaid
    - Gambarkan semua tabel baru dan relasi ke tabel existing
    - Sertakan deskripsi singkat setiap tabel
    - _Requirements: 9.2_

  - [~] 14.3 Perbarui `DEPLOYMENT_GUIDE.md`
    - Tambahkan langkah instalasi tabel baru (jalankan `sql/migration_se2026_enhancement.sql`)
    - Tambahkan konfigurasi environment baru: `WA_API_TOKEN`, `WA_PHONE_NUMBER_ID`, `OSS_API_KEY`, `OSS_BASE_URL`
    - Tambahkan prosedur rollback
    - _Requirements: 9.3_

  - [~] 14.4 Buat `CHANGELOG.md`
    - Catat semua perubahan dengan format: versi, tanggal, jenis (fitur baru/perbaikan/breaking change), deskripsi
    - _Requirements: 9.4_

- [ ] 15. Integrasi Akhir dan Wiring
  - [~] 15.1 Perbarui `index.php` dengan semua route baru
    - Tambahkan case: `dashboard-analitik`, `validasi`, `peta`, `notifikasi`, `responden`, `oss`, `api`
    - Tambahkan POST handler untuk semua action baru
    - Pastikan role guard sesuai: admin/operator untuk dashboard/validasi/peta/notifikasi/oss, public untuk responden
    - _Requirements: 2.1, 3.1, 4.1, 5.1, 6.1, 10.1_

  - [~] 15.2 Perbarui `views/partials/header.php`
    - Tambahkan link navigasi ke semua modul baru
    - Tambahkan registrasi Service Worker
    - Tambahkan banner offline status
    - _Requirements: 1.6_

  - [~] 15.3 Perbarui `config/config.php`
    - Tambahkan konstanta: `WA_API_TOKEN`, `WA_PHONE_NUMBER_ID`, `OSS_API_KEY`, `OSS_BASE_URL`
    - Baca dari environment variable dengan fallback ke `.env`
    - _Requirements: 5.1, 10.1_

  - [~] 15.4 Perbarui `composer.json`
    - Tambahkan `eris/eris` untuk property-based testing
    - Jalankan `composer install` untuk menginstall dependency baru
    - _Requirements: 8.3_

- [~] 16. Checkpoint Final — Pastikan semua test lulus
  - Jalankan `./vendor/bin/phpunit --configuration phpunit.xml`
  - Verifikasi semua route baru dapat diakses tanpa error 500
  - Verifikasi Service Worker terdaftar di browser
  - Tanyakan kepada user jika ada pertanyaan sebelum dianggap selesai

## Notes

- Task bertanda `*` bersifat opsional dan dapat dilewati untuk MVP yang lebih cepat
- Setiap task mereferensikan persyaratan spesifik untuk keterlacakan
- Checkpoint memastikan validasi inkremental di setiap fase
- Property test memvalidasi properti universal; unit test memvalidasi contoh spesifik dan edge case
- Semua kode harus kompatibel dengan PHP 7.4+ dan MySQL 5.7+ untuk shared hosting Jagoan Hosting
