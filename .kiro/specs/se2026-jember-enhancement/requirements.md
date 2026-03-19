# Dokumen Persyaratan

## Pendahuluan

Sistem Informasi Sensus Ekonomi 2026 (SISE2026) BPS Kabupaten Jember adalah aplikasi web berbasis PHP/MySQL yang saat ini mengelola rekrutmen petugas, pelatihan, pengolahan data anomali, dokumentasi kegiatan, dan administrasi teknis. Sistem ini di-deploy di shared hosting Jagoan Hosting dengan arsitektur MVC sederhana dan empat role pengguna: admin, operator, PML (Pengawas Mitra Lapangan), dan PCL (Pencacah Lapangan).

Enhancement ini bertujuan memperluas kapabilitas sistem secara komprehensif: menambahkan kemampuan offline-first, dashboard analitik real-time, validasi data otomatis berbasis aturan, integrasi pemetaan spasial, notifikasi WhatsApp, modul pelaporan mandiri responden, data dummy ekstensif untuk 31 kecamatan, serta automated testing suite — semuanya tetap kompatibel dengan infrastruktur shared hosting yang ada.

## Glosarium

- **SISE2026**: Sistem Informasi Sensus Ekonomi 2026 BPS Kabupaten Jember
- **BPS**: Badan Pusat Statistik
- **PCL**: Pencacah Lapangan — petugas yang melakukan wawancara langsung ke responden
- **PML**: Pengawas Mitra Lapangan — supervisor yang mengawasi PCL di wilayah tertentu
- **Operator**: Staf BPS yang mengelola data dan administrasi sistem
- **Admin**: Administrator sistem dengan akses penuh
- **Responden**: Pelaku usaha yang menjadi subjek pendataan sensus ekonomi
- **UMK**: Usaha Mikro dan Kecil
- **UM**: Usaha Menengah
- **UB**: Usaha Besar
- **KBLI**: Klasifikasi Baku Lapangan Usaha Indonesia
- **OSS**: Online Single Submission — sistem perizinan usaha terintegrasi pemerintah
- **NPWP**: Nomor Pokok Wajib Pajak
- **NIB**: Nomor Induk Berusaha
- **Anomali**: Data usaha yang terdeteksi memiliki inkonsistensi atau kejanggalan
- **Offline_Queue**: Antrian data yang dikumpulkan saat tidak ada koneksi internet
- **Sync_Engine**: Komponen yang menyinkronkan Offline_Queue ke server saat koneksi tersedia
- **Validator**: Komponen yang memeriksa integritas dan konsistensi data usaha
- **Dashboard**: Halaman visualisasi data real-time untuk monitoring progres sensus
- **Heatmap**: Visualisasi peta panas sebaran usaha per kecamatan
- **WhatsApp_Notifier**: Komponen pengirim notifikasi via WhatsApp Business API
- **Dummy_Generator**: Komponen pembuat data usaha sintetis untuk keperluan testing
- **Test_Suite**: Kumpulan automated test untuk validasi fungsionalitas sistem

---

## Persyaratan

### Persyaratan 1: Kemampuan Offline-First untuk Pengumpulan Data Lapangan

**User Story:** Sebagai PCL yang bekerja di area dengan koneksi internet buruk, saya ingin dapat mengisi dan menyimpan data usaha secara offline, sehingga pekerjaan lapangan tidak terganggu oleh ketidakstabilan jaringan.

#### Kriteria Penerimaan

