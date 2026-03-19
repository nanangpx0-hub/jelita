-- ============================================================================
-- SEED DUMMY DATA SISE2026 BPS KABUPATEN JEMBER
-- Import setelah sql/schema.sql agar seluruh modul utama memiliki data uji.
-- Semua akun demo memakai password: DemoSE2026!
-- ============================================================================

START TRANSACTION;

-- Instalasi lama pernah tidak memiliki tiga tabel ini. Seed menjaga migrasi demo tetap mulus.
CREATE TABLE IF NOT EXISTS lowongan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posisi VARCHAR(150) NOT NULL,
    wilayah VARCHAR(150) NOT NULL,
    tipe ENUM('PCL','PML') NOT NULL,
    kuota INT NOT NULL DEFAULT 0,
    deadline DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pendaftaran_petugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(150) NOT NULL,
    nik VARCHAR(16) NOT NULL,
    email VARCHAR(150) NOT NULL,
    no_hp VARCHAR(30) NOT NULL,
    alamat TEXT NOT NULL,
    posisi ENUM('PCL','PML') NOT NULL,
    wilayah VARCHAR(150) NOT NULL,
    dok_ktp VARCHAR(255),
    dok_ijazah VARCHAR(255),
    dok_foto VARCHAR(255),
    status ENUM('pending','verified','rejected','accepted') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unik_nik_email (nik, email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS materi_pelatihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(250) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    tipe VARCHAR(20) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT DEFAULT 0,
    downloads INT DEFAULT 0,
    is_published BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS memorandum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor VARCHAR(100),
    tipe ENUM('memo','undangan') DEFAULT 'memo',
    judul VARCHAR(200) NOT NULL,
    konten TEXT,
    tanggal DATE NOT NULL,
    waktu TIME,
    tempat VARCHAR(200),
    distribusi_email BOOLEAN DEFAULT FALSE,
    distribusi_sms BOOLEAN DEFAULT FALSE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS konfirmasi_kehadiran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    memorandum_id INT,
    user_id INT,
    status ENUM('pending','hadir','tidak_hadir') DEFAULT 'pending',
    responded_at TIMESTAMP NULL,
    FOREIGN KEY (memorandum_id) REFERENCES memorandum(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dokumentasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    kategori ENUM('pelatihan_online','pelatihan_offline','rapat','foto_kegiatan') NOT NULL,
    deskripsi TEXT,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(10),
    thumbnail VARCHAR(255),
    tanggal DATE,
    tags TEXT,
    watermark BOOLEAN DEFAULT FALSE,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Akun demo disamakan dengan kredensial yang ditampilkan di halaman login.
INSERT INTO users (id, nip, username, password_hash, nama_lengkap, email, no_hp, role, is_active)
VALUES
    (1, '197901012006041001', 'admin', '$2y$10$s7IZyQCj25/V0rw8MM.6uOngY4eISUL2JqLjsbq5C19O3JoxN5.Mi', 'Administrator SE2026', 'admin@bpsjember.go.id', '081130000001', 'admin', 1),
    (2, '198402142009021002', 'operator.jember', '$2y$10$s7IZyQCj25/V0rw8MM.6uOngY4eISUL2JqLjsbq5C19O3JoxN5.Mi', 'Operator Lapangan Jember', 'operator@bpsjember.go.id', '081130000002', 'operator', 1),
    (3, '198911302011121003', 'pml.kaliwates', '$2y$10$s7IZyQCj25/V0rw8MM.6uOngY4eISUL2JqLjsbq5C19O3JoxN5.Mi', 'Koordinator PML Kaliwates', 'pml.kaliwates@bpsjember.go.id', '081130000003', 'pml', 1),
    (4, '199604052024011004', 'pcl.sumbersari', '$2y$10$s7IZyQCj25/V0rw8MM.6uOngY4eISUL2JqLjsbq5C19O3JoxN5.Mi', 'Petugas PCL Sumbersari', 'pcl.sumbersari@bpsjember.go.id', '081130000004', 'pcl', 1)
ON DUPLICATE KEY UPDATE
    nip = VALUES(nip),
    password_hash = VALUES(password_hash),
    nama_lengkap = VALUES(nama_lengkap),
    email = VALUES(email),
    no_hp = VALUES(no_hp),
    role = VALUES(role),
    is_active = VALUES(is_active);

-- Keterisian dibuat campuran agar dashboard dan peta kebutuhan mengandung kondisi penuh, aman, dan kritis.
UPDATE wilayah_kerja
SET
    terisi_pcl = CASE nama_kecamatan
        WHEN 'Sumbersari' THEN 18
        WHEN 'Kaliwates' THEN 16
        WHEN 'Patrang' THEN 11
        WHEN 'Silo' THEN 3
        WHEN 'Tempurejo' THEN 0
        WHEN 'Puger' THEN 14
        WHEN 'Tanggul' THEN 7
        WHEN 'Kalisat' THEN 12
        ELSE terisi_pcl
    END,
    terisi_pml = CASE nama_kecamatan
        WHEN 'Sumbersari' THEN 4
        WHEN 'Kaliwates' THEN 4
        WHEN 'Patrang' THEN 3
        WHEN 'Silo' THEN 1
        WHEN 'Tempurejo' THEN 0
        WHEN 'Puger' THEN 3
        WHEN 'Tanggul' THEN 2
        WHEN 'Kalisat' THEN 2
        ELSE terisi_pml
    END;

-- Lowongan memuat kuota besar, kuota kecil, dan tenggat yang sangat dekat.
INSERT INTO lowongan (id, posisi, wilayah, tipe, kuota, deadline)
VALUES
    (1, 'Petugas Pencacah Lapangan (PCL)', 'Kecamatan Sumbersari', 'PCL', 24, '2026-03-28'),
    (2, 'Petugas Pemeriksa Lapangan (PML)', 'Kecamatan Patrang', 'PML', 6, '2026-03-26'),
    (3, 'Petugas Pencacah Lapangan (PCL)', 'Kecamatan Kaliwates', 'PCL', 18, '2026-03-30'),
    (4, 'Petugas Pencacah Lapangan (PCL)', 'Kecamatan Tempurejo', 'PCL', 1, '2026-03-21')
ON DUPLICATE KEY UPDATE
    posisi = VALUES(posisi),
    wilayah = VALUES(wilayah),
    tipe = VALUES(tipe),
    kuota = VALUES(kuota),
    deadline = VALUES(deadline);

-- Status pendaftaran mencakup pending, verified, rejected, dan accepted untuk menguji badge serta lookup.
INSERT INTO pendaftaran_petugas (id, nama_lengkap, nik, email, no_hp, alamat, posisi, wilayah, dok_ktp, dok_ijazah, dok_foto, status, catatan)
VALUES
    (1, 'Ayu Nabila Putri', '3509305501010001', 'ayu.nabila@example.id', '081233445566', 'Jl. Mastrip No. 14, Sumbersari, Jember', 'PCL', 'Sumbersari', 'pendaftaran/ayu_ktp.pdf', 'pendaftaran/ayu_ijazah.pdf', 'pendaftaran/ayu_foto.jpg', 'pending', NULL),
    (2, 'Rizky Fadilah', '3509291209980002', 'rizky.fadilah@example.id', '082145678901', 'Perumahan Bumi Tegal Besar, Kaliwates, Jember', 'PML', 'Kaliwates', 'pendaftaran/rizky_ktp.pdf', 'pendaftaran/rizky_ijazah.pdf', 'pendaftaran/rizky_foto.jpg', 'verified', 'Semua dokumen sesuai dan siap mengikuti CBT.'),
    (3, 'Siti Rahmawati', '3509074309970003', 'siti.rahmawati@example.id', '085733210987', 'Dusun Krajan, Silo, Jember', 'PCL', 'Silo', 'pendaftaran/siti_ktp.pdf', 'pendaftaran/siti_ijazah.pdf', 'pendaftaran/siti_foto.jpg', 'rejected', 'Foto KTP buram dan perlu unggah ulang.'),
    (4, 'Bagus Prasetyo', '3509060101960004', 'bagus.prasetyo@example.id', '081998877665', 'Dusun Curah Nongko, Tempurejo, Jember', 'PCL', 'Tempurejo', 'pendaftaran/bagus_ktp.pdf', 'pendaftaran/bagus_ijazah.pdf', 'pendaftaran/bagus_foto.jpg', 'accepted', 'Diterima untuk wilayah blank spot dan siap penugasan cepat.')
ON DUPLICATE KEY UPDATE
    nama_lengkap = VALUES(nama_lengkap),
    email = VALUES(email),
    no_hp = VALUES(no_hp),
    alamat = VALUES(alamat),
    posisi = VALUES(posisi),
    wilayah = VALUES(wilayah),
    dok_ktp = VALUES(dok_ktp),
    dok_ijazah = VALUES(dok_ijazah),
    dok_foto = VALUES(dok_foto),
    status = VALUES(status),
    catatan = VALUES(catatan);

-- Tabel legacy juga diisi agar instalasi lama tetap bisa diuji tanpa migrasi penuh.
INSERT INTO pendaftaran (id, nama_lengkap, nik, email, no_hp, alamat, posisi_dilamar, wilayah_id, status, catatan)
VALUES
    (101, 'Ayu Nabila Putri', '3509305501010001', 'ayu.nabila@example.id', '081233445566', 'Jl. Mastrip No. 14, Sumbersari, Jember', 'PCL', (SELECT id FROM wilayah_kerja WHERE nama_kecamatan = 'Sumbersari' LIMIT 1), 'pending', NULL),
    (102, 'Rizky Fadilah', '3509291209980002', 'rizky.fadilah@example.id', '082145678901', 'Perumahan Bumi Tegal Besar, Kaliwates, Jember', 'PML', (SELECT id FROM wilayah_kerja WHERE nama_kecamatan = 'Kaliwates' LIMIT 1), 'verified', 'Semua dokumen sesuai dan siap mengikuti CBT.'),
    (103, 'Siti Rahmawati', '3509074309970003', 'siti.rahmawati@example.id', '085733210987', 'Dusun Krajan, Silo, Jember', 'PCL', (SELECT id FROM wilayah_kerja WHERE nama_kecamatan = 'Silo' LIMIT 1), 'rejected', 'Foto KTP buram dan perlu unggah ulang.'),
    (104, 'Bagus Prasetyo', '3509060101960004', 'bagus.prasetyo@example.id', '081998877665', 'Dusun Curah Nongko, Tempurejo, Jember', 'PCL', (SELECT id FROM wilayah_kerja WHERE nama_kecamatan = 'Tempurejo' LIMIT 1), 'accepted', 'Diterima untuk wilayah blank spot dan siap penugasan cepat.')
ON DUPLICATE KEY UPDATE
    nama_lengkap = VALUES(nama_lengkap),
    email = VALUES(email),
    no_hp = VALUES(no_hp),
    alamat = VALUES(alamat),
    posisi_dilamar = VALUES(posisi_dilamar),
    wilayah_id = VALUES(wilayah_id),
    status = VALUES(status),
    catatan = VALUES(catatan);

-- Satu pengumuman tidak dipublikasikan untuk menguji query yang seharusnya memfilter data publik.
INSERT INTO pengumuman (id, judul, konten, tipe, file_lampiran, is_published, published_at, created_by)
VALUES
    (1, 'Pengumuman Hasil Seleksi Administrasi PCL Gelombang 1', 'Peserta yang lolos seleksi administrasi wajib mengikuti CBT sesuai jadwal terlampir.', 'hasil_seleksi', 'pengumuman_hasil_administrasi_pcl_gel1.pdf', 1, '2026-03-18 09:00:00', 1),
    (2, 'Jadwal Pelatihan Petugas SE2026 Gelombang 1', 'Pelatihan dilaksanakan secara hybrid dengan sesi daring dan luring terpisah.', 'jadwal', 'jadwal_pelatihan_petugas_gel1.pdf', 1, '2026-03-19 07:30:00', 1),
    (3, 'Informasi Pembaruan Persyaratan Unggah Dokumen', 'Ukuran berkas maksimal 5 MB dan dokumen buram akan diminta revisi.', 'info', NULL, 1, '2026-03-14 16:45:00', 2),
    (4, 'Draft Penyesuaian Jadwal Wawancara Tempurejo', 'Masih menunggu konfirmasi ruang dan tidak boleh tampil di kanal publik.', 'jadwal', NULL, 0, NULL, 2)
ON DUPLICATE KEY UPDATE
    judul = VALUES(judul),
    konten = VALUES(konten),
    tipe = VALUES(tipe),
    file_lampiran = VALUES(file_lampiran),
    is_published = VALUES(is_published),
    published_at = VALUES(published_at),
    created_by = VALUES(created_by);

INSERT INTO pelatihan (id, judul, tipe, tanggal_mulai, tanggal_selesai, waktu_mulai, waktu_selesai, tempat, deskripsi, zoom_link, status, created_by)
VALUES
    (1, 'Pelatihan CAPI SE2026 Gelombang 1', 'online', '2026-04-15', '2026-04-15', '08:00:00', '11:30:00', 'Zoom Meeting BPS Jember', 'Pengenalan alur login, listing, dan sinkronisasi CAPI.', 'https://zoom.example.id/sise2026-gel1', 'scheduled', 2),
    (2, 'Klinik Validasi Anomali Lapangan', 'online', '2026-04-18', NULL, '13:00:00', '15:00:00', 'Ruang Virtual QA', 'Sesi tanya jawab untuk kasus duplikasi dan usaha tutup.', NULL, 'scheduled', 2),
    (3, 'Classroom Training PCL Gelombang 1', 'offline', '2026-04-20', '2026-04-21', '08:00:00', '16:00:00', 'Aula BPS Kabupaten Jember', 'Simulasi wawancara dan role play kunjungan usaha.', NULL, 'scheduled', 3),
    (4, 'Briefing Monitoring Mingguan Korwil', 'offline', '2026-03-12', '2026-03-12', '09:00:00', '10:30:00', 'Ruang Rapat Lt. 2', 'Review progres mingguan dan distribusi tindak lanjut.', NULL, 'completed', 1)
ON DUPLICATE KEY UPDATE
    judul = VALUES(judul),
    tipe = VALUES(tipe),
    tanggal_mulai = VALUES(tanggal_mulai),
    tanggal_selesai = VALUES(tanggal_selesai),
    waktu_mulai = VALUES(waktu_mulai),
    waktu_selesai = VALUES(waktu_selesai),
    tempat = VALUES(tempat),
    deskripsi = VALUES(deskripsi),
    zoom_link = VALUES(zoom_link),
    status = VALUES(status),
    created_by = VALUES(created_by);

-- Ada pertanyaan yang belum dijawab agar fallback badge dan blok jawaban kosong ikut teruji.
INSERT INTO qna_pelatihan (id, pelatihan_id, user_id, pertanyaan, jawaban, votes, is_moderated)
VALUES
    (1, 1, 4, 'Apakah sinkronisasi bisa dilakukan saat koneksi putus-putus?', 'Bisa. Data akan masuk antrean lokal dan dikirim ulang saat perangkat kembali online.', 7, 1),
    (2, 1, 3, 'Bagaimana jika usaha tutup permanen tetapi alamat masih aktif di daftar?', NULL, 3, 1),
    (3, 2, 2, 'Apakah kasus duplikasi lintas blok sensus harus dilaporkan dua kali?', 'Tidak. Laporkan satu kali dengan referensi blok terkait di deskripsi anomali.', 5, 1),
    (4, 2, 4, 'Jika link Zoom kosong apakah materi rekaman tetap dibagikan?', 'Ya. Tautan rekaman akan dikirim setelah sesi selesai melalui pengumuman pelatihan.', 1, 1)
ON DUPLICATE KEY UPDATE
    pelatihan_id = VALUES(pelatihan_id),
    user_id = VALUES(user_id),
    pertanyaan = VALUES(pertanyaan),
    jawaban = VALUES(jawaban),
    votes = VALUES(votes),
    is_moderated = VALUES(is_moderated);

-- Materi mencakup PDF, spreadsheet, slide, dan video agar ikon serta validasi tipe file bisa diuji.
INSERT INTO materi_pelatihan (id, judul, kategori, tipe, file_path, file_size, downloads, is_published, created_by)
VALUES
    (1, 'Pedoman Pencacahan SE2026', 'Pedoman', 'PDF', 'pedoman_pencacahan_se2026.pdf', 4200000, 856, 1, 1),
    (2, 'Template Quality Control Anomali', 'Template', 'XLSX', 'template_qc_anomali_se2026.xlsx', 248000, 91, 1, 2),
    (3, 'Bahan Tayang KBLI 2020 untuk PML', 'Pelatihan', 'PPTX', 'bahan_tayang_kbli_2020_pml.pptx', 3100000, 134, 1, 2),
    (4, 'Video Simulasi Wawancara Responden Sulit', 'Video', 'MP4', 'video_simulasi_wawancara_responden_sulit.mp4', 15800000, 47, 1, 3)
ON DUPLICATE KEY UPDATE
    judul = VALUES(judul),
    kategori = VALUES(kategori),
    tipe = VALUES(tipe),
    file_path = VALUES(file_path),
    file_size = VALUES(file_size),
    downloads = VALUES(downloads),
    is_published = VALUES(is_published),
    created_by = VALUES(created_by);

-- Status anomali dibuat lengkap agar ringkasan dashboard memuat semua badge workflow.
INSERT INTO anomaly (id, judul, deskripsi, jenis, wilayah, pelapor_id, status, tindak_lanjut, resolved_by, resolved_at)
VALUES
    (1, 'SLS 004 tidak ditemukan di lapangan', 'Koordinat blok sensus tidak sesuai peta cetak.', 'wilayah', 'Kec. Silo', 4, 'reported', NULL, NULL, NULL),
    (2, 'Duplikasi usaha kuliner pada dua blok sensus', 'Usaha tercatat dua kali pada blok bertetangga.', 'duplikasi', 'Kec. Kaliwates', 3, 'review', 'Menunggu verifikasi lapangan ulang oleh PML.', NULL, NULL),
    (3, 'Usaha besar belum memiliki KBLI rinci', 'Perlu klarifikasi aktivitas utama agar klasifikasi tepat.', 'klasifikasi', 'Kec. Patrang', 2, 'resolved', 'KBLI diperbarui setelah konsultasi dengan penanggung jawab usaha.', 2, '2026-05-12 14:10:00'),
    (4, 'Laporan bukti tidak lengkap untuk usaha musiman', 'Foto lokasi tidak menunjukkan plang usaha.', 'bukti', 'Kec. Tempurejo', 4, 'rejected', 'Pelapor diminta unggah ulang bukti visual.', 1, '2026-05-13 09:00:00')
ON DUPLICATE KEY UPDATE
    judul = VALUES(judul),
    deskripsi = VALUES(deskripsi),
    jenis = VALUES(jenis),
    wilayah = VALUES(wilayah),
    pelapor_id = VALUES(pelapor_id),
    status = VALUES(status),
    tindak_lanjut = VALUES(tindak_lanjut),
    resolved_by = VALUES(resolved_by),
    resolved_at = VALUES(resolved_at);

INSERT INTO surat_keputusan (id, nomor_sk, judul, tanggal_sk, file_path, status, created_by)
VALUES
    (1, 'SK/001/SE2026/2026', 'Penetapan Panitia Pelaksana SE2026', '2026-01-15', 'sk_panitia_pelaksana_2026.pdf', 'published', 1),
    (2, 'SK/002/SE2026/2026', 'Pengangkatan PCL dan PML SE2026', '2026-02-01', 'sk_pengangkatan_petugas_2026.pdf', 'draft', 1)
ON DUPLICATE KEY UPDATE
    judul = VALUES(judul),
    tanggal_sk = VALUES(tanggal_sk),
    file_path = VALUES(file_path),
    status = VALUES(status),
    created_by = VALUES(created_by);

INSERT INTO surat_masuk (id, nomor_surat, nomor_agenda, tanggal_surat, tanggal_terima, pengirim, perihal, file_path, status, created_by)
VALUES
    (1, 'SM/001/2026', 'AGENDA-001', '2026-01-08', '2026-01-10', 'BPS Provinsi Jawa Timur', 'Pedoman Pelaksanaan SE2026', 'surat_masuk_pedoman_se2026.pdf', 'selesai', 1),
    (2, 'SM/014/2026', 'AGENDA-014', '2026-03-15', '2026-03-16', 'Bagian Pemerintahan Kabupaten Jember', 'Permintaan Data Pendukung Sosialisasi', 'surat_masuk_permohonan_data.pdf', 'proses', 2),
    (3, 'SM/017/2026', 'AGENDA-017', '2026-03-18', '2026-03-18', 'Kecamatan Silo', 'Permintaan dukungan jaringan untuk pelatihan daring wilayah blank spot', NULL, 'baru', 2)
ON DUPLICATE KEY UPDATE
    nomor_agenda = VALUES(nomor_agenda),
    tanggal_surat = VALUES(tanggal_surat),
    tanggal_terima = VALUES(tanggal_terima),
    pengirim = VALUES(pengirim),
    perihal = VALUES(perihal),
    file_path = VALUES(file_path),
    status = VALUES(status),
    created_by = VALUES(created_by);

INSERT INTO surat_keluar (id, nomor_surat, tanggal_surat, tujuan, perihal, file_path, status, created_by)
VALUES
    (1, 'SK/001/OUT/2026', '2026-01-20', 'Seluruh Kecamatan', 'Sosialisasi SE2026', 'surat_keluar_sosialisasi_se2026.pdf', 'sent', 1),
    (2, 'SK/022/OUT/2026', '2026-03-18', 'Kecamatan Tempurejo', 'Permintaan Klarifikasi Wilayah Blank Spot', 'surat_keluar_klarifikasi_blank_spot.pdf', 'draft', 2),
    (3, 'SK/024/OUT/2026', '2026-03-20', 'BPS Provinsi Jawa Timur', 'Laporan percepatan pengisian petugas wilayah kritis', NULL, 'archived', 1)
ON DUPLICATE KEY UPDATE
    tanggal_surat = VALUES(tanggal_surat),
    tujuan = VALUES(tujuan),
    perihal = VALUES(perihal),
    file_path = VALUES(file_path),
    status = VALUES(status),
    created_by = VALUES(created_by);

-- Memorandum disediakan langsung di DB agar modul teknis tidak lagi bergantung penuh pada fixture in-memory.
INSERT INTO memorandum (id, nomor, tipe, judul, konten, tanggal, waktu, tempat, distribusi_email, distribusi_sms, created_by)
VALUES
    (1, 'MEMO/001/2026', 'undangan', 'Rapat Koordinasi Tim SE2026', 'Undangan rapat koordinasi persiapan operasional mingguan bersama seluruh koordinator lapangan.', '2026-03-20', '09:00:00', 'Aula BPS Jember', 1, 1, 1),
    (2, 'MEMO/002/2026', 'memo', 'Memorandum Kesiapan Peralatan Pencacahan', 'Mohon seluruh korwil memastikan perangkat, charger, dan kartu identitas siap sebelum distribusi lapangan.', '2026-03-18', NULL, NULL, 1, 0, 1),
    (3, 'MEMO/003/2026', 'undangan', 'Undangan Sosialisasi SE2026 ke Kecamatan', 'Agenda sosialisasi lintas kecamatan untuk wilayah dengan kebutuhan petugas tertinggi.', '2026-03-25', '10:00:00', 'Pendopo Kabupaten', 1, 1, 2),
    (4, 'MEMO/004/2026', 'memo', 'Catatan Perbaikan Daftar Usaha Musiman', 'Instruksi revisi daftar usaha musiman yang belum memiliki bukti foto memadai.', '2026-03-27', NULL, NULL, 0, 1, 2)
ON DUPLICATE KEY UPDATE
    nomor = VALUES(nomor),
    tipe = VALUES(tipe),
    judul = VALUES(judul),
    konten = VALUES(konten),
    tanggal = VALUES(tanggal),
    waktu = VALUES(waktu),
    tempat = VALUES(tempat),
    distribusi_email = VALUES(distribusi_email),
    distribusi_sms = VALUES(distribusi_sms),
    created_by = VALUES(created_by);

INSERT INTO konfirmasi_kehadiran (id, memorandum_id, user_id, status, responded_at)
VALUES
    (1, 1, 2, 'hadir', '2026-03-18 08:00:00'),
    (2, 1, 3, 'hadir', '2026-03-18 08:10:00'),
    (3, 1, 4, 'pending', NULL),
    (4, 3, 2, 'hadir', '2026-03-22 11:30:00'),
    (5, 3, 3, 'tidak_hadir', '2026-03-22 11:45:00'),
    (6, 3, 4, 'pending', NULL)
ON DUPLICATE KEY UPDATE
    memorandum_id = VALUES(memorandum_id),
    user_id = VALUES(user_id),
    status = VALUES(status),
    responded_at = VALUES(responded_at);

INSERT INTO dokumentasi (id, judul, kategori, deskripsi, file_path, file_type, thumbnail, tanggal, tags, watermark, uploaded_by)
VALUES
    (1, 'Rekaman Pelatihan CAPI Gelombang 1', 'pelatihan_online', 'Rekaman sesi daring pembukaan dan pengenalan workflow CAPI.', 'dokumentasi_pelatihan_online_gel1.mp4', 'MP4', NULL, '2026-04-15', '["capi","gelombang-1","zoom"]', 0, 2),
    (2, 'Klinik Validasi Anomali Lapangan', 'pelatihan_online', 'Video sesi tanya jawab mengenai duplikasi usaha dan KBLI.', 'dokumentasi_klinik_anomali_online.mp4', 'MP4', NULL, '2026-04-18', '["anomali","kbli","qna"]', 0, 2),
    (3, 'Album Classroom Training PCL Gelombang 1', 'pelatihan_offline', 'Dokumentasi visual sesi role play dan simulasi wawancara responden.', 'dokumentasi_pelatihan_offline_album1.jpg', 'JPG', NULL, '2026-04-20', '["offline","pcl","album"]', 0, 3),
    (4, 'Notulen Rakor Persiapan Lapangan SE2026', 'rapat', 'Notulen rapat koordinasi lintas tim menjelang distribusi wilayah kerja.', 'dokumentasi_rapat_rakor_se2026.pdf', 'PDF', NULL, '2026-03-10', '["rapat","notulen","persiapan"]', 0, 1),
    (5, 'Foto Monitoring Wilayah Tempurejo', 'foto_kegiatan', 'Foto lapangan untuk monitoring wilayah blank spot dan kesiapan petugas.', 'dokumentasi_foto_monitoring_tempurejo.jpg', 'JPG', NULL, '2026-05-03', '["monitoring","tempurejo","lapangan"]', 1, 2),
    (6, 'Foto Sosialisasi Kecamatan Sumbersari', 'foto_kegiatan', 'Dokumentasi kegiatan sosialisasi dan koordinasi awal dengan aparat wilayah.', 'dokumentasi_foto_sosialisasi_sumbersari.jpg', 'JPG', NULL, '2026-03-05', '["sosialisasi","sumbersari"]', 1, 1)
ON DUPLICATE KEY UPDATE
    judul = VALUES(judul),
    kategori = VALUES(kategori),
    deskripsi = VALUES(deskripsi),
    file_path = VALUES(file_path),
    file_type = VALUES(file_type),
    thumbnail = VALUES(thumbnail),
    tanggal = VALUES(tanggal),
    tags = VALUES(tags),
    watermark = VALUES(watermark),
    uploaded_by = VALUES(uploaded_by);

COMMIT;
