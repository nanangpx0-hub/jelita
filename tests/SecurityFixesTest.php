<?php
/**
 * Security Fixes Test — SISE2026 BPS Kabupaten Jember
 * Memverifikasi semua perbaikan keamanan Prioritas 1.
 *
 * Jalankan dengan:
 *   php tests/SecurityFixesTest.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

$pass = 0;
$fail = 0;

function assert_test($condition, $message) {
    global $pass, $fail;
    if ($condition) {
        echo "[OK]   {$message}\n";
        $pass++;
    } else {
        echo "[FAIL] {$message}\n";
        $fail++;
    }
}

echo "=== Security Fixes Test Suite ===\n\n";

// --- P1-2: .env Loading ---
echo "--- P1-2: ENV Loading ---\n";
assert_test(
    defined('DB_HOST') && DB_HOST !== '',
    "DB_HOST harus terdefinisi dan tidak kosong"
);
assert_test(
    defined('DB_NAME') && DB_NAME !== '',
    "DB_NAME harus terdefinisi dan tidak kosong"
);
assert_test(
    function_exists('env'),
    "Fungsi env() harus tersedia"
);
assert_test(
    env('APP_ENV', 'fallback') !== null,
    "env() harus bisa membaca environment variable atau return default"
);

// --- P1-3: Session Regeneration ---
echo "\n--- P1-3 & P1-4: Session & CSRF ---\n";
$old_session_id = session_id();
assert_test(
    !empty($old_session_id),
    "Session harus aktif"
);

// Test CSRF generation
$_SESSION['csrf_token'] = null; // reset
$token1 = generate_csrf_token();
assert_test(
    !empty($token1) && strlen($token1) === 64,
    "CSRF token harus 64 karakter hex (32 bytes)"
);

// Test CSRF validation + rotation
$token_before = $_SESSION['csrf_token'];
$valid = validate_csrf($token_before);
assert_test(
    $valid === true,
    "validate_csrf harus return true untuk token yang valid"
);

$token_after = $_SESSION['csrf_token'];
assert_test(
    $token_before !== $token_after,
    "CSRF token harus di-rotasi setelah validasi sukses (P1-4)"
);

assert_test(
    validate_csrf('invalid-token') === false,
    "validate_csrf harus return false untuk token yang salah"
);

// --- P1-5: .htaccess Upload ---
echo "\n--- P1-5: Upload Security ---\n";
$htaccess_path = __DIR__ . '/../uploads/.htaccess';
assert_test(
    is_file($htaccess_path),
    ".htaccess harus ada di folder uploads"
);
if (is_file($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
    assert_test(
        strpos($htaccess_content, 'Require all denied') !== false,
        ".htaccess harus memblokir akses ke file PHP"
    );
}

// --- P1-6: MIME Type Validation ---
echo "\n--- P1-6: MIME Type Validation ---\n";
assert_test(
    function_exists('validate_file_mime'),
    "Fungsi validate_file_mime() harus tersedia"
);

assert_test(
    defined('ALLOWED_MIME_TYPES') && is_array(ALLOWED_MIME_TYPES),
    "ALLOWED_MIME_TYPES harus terdefinisi sebagai array"
);

// Test dengan file PHP yang di-rename jadi .jpg (simulasi serangan)
$fake_jpg = tempnam(sys_get_temp_dir(), 'test_');
file_put_contents($fake_jpg, '<?php echo "hacked"; ?>');
assert_test(
    validate_file_mime($fake_jpg, 'jpg') === false,
    "validate_file_mime harus menolak file PHP yang di-rename jadi .jpg"
);
unlink($fake_jpg);

// Test dengan file PNG asli (simulasi file valid)
$valid_png = tempnam(sys_get_temp_dir(), 'test_');
// Minimal PNG header
file_put_contents($valid_png, hex2bin('89504e470d0a1a0a0000000d49484452') . str_repeat("\x00", 100));
assert_test(
    validate_file_mime($valid_png, 'png') === true,
    "validate_file_mime harus menerima file PNG yang valid"
);
unlink($valid_png);

// Test nonexistent file
assert_test(
    validate_file_mime('/tmp/nonexistent_file_xyz', 'pdf') === false,
    "validate_file_mime harus return false untuk file yang tidak ada"
);

// --- P1-7: HTTPS Detection ---
echo "\n--- P1-7: HTTPS & Base URL ---\n";
assert_test(
    isset($is_https) && is_bool($is_https),
    "Variable \$is_https harus terdefinisi sebagai boolean"
);

assert_test(
    isset($base_url) && (strpos($base_url, 'http://') === 0 || strpos($base_url, 'https://') === 0),
    "base_url harus dimulai dengan http:// atau https://"
);

// --- P1-8: Secure Cookie ---
echo "\n--- P1-8: Session Cookie ---\n";
$cookie_params = session_get_cookie_params();
assert_test(
    $cookie_params['httponly'] === true,
    "Session cookie harus httponly"
);
assert_test(
    $cookie_params['samesite'] === 'Strict',
    "Session cookie harus samesite=Strict"
);

// --- Summary ---
echo "\n=================================\n";
echo "Total: " . ($pass + $fail) . " tests\n";
echo "  Passed: {$pass}\n";
echo "  Failed: {$fail}\n";
echo "=================================\n";

if ($fail > 0) {
    echo "\n⚠️  Ada test yang gagal!\n";
    exit(1);
} else {
    echo "\n✅ Semua security test berhasil!\n";
}