1. WHEN seorang PCL mengakses formulir pendataan usaha tanpa koneksi internet, THE SISE2026 SHALL menyimpan data yang diisi ke Offline_Queue di penyimpanan lokal browser (IndexedDB).
2. WHEN koneksi internet tersedia kembali, THE Sync_Engine SHALL mengunggah semua entri di Offline_Queue ke server secara otomatis dalam urutan kronologis.
3. WHILE proses sinkronisasi berlangsung, THE SISE2026 SHALL menampilkan indikator progres sinkronisasi yang menunjukkan jumlah entri yang berhasil diunggah dari total entri yang antri.
4. IF sinkronisasi sebuah entri gagal karena konflik data duplikat, THEN THE Sync_Engine SHALL menandai entri tersebut dengan status "konflik" dan menampilkan notifikasi kepada PCL untuk resolusi manual.
5. THE SISE2026 SHALL menyimpan hingga 500 entri data usaha di Offline_Queue sebelum memerlukan sinkronisasi.
6. WHEN PCL membuka aplikasi dalam mode offline, THE SISE2026 SHALL menampilkan banner status offline yang jelas beserta jumlah entri yang belum tersinkronisasi.
7. IF data di Offline_Queue berusia lebih dari 7 hari tanpa sinkronisasi, THEN THE SISE2026 SHALL menampilkan peringatan kepada PCL bahwa data berisiko kedaluwarsa.

---

### Persyaratan 2: Dashboard Analitik Real-Time

**User Story:** Sebagai Admin atau Operator BPS, saya ingin melihat dashboard analitik real-time dengan visualisasi pertumbuhan UMKM dan progres pendataan per kecamatan, sehingga saya dapat memantau dan mengambil keputusan berbasis data secara cepat.

#### Kriteria Penerimaan

1. THE Dashboard SHALL menampilkan total usaha yang telah terdata, dikelompokkan berdasarkan kategori (UMK, UM, UB) dan diperbarui setiap 5 menit.
2. WHEN Admin atau Operator mengakses halaman dashboard, THE Dashboard SHALL menampilkan grafik batang progres pendataan per kecamatan dari 31 kecamatan di Kabupaten Jember.
3. THE Dashboard SHALL menampilkan grafik tren pertumbuhan jumlah usaha terdata per hari selama periode sensus aktif.
4. WHEN Admin memilih filter kecamatan tertentu, THE Dashboard SHALL memperbarui semua visualisasi untuk menampilkan data kecamatan yang dipilih dalam waktu kurang dari 3 detik.
5. THE Dashboard SHALL menampilkan ringkasan kinerja enumerator: jumlah usaha yang didata per PCL per hari, diurutkan dari tertinggi ke terendah.
6. WHERE fitur ekspor diaktifkan, THE Dashboard SHALL menghasilkan file CSV yang berisi data ringkasan yang sedang ditampilkan.
7. THE Dashboard SHALL menampilkan persentase capaian target per sektor KBLI (pertanian, perdagangan, jasa, manufaktur, dan lainnya).

---

### Persyaratan 3: Sistem Validasi Data Otomatis Berbasis Aturan

**User Story:** Sebagai Operator atau PML, saya ingin sistem secara otomatis mendeteksi data usaha yang anomali atau tidak konsisten, sehingga kualitas data sensus dapat terjaga tanpa pemeriksaan manual satu per satu.

#### Kriteria Penerimaan

1. WHEN data usaha baru disimpan ke sistem, THE Validator SHALL memeriksa konsistensi data berdasarkan aturan validasi yang telah dikonfigurasi dan menandai data yang tidak lolos sebagai anomali.
2. THE Validator SHALL mendeteksi data duplikat berdasarkan kombinasi nama usaha, alamat, dan nomor telepon yang identik atau memiliki kemiripan di atas 90%.
3. THE Validator SHALL memvalidasi format NPWP (15 digit numerik) dan menandai entri dengan NPWP yang tidak sesuai format sebagai anomali dengan kode "NPWP_INVALID".
4. THE Validator SHALL memvalidasi kelengkapan alamat dengan memeriksa keberadaan nama jalan, nomor, dan nama kecamatan; entri yang tidak memiliki salah satu komponen tersebut ditandai dengan kode "ALAMAT_TIDAK_LENGKAP".
5. WHEN Validator mendeteksi anomali, THE SISE2026 SHALL membuat entri baru di tabel anomaly dengan detail jenis anomali, kode anomali, dan ID data usaha yang bersangkutan.
6. THE Validator SHALL menghasilkan laporan ringkasan harian yang mencantumkan jumlah anomali per jenis, dapat diakses oleh Admin dan Operator.
7. IF data usaha yang sama dikirimkan oleh dua PCL berbeda dalam rentang 24 jam, THEN THE Validator SHALL menandai salah satu sebagai duplikat potensial dan memerlukan konfirmasi dari PML sebelum data disimpan.
8. THE Validator SHALL memeriksa konsistensi antara skala usaha yang dilaporkan (mikro/kecil/menengah/besar) dengan jumlah tenaga kerja yang diisi, berdasarkan definisi UU No. 20 Tahun 2008 tentang UMKM.

