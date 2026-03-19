/*
 Navicat Premium Dump SQL

 Source Server         : Laragon
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : bps_jember_se2026

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 15/03/2026 21:32:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity_logs
-- ----------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `module` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_activity_logs_user`(`user_id` ASC) USING BTREE,
  INDEX `idx_activity_logs_created`(`created_at` ASC) USING BTREE,
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity_logs
-- ----------------------------
INSERT INTO `activity_logs` VALUES (1, 1, 'login', 'auth', 'User logged in', '::1', 'Mozilla/5.0 (Linux; Android 12; M2010J19CG Build/SKQ1.211202.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.192 Mobile Safari/537.36', '2026-03-12 15:09:54');
INSERT INTO `activity_logs` VALUES (2, NULL, 'logout', 'auth', 'User logged out', '::1', 'Mozilla/5.0 (Linux; Android 12; M2010J19CG Build/SKQ1.211202.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.192 Mobile Safari/537.36', '2026-03-12 15:49:05');
INSERT INTO `activity_logs` VALUES (3, 1, 'login', 'auth', 'User logged in', '::1', 'Mozilla/5.0 (Linux; Android 12; M2010J19CG Build/SKQ1.211202.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/143.0.7499.192 Mobile Safari/537.36', '2026-03-12 15:52:02');

-- ----------------------------
-- Table structure for anomaly
-- ----------------------------
DROP TABLE IF EXISTS `anomaly`;
CREATE TABLE `anomaly`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `jenis` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `wilayah` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pelapor_id` int NULL DEFAULT NULL,
  `file_bukti` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('reported','review','resolved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'reported',
  `tindak_lanjut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `resolved_by` int NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pelapor_id`(`pelapor_id` ASC) USING BTREE,
  INDEX `resolved_by`(`resolved_by` ASC) USING BTREE,
  CONSTRAINT `anomaly_ibfk_1` FOREIGN KEY (`pelapor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `anomaly_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of anomaly
-- ----------------------------

-- ----------------------------
-- Table structure for dokumen_persyaratan
-- ----------------------------
DROP TABLE IF EXISTS `dokumen_persyaratan`;
CREATE TABLE `dokumen_persyaratan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `pendaftaran_id` int NULL DEFAULT NULL,
  `jenis_dokumen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_size` int NULL DEFAULT NULL,
  `is_verified` tinyint(1) NULL DEFAULT 0,
  `verified_by` int NULL DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pendaftaran_id`(`pendaftaran_id` ASC) USING BTREE,
  INDEX `verified_by`(`verified_by` ASC) USING BTREE,
  CONSTRAINT `dokumen_persyaratan_ibfk_1` FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftaran` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `dokumen_persyaratan_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dokumen_persyaratan
-- ----------------------------

-- ----------------------------
-- Table structure for dokumentasi
-- ----------------------------
DROP TABLE IF EXISTS `dokumentasi`;
CREATE TABLE `dokumentasi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kategori` enum('pelatihan_online','pelatihan_offline','rapat','foto_kegiatan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `watermark` tinyint(1) NULL DEFAULT 0,
  `uploaded_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uploaded_by`(`uploaded_by` ASC) USING BTREE,
  CONSTRAINT `dokumentasi_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dokumentasi
-- ----------------------------

-- ----------------------------
-- Table structure for jadwal_pertemuan
-- ----------------------------
DROP TABLE IF EXISTS `jadwal_pertemuan`;
CREATE TABLE `jadwal_pertemuan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NULL DEFAULT NULL,
  `waktu_selesai` time NULL DEFAULT NULL,
  `tempat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `google_calendar_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `reminder_sent` tinyint(1) NULL DEFAULT 0,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `jadwal_pertemuan_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jadwal_pertemuan
-- ----------------------------

-- ----------------------------
-- Table structure for jadwal_seleksi
-- ----------------------------
DROP TABLE IF EXISTS `jadwal_seleksi`;
CREATE TABLE `jadwal_seleksi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NULL DEFAULT NULL,
  `lokasi` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tipe` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'administrasi',
  `status` enum('upcoming','ongoing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'upcoming',
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `jadwal_seleksi_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jadwal_seleksi
-- ----------------------------

-- ----------------------------
-- Table structure for konfirmasi_kehadiran
-- ----------------------------
DROP TABLE IF EXISTS `konfirmasi_kehadiran`;
CREATE TABLE `konfirmasi_kehadiran`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `memorandum_id` int NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT NULL,
  `status` enum('pending','hadir','tidak_hadir') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'pending',
  `responded_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `memorandum_id`(`memorandum_id` ASC) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  CONSTRAINT `konfirmasi_kehadiran_ibfk_1` FOREIGN KEY (`memorandum_id`) REFERENCES `memorandum` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `konfirmasi_kehadiran_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of konfirmasi_kehadiran
-- ----------------------------

-- ----------------------------
-- Table structure for laporan_kegiatan
-- ----------------------------
DROP TABLE IF EXISTS `laporan_kegiatan`;
CREATE TABLE `laporan_kegiatan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `lokasi` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `uraian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `hasil` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `foto_lampiran` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `status` enum('draft','submitted','approved','revision') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'draft',
  `approved_by` int NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `approved_by`(`approved_by` ASC) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `laporan_kegiatan_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `laporan_kegiatan_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of laporan_kegiatan
-- ----------------------------

-- ----------------------------
-- Table structure for materi_bahan
-- ----------------------------
DROP TABLE IF EXISTS `materi_bahan`;
CREATE TABLE `materi_bahan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `kategori` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `file_size` int NULL DEFAULT NULL,
  `akses_role` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `download_count` int NULL DEFAULT 0,
  `uploaded_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `uploaded_by`(`uploaded_by` ASC) USING BTREE,
  CONSTRAINT `materi_bahan_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of materi_bahan
-- ----------------------------

-- ----------------------------
-- Table structure for memorandum
-- ----------------------------
DROP TABLE IF EXISTS `memorandum`;
CREATE TABLE `memorandum`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tipe` enum('memo','undangan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'memo',
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `konten` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `tanggal` date NOT NULL,
  `waktu` time NULL DEFAULT NULL,
  `tempat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `distribusi_email` tinyint(1) NULL DEFAULT 0,
  `distribusi_sms` tinyint(1) NULL DEFAULT 0,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `memorandum_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of memorandum
-- ----------------------------

-- ----------------------------
-- Table structure for monitoring_progress
-- ----------------------------
DROP TABLE IF EXISTS `monitoring_progress`;
CREATE TABLE `monitoring_progress`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `wilayah_id` int NULL DEFAULT NULL,
  `tanggal` date NOT NULL,
  `target` int NULL DEFAULT 0,
  `realisasi` int NULL DEFAULT 0,
  `persentase` decimal(5, 2) NULL DEFAULT 0.00,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `updated_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `wilayah_id`(`wilayah_id` ASC) USING BTREE,
  INDEX `updated_by`(`updated_by` ASC) USING BTREE,
  CONSTRAINT `monitoring_progress_ibfk_1` FOREIGN KEY (`wilayah_id`) REFERENCES `wilayah_kerja` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `monitoring_progress_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of monitoring_progress
-- ----------------------------

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `tipe` enum('info','warning','success','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'info',
  `is_read` tinyint(1) NULL DEFAULT 0,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_notifications_user`(`user_id` ASC, `is_read` ASC) USING BTREE,
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notifications
-- ----------------------------

-- ----------------------------
-- Table structure for notulen_rapat
-- ----------------------------
DROP TABLE IF EXISTS `notulen_rapat`;
CREATE TABLE `notulen_rapat`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_rapat` date NOT NULL,
  `waktu_mulai` time NULL DEFAULT NULL,
  `waktu_selesai` time NULL DEFAULT NULL,
  `tempat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `pimpinan_rapat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `peserta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `agenda` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `pembahasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `keputusan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `tindak_lanjut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `file_lampiran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `notulen_rapat_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notulen_rapat
-- ----------------------------

-- ----------------------------
-- Table structure for pelatihan
-- ----------------------------
DROP TABLE IF EXISTS `pelatihan`;
CREATE TABLE `pelatihan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tipe` enum('online','offline') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NULL DEFAULT NULL,
  `waktu_mulai` time NULL DEFAULT NULL,
  `waktu_selesai` time NULL DEFAULT NULL,
  `tempat` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `zoom_link` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `zoom_meeting_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `zoom_passcode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `video_rekaman` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `transkrip` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'scheduled',
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `pelatihan_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pelatihan
-- ----------------------------

-- ----------------------------
-- Table structure for pendaftaran
-- ----------------------------
DROP TABLE IF EXISTS `pendaftaran`;
CREATE TABLE `pendaftaran`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nik` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `posisi_dilamar` enum('PCL','PML') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'PCL',
  `wilayah_id` int NULL DEFAULT NULL,
  `status` enum('pending','verified','rejected','accepted') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'pending',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nik`(`nik` ASC) USING BTREE,
  INDEX `idx_pendaftaran_status`(`status` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pendaftaran
-- ----------------------------

-- ----------------------------
-- Table structure for pengumuman
-- ----------------------------
DROP TABLE IF EXISTS `pengumuman`;
CREATE TABLE `pengumuman`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `konten` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `tipe` enum('umum','hasil_seleksi','jadwal','info') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'umum',
  `file_lampiran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `is_published` tinyint(1) NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `pengumuman_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengumuman
-- ----------------------------

-- ----------------------------
-- Table structure for materi_pelatihan
-- ----------------------------
DROP TABLE IF EXISTS `materi_pelatihan`;
CREATE TABLE `materi_pelatihan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kategori` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tipe` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_size` int NULL DEFAULT 0,
  `downloads` int NULL DEFAULT 0,
  `is_published` tinyint(1) NULL DEFAULT 1,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `materi_pelatihan_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of materi_pelatihan
-- ----------------------------

-- ----------------------------
-- Table structure for presensi_pelatihan
-- ----------------------------
DROP TABLE IF EXISTS `presensi_pelatihan`;
CREATE TABLE `presensi_pelatihan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `pelatihan_id` int NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT NULL,
  `waktu_checkin` timestamp NULL DEFAULT NULL,
  `metode` enum('manual','qrcode','link') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'manual',
  `keterangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `pelatihan_id`(`pelatihan_id` ASC, `user_id` ASC) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  CONSTRAINT `presensi_pelatihan_ibfk_1` FOREIGN KEY (`pelatihan_id`) REFERENCES `pelatihan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `presensi_pelatihan_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of presensi_pelatihan
-- ----------------------------

-- ----------------------------
-- Table structure for qna_pelatihan
-- ----------------------------
DROP TABLE IF EXISTS `qna_pelatihan`;
CREATE TABLE `qna_pelatihan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `pelatihan_id` int NULL DEFAULT NULL,
  `user_id` int NULL DEFAULT NULL,
  `pertanyaan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `jawaban` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `answered_by` int NULL DEFAULT NULL,
  `votes` int NULL DEFAULT 0,
  `is_moderated` tinyint(1) NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pelatihan_id`(`pelatihan_id` ASC) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  INDEX `answered_by`(`answered_by` ASC) USING BTREE,
  CONSTRAINT `qna_pelatihan_ibfk_1` FOREIGN KEY (`pelatihan_id`) REFERENCES `pelatihan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `qna_pelatihan_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `qna_pelatihan_ibfk_3` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qna_pelatihan
-- ----------------------------

-- ----------------------------
-- Table structure for surat_keluar
-- ----------------------------
DROP TABLE IF EXISTS `surat_keluar`;
CREATE TABLE `surat_keluar`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_surat` date NOT NULL,
  `tujuan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `perihal` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanda_tangan_digital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('draft','sent','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'draft',
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nomor_surat`(`nomor_surat` ASC) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `surat_keluar_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of surat_keluar
-- ----------------------------

-- ----------------------------
-- Table structure for surat_keputusan
-- ----------------------------
DROP TABLE IF EXISTS `surat_keputusan`;
CREATE TABLE `surat_keputusan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_sk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal_sk` date NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('draft','published','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'draft',
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nomor_sk`(`nomor_sk` ASC) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `surat_keputusan_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of surat_keputusan
-- ----------------------------

-- ----------------------------
-- Table structure for surat_masuk
-- ----------------------------
DROP TABLE IF EXISTS `surat_masuk`;
CREATE TABLE `surat_masuk`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nomor_agenda` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal_surat` date NOT NULL,
  `tanggal_terima` date NOT NULL,
  `pengirim` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `perihal` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `disposisi_ke` int NULL DEFAULT NULL,
  `catatan_disposisi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `status` enum('baru','disposisi','proses','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'baru',
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `disposisi_ke`(`disposisi_ke` ASC) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  INDEX `idx_surat_masuk_status`(`status` ASC) USING BTREE,
  CONSTRAINT `surat_masuk_ibfk_1` FOREIGN KEY (`disposisi_ke`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `surat_masuk_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of surat_masuk
-- ----------------------------

-- ----------------------------
-- Table structure for surat_tugas
-- ----------------------------
DROP TABLE IF EXISTS `surat_tugas`;
CREATE TABLE `surat_tugas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `judul` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tanggal` date NOT NULL,
  `petugas_id` int NULL DEFAULT NULL,
  `wilayah` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `qr_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanda_tangan_digital` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('active','expired','revoked') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'active',
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nomor_surat`(`nomor_surat` ASC) USING BTREE,
  INDEX `petugas_id`(`petugas_id` ASC) USING BTREE,
  INDEX `created_by`(`created_by` ASC) USING BTREE,
  CONSTRAINT `surat_tugas_ibfk_1` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `surat_tugas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of surat_tugas
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `role` enum('admin','operator','pml','pcl') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pcl',
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username` ASC) USING BTREE,
  UNIQUE INDEX `nip`(`nip` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, NULL, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator SE2026', 'admin@bpsjember.go.id', NULL, 'admin', NULL, 1, '2026-03-12 15:52:02', '2026-03-12 15:08:08', '2026-03-12 15:52:02');

-- ----------------------------
-- Table structure for visum
-- ----------------------------
DROP TABLE IF EXISTS `visum`;
CREATE TABLE `visum`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `surat_tugas_id` int NULL DEFAULT NULL,
  `petugas_id` int NULL DEFAULT NULL,
  `tanggal` date NOT NULL,
  `hasil` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'pending',
  `approved_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `surat_tugas_id`(`surat_tugas_id` ASC) USING BTREE,
  INDEX `petugas_id`(`petugas_id` ASC) USING BTREE,
  INDEX `approved_by`(`approved_by` ASC) USING BTREE,
  CONSTRAINT `visum_ibfk_1` FOREIGN KEY (`surat_tugas_id`) REFERENCES `surat_tugas` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `visum_ibfk_2` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `visum_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of visum
-- ----------------------------

-- ----------------------------
-- Table structure for wilayah_kerja
-- ----------------------------
DROP TABLE IF EXISTS `wilayah_kerja`;
CREATE TABLE `wilayah_kerja`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_kecamatan` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_kecamatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kebutuhan_pcl` int NULL DEFAULT 0,
  `kebutuhan_pml` int NULL DEFAULT 0,
  `terisi_pcl` int NULL DEFAULT 0,
  `terisi_pml` int NULL DEFAULT 0,
  `lat` decimal(10, 7) NULL DEFAULT NULL,
  `lng` decimal(10, 7) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wilayah_kerja
-- ----------------------------
INSERT INTO `wilayah_kerja` VALUES (1, '3509010', 'Kencong', 15, 3, 0, 0, -8.2833000, 113.3667000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (2, '3509020', 'Gumukmas', 12, 2, 0, 0, -8.2667000, 113.4167000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (3, '3509030', 'Puger', 18, 4, 0, 0, -8.3333000, 113.4500000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (4, '3509040', 'Wuluhan', 16, 3, 0, 0, -8.2500000, 113.5000000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (5, '3509050', 'Ambulu', 14, 3, 0, 0, -8.3500000, 113.6000000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (6, '3509060', 'Tempurejo', 10, 2, 0, 0, -8.2833000, 113.6833000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (7, '3509070', 'Silo', 12, 2, 0, 0, -8.2000000, 113.8500000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (8, '3509080', 'Mayang', 8, 2, 0, 0, -8.1833000, 113.7000000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (9, '3509090', 'Mumbulsari', 10, 2, 0, 0, -8.2167000, 113.6500000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (10, '3509100', 'Jenggawah', 14, 3, 0, 0, -8.2167000, 113.5667000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (11, '3509110', 'Ajung', 12, 2, 0, 0, -8.1833000, 113.6333000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (12, '3509120', 'Rambipuji', 14, 3, 0, 0, -8.1667000, 113.6000000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (13, '3509130', 'Balung', 13, 3, 0, 0, -8.2500000, 113.5500000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (14, '3509140', 'Umbulsari', 11, 2, 0, 0, -8.2667000, 113.5167000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (15, '3509150', 'Semboro', 9, 2, 0, 0, -8.2333000, 113.4667000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (16, '3509160', 'Jombang', 10, 2, 0, 0, -8.2500000, 113.4333000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (17, '3509170', 'Sumberbaru', 14, 3, 0, 0, -8.2833000, 113.3833000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (18, '3509180', 'Tanggul', 13, 3, 0, 0, -8.1667000, 113.4667000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (19, '3509190', 'Bangsalsari', 15, 3, 0, 0, -8.1500000, 113.5333000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (20, '3509200', 'Panti', 11, 2, 0, 0, -8.1333000, 113.6167000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (21, '3509210', 'Sukorambi', 8, 2, 0, 0, -8.1167000, 113.6500000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (22, '3509220', 'Arjasa', 10, 2, 0, 0, -8.0833000, 113.7000000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (23, '3509230', 'Pakusari', 9, 2, 0, 0, -8.1333000, 113.7167000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (24, '3509240', 'Kalisat', 14, 3, 0, 0, -8.1500000, 113.7500000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (25, '3509250', 'Ledokombo', 11, 2, 0, 0, -8.1000000, 113.7833000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (26, '3509260', 'Sumberjambe', 10, 2, 0, 0, -8.0667000, 113.8167000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (27, '3509270', 'Sukowono', 11, 2, 0, 0, -8.1167000, 113.7833000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (28, '3509280', 'Jelbuk', 7, 1, 0, 0, -8.0833000, 113.7167000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (29, '3509290', 'Kaliwates', 20, 4, 0, 0, -8.1667000, 113.7000000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (30, '3509300', 'Sumbersari', 22, 5, 0, 0, -8.1737000, 113.7131000, '2026-03-12 15:08:08');
INSERT INTO `wilayah_kerja` VALUES (31, '3509310', 'Patrang', 18, 4, 0, 0, -8.1500000, 113.7167000, '2026-03-12 15:08:08');

-- ----------------------------
-- Tabel tambahan: lowongan & pendaftaran_petugas
-- ----------------------------

DROP TABLE IF EXISTS `lowongan`;
CREATE TABLE `lowongan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `posisi` varchar(150) NOT NULL,
  `wilayah` varchar(150) NOT NULL,
  `tipe` enum('PCL','PML') NOT NULL,
  `kuota` int NOT NULL DEFAULT 0,
  `deadline` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `pendaftaran_petugas`;
CREATE TABLE `pendaftaran_petugas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(150) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `email` varchar(150) NOT NULL,
  `no_hp` varchar(30) NOT NULL,
  `alamat` text NOT NULL,
  `posisi` enum('PCL','PML') NOT NULL,
  `wilayah` varchar(150) NOT NULL,
  `dok_ktp` varchar(255) NULL DEFAULT NULL,
  `dok_ijazah` varchar(255) NULL DEFAULT NULL,
  `dok_foto` varchar(255) NULL DEFAULT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `catatan` text NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unik_nik_email`(`nik` ASC, `email` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
