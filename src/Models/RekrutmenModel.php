<?php
namespace App\Models;

use App\Utils\DummyData;
use PDO;
use PDOException;

class RekrutmenModel {
    public static function getAllLowongan($limit = null, $offset = 0) {
        global $pdo;
        if (!$pdo || !self::tableExists('lowongan')) return self::getMockLowongan();
        try {
            $sql = "SELECT * FROM lowongan ORDER BY created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            return $rows ?: self::getMockLowongan();
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return self::getMockLowongan();
        }
    }

    public static function countLowongan() {
        global $pdo;
        if (!$pdo || !self::tableExists('lowongan')) return count(self::getMockLowongan());
        try {
            return (int)$pdo->query("SELECT COUNT(*) FROM lowongan")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return count(self::getMockLowongan());
        }
    }

    public static function getLowonganById($id) {
        global $pdo;
        if (!$pdo || !self::tableExists('lowongan')) return null;
        try {
            $stmt = $pdo->prepare("SELECT * FROM lowongan WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return null;
        }
    }

    public static function addLowongan($data) {
        global $pdo;
        if (!$pdo) return false;
        try {
            $sql = "INSERT INTO lowongan (posisi, wilayah, tipe, kuota, deadline) 
                    VALUES (:posisi, :wilayah, :tipe, :kuota, :deadline)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':posisi'   => sanitize_input($data['posisi']),
                ':wilayah'  => sanitize_input($data['wilayah']),
                ':tipe'     => sanitize_input($data['tipe']),
                ':kuota'    => (int)$data['kuota'],
                ':deadline' => $data['deadline']
            ]);
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function addPendaftaranPetugas(array $data) {
        global $pdo;
        if (!$pdo) return false;
        try {
            // Kompatibilitas dump lama: pakai tabel legacy bila tabel baru belum tersedia.
            if (!self::tableExists('pendaftaran_petugas')) {
                return self::addLegacyPendaftaranPetugas($data);
            }

            $sql = "INSERT INTO pendaftaran_petugas 
                    (nama_lengkap, nik, email, no_hp, alamat, posisi, wilayah, dok_ktp, dok_ijazah, dok_foto, status) 
                    VALUES 
                    (:nama_lengkap, :nik, :email, :no_hp, :alamat, :posisi, :wilayah, :dok_ktp, :dok_ijazah, :dok_foto, 'pending')";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nama_lengkap' => $data['nama_lengkap'],
                ':nik'          => $data['nik'],
                ':email'        => $data['email'],
                ':no_hp'        => $data['no_hp'],
                ':alamat'       => $data['alamat'],
                ':posisi'       => $data['posisi'],
                ':wilayah'      => $data['wilayah'],
                ':dok_ktp'      => $data['dok_ktp'] ?? null,
                ':dok_ijazah'   => $data['dok_ijazah'] ?? null,
                ':dok_foto'     => $data['dok_foto'] ?? null,
            ]);
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getPendaftaranStatus($keyword) {
        global $pdo;
        if (!$pdo) return DummyData::findPendaftaranByKeyword((string) $keyword);
        try {
            if (!self::tableExists('pendaftaran_petugas')) {
                return self::getLegacyPendaftaranStatus($keyword);
            }

            $sql = "SELECT nama_lengkap, nik, email, posisi, wilayah, status, created_at 
                    FROM pendaftaran_petugas 
                    WHERE nik = :nik_kw OR email = :email_kw 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nik_kw' => $keyword,
                ':email_kw' => $keyword,
            ]);
            $row = $stmt->fetch();
            return $row ?: DummyData::findPendaftaranByKeyword((string) $keyword);
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return DummyData::findPendaftaranByKeyword((string) $keyword);
        }
    }

    public static function getAllWilayah() {
        global $pdo;
        if (!$pdo || !self::tableExists('wilayah_kerja')) return self::getMockWilayah();
        try {
            $stmt = $pdo->query("SELECT * FROM wilayah_kerja ORDER BY nama_kecamatan ASC");
            $rows = $stmt->fetchAll();
            return $rows ?: self::getMockWilayah();
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return self::getMockWilayah();
        }
    }

    public static function getAllPengumuman($limit = null, $offset = 0) {
        global $pdo;
        if (!$pdo || !self::tableExists('pengumuman')) return self::getMockPengumuman();
        try {
            // Alias tanggal/file menjaga view tetap konsisten antara DB dan dummy data.
            $sql = "SELECT id, judul, konten, tipe, file_lampiran, file_lampiran AS file, is_published, published_at,
                           DATE(COALESCE(published_at, created_at)) AS tanggal
                    FROM pengumuman
                    WHERE is_published = 1
                    ORDER BY published_at DESC, created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            return $rows ?: self::getMockPengumuman();
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return self::getMockPengumuman();
        }
    }

    public static function countPengumuman() {
        global $pdo;
        if (!$pdo || !self::tableExists('pengumuman')) return count(self::getMockPengumuman());
        try {
            return (int)$pdo->query("SELECT COUNT(*) FROM pengumuman WHERE is_published = 1")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Error: ' . $e->getMessage());
            return count(self::getMockPengumuman());
        }
    }

    private static function getMockLowongan() {
        return DummyData::getLowongan();
    }

    private static function getMockWilayah() {
        return DummyData::getWilayah();
    }

    private static function getMockPengumuman() {
        return DummyData::getPengumuman();
    }

    /**
     * Cek tabel dipakai untuk menjaga kompatibilitas dengan dump lama yang belum sinkron.
     */
    private static function tableExists(string $table): bool
    {
        global $pdo;
        if (!$pdo) return false;

        try {
            $stmt = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($table));
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function addLegacyPendaftaranPetugas(array $data): bool
    {
        global $pdo;
        if (!$pdo || !self::tableExists('pendaftaran')) return false;

        $wilayahId = self::resolveWilayahId((string) ($data['wilayah'] ?? ''));

        $sql = "INSERT INTO pendaftaran
                (nama_lengkap, nik, email, no_hp, alamat, posisi_dilamar, wilayah_id, status)
                VALUES
                (:nama_lengkap, :nik, :email, :no_hp, :alamat, :posisi, :wilayah_id, 'pending')";

        $stmt = $pdo->prepare($sql);
        $saved = $stmt->execute([
            ':nama_lengkap' => $data['nama_lengkap'],
            ':nik' => $data['nik'],
            ':email' => $data['email'],
            ':no_hp' => $data['no_hp'],
            ':alamat' => $data['alamat'],
            ':posisi' => $data['posisi'],
            ':wilayah_id' => $wilayahId,
        ]);

        if (!$saved) {
            return false;
        }

        // Dokumen tambahan tetap dicatat bila instalasi masih memakai struktur lama.
        $pendaftaranId = (int) $pdo->lastInsertId();
        self::saveLegacyDokumen($pendaftaranId, 'ktp', $data['dok_ktp'] ?? null);
        self::saveLegacyDokumen($pendaftaranId, 'ijazah', $data['dok_ijazah'] ?? null);
        self::saveLegacyDokumen($pendaftaranId, 'foto', $data['dok_foto'] ?? null);

        return true;
    }

    private static function getLegacyPendaftaranStatus($keyword)
    {
        global $pdo;
        if (!$pdo || !self::tableExists('pendaftaran')) {
            return DummyData::findPendaftaranByKeyword((string) $keyword);
        }

        $sql = "SELECT p.nama_lengkap,
                       p.nik,
                       p.email,
                       p.posisi_dilamar AS posisi,
                       COALESCE(w.nama_kecamatan, '-') AS wilayah,
                       p.status,
                       p.created_at
                FROM pendaftaran p
                LEFT JOIN wilayah_kerja w ON p.wilayah_id = w.id
                WHERE p.nik = :nik_kw OR p.email = :email_kw
                ORDER BY p.created_at DESC
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nik_kw' => $keyword,
            ':email_kw' => $keyword,
        ]);
        $row = $stmt->fetch();

        return $row ?: DummyData::findPendaftaranByKeyword((string) $keyword);
    }

    private static function resolveWilayahId(string $wilayah): ?int
    {
        global $pdo;
        if (!$pdo || $wilayah === '' || !self::tableExists('wilayah_kerja')) return null;

        try {
            $stmt = $pdo->prepare("SELECT id FROM wilayah_kerja WHERE nama_kecamatan = :wilayah LIMIT 1");
            $stmt->execute([':wilayah' => $wilayah]);
            $value = $stmt->fetchColumn();
            return $value !== false ? (int) $value : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private static function saveLegacyDokumen(int $pendaftaranId, string $jenis, $path): void
    {
        global $pdo;
        if (!$pdo || !$path || !self::tableExists('dokumen_persyaratan')) return;

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO dokumen_persyaratan (pendaftaran_id, jenis_dokumen, file_path, file_size, is_verified)
                 VALUES (:pendaftaran_id, :jenis_dokumen, :file_path, 0, 0)"
            );
            $stmt->execute([
                ':pendaftaran_id' => $pendaftaranId,
                ':jenis_dokumen' => $jenis,
                ':file_path' => $path,
            ]);
        } catch (PDOException $e) {
            error_log('[RekrutmenModel] Legacy dokumen save error: ' . $e->getMessage());
        }
    }
}
