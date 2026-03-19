<?php
namespace App\Controllers;

use App\Utils\UploadHelper;
use App\Models\PelatihanModel;

class PelatihanController {
    public static function handleAsk() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL]);
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!validate_csrf($csrf_token)) {
            set_flash('error', 'Sesi tidak valid atau telah kedaluwarsa.');
            header('Location: ?page=pelatihan&sub=online');
            exit;
        }

        $pelatihan_id = (int)($_POST['pelatihan_id'] ?? 0);
        $pertanyaan   = trim($_POST['pertanyaan'] ?? '');
        if ($pelatihan_id <= 0 || $pertanyaan === '') {
            set_flash('error', 'Pertanyaan tidak boleh kosong.');
            header('Location: ?page=pelatihan&sub=online');
            exit;
        }

        $ok = PelatihanModel::addQnaPelatihan($pelatihan_id, sanitize_input($pertanyaan));
        set_flash($ok ? 'success' : 'error', $ok ? 'Pertanyaan Anda telah dikirim.' : 'Pertanyaan gagal dikirim.');
        header('Location: ?page=pelatihan&sub=online');
        exit;
    }

    public static function handleUploadMateri() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!validate_csrf($csrf_token)) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ?page=pelatihan&sub=materi');
            exit;
        }

        $judul    = sanitize_input($_POST['judul'] ?? '');
        $kategori = sanitize_input($_POST['kategori'] ?? '');
        $tipe     = strtoupper(sanitize_input($_POST['tipe'] ?? ''));

        $errors = [];
        if ($judul === '') $errors[] = 'Judul materi wajib diisi.';
        if ($kategori === '') $errors[] = 'Kategori materi wajib diisi.';
        if ($tipe === '') $errors[] = 'Format materi wajib diisi.';

        $file_path = null;
        if (empty($errors)) {
            // Tipe file diselaraskan dengan ekstensi upload supaya metadata tidak menipu pengguna.
            $detected_type = strtoupper(pathinfo($_FILES['file_materi']['name'] ?? '', PATHINFO_EXTENSION));
            if ($detected_type !== '' && $tipe !== $detected_type) {
                $errors[] = 'Format materi harus sesuai dengan berkas yang diunggah.';
            }
            $file_path = UploadHelper::handle($_FILES['file_materi'] ?? [], 'materi_', $judul, $errors);
        }

        if (!empty($errors) || !$file_path) {
            set_flash('error', implode(' ', $errors) ?: 'Berkas materi wajib diunggah.');
            header('Location: ?page=pelatihan&sub=materi');
            exit;
        }

        $ok = PelatihanModel::addMateri([
            'judul'     => $judul,
            'kategori'  => $kategori,
            'tipe'      => $tipe,
            'file_path' => $file_path,
            'file_size' => $_FILES['file_materi']['size'] ?? 0,
        ]);
        set_flash($ok ? 'success' : 'error', $ok ? 'Materi berhasil diunggah.' : 'Materi gagal diunggah.');
        header('Location: ?page=pelatihan&sub=materi');
        exit;
    }

    public static function handleDownloadMateri() {
        // Materi tetap dilindungi login walau route download diproses sebelum guard halaman.
        require_role([ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL]);
        $id = (int)($_GET['id'] ?? 0);
        $materi = PelatihanModel::getMateriById($id);
        if (!$materi || empty($materi['file_path'])) {
            set_flash('error', 'Materi tidak ditemukan.');
            header('Location: ?page=pelatihan&sub=materi');
            exit;
        }

        // basename menutup kemungkinan traversal bila nilai file_path rusak di database.
        $stored_name = basename((string) $materi['file_path']);
        $file_path = rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $stored_name;
        if (!is_file($file_path)) {
            set_flash('error', 'File materi tidak tersedia.');
            header('Location: ?page=pelatihan&sub=materi');
            exit;
        }

        PelatihanModel::incrementMateriDownloads($id);

        // File dikirim lewat PHP karena folder uploads sekarang tidak dibuka langsung ke publik.
        $download_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $materi['judul'] ?? basename($stored_name));
        $extension = pathinfo($stored_name, PATHINFO_EXTENSION);
        if ($extension !== '' && stripos($download_name, '.' . $extension) === false) {
            $download_name .= '.' . $extension;
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($file_path) : 'application/octet-stream';
        header('Content-Description: File Transfer');
        header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $download_name . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        readfile($file_path);
        exit;
    }
}