---

### Persyaratan 4: Integrasi Pemetaan Spasial dengan Heatmap

**User Story:** Sebagai Admin BPS, saya ingin melihat peta sebaran usaha per kecamatan dalam bentuk heatmap, sehingga saya dapat mengidentifikasi konsentrasi usaha dan area yang belum terjangkau pendataan.

#### Kriteria Penerimaan

1. THE SISE2026 SHALL menampilkan peta interaktif Kabupaten Jember yang menunjukkan sebaran usaha terdata menggunakan library Leaflet.js dengan tile map OpenStreetMap.
2. WHEN Admin mengakses halaman peta, THE SISE2026 SHALL merender heatmap yang menunjukkan kepadatan usaha per kecamatan berdasarkan data terkini dalam waktu kurang dari 5 detik.
3. THE SISE2026 SHALL menampilkan marker pada peta untuk setiap kecamatan yang menunjukkan: nama kecamatan, jumlah usaha terdata, dan persentase capaian target.
4. WHEN Admin mengklik marker kecamatan, THE SISE2026 SHALL menampilkan popup yang berisi rincian data usaha per sektor KBLI untuk kecamatan tersebut.
5. THE SISE2026 SHALL menyediakan filter pada peta berdasarkan sektor usaha (pertanian, perdagangan, jasa, manufaktur) dan skala usaha (UMK, UM, UB).
6. WHERE koordinat GPS tersedia pada data usaha, THE SISE2026 SHALL menampilkan titik lokasi usaha individual pada peta sebagai layer terpisah yang dapat diaktifkan/dinonaktifkan.

---

### Persyaratan 5: Sistem Notifikasi WhatsApp untuk Follow-up Responden

**User Story:** Sebagai PML atau Admin, saya ingin mengirimkan notifikasi WhatsApp kepada responden yang belum diwawancarai, sehingga tingkat respons pendataan dapat ditingkatkan secara efisien.

#### Kriteria Penerimaan

1. WHEN Admin atau PML memilih daftar responden yang belum diwawancarai dan mengklik "Kirim Notifikasi", THE WhatsApp_Notifier SHALL mengirimkan pesan WhatsApp ke nomor telepon responden menggunakan template pesan yang telah dikonfigurasi.
2. THE WhatsApp_Notifier SHALL mencatat status pengiriman setiap pesan (terkirim, gagal, dibaca) ke dalam tabel log notifikasi.
3. IF nomor telepon responden tidak valid atau tidak terdaftar di WhatsApp, THEN THE WhatsApp_Notifier SHALL menandai nomor tersebut sebagai tidak valid dan tidak mencoba pengiriman ulang otomatis.
4. THE SISE2026 SHALL membatasi pengiriman notifikasi WhatsApp maksimal 3 kali per responden per periode sensus untuk menghindari spam.
5. WHEN Admin mengakses halaman manajemen notifikasi, THE SISE2026 SHALL menampilkan ringkasan statistik pengiriman: total terkirim, total gagal, dan tingkat respons (persentase responden yang diwawancarai setelah menerima notifikasi).
6. THE WhatsApp_Notifier SHALL mendukung pengiriman pesan terjadwal, di mana Admin dapat mengatur waktu pengiriman batch notifikasi pada jam kerja (08.00–17.00 WIB).
7. WHERE integrasi WhatsApp Business API tidak tersedia, THE SISE2026 SHALL menyediakan fallback berupa ekspor daftar nomor telepon dan template pesan dalam format CSV untuk pengiriman manual.

---

### Persyaratan 6: Modul Pelaporan Mandiri Responden via Aplikasi Mobile

**User Story:** Sebagai responden (pelaku usaha), saya ingin dapat melaporkan data usaha saya secara mandiri melalui antarmuka mobile-friendly, sehingga proses pendataan dapat dilakukan tanpa harus menunggu kunjungan petugas.

