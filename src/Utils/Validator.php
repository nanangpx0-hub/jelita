<?php
namespace App\Utils;

use PDOException;

/**
 * ValidationResult — Hasil validasi data usaha
 */
class ValidationResult
{
    /** @var bool Apakah data valid (tidak ada error) */
    public bool $valid;

    /**
     * Daftar error validasi
     * Format: [ ['code' => 'NPWP_INVALID', 'field' => 'npwp', 'message' => '...'] ]
     * @var array
     */
    public array $errors;

    /**
     * Daftar peringatan (duplikat potensial, dll)
     * Format: [ ['code' => 'DUPLIKAT_POTENSIAL', 'field' => '...', 'message' => '...'] ]
     * @var array
     */
    public array $warnings;

    /** @var bool Flag jika terjadi error database saat menyimpan anomali */
    public bool $db_error = false;

    public function __construct(bool $valid = true, array $errors = [], array $warnings = [])
    {
        $this->valid    = $valid;
        $this->errors   = $errors;
        $this->warnings = $warnings;
    }
}

/**
 * Validator — Validasi data usaha SE2026
 *
 * Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8
 */
class Validator
{
    /**
     * Jalankan semua aturan validasi pada data usaha.
     * Anomali yang ditemukan disimpan ke tabel anomali_validasi.
     *
     * @param array $data Data usaha (field: nama_usaha, npwp, jalan, nomor, kecamatan_id,
     *                    skala, jumlah_tk, no_telepon, usaha_id, pcl_id, dsb.)
     * @return ValidationResult
     */
    public static function validate(array $data): ValidationResult
    {
        $errors   = [];
        $warnings = [];

        // 1. Validasi NPWP
        if (!empty($data['npwp']) && !self::validateNpwp((string)$data['npwp'])) {
            $errors[] = [
                'code'    => 'NPWP_INVALID',
                'field'   => 'npwp',
                'message' => 'NPWP harus tepat 15 digit numerik.',
            ];
        }

        // 2. Validasi alamat
        $alamat = [
            'jalan'        => $data['jalan'] ?? null,
            'nomor'        => $data['nomor'] ?? null,
            'kecamatan_id' => $data['kecamatan_id'] ?? null,
        ];
        if (!self::validateAlamat($alamat)) {
            $errors[] = [
                'code'    => 'ALAMAT_TIDAK_LENGKAP',
                'field'   => 'alamat',
                'message' => 'Alamat tidak lengkap: jalan, nomor, dan kecamatan wajib diisi.',
            ];
        }

        // 3. Konsistensi skala vs jumlah TK
        if (!empty($data['skala']) && isset($data['jumlah_tk'])) {
            if (!self::checkSkalaConsistency((string)$data['skala'], (int)$data['jumlah_tk'])) {
                $errors[] = [
                    'code'    => 'SKALA_TK_TIDAK_KONSISTEN',
                    'field'   => 'skala',
                    'message' => 'Skala usaha tidak sesuai dengan jumlah tenaga kerja (UU No. 20/2008).',
                ];
            }
        }

        // 4. Deteksi duplikat potensial (similarity > 90%)
        if (self::checkDuplicate($data)) {
            $warnings[] = [
                'code'    => 'DUPLIKAT_POTENSIAL',
                'field'   => 'nama_usaha',
                'message' => 'Data usaha ini mirip (>90%) dengan entri yang sudah ada. Mohon konfirmasi PML.',
            ];
        }

        // 5. Deteksi duplikat PCL (data sama dari 2 PCL berbeda dalam 24 jam)
        if (self::checkDuplicatePcl($data)) {
            $warnings[] = [
                'code'    => 'DUPLIKAT_PCL',
                'field'   => 'pcl_id',
                'message' => 'Data usaha ini sudah diinput oleh PCL lain dalam 24 jam terakhir. Mohon konfirmasi PML.',
            ];
        }

        $valid  = empty($errors);
        $result = new ValidationResult($valid, $errors, $warnings);

        // Simpan anomali ke database jika ada error atau warning
        if (!empty($errors) || !empty($warnings)) {
            $usahaId = isset($data['usaha_id']) ? (int)$data['usaha_id'] : 0;
            if ($usahaId > 0) {
                $saved = self::saveAnomalies($usahaId, array_merge($errors, $warnings));
                if (!$saved) {
                    $result->db_error = true;
                }
            }
        }

        return $result;
    }

