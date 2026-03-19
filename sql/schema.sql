-- =========================================================================
-- SCHEMA DATABASE SISE2026 BPS KABUPATEN JEMBER
-- MySQL / MariaDB Compatible
-- =========================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. USERS & AUTHENTICATION
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) UNIQUE,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    role ENUM('admin','operator','pml','pcl') NOT NULL DEFAULT 'pcl',
    foto VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. REKRUTMEN
-- Tabel lowongan dipertahankan untuk kompatibilitas halaman legacy views/rekrutmen.php.
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

-- WARNING: The following table is included for backward compatibility
-- (export dump includes this table, but application uses pendaftaran_petugas).
CREATE TABLE IF NOT EXISTS pendaftaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    nik VARCHAR(16) UNIQUE NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(20),
    alamat TEXT,
    posisi_dilamar ENUM('PCL','PML') DEFAULT 'PCL',
    wilayah_id INT,
    status ENUM('pending','verified','rejected','accepted') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dokumen_persyaratan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pendaftaran_id INT,
    jenis_dokumen VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran_petugas(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jadwal_seleksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    lokasi VARCHAR(200),
    tipe VARCHAR(30) DEFAULT 'administrasi',
    status ENUM('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wilayah_kerja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_kecamatan VARCHAR(10) NOT NULL,
    nama_kecamatan VARCHAR(100) NOT NULL,
    kebutuhan_pcl INT DEFAULT 0,
    kebutuhan_pml INT DEFAULT 0,
    terisi_pcl INT DEFAULT 0,
    terisi_pml INT DEFAULT 0,
    lat DECIMAL(10,7),
    lng DECIMAL(10,7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    konten TEXT,
    tipe ENUM('umum','hasil_seleksi','jadwal','info') DEFAULT 'umum',
    file_lampiran VARCHAR(255),
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 2.b MATERI PELATIHAN
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

-- 3. TEKNIS & ADMINISTRASI
CREATE TABLE IF NOT EXISTS surat_keputusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_sk VARCHAR(100) UNIQUE NOT NULL,
    judul VARCHAR(200) NOT NULL,
    tanggal_sk DATE NOT NULL,
    file_path VARCHAR(255),
    status ENUM('draft','published','archived') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) NOT NULL,
    nomor_agenda VARCHAR(50),
    tanggal_surat DATE NOT NULL,
    tanggal_terima DATE NOT NULL,
    pengirim VARCHAR(200) NOT NULL,
    perihal VARCHAR(300) NOT NULL,
    file_path VARCHAR(255),
    disposisi_ke INT,
    catatan_disposisi TEXT,
    status ENUM('baru','disposisi','proses','selesai') DEFAULT 'baru',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (disposisi_ke) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) UNIQUE NOT NULL,
    tanggal_surat DATE NOT NULL,
    tujuan VARCHAR(200) NOT NULL,
    perihal VARCHAR(300) NOT NULL,
    file_path VARCHAR(255),
    tanda_tangan_digital VARCHAR(255),
    status ENUM('draft','sent','archived') DEFAULT 'draft',
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

CREATE TABLE IF NOT EXISTS laporan_kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    tanggal_kegiatan DATE NOT NULL,
    lokasi VARCHAR(200),
    uraian TEXT,
    hasil TEXT,
    foto_lampiran TEXT, -- JSON array of file paths
    status ENUM('draft','submitted','approved','revision') DEFAULT 'draft',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notulen_rapat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    tanggal_rapat DATE NOT NULL,
    waktu_mulai TIME,
    waktu_selesai TIME,
    tempat VARCHAR(200),
    pimpinan_rapat VARCHAR(100),
    peserta TEXT, -- JSON array
    agenda TEXT,
    pembahasan TEXT,
    keputusan TEXT,
    tindak_lanjut TEXT, -- JSON array of assignments
    file_lampiran VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. PELATIHAN
CREATE TABLE IF NOT EXISTS pelatihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    tipe ENUM('online','offline') NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    waktu_mulai TIME,
    waktu_selesai TIME,
    tempat VARCHAR(200),
    deskripsi TEXT,
    zoom_link VARCHAR(500),
    zoom_meeting_id VARCHAR(50),
    zoom_passcode VARCHAR(50),
    video_rekaman VARCHAR(500),
    transkrip TEXT,
    status ENUM('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS presensi_pelatihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelatihan_id INT,
    user_id INT,
    waktu_checkin TIMESTAMP NULL,
    metode ENUM('manual','qrcode','link') DEFAULT 'manual',
    keterangan VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(pelatihan_id, user_id),
    FOREIGN KEY (pelatihan_id) REFERENCES pelatihan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS qna_pelatihan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelatihan_id INT,
    user_id INT,
    pertanyaan TEXT NOT NULL,
    jawaban TEXT,
    answered_by INT,
    votes INT DEFAULT 0,
    is_moderated BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelatihan_id) REFERENCES pelatihan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (answered_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS materi_bahan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    kategori VARCHAR(50),
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(10), -- pdf, pptx, xlsx, etc
    file_size INT,
    akses_role TEXT NULL, -- Removed default 'all' (Error 1101)
    download_count INT DEFAULT 0,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. PELAKSANAAN
CREATE TABLE IF NOT EXISTS surat_tugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) UNIQUE NOT NULL,
    judul VARCHAR(200) NOT NULL,
    tanggal DATE NOT NULL,
    petugas_id INT,
    wilayah VARCHAR(200),
    qr_code VARCHAR(255),
    tanda_tangan_digital VARCHAR(255),
    status ENUM('active','expired','revoked') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS visum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surat_tugas_id INT,
    petugas_id INT,
    tanggal DATE NOT NULL,
    hasil TEXT,
    file_path VARCHAR(255),
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    approved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (surat_tugas_id) REFERENCES surat_tugas(id) ON DELETE SET NULL,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jadwal_pertemuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    tanggal DATE NOT NULL,
    waktu_mulai TIME,
    waktu_selesai TIME,
    tempat VARCHAR(200),
    deskripsi TEXT,
    google_calendar_id VARCHAR(255),
    reminder_sent BOOLEAN DEFAULT FALSE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. PENGOLAHAN
CREATE TABLE IF NOT EXISTS anomaly (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    jenis VARCHAR(50),
    wilayah VARCHAR(200),
    pelapor_id INT,
    file_bukti VARCHAR(255),
    status ENUM('reported','review','resolved','rejected') DEFAULT 'reported',
    tindak_lanjut TEXT,
    resolved_by INT,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelapor_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS monitoring_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wilayah_id INT,
    tanggal DATE NOT NULL,
    target INT DEFAULT 0,
    realisasi INT DEFAULT 0,
    persentase DECIMAL(5,2) DEFAULT 0,
    catatan TEXT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wilayah_id) REFERENCES wilayah_kerja(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. DOKUMENTASI
CREATE TABLE IF NOT EXISTS dokumentasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    kategori ENUM('pelatihan_online','pelatihan_offline','rapat','foto_kegiatan') NOT NULL,
    deskripsi TEXT,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(10),
    thumbnail VARCHAR(255),
    tanggal DATE,
    tags TEXT, -- JSON array
    watermark BOOLEAN DEFAULT FALSE,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. CROSS-CUTTING
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50),
    detail TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    tipe ENUM('info','warning','success','error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- SEED: Default Admin User (password: password)
INSERT IGNORE INTO users (username, password_hash, nama_lengkap, email, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator SE2026', 'admin@bpsjember.go.id', 'admin');

-- Untuk data dummy lintas modul dan akun demo tambahan, impor sql/seed_dummy_data.sql setelah schema ini.

-- SEED: Wilayah Kerja (31 Kecamatan Jember)
INSERT IGNORE INTO wilayah_kerja (kode_kecamatan, nama_kecamatan, kebutuhan_pcl, kebutuhan_pml, lat, lng) VALUES
('3509010', 'Kencong', 15, 3, -8.2833, 113.3667),
('3509020', 'Gumukmas', 12, 2, -8.2667, 113.4167),
('3509030', 'Puger', 18, 4, -8.3333, 113.4500),
('3509040', 'Wuluhan', 16, 3, -8.2500, 113.5000),
('3509050', 'Ambulu', 14, 3, -8.3500, 113.6000),
('3509060', 'Tempurejo', 10, 2, -8.2833, 113.6833),
('3509070', 'Silo', 12, 2, -8.2000, 113.8500),
('3509080', 'Mayang', 8, 2, -8.1833, 113.7000),
('3509090', 'Mumbulsari', 10, 2, -8.2167, 113.6500),
('3509100', 'Jenggawah', 14, 3, -8.2167, 113.5667),
('3509110', 'Ajung', 12, 2, -8.1833, 113.6333),
('3509120', 'Rambipuji', 14, 3, -8.1667, 113.6000),
('3509130', 'Balung', 13, 3, -8.2500, 113.5500),
('3509140', 'Umbulsari', 11, 2, -8.2667, 113.5167),
('3509150', 'Semboro', 9, 2, -8.2333, 113.4667),
('3509160', 'Jombang', 10, 2, -8.2500, 113.4333),
('3509170', 'Sumberbaru', 14, 3, -8.2833, 113.3833),
('3509180', 'Tanggul', 13, 3, -8.1667, 113.4667),
('3509190', 'Bangsalsari', 15, 3, -8.1500, 113.5333),
('3509200', 'Panti', 11, 2, -8.1333, 113.6167),
('3509210', 'Sukorambi', 8, 2, -8.1167, 113.6500),
('3509220', 'Arjasa', 10, 2, -8.0833, 113.7000),
('3509230', 'Pakusari', 9, 2, -8.1333, 113.7167),
('3509240', 'Kalisat', 14, 3, -8.1500, 113.7500),
('3509250', 'Ledokombo', 11, 2, -8.1000, 113.7833),
('3509260', 'Sumberjambe', 10, 2, -8.0667, 113.8167),
('3509270', 'Sukowono', 11, 2, -8.1167, 113.7833),
('3509280', 'Jelbuk', 7, 1, -8.0833, 113.7167),
('3509290', 'Kaliwates', 20, 4, -8.1667, 113.7000),
('3509300', 'Sumbersari', 22, 5, -8.1737, 113.7131),
('3509310', 'Patrang', 18, 4, -8.1500, 113.7167);

-- Guard ini menjaga schema tetap bisa diimport ulang tanpa gagal karena index sudah ada.
SET @sql := IF (
    EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'activity_logs' AND index_name = 'idx_activity_logs_user'
    ),
    'SELECT 1',
    'CREATE INDEX idx_activity_logs_user ON activity_logs(user_id)'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF (
    EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'activity_logs' AND index_name = 'idx_activity_logs_created'
    ),
    'SELECT 1',
    'CREATE INDEX idx_activity_logs_created ON activity_logs(created_at)'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF (
    EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'notifications' AND index_name = 'idx_notifications_user'
    ),
    'SELECT 1',
    'CREATE INDEX idx_notifications_user ON notifications(user_id, is_read)'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF (
    EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'pendaftaran' AND index_name = 'idx_pendaftaran_status'
    ),
    'SELECT 1',
    'CREATE INDEX idx_pendaftaran_status ON pendaftaran(status)'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF (
    EXISTS (
        SELECT 1 FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'surat_masuk' AND index_name = 'idx_surat_masuk_status'
    ),
    'SELECT 1',
    'CREATE INDEX idx_surat_masuk_status ON surat_masuk(status)'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
