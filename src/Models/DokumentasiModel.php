<?php
namespace App\Models;

use App\Utils\DummyData;
use PDO;
use PDOException;

class DokumentasiModel
{
    public static function getAllByCategory(string $category, $limit = null, $offset = 0): array
    {
        global $pdo;
        if (!$pdo || !self::tableExists('dokumentasi')) {
            return self::getMockByCategory($category);
        }

        try {
            $sql = "SELECT id, judul, kategori, deskripsi, file_path, file_type, thumbnail, tanggal, tags, watermark
                    FROM dokumentasi
                    WHERE kategori = :kategori
                    ORDER BY tanggal DESC, created_at DESC";
            if ($limit !== null) {
                $sql .= " LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':kategori' => $category]);
            $rows = $stmt->fetchAll();

            return $rows ? array_map([self::class, 'normalizeRow'], $rows) : self::getMockByCategory($category);
        } catch (PDOException $e) {
            error_log('[DokumentasiModel] Error: ' . $e->getMessage());
            return self::getMockByCategory($category);
        }
    }

    public static function countByCategory(string $category): int
    {
        global $pdo;
        if (!$pdo || !self::tableExists('dokumentasi')) {
            return count(self::getMockByCategory($category));
        }

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM dokumentasi WHERE kategori = :kategori");
            $stmt->execute([':kategori' => $category]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('[DokumentasiModel] Error: ' . $e->getMessage());
            return count(self::getMockByCategory($category));
        }
    }

    public static function getById(int $id)
    {
        global $pdo;
        if (!$pdo || !self::tableExists('dokumentasi')) {
            return self::findMockById($id);
        }

        try {
            $stmt = $pdo->prepare("SELECT id, judul, kategori, deskripsi, file_path, file_type, thumbnail, tanggal, tags, watermark
                                   FROM dokumentasi
                                   WHERE id = :id
                                   LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row ? self::normalizeRow($row) : self::findMockById($id);
        } catch (PDOException $e) {
            error_log('[DokumentasiModel] Error: ' . $e->getMessage());
            return self::findMockById($id);
        }
    }

    public static function add(array $data): bool
    {
        global $pdo;
        if (!$pdo || !self::tableExists('dokumentasi')) return false;

        try {
            $sql = "INSERT INTO dokumentasi
                    (judul, kategori, deskripsi, file_path, file_type, thumbnail, tanggal, tags, watermark, uploaded_by)
                    VALUES
                    (:judul, :kategori, :deskripsi, :file_path, :file_type, :thumbnail, :tanggal, :tags, :watermark, :uploaded_by)";
            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':judul' => $data['judul'],
                ':kategori' => $data['kategori'],
                ':deskripsi' => $data['deskripsi'] ?: null,
                ':file_path' => $data['file_path'],
                ':file_type' => strtoupper((string) $data['file_type']),
                ':thumbnail' => $data['thumbnail'] ?? null,
                ':tanggal' => $data['tanggal'],
                ':tags' => self::encodeTags($data['tags'] ?? []),
                ':watermark' => !empty($data['watermark']) ? 1 : 0,
                ':uploaded_by' => self::currentUserId(),
            ]);
        } catch (PDOException $e) {
            error_log('[DokumentasiModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function update(int $id, array $data): bool
    {
        global $pdo;
        if (!$pdo || !self::tableExists('dokumentasi')) return false;

        try {
            $sql = "UPDATE dokumentasi
                    SET judul = :judul,
                        kategori = :kategori,
                        deskripsi = :deskripsi,
                        file_path = :file_path,
                        file_type = :file_type,
                        thumbnail = :thumbnail,
                        tanggal = :tanggal,
                        tags = :tags,
                        watermark = :watermark
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':id' => $id,
                ':judul' => $data['judul'],
                ':kategori' => $data['kategori'],
                ':deskripsi' => $data['deskripsi'] ?: null,
                ':file_path' => $data['file_path'],
                ':file_type' => strtoupper((string) $data['file_type']),
                ':thumbnail' => $data['thumbnail'] ?? null,
                ':tanggal' => $data['tanggal'],
                ':tags' => self::encodeTags($data['tags'] ?? []),
                ':watermark' => !empty($data['watermark']) ? 1 : 0,
            ]);
        } catch (PDOException $e) {
            error_log('[DokumentasiModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function delete(int $id): bool
    {
        global $pdo;
        if (!$pdo || !self::tableExists('dokumentasi')) return false;

        try {
            $stmt = $pdo->prepare("DELETE FROM dokumentasi WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('[DokumentasiModel] Error: ' . $e->getMessage());
            return false;
        }
    }

    private static function normalizeRow(array $row): array
    {
        $fileType = strtoupper((string) ($row['file_type'] ?? pathinfo((string) ($row['file_path'] ?? ''), PATHINFO_EXTENSION)));
        $tags = self::decodeTags($row['tags'] ?? '[]');
        $storedName = basename((string) ($row['file_path'] ?? ''));
        $fullPath = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storedName;
        $fileSize = is_file($fullPath) ? filesize($fullPath) : 0;

        $iconMap = [
            'PDF' => 'fa-file-pdf text-red-500',
            'DOC' => 'fa-file-word text-blue-500',
            'DOCX' => 'fa-file-word text-blue-500',
            'JPG' => 'fa-image text-emerald-500',
            'JPEG' => 'fa-image text-emerald-500',
            'PNG' => 'fa-image text-emerald-500',
            'MP4' => 'fa-video text-orange-500',
        ];

        $row['kategori'] = (string) ($row['kategori'] ?? '');
        $row['judul'] = $row['judul'] ?? '';
        $row['deskripsi'] = $row['deskripsi'] ?? '';
        $row['file_path'] = $storedName;
        $row['file_type'] = $fileType !== '' ? $fileType : 'FILE';
        $row['thumbnail'] = $row['thumbnail'] ?? null;
        $row['tanggal'] = $row['tanggal'] ?? date('Y-m-d');
        $row['tags'] = $tags;
        $row['watermark'] = !empty($row['watermark']);
        $row['file_size'] = $fileSize;
        $row['size_label'] = self::formatBytes($fileSize);
        $row['icon'] = $iconMap[$row['file_type']] ?? 'fa-file-lines text-slate-500';

        return $row;
    }

    private static function getMockByCategory(string $category): array
    {
        $rows = [];

        switch ($category) {
            case 'pelatihan_online':
                $rows = array_map(static function ($item) {
                    return [
                        'id' => $item['id'],
                        'judul' => $item['judul'],
                        'kategori' => 'pelatihan_online',
                        'deskripsi' => 'Rekaman pelatihan online. Durasi: ' . ($item['durasi'] ?? '-') . '. Views: ' . ($item['views'] ?? 0),
                        'file_path' => '',
                        'file_type' => 'MP4',
                        'thumbnail' => null,
                        'tanggal' => $item['tanggal'],
                        'tags' => ['video', 'pelatihan-online'],
                        'watermark' => false,
                    ];
                }, DummyData::getDocumentationVideos());
                break;
            case 'pelatihan_offline':
                $rows = array_map(static function ($item) {
                    return [
                        'id' => $item['id'],
                        'judul' => $item['judul'],
                        'kategori' => 'pelatihan_offline',
                        'deskripsi' => 'Album pelatihan offline. Foto: ' . ($item['foto_count'] ?? 0) . '. Peserta: ' . ($item['peserta'] ?? 0),
                        'file_path' => '',
                        'file_type' => 'JPG',
                        'thumbnail' => null,
                        'tanggal' => $item['tanggal'],
                        'tags' => ['album', 'pelatihan-offline'],
                        'watermark' => false,
                    ];
                }, DummyData::getDocumentationAlbums());
                break;
            case 'rapat':
                $rows = array_map(static function ($item) {
                    return [
                        'id' => $item['id'],
                        'judul' => $item['judul'],
                        'kategori' => 'rapat',
                        'deskripsi' => 'Dokumentasi rapat. Hadir: ' . ($item['hadir'] ?? 0) . '. Foto: ' . ($item['foto'] ?? 0),
                        'file_path' => '',
                        'file_type' => !empty($item['notulen']) ? 'PDF' : 'JPG',
                        'thumbnail' => null,
                        'tanggal' => $item['tanggal'],
                        'tags' => !empty($item['notulen']) ? ['rapat', 'notulen'] : ['rapat', 'foto'],
                        'watermark' => false,
                    ];
                }, DummyData::getDocumentationMeetings());
                break;
            case 'foto_kegiatan':
                $rows = array_map(static function ($item) {
                    $fileType = strtoupper((string) pathinfo((string) ($item['nama'] ?? ''), PATHINFO_EXTENSION));

                    return [
                        'id' => $item['id'],
                        'judul' => $item['kegiatan'],
                        'kategori' => 'foto_kegiatan',
                        'deskripsi' => 'Nama file: ' . ($item['nama'] ?? '-') . '. Ukuran: ' . ($item['size'] ?? '-'),
                        'file_path' => '',
                        'file_type' => $fileType !== '' ? $fileType : 'JPG',
                        'thumbnail' => null,
                        'tanggal' => $item['tanggal'],
                        'tags' => ['foto-kegiatan'],
                        'watermark' => true,
                    ];
                }, DummyData::getDocumentationPhotos());
                break;
        }

        return array_map([self::class, 'normalizeRow'], $rows);
    }

    private static function findMockById(int $id)
    {
        foreach ([
            ...self::getMockByCategory('pelatihan_online'),
            ...self::getMockByCategory('pelatihan_offline'),
            ...self::getMockByCategory('rapat'),
            ...self::getMockByCategory('foto_kegiatan'),
        ] as $row) {
            if ((int) $row['id'] === $id) {
                return $row;
            }
        }

        return null;
    }

    private static function encodeTags(array $tags): string
    {
        $tags = array_values(array_filter(array_map('trim', $tags), static function ($value) {
            return $value !== '';
        }));

        return json_encode($tags, JSON_UNESCAPED_UNICODE);
    }

    private static function decodeTags($tags): array
    {
        if (is_array($tags)) {
            return $tags;
        }

        $decoded = json_decode((string) $tags, true);
        return is_array($decoded) ? $decoded : [];
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 1) . ' ' . $units[$power];
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