#### Kriteria Penerimaan

1. THE SISE2026 SHALL menyediakan halaman formulir pelaporan mandiri yang dapat diakses melalui URL unik berbasis token yang dikirimkan kepada responden.
2. WHEN responden mengakses URL token yang valid, THE SISE2026 SHALL menampilkan formulir pendataan usaha yang telah terisi sebagian dengan data yang sudah diketahui sistem (nama usaha, alamat dari data awal).
3. THE SISE2026 SHALL memastikan formulir pelaporan mandiri dapat digunakan dengan baik pada layar smartphone dengan lebar minimum 320px.
4. WHEN responden mengirimkan formulir pelaporan mandiri, THE Validator SHALL menjalankan validasi data secara langsung dan menampilkan pesan kesalahan yang spesifik jika ada data yang tidak valid.
5. IF token akses responden telah digunakan atau telah kedaluwarsa (lebih dari 30 hari sejak diterbitkan), THEN THE SISE2026 SHALL menampilkan halaman informasi yang menjelaskan bahwa tautan tidak lagi valid dan menyediakan nomor kontak BPS Jember.
6. WHEN responden berhasil mengirimkan data, THE SISE2026 SHALL mengirimkan konfirmasi penerimaan melalui WhatsApp atau SMS ke nomor telepon responden.
7. THE SISE2026 SHALL mencatat sumber data setiap entri usaha (pelaporan mandiri atau kunjungan PCL) untuk keperluan audit kualitas data.

---

### Persyaratan 7: Pembuatan Data Dummy Ekstensif

**User Story:** Sebagai Developer atau QA Engineer, saya ingin tersedia data dummy yang realistis dan ekstensif untuk 31 kecamatan di Jember, sehingga pengujian fitur dan performa sistem dapat dilakukan secara komprehensif.

#### Kriteria Penerimaan

1. THE Dummy_Generator SHALL menghasilkan minimal 50.000 entri data usaha yang terdistribusi secara proporsional di 31 kecamatan Kabupaten Jember berdasarkan bobot populasi usaha per kecamatan.
2. THE Dummy_Generator SHALL menghasilkan data dengan variasi sektor usaha: pertanian (15%), perdagangan (40%), jasa (30%), dan manufaktur (15%).
3. THE Dummy_Generator SHALL menghasilkan data dengan variasi skala usaha: mikro (70%), kecil (20%), menengah (8%), dan besar (2%).
4. THE Dummy_Generator SHALL menyertakan edge case yang dapat digunakan untuk pengujian validasi: minimal 500 entri dengan NPWP tidak valid, 300 entri dengan alamat tidak lengkap, dan 200 entri data duplikat.
5. THE Dummy_Generator SHALL menghasilkan data historis untuk 3 tahun terakhir (2023, 2024, 2025) dengan total minimal 30.000 entri tambahan untuk pengujian fitur tren pertumbuhan.
6. THE Dummy_Generator SHALL menghasilkan data dalam format SQL INSERT yang dapat dieksekusi langsung pada database MySQL tanpa modifikasi.
7. WHEN Dummy_Generator dijalankan, THE SISE2026 SHALL menyelesaikan pembuatan 50.000 entri dalam waktu kurang dari 10 menit pada server dengan spesifikasi shared hosting standar.

---

### Persyaratan 8: Stress Testing dan Quality Assurance

**User Story:** Sebagai Tim QA BPS, saya ingin sistem telah melalui pengujian beban dan kualitas yang komprehensif, sehingga sistem dapat diandalkan saat digunakan oleh 100+ enumerator secara bersamaan selama periode sensus.

#### Kriteria Penerimaan

