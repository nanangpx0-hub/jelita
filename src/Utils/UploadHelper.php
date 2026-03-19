<?php
namespace App\Utils;

use finfo;

class UploadHelper {
    /**
     * Validasi MIME type file menggunakan finfo
     */
    public static function validateMime(string $tmp_path, string $extension): bool {
        if (!is_file($tmp_path)) return false;
        $extension = strtolower($extension);
        if (!isset(ALLOWED_MIME_TYPES[$extension])) return false;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detected_mime = $finfo->file($tmp_path);

        return in_array($detected_mime, ALLOWED_MIME_TYPES[$extension], true);
    }

    /**
     * Handle file upload dengan validasi lengkap.
     * 
     * @param array  $file        Elemen dari $_FILES (misal $_FILES['dok_ktp'])
     * @param string $prefix      Prefix nama file (misal 'ktp_')
     * @param string $identifier  ID unik (misal NIK atau Judul) untuk nama file
     * @param array  $errors      Array reference untuk menampung error
     * @return string|null        Nama file yang berhasil disimpan atau null jika gagal
     */
    public static function handle(array $file, string $prefix, string $identifier, array &$errors): ?string {
        // Guard ini mencegah undefined index saat controller mengirim array kosong dari $_FILES.
        if (empty($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Gagal mengunggah berkas {$prefix}.";
            return null;
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = "Ukuran berkas {$prefix} melebihi batas maksimum (5MB).";
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Khusus SK dan Surat hanya PDF
        $allowed = ALLOWED_EXTENSIONS;
        if (in_array($prefix, ['sk_', 'sm_', 'skel_'], true)) {
            $allowed = ['pdf'];
        }

        if (!in_array($ext, $allowed, true)) {
            $errors[] = "Ekstensi berkas {$prefix} tidak diizinkan.";
            return null;
        }

        if (!self::validateMime($file['tmp_name'], $ext)) {
            $errors[] = "Tipe berkas {$prefix} tidak sesuai dengan konten aslinya.";
            return null;
        }

        $clean_id = preg_replace('/[^a-zA-Z0-9]/', '_', $identifier);
        // Suffix acak mencegah tabrakan nama saat beberapa upload terjadi di detik yang sama.
        $safe_name = $prefix . $clean_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target_path = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safe_name;

        if (!is_dir(UPLOAD_DIR)) {
            @mkdir(UPLOAD_DIR, 0775, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            $errors[] = "Gagal menyimpan berkas {$safe_name} ke server.";
            return null;
        }

        return $safe_name;
    }
}
