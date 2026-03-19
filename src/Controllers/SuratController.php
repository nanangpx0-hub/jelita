<?php
namespace App\Controllers;

use App\Utils\UploadHelper;
use App\Models\SuratModel;

class SuratController {
    public static function handleTambahSK() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=sk');
            exit;
        }

        $nomor_sk   = sanitize_input($_POST['nomor_sk'] ?? '');
        $judul      = sanitize_input($_POST['judul'] ?? '');
        $tanggal_sk = $_POST['tanggal_sk'] ?? '';

        $errors = [];
        if ($nomor_sk === '') $errors[] = 'Nomor SK wajib diisi.';
        if ($judul === '') $errors[] = 'Judul SK wajib diisi.';
        if ($tanggal_sk === '') $errors[] = 'Tanggal SK wajib diisi.';

        $file_path = null;
        if (empty($errors)) {
            $file_path = UploadHelper::handle($_FILES['file_sk'] ?? [], 'sk_', $nomor_sk, $errors);
        }

        if (!empty($errors)) {
            set_flash('error', implode(' ', $errors));
            header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=sk');
            exit;
        }

        $ok = SuratModel::addSK([
            'nomor_sk'   => $nomor_sk,
            'judul'      => $judul,
            'tanggal_sk' => $tanggal_sk,
            'file_path'  => $file_path,
            'status'     => 'published',
        ]);
        set_flash($ok ? 'success' : 'error', $ok ? 'SK berhasil disimpan.' : 'SK gagal disimpan.');
        header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=sk');
        exit;
    }

    public static function handleTambahSuratMasuk() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=surat-masuk');
            exit;
        }

        $data = [
            'nomor_surat'    => sanitize_input($_POST['nomor_surat'] ?? ''),
            'pengirim'       => sanitize_input($_POST['pengirim'] ?? ''),
            'perihal'        => sanitize_input($_POST['perihal'] ?? ''),
            'tanggal_surat'  => $_POST['tanggal_surat'] ?? '',
            'tanggal_terima' => $_POST['tanggal_terima'] ?? '',
        ];

        $errors = [];
        foreach ($data as $key => $val) if ($val === '') $errors[] = ucfirst(str_replace('_', ' ', $key)) . ' wajib diisi.';

        $file_path = null;
        if (empty($errors)) {
            $file_path = UploadHelper::handle($_FILES['file_surat'] ?? [], 'sm_', $data['nomor_surat'], $errors);
        }

        if (!empty($errors)) {
            set_flash('error', implode(' ', $errors));
            header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=surat-masuk');
            exit;
        }

        $data['file_path'] = $file_path;
        $ok = SuratModel::addSuratMasuk($data);
        set_flash($ok ? 'success' : 'error', $ok ? 'Surat masuk dicatat.' : 'Gagal mencatat surat masuk.');
        header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=surat-masuk');
        exit;
    }

    public static function handleTambahSuratKeluar() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=surat-keluar');
            exit;
        }

        $data = [
            'nomor_surat'   => sanitize_input($_POST['nomor_surat'] ?? ''),
            'tujuan'        => sanitize_input($_POST['tujuan'] ?? ''),
            'perihal'       => sanitize_input($_POST['perihal'] ?? ''),
            'tanggal_surat' => $_POST['tanggal_surat'] ?? '',
        ];

        $errors = [];
        foreach ($data as $key => $val) if ($val === '') $errors[] = ucfirst(str_replace('_', ' ', $key)) . ' wajib diisi.';

        $file_path = null;
        if (empty($errors)) {
            $file_path = UploadHelper::handle($_FILES['file_surat'] ?? [], 'skel_', $data['nomor_surat'], $errors);
        }

        if (!empty($errors)) {
            set_flash('error', implode(' ', $errors));
            header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=surat-keluar');
            exit;
        }

        $data['file_path'] = $file_path;
        $ok = SuratModel::addSuratKeluar($data);
        set_flash($ok ? 'success' : 'error', $ok ? 'Surat keluar dibuat.' : 'Gagal membuat surat keluar.');
        header('Location: ?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=surat-keluar');
        exit;
    }

    public static function handleTambahMemorandum() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ' . self::memoRedirectUrl());
            exit;
        }

        $data = self::collectMemorandumData();
        $errors = self::validateMemorandumData($data);
        if (!empty($errors)) {
            set_flash('error', implode(' ', $errors));
            header('Location: ' . self::memoRedirectUrl());
            exit;
        }

        $ok = SuratModel::addMemorandum($data);
        set_flash($ok ? 'success' : 'error', $ok ? 'Memorandum berhasil disimpan.' : 'Gagal menyimpan memorandum.');
        header('Location: ' . self::memoRedirectUrl());
        exit;
    }

    public static function handleUpdateMemorandum() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        $memoId = (int) ($_GET['id'] ?? 0);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ' . self::memoRedirectUrl($memoId));
            exit;
        }

        if ($memoId <= 0) {
            set_flash('error', 'Memorandum yang akan diperbarui tidak valid.');
            header('Location: ' . self::memoRedirectUrl());
            exit;
        }

        $data = self::collectMemorandumData();
        $errors = self::validateMemorandumData($data);
        if (!empty($errors)) {
            set_flash('error', implode(' ', $errors));
            header('Location: ' . self::memoRedirectUrl($memoId));
            exit;
        }

        $ok = SuratModel::updateMemorandum($memoId, $data);
        set_flash($ok ? 'success' : 'error', $ok ? 'Memorandum berhasil diperbarui.' : 'Gagal memperbarui memorandum.');
        header('Location: ' . self::memoRedirectUrl());
        exit;
    }

    public static function handleHapusMemorandum() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR]);
        $memoId = (int) ($_GET['id'] ?? 0);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ' . self::memoRedirectUrl());
            exit;
        }

        if ($memoId <= 0) {
            set_flash('error', 'Memorandum yang akan dihapus tidak valid.');
            header('Location: ' . self::memoRedirectUrl());
            exit;
        }

        $ok = SuratModel::deleteMemorandum($memoId);
        set_flash($ok ? 'success' : 'error', $ok ? 'Memorandum berhasil dihapus.' : 'Gagal menghapus memorandum.');
        header('Location: ' . self::memoRedirectUrl());
        exit;
    }

    /**
     * Data memorandum disimpan dalam bentuk ter-trim agar escaping cukup dilakukan saat render view.
     */
    private static function collectMemorandumData(): array
    {
        return [
            'nomor' => trim((string) ($_POST['nomor'] ?? '')),
            'tipe' => trim((string) ($_POST['tipe'] ?? 'memo')),
            'judul' => trim((string) ($_POST['judul'] ?? '')),
            'konten' => trim((string) ($_POST['konten'] ?? '')),
            'tanggal' => trim((string) ($_POST['tanggal'] ?? '')),
            'waktu' => trim((string) ($_POST['waktu'] ?? '')),
            'tempat' => trim((string) ($_POST['tempat'] ?? '')),
            'distribusi_email' => isset($_POST['distribusi_email']),
            'distribusi_sms' => isset($_POST['distribusi_sms']),
        ];
    }

    private static function validateMemorandumData(array $data): array
    {
        $errors = [];
        if ($data['nomor'] === '') $errors[] = 'Nomor memorandum wajib diisi.';
        if (!in_array($data['tipe'], ['memo', 'undangan'], true)) $errors[] = 'Tipe memorandum tidak valid.';
        if ($data['judul'] === '') $errors[] = 'Judul memorandum wajib diisi.';
        if ($data['tanggal'] === '') $errors[] = 'Tanggal memorandum wajib diisi.';

        return $errors;
    }

    private static function memoRedirectUrl(int $memoId = 0): string
    {
        $url = '?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=memorandum';
        if ($memoId > 0) {
            $url .= '&edit=' . $memoId;
        }

        return $url;
    }
}
