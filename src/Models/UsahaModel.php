<?php
namespace App\Models;

use PDO;
use PDOException;

/**
 * UsahaModel — Model untuk data usaha SE2026
 *
 * Requirements: 3.1, 6.7, 10.2
 */
class UsahaModel
{
    /**
     * Insert atau update data usaha.
     * Jika $data['id'] ada dan > 0, lakukan UPDATE; jika tidak, lakukan INSERT.
     *
     * @param array $data Field-field tabel usaha
     * @return int ID record yang disimpan (lastInsertId untuk INSERT, id untuk UPDATE)
     */
    public static function save(array $data): int
    {
        global $pdo;
        if (!$pdo) return 0;

        try {
            if (!empty($data['id']) && (int)$data['id'] > 0) {
                // UPDATE
                $id = (int)$data['id'];
                $stmt = $pdo->prepare(
                    "UPDATE usaha SET
                        nama_usaha       = :nama_usaha,
                        nama_pemilik     = :nama_pemilik,
                        npwp             = :npwp,
                        nib              = :nib,
                        no_telepon       = :no_telepon,
                        email            = :email,
                        jalan            = :jalan,
                        nomor            = :nomor,
                        kecamatan_id     = :kecamatan_id,
                        kelurahan        = :kelurahan,
                        kode_pos         = :kode_pos,
                        lat              = :lat,
                        lng              = :lng,
                        kbli             = :kbli,
                        sektor           = :sektor,
                        skala            = :skala,
                        jumlah_tk        = :jumlah_tk,
                        omzet_tahunan    = :omzet_tahunan,
                        status_legalitas = :status_legalitas,
                        sumber_data      = :sumber_data,
                        pcl_id           = :pcl_id,
                        tahun_data       = :tahun_data
                    WHERE id = :id"
                );
                $stmt->execute([
                    ':nama_usaha'       => $data['nama_usaha'] ?? null,
                    ':nama_pemilik'     => $data['nama_pemilik'] ?? null,
                    ':npwp'             => $data['npwp'] ?? null,
                    ':nib'              => $data['nib'] ?? null,
                    ':no_telepon'       => $data['no_telepon'] ?? null,
                    ':email'            => $data['email'] ?? null,
                    ':jalan'            => $data['jalan'] ?? null,
                    ':nomor'            => $data['nomor'] ?? null,
                    ':kecamatan_id'     => isset($data['kecamatan_id']) ? (int)$data['kecamatan_id'] : null,
                    ':kelurahan'        => $data['kelurahan'] ?? null,
                    ':kode_pos'         => $data['kode_pos'] ?? null,
                    ':lat'              => isset($data['lat']) ? (float)$data['lat'] : null,
                    ':lng'              => isset($data['lng']) ? (float)$data['lng'] : null,
                    ':kbli'             => $data['kbli'] ?? null,
                    ':sektor'           => $data['sektor'] ?? null,
                    ':skala'            => $data['skala'] ?? null,
                    ':jumlah_tk'        => isset($data['jumlah_tk']) ? (int)$data['jumlah_tk'] : 0,
                    ':omzet_tahunan'    => isset($data['omzet_tahunan']) ? (float)$data['omzet_tahunan'] : null,
                    ':status_legalitas' => $data['status_legalitas'] ?? 'belum_terverifikasi',
                    ':sumber_data'      => $data['sumber_data'] ?? 'pcl',
                    ':pcl_id'           => isset($data['pcl_id']) ? (int)$data['pcl_id'] : null,
                    ':tahun_data'       => $data['tahun_data'] ?? 2026,
                    ':id'               => $id,
                ]);
                return $id;
            }

            // INSERT
            $stmt = $pdo->prepare(
                "INSERT INTO usaha
                    (nama_usaha, nama_pemilik, npwp, nib, no_telepon, email,
                     jalan, nomor, kecamatan_id, kelurahan, kode_pos, lat, lng,
                     kbli, sektor, skala, jumlah_tk, omzet_tahunan,
                     status_legalitas, sumber_data, pcl_id, tahun_data)
                 VALUES
                    (:nama_usaha, :nama_pemilik, :npwp, :nib, :no_telepon, :email,
                     :jalan, :nomor, :kecamatan_id, :kelurahan, :kode_pos, :lat, :lng,
                     :kbli, :sektor, :skala, :jumlah_tk, :omzet_tahunan,
                     :status_legalitas, :sumber_data, :pcl_id, :tahun_data)"
            );
            $stmt->execute([
                ':nama_usaha'       => $data['nama_usaha'] ?? null,
                ':nama_pemilik'     => $data['nama_pemilik'] ?? null,
                ':npwp'             => $data['npwp'] ?? null,
                ':nib'              => $data['nib'] ?? null,
                ':no_telepon'       => $data['no_telepon'] ?? null,
                ':email'            => $data['email'] ?? null,
                ':jalan'            => $data['jalan'] ?? null,
                ':nomor'            => $data['nomor'] ?? null,
                ':kecamatan_id'     => isset($data['kecamatan_id']) ? (int)$data['kecamatan_id'] : null,
                ':kelurahan'        => $data['kelurahan'] ?? null,
                ':kode_pos'         => $data['kode_pos'] ?? null,
                ':lat'              => isset($data['lat']) ? (float)$data['lat'] : null,
                ':lng'              => isset($data['lng']) ? (float)$data['lng'] : null,
                ':kbli'             => $data['kbli'] ?? null,
                ':sektor'           => $data['sektor'] ?? null,
                ':skala'            => $data['skala'] ?? null,
                ':jumlah_tk'        => isset($data['jumlah_tk']) ? (int)$data['jumlah_tk'] : 0,
                ':omzet_tahunan'    => isset($data['omzet_tahunan']) ? (float)$data['omzet_tahunan'] : null,
                ':status_legalitas' => $data['status_legalitas'] ?? 'belum_terverifikasi',
                ':sumber_data'      => $data['sumber_data'] ?? 'pcl',
                ':pcl_id'           => isset($data['pcl_id']) ? (int)$data['pcl_id'] : null,
                ':tahun_data'       => $data['tahun_data'] ?? 2026,
            ]);
            return (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('[UsahaModel] save() error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cari usaha berdasarkan ID.
     *
     * @param int $id
     * @return array|null Row data atau null jika tidak ditemukan
     */
    public static function findById(int $id): ?array
    {
        global $pdo;
        if (!$pdo) return null;

        try {
            $stmt = $pdo->prepare(
                "SELECT u.*, w.nama_kecamatan
                 FROM usaha u
                 LEFT JOIN wilayah_kerja w ON u.kecamatan_id = w.id
                 WHERE u.id = :id
                 LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row !== false ? $row : null;
        } catch (PDOException $e) {
            error_log('[UsahaModel] findById() error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cari semua usaha di kecamatan tertentu.
     *
     * @param int $kecamatanId
     * @return array
     */
    public static function findByKecamatan(int $kecamatanId): array
    {
        global $pdo;
        if (!$pdo) return [];

        try {
            $stmt = $pdo->prepare(
                "SELECT u.*, w.nama_kecamatan
                 FROM usaha u
                 LEFT JOIN wilayah_kerja w ON u.kecamatan_id = w.id
                 WHERE u.kecamatan_id = :kecamatan_id
                 ORDER BY u.nama_usaha ASC"
            );
            $stmt->execute([':kecamatan_id' => $kecamatanId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('[UsahaModel] findByKecamatan() error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cari semua usaha dengan filter opsional.
     *
     * Filter yang didukung: sektor, skala, kecamatan_id, tahun_data, sumber_data
     *
     * @param array $filters Associative array filter opsional
     * @return array
     */
    public static function findAll(array $filters = []): array
    {
        global $pdo;
        if (!$pdo) return [];

        try {
            $where  = [];
            $params = [];

            $allowed = ['sektor', 'skala', 'kecamatan_id', 'tahun_data', 'sumber_data'];
            foreach ($allowed as $field) {
                if (!empty($filters[$field])) {
                    $where[]          = "u.{$field} = :{$field}";
                    $params[":{$field}"] = $filters[$field];
                }
            }

            $sql = "SELECT u.*, w.nama_kecamatan
                    FROM usaha u
                    LEFT JOIN wilayah_kerja w ON u.kecamatan_id = w.id";

            if (!empty($where)) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }

            $sql .= ' ORDER BY u.created_at DESC';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('[UsahaModel] findAll() error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update field dari data OSS: status_legalitas, nama_usaha, jalan.
     *
     * Requirement 10.2: Jika OSS API error, data tidak boleh berubah.
     * Method ini hanya dipanggil setelah response OSS berhasil divalidasi.
     *
     * @param int   $id      ID usaha
     * @param array $ossData Data dari OSS (keys: status_legalitas, nama_usaha, jalan)
     * @return bool true jika berhasil diupdate
     */
    public static function updateFromOss(int $id, array $ossData): bool
    {
        global $pdo;
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare(
                "UPDATE usaha SET
                    status_legalitas = :status_legalitas,
                    nama_usaha       = :nama_usaha,
                    jalan            = :jalan
                 WHERE id = :id"
            );
            return $stmt->execute([
                ':status_legalitas' => $ossData['status_legalitas'] ?? 'belum_terverifikasi',
                ':nama_usaha'       => $ossData['nama_usaha'] ?? null,
                ':jalan'            => $ossData['jalan'] ?? null,
                ':id'               => $id,
            ]);
        } catch (PDOException $e) {
            error_log('[UsahaModel] updateFromOss() error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cek apakah tabel usaha tersedia di database.
     */
    private static function tableExists(): bool
    {
        global $pdo;
        if (!$pdo) return false;

        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'usaha'");
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }
}
