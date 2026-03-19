<?php
/**
 * Regression checks for compatibility between current code and mixed DB schemas.
 *
 * Jalankan dengan:
 *   php tests/ModelCompatibilityTest.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

$pass = 0;
$fail = 0;
$skip = 0;

function assert_model_test($condition, $message)
{
    global $pass, $fail;
    if ($condition) {
        echo "[OK]   {$message}\n";
        $pass++;
    } else {
        echo "[FAIL] {$message}\n";
        $fail++;
    }
}

function skip_model_test($message)
{
    global $skip;
    echo "[SKIP] {$message}\n";
    $skip++;
}

echo "=== Model Compatibility Test Suite ===\n\n";

assert_model_test(function_exists('e'), "Helper e() harus tersedia untuk escaping view");
assert_model_test(function_exists('auth_get_user_id'), "Alias auth_get_user_id harus tersedia untuk kode legacy");
assert_model_test(
    auth_get_user_id() === get_user_id(),
    "Alias auth_get_user_id harus mengembalikan nilai yang sama dengan get_user_id"
);

$uploadErrors = [];
assert_model_test(
    \App\Utils\UploadHelper::handle([], 'materi_', 'noop', $uploadErrors) === null && $uploadErrors === [],
    "UploadHelper::handle harus aman saat menerima payload file kosong"
);

$pengumuman = get_all_pengumuman();
assert_model_test(
    !empty($pengumuman) && isset($pengumuman[0]['tanggal']),
    "get_all_pengumuman harus menyuplai field tanggal untuk view pengumuman"
);

$memorandum = get_all_memorandum();
assert_model_test(
    !empty($memorandum) && isset($memorandum[0]['tipe']) && isset($memorandum[0]['konfirmasi']),
    "get_all_memorandum harus menyuplai field tipe dan konfirmasi untuk view memorandum"
);

$dokRapat = get_all_dokumentasi_by_kategori('rapat');
assert_model_test(
    !empty($dokRapat) && isset($dokRapat[0]['file_type']) && isset($dokRapat[0]['size_label']),
    "get_all_dokumentasi_by_kategori harus menyuplai metadata file untuk view dokumentasi"
);

$materi = get_all_materi();
assert_model_test(
    !empty($materi) && isset($materi[0]['icon']),
    "get_all_materi harus menyuplai field icon untuk view materi"
);

$materiDetail = !empty($materi) ? get_materi_by_id($materi[0]['id']) : null;
assert_model_test(
    is_array($materiDetail) && is_file(UPLOAD_DIR . $materiDetail['file_path']),
    "Dummy materi harus memiliki file placeholder agar alur download bisa diuji"
);

$online = get_pelatihan_by_type('online');
assert_model_test(
    !empty($online) && array_key_exists('zoom_link', $online[0]),
    "get_pelatihan_by_type('online') harus selalu menyuplai field zoom_link"
);

if (!$pdo) {
    skip_model_test("Write-path compatibility dilewati karena PDO tidak aktif");
} else {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = ROLE_ADMIN;
    $_SESSION['user_name'] = 'Regression Tester';

    $suffix = date('YmdHis');

    try {
        $pdo->beginTransaction();

        $pendaftaranSaved = \App\Models\RekrutmenModel::addPendaftaranPetugas([
            'nama_lengkap' => 'Tester Kompatibilitas',
            'nik' => '3512' . substr($suffix, -12),
            'email' => "compat-{$suffix}@example.id",
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Kalimantan No. 1, Jember',
            'posisi' => 'PCL',
            'wilayah' => 'Sumbersari',
            'dok_ktp' => null,
            'dok_ijazah' => null,
            'dok_foto' => null,
        ]);
        assert_model_test($pendaftaranSaved === true, "addPendaftaranPetugas harus tetap berjalan pada schema baru maupun legacy");

        $status = get_pendaftaran_status("compat-{$suffix}@example.id");
        assert_model_test(
            is_array($status) && ($status['email'] ?? '') === "compat-{$suffix}@example.id",
            "get_pendaftaran_status harus dapat membaca hasil insert kompatibel"
        );

        $materiSaved = \App\Models\PelatihanModel::addMateri([
            'judul' => "Materi Uji Kompatibilitas {$suffix}",
            'kategori' => 'Testing',
            'tipe' => 'PDF',
            'file_path' => "compat_materi_{$suffix}.pdf",
            'file_size' => 1024,
        ]);
        assert_model_test($materiSaved === true, "addMateri harus mendukung tabel materi baru maupun legacy");

        $anomalySaved = \App\Models\PengolahanModel::addAnomaly([
            'judul' => "Anomali Uji {$suffix}",
            'wilayah' => 'Kec. Sumbersari',
            'deskripsi' => 'Regression test insert anomaly',
            'status' => 'reported',
        ]);
        assert_model_test($anomalySaved === true, "addAnomaly tidak boleh gagal karena kolom schema drift");

        $skSaved = \App\Models\SuratModel::addSK([
            'nomor_sk' => "SK/TEST/{$suffix}",
            'judul' => 'SK Uji Kompatibilitas',
            'tanggal_sk' => '2026-03-19',
            'file_path' => null,
            'status' => 'draft',
        ]);
        assert_model_test($skSaved === true, "addSK harus memakai helper user yang valid");

        $memoSaved = \App\Models\SuratModel::addMemorandum([
            'nomor' => "MEMO/TEST/{$suffix}",
            'tipe' => 'memo',
            'judul' => 'Memorandum Uji Kompatibilitas',
            'konten' => 'Catatan regresi untuk memastikan CRUD memorandum berjalan.',
            'tanggal' => '2026-03-19',
            'waktu' => '09:30',
            'tempat' => 'Ruang QA',
            'distribusi_email' => true,
            'distribusi_sms' => false,
        ]);
        assert_model_test($memoSaved === true, "addMemorandum harus bisa menyimpan memorandum baru");

        $memoId = (int) $pdo->lastInsertId();
        $memoDetail = \App\Models\SuratModel::getMemorandumById($memoId);
        assert_model_test(
            is_array($memoDetail) && ($memoDetail['nomor'] ?? '') === "MEMO/TEST/{$suffix}",
            "getMemorandumById harus dapat membaca memorandum yang baru disimpan"
        );

        $memoUpdated = \App\Models\SuratModel::updateMemorandum($memoId, [
            'nomor' => "MEMO/TEST/{$suffix}",
            'tipe' => 'undangan',
            'judul' => 'Memorandum Uji Kompatibilitas Diperbarui',
            'konten' => 'Konten diperbarui untuk regression test.',
            'tanggal' => '2026-03-20',
            'waktu' => '10:15',
            'tempat' => 'Ruang Rapat Utama',
            'distribusi_email' => true,
            'distribusi_sms' => true,
        ]);
        assert_model_test($memoUpdated === true, "updateMemorandum harus dapat memperbarui data memorandum");

        $memoDeleted = \App\Models\SuratModel::deleteMemorandum($memoId);
        assert_model_test($memoDeleted === true, "deleteMemorandum harus dapat menghapus memorandum");

        $dokSaved = \App\Models\DokumentasiModel::add([
            'judul' => 'Dokumentasi Uji Kompatibilitas',
            'kategori' => 'rapat',
            'deskripsi' => 'Dokumen regresi untuk memastikan CRUD dokumentasi berjalan.',
            'file_path' => 'compat_dokumentasi_test.pdf',
            'file_type' => 'PDF',
            'thumbnail' => null,
            'tanggal' => '2026-03-19',
            'tags' => ['regression', 'rapat'],
            'watermark' => false,
        ]);
        assert_model_test($dokSaved === true, "add dokumentasi harus dapat menyimpan metadata dokumentasi baru");

        $dokId = (int) $pdo->lastInsertId();
        $dokDetail = \App\Models\DokumentasiModel::getById($dokId);
        assert_model_test(
            is_array($dokDetail) && ($dokDetail['judul'] ?? '') === 'Dokumentasi Uji Kompatibilitas',
            "getById dokumentasi harus dapat membaca entri yang baru disimpan"
        );

        $dokUpdated = \App\Models\DokumentasiModel::update($dokId, [
            'judul' => 'Dokumentasi Uji Kompatibilitas Diperbarui',
            'kategori' => 'rapat',
            'deskripsi' => 'Konten metadata diperbarui untuk regression test.',
            'file_path' => 'compat_dokumentasi_test.pdf',
            'file_type' => 'PDF',
            'thumbnail' => null,
            'tanggal' => '2026-03-20',
            'tags' => ['regression', 'updated'],
            'watermark' => true,
        ]);
        assert_model_test($dokUpdated === true, "update dokumentasi harus dapat memperbarui metadata");

        $dokDeleted = \App\Models\DokumentasiModel::delete($dokId);
        assert_model_test($dokDeleted === true, "delete dokumentasi harus dapat menghapus entri");
    } catch (Throwable $e) {
        assert_model_test(false, "Write-path compatibility melempar exception: " . $e->getMessage());
    } finally {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}

echo "\n=========================================\n";
echo "Passed: {$pass}\n";
echo "Failed: {$fail}\n";
echo "Skipped: {$skip}\n";
echo "=========================================\n";

exit($fail > 0 ? 1 : 0);
