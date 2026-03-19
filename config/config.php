<?php
/**
 * File Konfigurasi SISE2026 BPS Kabupaten Jember
 * Berisi pengaturan database, metadata aplikasi, session, CSRF, dan data muatan SE2026.
 * Mendukung multi-environment: development (local) dan production (hosting)
 */

// 0. Load Environment Variables dari .env
$env_file = __DIR__ . '/../.env';
if (is_file($env_file)) {
    $env_lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        // Kutip di .env dibersihkan agar DSN dan kredensial tidak ikut menyimpan tanda petik.
        $value = trim($value, "\"'");
        if (!isset($_ENV[$key]) && !isset($_SERVER[$key])) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Helper: ambil env variable dengan default value
 */
function env(string $key, $default = null) {
    $val = getenv($key);
    if ($val === false) return $default;
    return $val;
}

/**
 * Helper: Detect if running on localhost (XAMPP/Laragon)
 */
$is_localhost = in_array($_SERVER['HTTP_HOST'] ?? 'localhost', ['localhost', '127.0.0.1']);

/**
 * Helper: Determine application URL from environment or auto-detect
 */
function get_app_url() {
    global $is_localhost;
    
    // Check if APP_URL is set in environment
    $app_url = env('APP_URL');
    if ($app_url && !empty($app_url)) {
        return rtrim($app_url, '/');
    }
    
    // Auto-detect from request
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
             || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
             || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    
    $protocol = $is_https ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    $script_dir = str_replace('\\', '/', $script_dir);
    $script_dir = rtrim($script_dir, '/');
    $script_dir = $script_dir === '' ? '' : '/' . $script_dir;
    
    return $protocol . "://" . $host . $script_dir;
}

// 1. Determine Environment and HTTPS
$app_env = $is_localhost ? 'development' : 'production';
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
         || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
         || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

// Force HTTPS in production if configured
if ($app_env === 'production' && env('SESSION_SECURE', 'true') === 'true') {
    $is_https = true;
}

// 2. Session Management
if (session_status() === PHP_SESSION_NONE) {
    // Get session settings from environment or use defaults
    $session_lifetime = (int)env('SESSION_LIFETIME', '7200');
    $session_secure = env('SESSION_SECURE', $is_https ? 'true' : 'false') === 'true';
    
    session_set_cookie_params([
        'lifetime' => $session_lifetime,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Strict',
        'secure' => $session_secure,
    ]);
    session_start();
}

// 3. Database Configuration - Auto-detect based on environment
if ($is_localhost) {
    // LOCALHOST (XAMPP/Laragon)
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'bps_jember_se2026');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // PRODUCTION (bpsjember.my.id)
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'bpsjembe_se2026');
    define('DB_USER', 'bpsjembe_nanangpx');
    define('DB_PASS', 'N4n4n9J3mb3r350917');
}

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// Log database connection attempt (useful for debugging)
if ($app_env === 'development') {
    error_log("[SISE2026] Connecting to DB: " . DB_HOST . ":" . DB_PORT . "/" . DB_NAME . " as " . DB_USER);
}

try {
    // Charset eksplisit mencegah masalah encoding saat data dummy memuat karakter lokal.
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        // Jika database belum ada, coba konek ke MySQL dulu untuk create
        try {
            $pdo_temp = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT, DB_USER, DB_PASS);
            $pdo_temp->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e2) {
            error_log('[SISE2026] Database creation failed: ' . $e2->getMessage());
            $pdo = null;
        }
    } else {
        error_log('[SISE2026] Database connection failed: ' . $e->getMessage());
        $pdo = null;
    }
}

// 4. Application Metadata
define('APP_NAME', 'SISE2026 JEMBER');
define('APP_FULL_NAME', 'Sistem Informasi Sensus Ekonomi 2026');
define('BPS_OFFICE', 'Badan Pusat Statistik Kabupaten Jember');
define('SE_YEAR', '2026');
define('APP_ENV', $app_env);

// 5. URL Configuration - Support both environments
define('APP_URL', get_app_url());
define('BASE_URL', get_app_url() . '/');

// 6. Role Constants
define('ROLE_ADMIN', 'admin');
define('ROLE_OPERATOR', 'operator');
define('ROLE_PML', 'pml');
define('ROLE_PCL', 'pcl');

// 6. Data Muatan SE2026 Kabupaten Jember
$muatan_se2026 = [
    'total_usaha' => 400868,
    'rincian' => [
        'ub' => ['label' => 'Usaha Besar (UB)', 'jumlah' => 501, 'color' => '#3B82F6'],
        'um' => ['label' => 'Usaha Menengah (UM)', 'jumlah' => 2653, 'color' => '#F59E0B'],
        'umk' => ['label' => 'Usaha Mikro Kecil (UMK)', 'jumlah' => 397714, 'color' => '#10B981']
    ]
];

// 7. Pengaturan Periode Sensus
define('START_DATE', '2026-05-01');
define('END_DATE', '2026-07-31');

// 9. Upload Configuration - Environment-based max size
if ($is_localhost) {
    // Local development - use .env setting or default
    $max_upload_size = (int)env('MAX_UPLOAD_SIZE', '5242880');
} else {
    // Production - hosting allows up to 2GB, but we'll use reasonable limit
    $max_upload_size = (int)env('MAX_UPLOAD_SIZE', '5242880');
}
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', $max_upload_size);
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'mp4']);

// Map MIME types yang diizinkan per ekstensi
define('ALLOWED_MIME_TYPES', [
    'pdf'  => ['application/pdf'],
    'doc'  => ['application/msword'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'xls'  => ['application/vnd.ms-excel'],
    'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    'ppt'  => ['application/vnd.ms-powerpoint'],
    'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'png'  => ['image/png'],
    'mp4'  => ['video/mp4'],
]);

// 10. Global Path — Calculated dynamically for multi-environment support
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$script_dir = rtrim($script_dir, '/');
$script_dir = $script_dir === '' ? '' : $script_dir;

// Use BASE_URL constant if available, otherwise fall back to auto-detection
if (defined('BASE_URL')) {
    $base_url = BASE_URL;
} else {
    $base_url = ($is_https ? "https" : "http") . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $script_dir . "/";
}
?>
