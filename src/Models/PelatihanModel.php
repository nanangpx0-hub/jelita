<?php
namespace App\Models;

use App\Utils\DummyData;
use PDO;
use PDOException;

class PelatihanModel
{
    public static function getAllPelatihan($limit = null, $offset = 0)
    {
        global $pdo;
        if (!$pdo || !self::tableExists('pelatihan')) return self::getMockPelatihan();

        try {
            $sql = "SELECT * FROM pelatihan ORDER BY tanggal_mulai DESC, created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
            }

            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();

            if (!$rows) {
                return self::getMockPelatihan();
            }

            return array_map([self::class, 'normalizePelatihanRow'], $rows);
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return self::getMockPelatihan();
        }
    }

    public static function countPelatihan()
    {
        global $pdo;
        if (!$pdo || !self::tableExists('pelatihan')) return count(self::getMockPelatihan());

        try {
            return (int) $pdo->query("SELECT COUNT(*) FROM pelatihan")->fetchColumn();
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return count(self::getMockPelatihan());
        }
    }

    public static function getPelatihanByType($tipe)
    {
        $all = self::getAllPelatihan();
        return array_values(array_filter($all, function ($pelatihan) use ($tipe) {
            return ($pelatihan['tipe'] ?? '') === $tipe;
        }));
    }

    public static function addPelatihan(array $data)
    {
        global $pdo;
        if (!$pdo || !self::tableExists('pelatihan')) return false;

        try {
            $sql = "INSERT INTO pelatihan
                    (judul, tipe, tanggal_mulai, tanggal_selesai, waktu_mulai, waktu_selesai, tempat, deskripsi, zoom_link, status, created_by)
                    VALUES
                    (:judul, :tipe, :tanggal_mulai, :tanggal_selesai, :waktu_mulai, :waktu_selesai, :tempat, :deskripsi, :zoom_link, :status, :created_by)";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':judul' => $data['judul'],
                ':tipe' => $data['tipe'],
                ':tanggal_mulai' => $data['tanggal_mulai'],
                ':tanggal_selesai' => $data['tanggal_selesai'] ?? null,
                ':waktu_mulai' => $data['waktu_mulai'] ?? null,
                ':waktu_selesai' => $data['waktu_selesai'] ?? null,
                ':tempat' => $data['tempat'] ?? null,
                ':deskripsi' => $data['deskripsi'] ?? null,
                ':zoom_link' => $data['zoom_link'] ?? null,
                ':status' => $data['status'] ?? 'scheduled',
                // Gunakan helper user yang benar agar insert tidak fatal saat aksi tulis dipanggil.
                ':created_by' => self::currentUserId(),
            ]);
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getAllMateri($limit = null, $offset = 0)
    {
        global $pdo;
        if (!$pdo) return self::getMockMateri();

        try {
            // Dukungan tabel legacy menjaga modul materi tetap hidup pada dump lama.
            if (self::tableExists('materi_pelatihan')) {
                $sql = "SELECT id, judul, kategori, tipe, file_path, file_size, downloads
                        FROM materi_pelatihan
                        WHERE is_published = 1
                        ORDER BY created_at DESC";
            } elseif (self::tableExists('materi_bahan')) {
                $sql = "SELECT id,
                               judul,
                               COALESCE(kategori, 'Referensi') AS kategori,
                               UPPER(COALESCE(file_type, 'PDF')) AS tipe,
                               file_path,
                               COALESCE(file_size, 0) AS file_size,
                               COALESCE(download_count, 0) AS downloads
                        FROM materi_bahan
                        ORDER BY created_at DESC";
            } else {
                return self::getMockMateri();
            }

            if ($limit !== null) {
                $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
            }

            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();

            if (!$rows) {
                return self::getMockMateri();
            }

            return array_map([self::class, 'normalizeMateriRow'], $rows);
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return self::getMockMateri();
        }
    }

    public static function countMateri()
    {
        global $pdo;
        if (!$pdo) return count(self::getMockMateri());

        try {
            if (self::tableExists('materi_pelatihan')) {
                return (int) $pdo->query("SELECT COUNT(*) FROM materi_pelatihan WHERE is_published = 1")->fetchColumn();
            }

            if (self::tableExists('materi_bahan')) {
                return (int) $pdo->query("SELECT COUNT(*) FROM materi_bahan")->fetchColumn();
            }

            return count(self::getMockMateri());
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return count(self::getMockMateri());
        }
    }

    public static function getMateriById($id)
    {
        global $pdo;
        if (!$pdo) return self::findMockMateriById((int) $id);

        try {
            if (self::tableExists('materi_pelatihan')) {
                $stmt = $pdo->prepare("SELECT * FROM materi_pelatihan WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();
                return $row ? self::normalizeMateriRow($row) : self::findMockMateriById((int) $id);
            }

            if (self::tableExists('materi_bahan')) {
                $stmt = $pdo->prepare(
                    "SELECT id,
                            judul,
                            COALESCE(kategori, 'Referensi') AS kategori,
                            UPPER(COALESCE(file_type, 'PDF')) AS tipe,
                            file_path,
                            COALESCE(file_size, 0) AS file_size,
                            COALESCE(download_count, 0) AS downloads
                     FROM materi_bahan
                     WHERE id = :id
                     LIMIT 1"
                );
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();
                return $row ? self::normalizeMateriRow($row) : self::findMockMateriById((int) $id);
            }

            return self::findMockMateriById((int) $id);
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return self::findMockMateriById((int) $id);
        }
    }

    public static function addMateri(array $data)
    {
        global $pdo;
        if (!$pdo) return false;

        try {
            if (self::tableExists('materi_pelatihan')) {
                $sql = "INSERT INTO materi_pelatihan (judul, kategori, tipe, file_path, file_size, downloads, created_by)
                        VALUES (:judul, :kategori, :tipe, :file_path, :file_size, 0, :created_by)";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([
                    ':judul' => $data['judul'],
                    ':kategori' => $data['kategori'],
                    ':tipe' => $data['tipe'],
                    ':file_path' => $data['file_path'],
                    ':file_size' => $data['file_size'] ?? 0,
                    ':created_by' => self::currentUserId(),
                ]);
            }

            if (self::tableExists('materi_bahan')) {
                $sql = "INSERT INTO materi_bahan (judul, deskripsi, kategori, file_path, file_type, file_size, download_count, uploaded_by)
                        VALUES (:judul, :deskripsi, :kategori, :file_path, :file_type, :file_size, 0, :uploaded_by)";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([
                    ':judul' => $data['judul'],
                    ':deskripsi' => $data['deskripsi'] ?? null,
                    ':kategori' => $data['kategori'],
                    ':file_path' => $data['file_path'],
                    ':file_type' => strtolower($data['tipe']),
                    ':file_size' => $data['file_size'] ?? 0,
                    ':uploaded_by' => self::currentUserId(),
                ]);
            }

            return false;
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function incrementMateriDownloads($id)
    {
        global $pdo;
        if (!$pdo) return false;

        try {
            if (self::tableExists('materi_pelatihan')) {
                $stmt = $pdo->prepare("UPDATE materi_pelatihan SET downloads = downloads + 1 WHERE id = :id");
                return $stmt->execute([':id' => $id]);
            }

            if (self::tableExists('materi_bahan')) {
                $stmt = $pdo->prepare("UPDATE materi_bahan SET download_count = download_count + 1 WHERE id = :id");
                return $stmt->execute([':id' => $id]);
            }

            return false;
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getQnaPelatihan($pelatihan_id, $limit = 20)
    {
        global $pdo;
        if (!$pdo || !self::tableExists('qna_pelatihan')) return DummyData::getQnaPelatihan((int) $pelatihan_id);

        try {
            $sql = "SELECT q.id, q.pertanyaan, q.jawaban, q.votes, u.nama_lengkap AS user_nama
                    FROM qna_pelatihan q
                    LEFT JOIN users u ON q.user_id = u.id
                    WHERE q.pelatihan_id = :pid
                    ORDER BY q.created_at DESC
                    LIMIT :lim";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':pid', (int) $pelatihan_id, PDO::PARAM_INT);
            $stmt->bindValue(':lim', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            return $rows ?: DummyData::getQnaPelatihan((int) $pelatihan_id);
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return DummyData::getQnaPelatihan((int) $pelatihan_id);
        }
    }

    public static function addQnaPelatihan($pelatihan_id, $pertanyaan)
    {
        global $pdo;
        if (!$pdo || !self::tableExists('qna_pelatihan')) return false;

        try {
            $sql = "INSERT INTO qna_pelatihan (pelatihan_id, user_id, pertanyaan, votes, is_moderated)
                    VALUES (:pelatihan_id, :user_id, :pertanyaan, 0, 1)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':pelatihan_id' => $pelatihan_id,
                ':user_id' => self::currentUserId(),
                ':pertanyaan' => $pertanyaan,
            ]);
        } catch (PDOException $e) {
            error_log('[PelatihanModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    private static function getMockPelatihan()
    {
        return array_map([self::class, 'normalizePelatihanRow'], DummyData::getPelatihan());
    }

    private static function getMockMateri()
    {
        return array_map([self::class, 'normalizeMateriRow'], DummyData::getMateri());
    }

    /**
     * Normalisasi field mencegah view mengakses key yang hilang saat sumber data berubah.
     */
    private static function normalizePelatihanRow(array $row): array
    {
        $row['peserta'] = isset($row['peserta']) ? (int) $row['peserta'] : 0;
        $row['tanggal_selesai'] = $row['tanggal_selesai'] ?? null;
        $row['zoom_link'] = $row['zoom_link'] ?? '';
        $row['tempat'] = $row['tempat'] ?? '';

        return $row;
    }

    private static function normalizeMateriRow(array $row): array
    {
        $type = strtoupper((string) ($row['tipe'] ?? 'FILE'));
        $iconMap = [
            'PDF' => 'fa-file-pdf text-red-500',
            'PPT' => 'fa-file-powerpoint text-orange-500',
            'PPTX' => 'fa-file-powerpoint text-orange-500',
            'XLS' => 'fa-file-excel text-green-500',
            'XLSX' => 'fa-file-excel text-green-500',
            'MP4' => 'fa-file-video text-blue-500',
        ];

        $row['kategori'] = $row['kategori'] ?? 'Referensi';
        $row['tipe'] = $type;
        $row['downloads'] = (int) ($row['downloads'] ?? 0);
        $row['file_size'] = (int) ($row['file_size'] ?? 0);
        $row['icon'] = $row['icon'] ?? ($iconMap[$type] ?? 'fa-file-alt text-slate-500');

        return $row;
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

    private static function findMockMateriById(int $id)
    {
        foreach (self::getMockMateri() as $materi) {
            if ((int) $materi['id'] === $id) {
                return $materi;
            }
        }

        return null;
    }
}
