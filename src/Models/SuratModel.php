<?php
namespace App\Models;

use App\Utils\DummyData;
use PDO;
use PDOException;

class SuratModel {
    public static function getAllSK($limit = null, $offset = 0) {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_keputusan')) return self::getMockSK();
        try {
            $sql = "SELECT id, nomor_sk, judul, tanggal_sk, status FROM surat_keputusan ORDER BY tanggal_sk DESC, created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            return $rows ?: self::getMockSK();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return self::getMockSK();
        }
    }

    public static function countSK() {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_keputusan')) return count(self::getMockSK());
        try {
            return (int)$pdo->query("SELECT COUNT(*) FROM surat_keputusan")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return count(self::getMockSK());
        }
    }

    public static function addSK(array $data) {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_keputusan')) return false;
        try {
            $sql = "INSERT INTO surat_keputusan (nomor_sk, judul, tanggal_sk, file_path, status, created_by)
                    VALUES (:nomor_sk, :judul, :tanggal_sk, :file_path, :status, :created_by)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nomor_sk'   => $data['nomor_sk'],
                ':judul'      => $data['judul'],
                ':tanggal_sk' => $data['tanggal_sk'],
                ':file_path'  => $data['file_path'] ?? null,
                ':status'     => $data['status'] ?? 'draft',
                // Insert surat memakai helper user yang benar agar tidak fatal.
                ':created_by' => self::currentUserId(),
            ]);
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAllSuratMasuk($limit = null, $offset = 0) {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_masuk')) return self::getMockSuratMasuk();
        try {
            $sql = "SELECT id, nomor_surat, pengirim, perihal, tanggal_terima, status FROM surat_masuk ORDER BY tanggal_terima DESC, created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            return $rows ?: self::getMockSuratMasuk();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return self::getMockSuratMasuk();
        }
    }

    public static function countSuratMasuk() {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_masuk')) return count(self::getMockSuratMasuk());
        try {
            return (int)$pdo->query("SELECT COUNT(*) FROM surat_masuk")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return count(self::getMockSuratMasuk());
        }
    }

    public static function addSuratMasuk(array $data) {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_masuk')) return false;
        try {
            $sql = "INSERT INTO surat_masuk (nomor_surat, nomor_agenda, tanggal_surat, tanggal_terima, pengirim, perihal, file_path, status, created_by)
                    VALUES (:nomor_surat, :nomor_agenda, :tanggal_surat, :tanggal_terima, :pengirim, :perihal, :file_path, :status, :created_by)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nomor_surat'   => $data['nomor_surat'],
                ':nomor_agenda'  => $data['nomor_agenda'] ?? null,
                ':tanggal_surat' => $data['tanggal_surat'],
                ':tanggal_terima'=> $data['tanggal_terima'],
                ':pengirim'      => $data['pengirim'],
                ':perihal'       => $data['perihal'],
                ':file_path'     => $data['file_path'] ?? null,
                ':status'        => $data['status'] ?? 'baru',
                ':created_by'    => self::currentUserId(),
            ]);
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAllSuratKeluar($limit = null, $offset = 0) {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_keluar')) return self::getMockSuratKeluar();
        try {
            $sql = "SELECT id, nomor_surat, tujuan, perihal, tanggal_surat, status FROM surat_keluar ORDER BY tanggal_surat DESC, created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            return $rows ?: self::getMockSuratKeluar();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return self::getMockSuratKeluar();
        }
    }

    public static function countSuratKeluar() {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_keluar')) return count(self::getMockSuratKeluar());
        try {
            return (int)$pdo->query("SELECT COUNT(*) FROM surat_keluar")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return count(self::getMockSuratKeluar());
        }
    }

