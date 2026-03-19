-- =========================================================================
-- MIGRATION: SE2026 Jember Enhancement
-- Menambahkan tabel-tabel baru untuk fitur enhancement SISE2026
-- MySQL 5.7+ / MariaDB Compatible (Shared Hosting)
--
-- Idempotent: aman dijalankan berulang kali (CREATE TABLE IF NOT EXISTS)
-- Requires: schema.sql sudah diimport terlebih dahulu (tabel users & wilayah_kerja harus ada)
--
-- Requirements: 1.1, 3.5, 5.2, 6.1, 10.4
-- =========================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- -------------------------------------------------------------------------
-- 1. TABEL USAHA — Data Usaha Utama (Requirements 1.1, 6.1)
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usaha (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nama_usaha      VARCHAR(200) NOT NULL,
    nama_pemilik    VARCHAR(150),
    npwp            VARCHAR(20),
    nib             VARCHAR(20),
    no_telepon      VARCHAR(20),
    email           VARCHAR(150),
    jalan           VARCHAR(200),
    nomor           VARCHAR(20),
    kecamatan_id    INT,
    kelurahan       VARCHAR(100),
    kode_pos        VARCHAR(10),
    lat             DECIMAL(10,7),
    lng             DECIMAL(10,7),
    kbli            VARCHAR(10),
    sektor          ENUM('pertanian','perdagangan','jasa','manufaktur','lainnya'),
    skala           ENUM('mikro','kecil','menengah','besar'),
    jumlah_tk       INT DEFAULT 0,
    omzet_tahunan   DECIMAL(15,2),
    status_legalitas ENUM('belum_terverifikasi','terverifikasi_oss','tidak_terdaftar') DEFAULT 'belum_terverifikasi',
    sumber_data     ENUM('pcl','mandiri') DEFAULT 'pcl',
    pcl_id          INT,
    tahun_data      YEAR DEFAULT 2026,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kecamatan_id) REFERENCES wilayah_kerja(id) ON DELETE SET NULL,
    FOREIGN KEY (pcl_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_usaha_kecamatan (kecamatan_id),
    INDEX idx_usaha_sektor (sektor),
    INDEX idx_usaha_skala (skala),
    INDEX idx_usaha_tahun (tahun_data)
) ENGINE=InnoDB;

-- -------------------------------------------------------------------------
-- 2. TABEL ANOMALI_VALIDASI — Hasil Validasi Otomatis (Requirements 3.5)
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS anomali_validasi (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    usaha_id        INT NOT NULL,
    kode_anomali    VARCHAR(50) NOT NULL,
    jenis_anomali   VARCHAR(100),
    detail          TEXT,
    status          ENUM('open','resolved','dismissed') DEFAULT 'open',
    resolved_by     INT,
    resolved_at     TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usaha_id) REFERENCES usaha(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_anomali_kode (kode_anomali),
    INDEX idx_anomali_status (status)
) ENGINE=InnoDB;

-- -------------------------------------------------------------------------
-- 3. TABEL NOTIFIKASI_WA — Log Notifikasi WhatsApp (Requirements 5.2)
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifikasi_wa (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    usaha_id        INT,
    no_telepon      VARCHAR(20) NOT NULL,
    template_name   VARCHAR(100),
    status          ENUM('pending','terkirim','gagal','dibaca') DEFAULT 'pending',
    wa_message_id   VARCHAR(100),
    error_message   TEXT,
    kirim_ke        INT DEFAULT 1,
    scheduled_at    TIMESTAMP NULL,
    sent_at         TIMESTAMP NULL,
    created_by      INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usaha_id) REFERENCES usaha(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_notif_status (status),
    INDEX idx_notif_usaha (usaha_id)
) ENGINE=InnoDB;

-- -------------------------------------------------------------------------
-- 4. TABEL RESPONDEN_TOKENS — Token Pelaporan Mandiri (Requirements 6.1)
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS responden_tokens (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    token           CHAR(32) NOT NULL,
    usaha_id        INT,
    status          ENUM('active','used','expired') DEFAULT 'active',
    expires_at      TIMESTAMP NOT NULL,
    used_at         TIMESTAMP NULL,
    created_by      INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_token (token),
    FOREIGN KEY (usaha_id) REFERENCES usaha(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_token_lookup (token, status)
) ENGINE=InnoDB;

-- -------------------------------------------------------------------------
-- 5. TABEL OSS_SYNC_LOG — Riwayat Sinkronisasi OSS (Requirements 10.4)
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS oss_sync_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    usaha_id        INT NOT NULL,
    lookup_key      VARCHAR(30),
    lookup_type     ENUM('nib','npwp'),
    status          ENUM('berhasil','gagal') NOT NULL,
    data_updated    JSON,
    error_message   TEXT,
    synced_by       INT,
    synced_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usaha_id) REFERENCES usaha(id) ON DELETE CASCADE,
    FOREIGN KEY (synced_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_oss_usaha (usaha_id),
    INDEX idx_oss_synced (synced_at)
) ENGINE=InnoDB;

-- -------------------------------------------------------------------------
-- 6. TABEL OFFLINE_SYNC_LOG — Log Sinkronisasi Offline (Requirements 1.1)
-- -------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS offline_sync_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    pcl_id          INT,
    batch_id        VARCHAR(36),
    total_entries   INT DEFAULT 0,
    synced_ok       INT DEFAULT 0,
    synced_conflict INT DEFAULT 0,
    synced_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pcl_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================================
-- END OF MIGRATION
-- =========================================================================
