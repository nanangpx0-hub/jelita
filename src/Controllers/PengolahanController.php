<?php
namespace App\Controllers;

use App\Models\PengolahanModel;

class PengolahanController {
    public static function handleLaporAnomaly() {
        require_role([ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL]);
        if (!validate_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Sesi tidak valid.');
            header('Location: ?page=pengolahan&sub=anomaly');
            exit;
        }

        $judul   = sanitize_input($_POST['judul'] ?? '');
        $wilayah = sanitize_input($_POST['wilayah'] ?? '');

        if ($judul === '' || $wilayah === '') {
            set_flash('error', 'Judul dan wilayah wajib diisi.');
            header('Location: ?page=pengolahan&sub=anomaly');
            exit;
        }

        $ok = PengolahanModel::addAnomaly([
            'judul'   => $judul,
            'wilayah' => $wilayah,
            'status'  => 'reported'
        ]);
        set_flash($ok ? 'success' : 'error', $ok ? 'Anomali dilaporkan.' : 'Gagal melaporkan anomali.');
        header('Location: ?page=pengolahan&sub=anomaly');
        exit;
    }
}