    /**
     * Cek apakah data usaha merupakan duplikat potensial berdasarkan similarity
     * nama + alamat + telepon > 90% menggunakan similar_text().
     *
     * @param array $data Data usaha baru
     * @return bool true jika duplikat potensial ditemukan
     */
    public static function checkDuplicate(array $data): bool
    {
        global $pdo;
        if (!$pdo) return false;

        // Bangun string pembanding dari data baru
        $newStr = self::buildComparisonString($data);
        if (empty(trim($newStr))) return false;

        try {
            // Batasi pencarian ke kecamatan yang sama untuk efisiensi
            $params = [];
            $sql    = "SELECT id, nama_usaha, jalan, nomor, no_telepon FROM usaha WHERE 1=1";

            if (!empty($data['kecamatan_id'])) {
                $sql             .= " AND kecamatan_id = :kecamatan_id";
                $params[':kecamatan_id'] = (int)$data['kecamatan_id'];
            }

            // Kecualikan record yang sedang diedit
            if (!empty($data['usaha_id'])) {
                $sql             .= " AND id != :usaha_id";
                $params[':usaha_id'] = (int)$data['usaha_id'];
            }

            $sql .= " LIMIT 500";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            foreach ($rows as $row) {
                $existingStr = self::buildComparisonString([
                    'nama_usaha'  => $row['nama_usaha'] ?? '',
                    'jalan'       => $row['jalan'] ?? '',
                    'nomor'       => $row['nomor'] ?? '',
                    'no_telepon'  => $row['no_telepon'] ?? '',
                ]);

                similar_text(
                    strtolower($newStr),
                    strtolower($existingStr),
                    $percent
                );

                if ($percent > 90.0) {
                    return true;
                }
            }
        } catch (PDOException $e) {
            error_log('[Validator] checkDuplicate() error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Validasi format NPWP: tepat 15 digit numerik.
     *
     * @param string $npwp
     * @return bool
     */
    public static function validateNpwp(string $npwp): bool
    {
        // Hapus karakter non-digit (titik, strip) sebelum validasi
        $digits = preg_replace('/\D/', '', $npwp);
        return strlen($digits) === 15;
    }

    /**
     * Validasi kelengkapan alamat: jalan, nomor, dan kecamatan tidak boleh kosong.
     *
     * @param array $alamat Array dengan key: jalan, nomor, kecamatan_id (atau kecamatan)
     * @return bool
     */
    public static function validateAlamat(array $alamat): bool
    {
        $jalan      = trim((string)($alamat['jalan'] ?? ''));
        $nomor      = trim((string)($alamat['nomor'] ?? ''));
        // Terima kecamatan_id (int) atau kecamatan (string)
        $kecamatan  = trim((string)($alamat['kecamatan_id'] ?? $alamat['kecamatan'] ?? ''));

        return $jalan !== '' && $nomor !== '' && $kecamatan !== '' && $kecamatan !== '0';
    }

    /**
     * Cek konsistensi skala usaha vs jumlah tenaga kerja sesuai UU No. 20/2008:
     * - Mikro  : TK ≤ 4
     * - Kecil  : TK 5–19
     * - Menengah: TK 20–99
     * - Besar  : TK ≥ 100
     *
     * @param string $skala  Nilai: 'mikro', 'kecil', 'menengah', 'besar'
     * @param int    $tk     Jumlah tenaga kerja
     * @return bool true jika konsisten, false jika tidak
     */
    public static function checkSkalaConsistency(string $skala, int $tk): bool
    {
        switch (strtolower($skala)) {
            case 'mikro':
                return $tk <= 4;
            case 'kecil':
                return $tk >= 5 && $tk <= 19;
            case 'menengah':
                return $tk >= 20 && $tk <= 99;
            case 'besar':
                return $tk >= 100;
            default:
                return false;
        }
    }

    /**
     * Generate laporan harian anomali validasi, dikelompokkan per kode_anomali.
     *
     * @return array [ ['kode_anomali' => '...', 'jumlah' => N, 'tanggal' => 'YYYY-MM-DD'], ... ]
     */
    public static function generateDailyReport(): array
    {
        global $pdo;
        if (!$pdo) return [];

        try {
            $stmt = $pdo->prepare(
                "SELECT
                    kode_anomali,
                    COUNT(*) AS jumlah,
                    DATE(created_at) AS tanggal
                 FROM anomali_validasi
                 WHERE DATE(created_at) = CURDATE()
                 GROUP BY kode_anomali, DATE(created_at)
                 ORDER BY jumlah DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('[Validator] generateDailyReport() error: ' . $e->getMessage());
            return [];
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Cek duplikat dari PCL berbeda dalam 24 jam terakhir.
     *
     * @param array $data
     * @return bool
     */
    private static function checkDuplicatePcl(array $data): bool
    {
        global $pdo;
        if (!$pdo) return false;

        // Hanya relevan jika ada pcl_id
        if (empty($data['pcl_id'])) return false;

        $namaUsaha  = trim((string)($data['nama_usaha'] ?? ''));
        $jalan      = trim((string)($data['jalan'] ?? ''));
        if (empty($namaUsaha)) return false;

        try {
            $stmt = $pdo->prepare(
                "SELECT id, pcl_id FROM usaha
                 WHERE nama_usaha = :nama_usaha
                   AND jalan = :jalan
                   AND pcl_id != :pcl_id
                   AND created_at >= NOW() - INTERVAL 24 HOUR
                 LIMIT 1"
            );
            $stmt->execute([
                ':nama_usaha' => $namaUsaha,
                ':jalan'      => $jalan,
                ':pcl_id'     => (int)$data['pcl_id'],
            ]);
            return (bool)$stmt->fetch();
        } catch (PDOException $e) {
            error_log('[Validator] checkDuplicatePcl() error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Simpan daftar anomali ke tabel anomali_validasi.
     *
     * @param int   $usahaId
     * @param array $anomalies Array of ['code' => ..., 'field' => ..., 'message' => ...]
     * @return bool true jika semua berhasil disimpan
     */
    private static function saveAnomalies(int $usahaId, array $anomalies): bool
    {
        global $pdo;
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO anomali_validasi
                    (usaha_id, kode_anomali, jenis_anomali, detail, status)
                 VALUES
                    (:usaha_id, :kode_anomali, :jenis_anomali, :detail, 'open')"
            );

            foreach ($anomalies as $anomali) {
                $stmt->execute([
                    ':usaha_id'     => $usahaId,
                    ':kode_anomali' => $anomali['code'] ?? 'UNKNOWN',
                    ':jenis_anomali'=> $anomali['field'] ?? null,
                    ':detail'       => $anomali['message'] ?? null,
                ]);
            }
            return true;
        } catch (PDOException $e) {
            error_log('[Validator] saveAnomalies() error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bangun string gabungan nama_usaha + jalan + nomor + no_telepon untuk similarity check.
     *
     * @param array $data
     * @return string
     */
    private static function buildComparisonString(array $data): string
    {
        return implode('|', [
            trim((string)($data['nama_usaha'] ?? '')),
            trim((string)($data['jalan'] ?? '')),
            trim((string)($data['nomor'] ?? '')),
            preg_replace('/\D/', '', (string)($data['no_telepon'] ?? '')),
        ]);
    }
}