1. THE Test_Suite SHALL mencakup skenario stress test yang mensimulasikan 100 pengguna simultan yang melakukan operasi baca dan tulis data secara bersamaan selama 10 menit.
2. WHILE stress test dengan 100 pengguna simultan berlangsung, THE SISE2026 SHALL mempertahankan waktu respons rata-rata di bawah 3 detik untuk operasi baca data.
3. THE Test_Suite SHALL mencakup automated unit test dan integration test dengan code coverage minimal 80% untuk semua Controller dan Model yang ada.
4. THE Test_Suite SHALL mencakup test case untuk semua fitur baru yang ditambahkan dalam enhancement ini.
5. THE Test_Suite SHALL mencakup pengujian kompatibilitas yang memverifikasi tampilan dan fungsionalitas pada Chrome, Firefox, Safari, Edge, dan Opera versi terbaru.
6. THE Test_Suite SHALL mencakup pengujian pada Android versi 10, 11, dan 12 menggunakan browser Chrome Mobile.
7. IF sebuah test case gagal, THEN THE Test_Suite SHALL menghasilkan laporan yang mencantumkan nama test, input yang digunakan, output yang diharapkan, dan output aktual.
8. THE Test_Suite SHALL dapat dijalankan secara otomatis melalui satu perintah CLI tanpa konfigurasi tambahan.

---

### Persyaratan 9: Dokumentasi Teknis Komprehensif

**User Story:** Sebagai Developer atau Administrator Sistem, saya ingin tersedia dokumentasi teknis yang lengkap dan terkini, sehingga pemeliharaan, pengembangan lanjutan, dan onboarding developer baru dapat dilakukan dengan efisien.

#### Kriteria Penerimaan

1. THE SISE2026 SHALL menyediakan dokumentasi API specification dalam format OpenAPI 3.0 yang mencakup semua endpoint yang tersedia beserta contoh request dan response.
2. THE SISE2026 SHALL menyediakan ERD (Entity Relationship Diagram) yang mencerminkan skema database terkini setelah semua enhancement diterapkan.
3. THE SISE2026 SHALL menyediakan deployment guide yang mencakup langkah-langkah instalasi di shared hosting Jagoan Hosting, konfigurasi environment, dan prosedur rollback.
4. THE SISE2026 SHALL menyediakan change log yang mencatat setiap perubahan sistem dengan format: versi, tanggal, jenis perubahan (fitur baru/perbaikan/breaking change), dan deskripsi perubahan.
5. THE SISE2026 SHALL menyediakan bug report yang mendokumentasikan semua bug yang ditemukan selama pengujian beserta langkah reproduksi, severity, dan solusi yang diterapkan.
6. WHERE user manual diperlukan, THE SISE2026 SHALL menyediakan panduan pengguna dalam format PDF interaktif yang mencakup semua fitur baru dengan tangkapan layar.

---

### Persyaratan 10: Integrasi Data OSS (Online Single Submission)

**User Story:** Sebagai Admin BPS, saya ingin sistem dapat menyinkronkan data perizinan usaha dari OSS, sehingga data NIB dan status legalitas usaha dapat diverifikasi secara otomatis tanpa input manual.

#### Kriteria Penerimaan

1. WHEN Admin memicu sinkronisasi OSS untuk sebuah entri usaha, THE SISE2026 SHALL mengirimkan permintaan ke OSS API menggunakan NIB atau NPWP sebagai kunci pencarian.
2. WHEN OSS API mengembalikan data perizinan yang valid, THE SISE2026 SHALL memperbarui field status legalitas, nama usaha resmi, dan alamat usaha sesuai data OSS, serta mencatat timestamp sinkronisasi.
3. IF OSS API tidak dapat dijangkau atau mengembalikan error, THEN THE SISE2026 SHALL mencatat kegagalan sinkronisasi ke log dan menampilkan pesan error yang informatif kepada Admin tanpa mengubah data yang sudah ada.
4. THE SISE2026 SHALL menyimpan riwayat sinkronisasi OSS per entri usaha, mencakup tanggal sinkronisasi, status (berhasil/gagal), dan data yang diperbarui.
5. THE SISE2026 SHALL mendukung sinkronisasi batch untuk hingga 100 entri usaha sekaligus, dengan progres yang ditampilkan secara real-time kepada Admin.
6. WHERE NIB tidak tersedia pada data usaha, THE SISE2026 SHALL menandai entri tersebut sebagai "belum terverifikasi OSS" dan menyertakannya dalam laporan kelengkapan data.
