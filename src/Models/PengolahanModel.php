<?php
namespace App\Models;

use App\Utils\DummyData;
use PDO;
use PDOException;

class PengolahanModel {
    public static function getAllAnomaly($limit = null, $offset = 0) {
        global $pdo;
        if (!$pdo || !self::tableExists('anomaly')) return self::getMockAnomaly();
        try {
            $sql = "SELECT a.id, a.judul, a.wilayah, a.status, a.created_at AS tanggal, 
                           COALESCE(u.nama_lengkap, '—') AS pelapor
                    FROM anomaly a
                    LEFT JOIN users u ON a.pelapor_id = u.id
                    ORDER BY a.created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            return $rows ?: self::getMockAnomaly();
        } catch (PDOException $e) {
            error_log('[PengolahanModel] Error: ' . $e->getMessage());
            return self::getMockAnomaly();
        }
    }

    public static function countAnomaly() {
        global $pdo;
        if (!$pdo || !self::tableExists('anomaly')) return count(self::getMockAnomaly());
        try {
            return (int)$pdo->query("SELECT COUNT(*) FROM anomaly")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[PengolahanModel] Error: ' . $e->getMessage());
            return count(self::getMockAnomaly());
        }
    }

    public static function addAnomaly(array $data) {
        global $pdo;
        if (!$pdo || !self::tableExists('anomaly')) return false;
        try {
            // Kolom created_by tidak ada di schema anomaly, jadi insert dibatasi ke kolom yang benar.
            $user_id = function_exists('get_user_id') ? get_user_id() : null;
            $sql = "INSERT INTO anomaly (judul, wilayah, deskripsi, status, pelapor_id)
                    VALUES (:judul, :wilayah, :deskripsi, :status, :pelapor_id)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':judul'       => $data['judul'],
                ':wilayah'     => $data['wilayah'],
                ':deskripsi'   => $data['deskripsi'] ?? null,
                ':status'      => $data['status'] ?? 'reported',
                ':pelapor_id'  => $user_id,
            ]);
        } catch (PDOException $e) {
            error_log('[PengolahanModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getSektorProgress() {
        return DummyData::getSektorProgress();
    }

    public static function getSummaryStats() {
        global $muatan_se2026;
        $stats = $muatan_se2026;
        $stats['realisasi_total'] = 156780;
        $stats['persentase_global'] = round(($stats['realisasi_total'] / $stats['total_usaha']) * 100, 2);
        return $stats;
    }

    private static function getMockAnomaly() {
        return DummyData::getAnomaly();
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
