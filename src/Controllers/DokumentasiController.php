<?php
namespace App\Controllers;

use App\Models\DokumentasiModel;
use App\Utils\UploadHelper;

class DokumentasiController
{
    public static function handleTambah()
    {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            self::redirectWithError('Sesi tidak valid.');
        }

        [$sub, $category] = self::resolveCategory();
        $data = self::collectData($category);
        $errors = self::validateData($data, true);
        $filePath = null;

        if (empty($errors)) {
            $filePath = UploadHelper::handle($_FILES['file_dokumentasi'] ?? [], 'dok_', $data['judul'], $errors);
        }

        if (!empty($errors) || !$filePath) {
            self::redirectWithError(implode(' ', array_filter($errors)) ?: 'File dokumentasi wajib diunggah.', $sub);
        }

        $data['file_path'] = $filePath;
        $data['file_type'] = strtoupper((string) pathinfo($filePath, PATHINFO_EXTENSION));

        $ok = DokumentasiModel::add($data);
        set_flash($ok ? 'success' : 'error', $ok ? 'Dokumentasi berhasil disimpan.' : 'Gagal menyimpan dokumentasi.');
        header('Location: ' . self::buildBaseUrl($sub));
        exit;
    }

    public static function handleUpdate()
    {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        $docId = (int) ($_GET['id'] ?? 0);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            self::redirectWithError('Sesi tidak valid.');
        }

        $existing = DokumentasiModel::getById($docId);
        if (!$existing) {
            self::redirectWithError('Dokumentasi yang akan diperbarui tidak ditemukan.');
        }

        [$sub, $category] = self::resolveCategory();
        $data = self::collectData($category);
        $errors = self::validateData($data, false);
        $filePath = $existing['file_path'];
        $newFilePath = null;

        if (empty($_FILES['file_dokumentasi']['name'] ?? '')) {
            $data['file_path'] = $filePath;
            $data['file_type'] = $existing['file_type'];
        } else {
            $newFilePath = UploadHelper::handle($_FILES['file_dokumentasi'] ?? [], 'dok_', $data['judul'], $errors);
            if ($newFilePath) {
                $data['file_path'] = $newFilePath;
                $data['file_type'] = strtoupper((string) pathinfo($newFilePath, PATHINFO_EXTENSION));
            }
        }

        if (!empty($errors)) {
            self::redirectWithError(implode(' ', $errors), $sub, $docId);
        }

        $ok = DokumentasiModel::update($docId, $data);
        if ($ok && $newFilePath && $filePath !== '' && $filePath !== $newFilePath) {
            self::deleteStoredFile($filePath);
        }

        set_flash($ok ? 'success' : 'error', $ok ? 'Dokumentasi berhasil diperbarui.' : 'Gagal memperbarui dokumentasi.');
        header('Location: ' . self::buildBaseUrl($sub));
        exit;
    }

    public static function handleHapus()
    {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        $docId = (int) ($_GET['id'] ?? 0);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            self::redirectWithError('Sesi tidak valid.');
        }

        $existing = DokumentasiModel::getById($docId);
        if (!$existing) {
            self::redirectWithError('Dokumentasi yang akan dihapus tidak ditemukan.');
        }

        $sub = self::subFromCategory($existing['kategori']);
        $ok = DokumentasiModel::delete($docId);
        if ($ok) {
            self::deleteStoredFile($existing['file_path']);
        }

        set_flash($ok ? 'success' : 'error', $ok ? 'Dokumentasi berhasil dihapus.' : 'Gagal menghapus dokumentasi.');
        header('Location: ' . self::buildBaseUrl($sub));
        exit;
    }

    public static function handleDownload()
    {
        require_role([ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL]);
        $docId = (int) ($_GET['id'] ?? 0);
        $doc = DokumentasiModel::getById($docId);

        if (!$doc || empty($doc['file_path'])) {
            set_flash('error', 'File dokumentasi tidak ditemukan.');
            header('Location: ' . self::buildBaseUrl($_GET['sub'] ?? 'pelatihan-online'));
            exit;
        }

        $storedName = basename((string) $doc['file_path']);
        $filePath = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storedName;
        if (!is_file($filePath)) {
            set_flash('error', 'Berkas dokumentasi tidak tersedia di server.');
            header('Location: ' . self::buildBaseUrl(self::subFromCategory($doc['kategori'])));
            exit;
        }

        $downloadName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $doc['judul'] ?: $storedName);
        $extension = strtolower((string) pathinfo($storedName, PATHINFO_EXTENSION));
        if ($extension !== '') {
            $lowerName = strtolower($downloadName);
            $suffix = '.' . $extension;
            $hasSuffix = substr($lowerName, -strlen($suffix)) === $suffix;
            $downloadName .= $hasSuffix ? '' : $suffix;
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($filePath) : 'application/octet-stream';
        header('Content-Description: File Transfer');
        header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=0, must-revalidate');

        readfile($filePath);
        exit;
    }

    private static function collectData(string $category): array
    {
        return [
            'judul' => trim((string) ($_POST['judul'] ?? '')),
            'kategori' => $category,
            'deskripsi' => trim((string) ($_POST['deskripsi'] ?? '')),
            'tanggal' => trim((string) ($_POST['tanggal'] ?? '')),
            'tags' => preg_split('/\s*,\s*/', trim((string) ($_POST['tags'] ?? '')), -1, PREG_SPLIT_NO_EMPTY) ?: [],
            'watermark' => isset($_POST['watermark']),
            'thumbnail' => null,
        ];
    }

    private static function validateData(array $data, bool $requireFile): array
    {
        $errors = [];
        if ($data['judul'] === '') $errors[] = 'Judul dokumentasi wajib diisi.';
        if ($data['tanggal'] === '') $errors[] = 'Tanggal dokumentasi wajib diisi.';
        if (!in_array($data['kategori'], ['pelatihan_online', 'pelatihan_offline', 'rapat', 'foto_kegiatan'], true)) {
            $errors[] = 'Kategori dokumentasi tidak valid.';
        }

        $file = $_FILES['file_dokumentasi'] ?? [];
        if ($requireFile && empty($file['name'] ?? '')) {
            $errors[] = 'File dokumentasi wajib diunggah.';
        }

        if (!empty($file['name'] ?? '')) {
            $ext = strtolower((string) pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            $allowed = self::allowedExtensionsByCategory($data['kategori']);
            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Ekstensi file tidak sesuai dengan kategori dokumentasi.';
            }
        }

        return $errors;
    }

    private static function allowedExtensionsByCategory(string $category): array
    {
        switch ($category) {
            case 'pelatihan_online':
                return ['mp4'];
            case 'pelatihan_offline':
            case 'foto_kegiatan':
                return ['jpg', 'jpeg', 'png'];
            case 'rapat':
                return ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            default:
                return ALLOWED_EXTENSIONS;
        }
    }

    private static function resolveCategory(): array
    {
        $sub = (string) ($_GET['sub'] ?? 'pelatihan-online');
        switch ($sub) {
            case 'pelatihan-offline':
                $category = 'pelatihan_offline';
                break;
            case 'rapat':
                $category = 'rapat';
                break;
            case 'foto':
                $category = 'foto_kegiatan';
                break;
            case 'pelatihan-online':
            default:
                $category = 'pelatihan_online';
                break;
        }

        return [$sub, $category];
    }

    private static function subFromCategory(string $category): string
    {
        switch ($category) {
            case 'pelatihan_offline':
                return 'pelatihan-offline';
            case 'rapat':
                return 'rapat';
            case 'foto_kegiatan':
                return 'foto';
            case 'pelatihan_online':
            default:
                return 'pelatihan-online';
        }
    }

    private static function buildBaseUrl(string $sub): string
    {
        return '?page=dokumentasi&sub=' . $sub;
    }

    private static function redirectWithError(string $message, ?string $sub = null, int $editId = 0): void
    {
        set_flash('error', $message);
        $sub = $sub ?? (string) ($_GET['sub'] ?? 'pelatihan-online');
        $url = self::buildBaseUrl($sub);
        if ($editId > 0) {
            $url .= '&edit=' . $editId;
        }

        header('Location: ' . $url);
        exit;
    }

    private static function deleteStoredFile(string $filePath): void
    {
        if ($filePath === '') return;
        $storedName = basename($filePath);
        $fullPath = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $storedName;
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
