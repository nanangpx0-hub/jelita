<?php
/**
 * Smoke render for representative views with warnings/notices promoted to exceptions.
 *
 * Jalankan dengan:
 *   php tests/ViewSmokeTest.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = ROLE_ADMIN;
$_SESSION['user_name'] = 'Smoke Tester';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

$pass = 0;
$fail = 0;

function assert_view_renders(array $case)
{
    global $pass, $fail;

    $_GET = [
        'page' => $case['page'],
        'sub' => $case['sub'] ?? '',
    ];

    if (!empty($case['item'])) {
        $_GET['item'] = $case['item'];
    }

    if (!empty($case['query']) && is_array($case['query'])) {
        $_GET = array_merge($_GET, $case['query']);
    }

    $_SESSION['flash'] = null;
    $initialBufferLevel = ob_get_level();
    ob_start();

    set_error_handler(function ($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    try {
        include __DIR__ . '/../views/partials/header.php';
        include $case['view'];
        include __DIR__ . '/../views/partials/footer.php';
        ob_end_clean();
        echo "[OK]   {$case['label']}\n";
        $pass++;
    } catch (Throwable $e) {
        while (ob_get_level() > $initialBufferLevel) {
            ob_end_clean();
        }
        echo "[FAIL] {$case['label']}\n";
        echo "       {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n";
        $fail++;
    } finally {
        restore_error_handler();
    }
}

echo "=== View Smoke Test Suite ===\n\n";

$cases = [
    ['label' => 'Beranda', 'page' => 'beranda', 'view' => __DIR__ . '/../views/home.php'],
    ['label' => 'Dashboard', 'page' => 'dashboard', 'view' => __DIR__ . '/../views/dashboard.php'],
    ['label' => 'Login', 'page' => 'login', 'view' => __DIR__ . '/../views/login.php'],
    ['label' => 'Landing Rekrutmen', 'page' => 'rekrutmen', 'view' => __DIR__ . '/../views/rekrutmen.php'],
    ['label' => 'Rekrutmen Administrasi', 'page' => 'rekrutmen-petugas', 'sub' => 'administrasi', 'view' => __DIR__ . '/../views/rekrutmen/administrasi.php'],
    ['label' => 'Rekrutmen Pengumuman', 'page' => 'rekrutmen-petugas', 'sub' => 'pengumuman', 'view' => __DIR__ . '/../views/rekrutmen/pengumuman.php'],
    ['label' => 'Pelatihan Online', 'page' => 'pelatihan', 'sub' => 'online', 'view' => __DIR__ . '/../views/pelatihan/online.php'],
    ['label' => 'Pelatihan Offline', 'page' => 'pelatihan', 'sub' => 'offline', 'view' => __DIR__ . '/../views/pelatihan/offline.php'],
    ['label' => 'Pelatihan Materi', 'page' => 'pelatihan', 'sub' => 'materi', 'view' => __DIR__ . '/../views/pelatihan/materi.php'],
    ['label' => 'Pengolahan Anomaly', 'page' => 'pengolahan', 'sub' => 'anomaly', 'view' => __DIR__ . '/../views/pengolahan/anomaly.php'],
    ['label' => 'Pengolahan Monitoring', 'page' => 'pengolahan', 'sub' => 'monitoring', 'view' => __DIR__ . '/../views/pengolahan/monitoring.php'],
    ['label' => 'Teknis SK', 'page' => 'teknis-dan-administrasi', 'sub' => 'kelengkapan-administrasi', 'item' => 'sk', 'view' => __DIR__ . '/../views/teknis/sk.php'],
    ['label' => 'Teknis Surat Masuk', 'page' => 'teknis-dan-administrasi', 'sub' => 'kelengkapan-administrasi', 'item' => 'surat-masuk', 'view' => __DIR__ . '/../views/teknis/surat_masuk.php'],
    ['label' => 'Teknis Surat Keluar', 'page' => 'teknis-dan-administrasi', 'sub' => 'kelengkapan-administrasi', 'item' => 'surat-keluar', 'view' => __DIR__ . '/../views/teknis/surat_keluar.php'],
    ['label' => 'Teknis Memorandum', 'page' => 'teknis-dan-administrasi', 'sub' => 'kelengkapan-administrasi', 'item' => 'memorandum', 'view' => __DIR__ . '/../views/teknis/memorandum.php'],
    ['label' => 'Teknis Memorandum Edit', 'page' => 'teknis-dan-administrasi', 'sub' => 'kelengkapan-administrasi', 'item' => 'memorandum', 'query' => ['edit' => 1], 'view' => __DIR__ . '/../views/teknis/memorandum.php'],
    ['label' => 'Teknis Laporan', 'page' => 'teknis-dan-administrasi', 'sub' => 'kelengkapan-administrasi', 'item' => 'laporan', 'view' => __DIR__ . '/../views/teknis/laporan_kegiatan.php'],
    ['label' => 'Teknis Notulen', 'page' => 'teknis-dan-administrasi', 'sub' => 'kelengkapan-administrasi', 'item' => 'notulen', 'view' => __DIR__ . '/../views/teknis/notulen_rapat.php'],
    ['label' => 'Dokumentasi Video', 'page' => 'dokumentasi', 'sub' => 'pelatihan-online', 'view' => __DIR__ . '/../views/dokumentasi/pelatihan_online.php'],
    ['label' => 'Dokumentasi Video Edit', 'page' => 'dokumentasi', 'sub' => 'pelatihan-online', 'query' => ['edit' => 1], 'view' => __DIR__ . '/../views/dokumentasi/pelatihan_online.php'],
    ['label' => 'Dokumentasi Offline', 'page' => 'dokumentasi', 'sub' => 'pelatihan-offline', 'view' => __DIR__ . '/../views/dokumentasi/pelatihan_offline.php'],
    ['label' => 'Dokumentasi Foto', 'page' => 'dokumentasi', 'sub' => 'foto', 'view' => __DIR__ . '/../views/dokumentasi/foto_kegiatan.php'],
    ['label' => 'Dokumentasi Rapat', 'page' => 'dokumentasi', 'sub' => 'rapat', 'view' => __DIR__ . '/../views/dokumentasi/rapat.php'],
];

foreach ($cases as $case) {
    assert_view_renders($case);
}

echo "\n==============================\n";
echo "Passed: {$pass}\n";
echo "Failed: {$fail}\n";
echo "==============================\n";

exit($fail > 0 ? 1 : 0);