    public static function addSuratKeluar(array $data) {
        global $pdo;
        if (!$pdo || !self::tableExists('surat_keluar')) return false;
        try {
            $sql = "INSERT INTO surat_keluar (nomor_surat, tanggal_surat, tujuan, perihal, file_path, status, created_by)
                    VALUES (:nomor_surat, :tanggal_surat, :tujuan, :perihal, :file_path, :status, :created_by)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nomor_surat'   => $data['nomor_surat'],
                ':tanggal_surat' => $data['tanggal_surat'],
                ':tujuan'        => $data['tujuan'],
                ':perihal'       => $data['perihal'],
                ':file_path'     => $data['file_path'] ?? null,
                ':status'        => $data['status'] ?? 'draft',
                ':created_by'    => self::currentUserId(),
            ]);
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAllMemorandum($limit = null, $offset = 0) {
        global $pdo;
        if (!$pdo || !self::tableExists('memorandum')) return self::getMockMemorandum();
        try {
            $hasKonfirmasi = self::tableExists('konfirmasi_kehadiran');
            // Join konfirmasi hadir agar kartu undangan langsung menampilkan angka partisipasi aktual.
            $sql = $hasKonfirmasi
                ? "SELECT m.id,
                          m.nomor,
                          m.tipe,
                          m.judul,
                          m.konten,
                          m.tanggal,
                          COALESCE(DATE_FORMAT(m.waktu, '%H:%i'), '-') AS waktu,
                          COALESCE(NULLIF(m.tempat, ''), '-') AS tempat,
                          COALESCE(m.distribusi_email, 0) AS distribusi_email,
                          COALESCE(m.distribusi_sms, 0) AS distribusi_sms,
                          COUNT(CASE WHEN k.status = 'hadir' THEN 1 END) AS konfirmasi
                   FROM memorandum m
                   LEFT JOIN konfirmasi_kehadiran k ON k.memorandum_id = m.id
                   GROUP BY m.id, m.nomor, m.tipe, m.judul, m.konten, m.tanggal, m.waktu, m.tempat, m.distribusi_email, m.distribusi_sms
                   ORDER BY m.tanggal DESC, m.created_at DESC"
                : "SELECT m.id,
                          m.nomor,
                          m.tipe,
                          m.judul,
                          m.konten,
                          m.tanggal,
                          COALESCE(DATE_FORMAT(m.waktu, '%H:%i'), '-') AS waktu,
                          COALESCE(NULLIF(m.tempat, ''), '-') AS tempat,
                          COALESCE(m.distribusi_email, 0) AS distribusi_email,
                          COALESCE(m.distribusi_sms, 0) AS distribusi_sms,
                          0 AS konfirmasi
                   FROM memorandum m
                   ORDER BY m.tanggal DESC, m.created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            return $rows ? array_map([self::class, 'normalizeMemoRow'], $rows) : self::getMockMemorandum();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return self::getMockMemorandum();
        }
    }

    public static function countMemorandum() {
        global $pdo;
        if (!$pdo || !self::tableExists('memorandum')) return count(self::getMockMemorandum());
        try {
            return (int)$pdo->query("SELECT COUNT(*) FROM memorandum")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return count(self::getMockMemorandum());
        }
    }

    public static function getMemorandumById($id) {
        global $pdo;
        if (!$pdo || !self::tableExists('memorandum')) return self::findMockMemorandumById((int) $id);
        try {
            $hasKonfirmasi = self::tableExists('konfirmasi_kehadiran');
            $sql = $hasKonfirmasi
                ? "SELECT m.id,
                          m.nomor,
                          m.tipe,
                          m.judul,
                          m.konten,
                          m.tanggal,
                          COALESCE(DATE_FORMAT(m.waktu, '%H:%i'), '-') AS waktu,
                          COALESCE(NULLIF(m.tempat, ''), '-') AS tempat,
                          COALESCE(m.distribusi_email, 0) AS distribusi_email,
                          COALESCE(m.distribusi_sms, 0) AS distribusi_sms,
                          COUNT(CASE WHEN k.status = 'hadir' THEN 1 END) AS konfirmasi
                   FROM memorandum m
                   LEFT JOIN konfirmasi_kehadiran k ON k.memorandum_id = m.id
                   WHERE m.id = :id
                   GROUP BY m.id, m.nomor, m.tipe, m.judul, m.konten, m.tanggal, m.waktu, m.tempat, m.distribusi_email, m.distribusi_sms
                   LIMIT 1"
                : "SELECT m.id,
                          m.nomor,
                          m.tipe,
                          m.judul,
                          m.konten,
                          m.tanggal,
                          COALESCE(DATE_FORMAT(m.waktu, '%H:%i'), '-') AS waktu,
                          COALESCE(NULLIF(m.tempat, ''), '-') AS tempat,
                          COALESCE(m.distribusi_email, 0) AS distribusi_email,
                          COALESCE(m.distribusi_sms, 0) AS distribusi_sms,
                          0 AS konfirmasi
                   FROM memorandum m
                   WHERE m.id = :id
                   LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ? self::normalizeMemoRow($row) : self::findMockMemorandumById((int) $id);
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return self::findMockMemorandumById((int) $id);
        }
    }

