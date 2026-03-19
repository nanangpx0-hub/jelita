<?php
/**
 * Basic unit-like tests for core utility functions.
 *
 * Jalankan dengan:
 *   php tests/UtilFunctionsTest.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

function assert_equal($expected, $actual, $message)
{
    if ($expected !== $actual) {
        echo "[FAIL] {$message}\n";
        echo "  Expected: " . var_export($expected, true) . "\n";
        echo "  Actual  : " . var_export($actual, true) . "\n";
        exit(1);
    } else {
        echo "[OK]   {$message}\n";
    }
}

// sanitize_input
$raw = "  <script>alert('x');</script>  ";
$sanitized = sanitize_input($raw);
assert_equal("&lt;script&gt;alert(&#039;x&#039;);&lt;/script&gt;", $sanitized, "sanitize_input harus escape HTML dan trim whitespace");

// status_badge — hanya cek bahwa status tertentu menghasilkan class yang benar tanpa XSS
$badge_html = status_badge('pending');
assert_equal(true, strpos($badge_html, 'bg-yellow-100 text-yellow-700') !== false, "status_badge('pending') harus menggunakan warna yang sesuai");

// role_badge — cek mapping role ke kelas CSS
$role_html = role_badge('pml');
assert_equal(true, strpos($role_html, 'bg-blue-100 text-blue-700') !== false, "role_badge('pml') harus menggunakan warna yang sesuai");

// CSRF helpers
$token = generate_csrf_token();
assert_equal(true, !empty($token), "generate_csrf_token harus menghasilkan token yang tidak kosong");
assert_equal(true, validate_csrf($token), "validate_csrf harus mengembalikan true untuk token yang valid");
assert_equal(false, validate_csrf('token-salah'), "validate_csrf harus mengembalikan false untuk token yang tidak valid");

// get_pendaftaran_status (akan mengembalikan null jika belum ada data, itu tetap acceptable)
$status = get_pendaftaran_status('dummy@example.com');
assert_equal(true, $status === null || isset($status['status']), "get_pendaftaran_status harus mengembalikan null atau array dengan key 'status'");

// get_pelatihan_by_type — harus mengembalikan array (boleh kosong) dan tiap elemen punya key 'tipe'
$online_list = get_pelatihan_by_type('online');
assert_equal(true, is_array($online_list), "get_pelatihan_by_type('online') harus mengembalikan array");
if (!empty($online_list)) {
    assert_equal('online', $online_list[0]['tipe'], "Elemen pertama get_pelatihan_by_type('online') harus bertipe 'online'");
}

// get_all_anomaly — harus mengembalikan array dan (jika ada data) memiliki key status
$anom_list = get_all_anomaly();
assert_equal(true, is_array($anom_list), "get_all_anomaly harus mengembalikan array");
if (!empty($anom_list)) {
    assert_equal(true, isset($anom_list[0]['status']), "Elemen pertama get_all_anomaly harus memiliki key 'status'");
}

echo "\nSemua test utilitas selesai dengan sukses.\n";

