<?php
/**
 * =========================================================================
 * PORTAL UTAMA SISE2026 BPS KABUPATEN JEMBER
 * =========================================================================
 * Front Controller — semua akses halaman melalui file ini.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/functions.php';

// Routing
$page = isset($_GET['page']) ? sanitize_input($_GET['page']) : 'beranda';
$sub  = isset($_GET['sub'])  ? sanitize_input($_GET['sub'])  : '';

// Handle POST actions (delegated to Controllers)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page === 'login') \App\Controllers\AuthController::handleLogin();
    
    if ($page === 'rekrutmen-petugas' && $sub === 'administrasi' && ($_GET['action'] ?? '') === 'daftar') {
        \App\Controllers\RekrutmenController::handlePendaftaran();
    }

    if ($page === 'pelatihan') {
        if ($sub === 'online' && ($_GET['action'] ?? '') === 'ask') \App\Controllers\PelatihanController::handleAsk();
        if ($sub === 'materi' && ($_GET['action'] ?? '') === 'upload') \App\Controllers\PelatihanController::handleUploadMateri();
    }

    if ($page === 'teknis-dan-administrasi' && $sub === 'kelengkapan-administrasi') {
        $item = $_GET['item'] ?? '';
        $action = $_GET['action'] ?? '';
        if ($item === 'sk' && $action === 'tambah-sk') \App\Controllers\SuratController::handleTambahSK();
        if ($item === 'surat-masuk' && $action === 'tambah-surat') \App\Controllers\SuratController::handleTambahSuratMasuk();
        if ($item === 'surat-keluar' && $action === 'tambah-surat') \App\Controllers\SuratController::handleTambahSuratKeluar();
        if ($item === 'memorandum' && $action === 'tambah-memorandum') \App\Controllers\SuratController::handleTambahMemorandum();
        if ($item === 'memorandum' && $action === 'update-memorandum') \App\Controllers\SuratController::handleUpdateMemorandum();
        if ($item === 'memorandum' && $action === 'hapus-memorandum') \App\Controllers\SuratController::handleHapusMemorandum();
    }

    if ($page === 'pengolahan' && $sub === 'anomaly' && ($_GET['action'] ?? '') === 'lapor') {
        \App\Controllers\PengolahanController::handleLaporAnomaly();
    }

    if ($page === 'dokumentasi') {
        $action = $_GET['action'] ?? '';
        if ($action === 'tambah') \App\Controllers\DokumentasiController::handleTambah();
        if ($action === 'update') \App\Controllers\DokumentasiController::handleUpdate();
        if ($action === 'hapus') \App\Controllers\DokumentasiController::handleHapus();
    }
}

// Special case for logout (GET)
if ($page === 'logout') {
    \App\Controllers\AuthController::handleLogout();
}

// Special case for material download (GET/POST in original, but mostly used as GET redirect)
if ($page === 'pelatihan' && $sub === 'materi' && ($_GET['action'] ?? '') === 'download' && isset($_GET['id'])) {
    \App\Controllers\PelatihanController::handleDownloadMateri();
}

if ($page === 'dokumentasi' && ($_GET['action'] ?? '') === 'download' && isset($_GET['id'])) {
    \App\Controllers\DokumentasiController::handleDownload();
}

// Protected pages map: page => allowed roles (empty = all authenticated)
$protected_pages = [
    // Gunakan nama route aktual agar guard top-level tidak terlewat.
    // Route ini menampung dua area: administrasi khusus admin/operator dan pelatihan untuk semua user login.
    'teknis-dan-administrasi' => [ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL],
    'pelatihan'    => [ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL],
    'pengolahan'   => [ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL],
    'dokumentasi'  => [ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL],
];

if (isset($protected_pages[$page])) {
    require_role($protected_pages[$page]);
}

// Load Header
require_once __DIR__ . '/views/partials/header.php';

// Routing
switch ($page) {
    case 'beranda':
        require_once __DIR__ . '/views/home.php';
        break;

    case 'rekrutmen':
        require_once __DIR__ . '/views/rekrutmen.php';
        break;

    // === REKRUTMEN (Public) ===
    case 'rekrutmen-petugas':
        if ($sub === 'administrasi') {
            require_once __DIR__ . '/views/rekrutmen/administrasi.php';
        } elseif ($sub === 'alokasi-petugas-dan-wilayah') {
            require_role([ROLE_ADMIN]);
            require_once __DIR__ . '/views/rekrutmen/alokasi.php';
        } elseif ($sub === 'pengumuman') {
            require_role([ROLE_ADMIN]);
            require_once __DIR__ . '/views/rekrutmen/pengumuman.php';
        } else {
            require_once __DIR__ . '/views/rekrutmen/administrasi.php';
        }
        break;

    // === TEKNIS & ADMINISTRASI (Admin + Operator) ===
    case 'teknis-dan-administrasi':
        if ($sub === 'kelengkapan-administrasi') {
            require_role([ROLE_ADMIN, ROLE_OPERATOR]);
            $admin_map = [
                'sk'            => '/views/teknis/sk.php',
                'surat-masuk'   => '/views/teknis/surat_masuk.php',
                'surat-keluar'  => '/views/teknis/surat_keluar.php',
                'memorandum'    => '/views/teknis/memorandum.php',
                'laporan'       => '/views/teknis/laporan_kegiatan.php',
                'notulen'       => '/views/teknis/notulen_rapat.php',
            ];
            $file = $admin_map[$_GET['item'] ?? 'sk'] ?? '/views/teknis/sk.php';
            require_once __DIR__ . $file;
        } elseif ($sub === 'pelatihan') {
            require_role([ROLE_ADMIN, ROLE_OPERATOR, ROLE_PML, ROLE_PCL]);
            $pelatihan_map = [
                'online'       => '/views/pelatihan/online.php',
                'offline'      => '/views/pelatihan/offline.php',
                'materi'       => '/views/pelatihan/materi.php',
                'pelaksanaan'  => '/views/pelatihan/pelaksanaan.php',
            ];
            $file = $pelatihan_map[$_GET['item'] ?? 'online'] ?? '/views/pelatihan/online.php';
            require_once __DIR__ . $file;
        } else {
            // Default view for 'teknis-dan-administrasi'
            require_once __DIR__ . '/views/home.php';
        }
        break;

    // === PELATIHAN (All Authenticated) ===
    case 'pelatihan':
        $pelatihan_map = [
            'online'        => '/views/pelatihan/online.php',
            'offline'       => '/views/pelatihan/offline.php',
            'materi'        => '/views/pelatihan/materi.php',
            'pelaksanaan'   => '/views/pelatihan/pelaksanaan.php',
        ];
        $file = $pelatihan_map[$sub] ?? '/views/pelatihan/online.php';
        require_once __DIR__ . $file;
        break;

    // === PENGOLAHAN ===
    case 'pengolahan':
        if ($sub === 'monitoring') {
            require_once __DIR__ . '/views/pengolahan/monitoring.php';
        } else {
            require_once __DIR__ . '/views/pengolahan/anomaly.php';
        }
        break;

    // === DOKUMENTASI ===
    case 'dokumentasi':
        $dok_map = [
            'pelatihan-online'  => '/views/dokumentasi/pelatihan_online.php',
            'pelatihan-offline' => '/views/dokumentasi/pelatihan_offline.php',
            'rapat'             => '/views/dokumentasi/rapat.php',
            'foto'              => '/views/dokumentasi/foto_kegiatan.php',
        ];
        $file = $dok_map[$sub] ?? '/views/dokumentasi/pelatihan_online.php';
        require_once __DIR__ . $file;
        break;

    case 'dashboard':
        require_once __DIR__ . '/views/dashboard.php';
        break;

    case 'login':
        require_once __DIR__ . '/views/login.php';
        break;

    case 'unauthorized':
        echo '<div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-6"><i class="fas fa-lock text-4xl text-red-400"></i></div>
                <h1 class="text-4xl font-black text-slate-800 mb-4">Akses Ditolak</h1>
                <p class="text-lg text-slate-600 mb-8 max-w-md">Anda tidak memiliki izin untuk mengakses halaman ini. Silakan login dengan akun yang sesuai.</p>
                <a href="?page=beranda" class="bg-orange-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-orange-700 transition-colors">Kembali ke Beranda</a>
              </div>';
        break;

    default:
        echo '<div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
                <i class="fas fa-exclamation-triangle text-6xl text-red-400 mb-6"></i>
                <h1 class="text-5xl font-black text-slate-800 mb-4">404</h1>
                <p class="text-xl text-slate-600 mb-8">Halaman yang Anda cari tidak ditemukan.</p>
                <a href="?page=beranda" class="bg-orange-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-orange-700 transition-colors">Kembali ke Beranda</a>
              </div>';
        break;
}

// Load Footer
require_once __DIR__ . '/views/partials/footer.php';
?>