    public static function addMemorandum(array $data) {
        global $pdo;
        if (!$pdo || !self::tableExists('memorandum')) return false;
        try {
            $sql = "INSERT INTO memorandum
                    (nomor, tipe, judul, konten, tanggal, waktu, tempat, distribusi_email, distribusi_sms, created_by)
                    VALUES
                    (:nomor, :tipe, :judul, :konten, :tanggal, :waktu, :tempat, :distribusi_email, :distribusi_sms, :created_by)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nomor' => $data['nomor'],
                ':tipe' => $data['tipe'],
                ':judul' => $data['judul'],
                ':konten' => $data['konten'] ?: null,
                ':tanggal' => $data['tanggal'],
                ':waktu' => $data['waktu'] ?: null,
                ':tempat' => $data['tempat'] ?: null,
                ':distribusi_email' => !empty($data['distribusi_email']) ? 1 : 0,
                ':distribusi_sms' => !empty($data['distribusi_sms']) ? 1 : 0,
                ':created_by' => self::currentUserId(),
            ]);
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function updateMemorandum(int $id, array $data) {
        global $pdo;
        if (!$pdo || !self::tableExists('memorandum')) return false;
        try {
            $sql = "UPDATE memorandum
                    SET nomor = :nomor,
                        tipe = :tipe,
                        judul = :judul,
                        konten = :konten,
                        tanggal = :tanggal,
                        waktu = :waktu,
                        tempat = :tempat,
                        distribusi_email = :distribusi_email,
                        distribusi_sms = :distribusi_sms
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nomor' => $data['nomor'],
                ':tipe' => $data['tipe'],
                ':judul' => $data['judul'],
                ':konten' => $data['konten'] ?: null,
                ':tanggal' => $data['tanggal'],
                ':waktu' => $data['waktu'] ?: null,
                ':tempat' => $data['tempat'] ?: null,
                ':distribusi_email' => !empty($data['distribusi_email']) ? 1 : 0,
                ':distribusi_sms' => !empty($data['distribusi_sms']) ? 1 : 0,
            ]);
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function deleteMemorandum(int $id) {
        global $pdo;
        if (!$pdo || !self::tableExists('memorandum')) return false;
        try {
            $stmt = $pdo->prepare("DELETE FROM memorandum WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('[SuratModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    private static function getMockSK() {
        return DummyData::getSuratKeputusan();
    }

    private static function getMockSuratMasuk() {
        return DummyData::getSuratMasuk();
    }

    private static function getMockSuratKeluar() {
        return DummyData::getSuratKeluar();
    }

    private static function getMockMemorandum() {
        return array_map([self::class, 'normalizeMemoRow'], DummyData::getMemoList());
    }

    private static function normalizeMemoRow(array $row): array
    {
        $row['nomor'] = $row['nomor'] ?? '';
        $row['tipe'] = $row['tipe'] ?? 'memo';
        $row['judul'] = $row['judul'] ?? '';
        $row['konten'] = $row['konten'] ?? '';
        $row['waktu'] = (!isset($row['waktu']) || $row['waktu'] === '' || $row['waktu'] === null) ? '-' : (string) $row['waktu'];
        $row['tempat'] = (!isset($row['tempat']) || $row['tempat'] === '' || $row['tempat'] === null) ? '-' : (string) $row['tempat'];
        $row['konfirmasi'] = (int) ($row['konfirmasi'] ?? 0);
        $row['distribusi_email'] = !empty($row['distribusi_email']);
        $row['distribusi_sms'] = !empty($row['distribusi_sms']);

        return $row;
    }

    private static function findMockMemorandumById(int $id)
    {
        foreach (self::getMockMemorandum() as $memo) {
            if ((int) $memo['id'] === $id) {
                return $memo;
            }
        }

        return null;
    }

    private static function currentUserId(): int
    {
        $userId = function_exists('get_user_id') ? get_user_id() : null;
        return $userId ? (int) $userId : 1;
    }

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
}
