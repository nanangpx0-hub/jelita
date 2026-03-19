<?php
namespace App\Controllers;

use App\Utils\UploadHelper;
use App\Models\RekrutmenModel;

class RekrutmenController {
    public static function handlePendaftaran() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!validate_csrf($csrf_token)) {
            set_flash('error', 'Sesi tidak valid atau telah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.');
            header('Location: ?page=rekrutmen-petugas&sub=administrasi');
            exit;
        }

        $nama_lengkap = sanitize_input($_POST['nama_lengkap'] ?? '');
        $nik          = preg_replace('/\D/', '', $_POST['nik'] ?? '');
        $email        = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $no_hp        = sanitize_input($_POST['no_hp'] ?? '');
        $alamat       = sanitize_input($_POST['alamat'] ?? '');
        $posisi       = in_array($_POST['posisi'] ?? '', ['PCL', 'PML'], true) ? $_POST['posisi'] : '';
        $wilayah      = sanitize_input($_POST['wilayah'] ?? '');

        $errors = [];
        if ($nama_lengkap === '') $errors[] = 'Nama lengkap wajib diisi.';
        if (strlen($nik) !== 16) $errors[] = 'NIK harus terdiri dari 16 digit.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
        if ($no_hp === '') $errors[] = 'No. HP wajib diisi.';
        if ($alamat === '') $errors[] = 'Alamat wajib diisi.';
        if ($posisi === '') $errors[] = 'Posisi yang dilamar wajib dipilih.';
        if ($wilayah === '') $errors[] = 'Wilayah pilihan wajib dipilih.';

        $upload_paths = ['dok_ktp' => null, 'dok_ijazah' => null, 'dok_foto' => null];
        if (empty($errors)) {
            foreach (['dok_ktp', 'dok_ijazah', 'dok_foto'] as $field) {
                $prefix = str_replace('dok_', '', $field) . '_';
                $path = UploadHelper::handle($_FILES[$field] ?? [], $prefix, $nik, $errors);
                if ($path) {
                    $upload_paths[$field] = $path;
                }
            }
        }

        if (!empty($errors)) {
            set_flash('error', implode(' ', $errors));
            header('Location: ?page=rekrutmen-petugas&sub=administrasi');
            exit;
        }

        $saved = RekrutmenModel::addPendaftaranPetugas([
            'nama_lengkap' => $nama_lengkap,
            'nik'          => $nik,
            'email'        => $email,
            'no_hp'        => $no_hp,
            'alamat'       => $alamat,
            'posisi'       => $posisi,
            'wilayah'      => $wilayah,
            'dok_ktp'      => $upload_paths['dok_ktp'],
            'dok_ijazah'   => $upload_paths['dok_ijazah'],
            'dok_foto'     => $upload_paths['dok_foto'],
        ]);

        if ($saved) {
            set_flash('success', 'Pendaftaran Anda berhasil dikirim. Silakan cek status kelengkapan berkas secara berkala.');
        } else {
            set_flash('error', 'Pendaftaran gagal disimpan. Pastikan NIK dan email belum pernah digunakan.');
        }
        header('Location: ?page=rekrutmen-petugas&sub=administrasi');
        exit;
    }
}
