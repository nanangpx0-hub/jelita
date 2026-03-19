<?php
/**
 * File Fungsi Perantara (Facade) SISE2026 BPS Kabupaten Jember
 * Versi Refaktor P2: Mendistribusikan panggila ke Model dan Helper baru.
 * Memberikan kompatibilitas mundur bagi file views yang masih memanggil fungsi global.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';

// Muat autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

// --- ALIAS UNTUK KOMPATIBILITAS (Backward Compatibility) ---
// Semua fungsi di bawah ini sekarang memanggil method namespaced di src/

function get_all_lowongan($limit = null, $offset = 0) { return \App\Models\RekrutmenModel::getAllLowongan($limit, $offset); }
function count_lowongan() { return \App\Models\RekrutmenModel::countLowongan(); }
function get_lowongan_by_id($id) { return \App\Models\RekrutmenModel::getLowonganById($id); }
function add_pendaftaran_petugas(array $data) { return \App\Models\RekrutmenModel::addPendaftaranPetugas($data); }
function get_pendaftaran_status($keyword) { return \App\Models\RekrutmenModel::getPendaftaranStatus($keyword); }
function get_all_wilayah() { return \App\Models\RekrutmenModel::getAllWilayah(); }
function get_all_pengumuman($limit = null, $offset = 0) { return \App\Models\RekrutmenModel::getAllPengumuman($limit, $offset); }
function count_pengumuman() { return \App\Models\RekrutmenModel::countPengumuman(); }

function get_sektor_progress() { return \App\Models\PengolahanModel::getSektorProgress(); }
function get_summary_stats() { return \App\Models\PengolahanModel::getSummaryStats(); }
function get_all_anomaly($limit = null, $offset = 0) { return \App\Models\PengolahanModel::getAllAnomaly($limit, $offset); }
function count_anomaly() { return \App\Models\PengolahanModel::countAnomaly(); }
function add_anomaly(array $data) { return \App\Models\PengolahanModel::addAnomaly($data); }

function get_all_sk($limit = null, $offset = 0) { return \App\Models\SuratModel::getAllSK($limit, $offset); }
function count_sk() { return \App\Models\SuratModel::countSK(); }
function add_sk(array $data) { return \App\Models\SuratModel::addSK($data); }
function get_all_surat_masuk($limit = null, $offset = 0) { return \App\Models\SuratModel::getAllSuratMasuk($limit, $offset); }
function count_surat_masuk() { return \App\Models\SuratModel::countSuratMasuk(); }
function add_surat_masuk(array $data) { return \App\Models\SuratModel::addSuratMasuk($data); }
function get_all_surat_keluar($limit = null, $offset = 0) { return \App\Models\SuratModel::getAllSuratKeluar($limit, $offset); }
function count_surat_keluar() { return \App\Models\SuratModel::countSuratKeluar(); }
function add_surat_keluar(array $data) { return \App\Models\SuratModel::addSuratKeluar($data); }
function get_all_memorandum($limit = null, $offset = 0) { return \App\Models\SuratModel::getAllMemorandum($limit, $offset); }
function count_memorandum() { return \App\Models\SuratModel::countMemorandum(); }
function get_memorandum_by_id($id) { return \App\Models\SuratModel::getMemorandumById($id); }
function add_memorandum(array $data) { return \App\Models\SuratModel::addMemorandum($data); }
function update_memorandum($id, array $data) { return \App\Models\SuratModel::updateMemorandum((int) $id, $data); }
function delete_memorandum($id) { return \App\Models\SuratModel::deleteMemorandum((int) $id); }

function get_all_pelatihan($limit = null, $offset = 0) { return \App\Models\PelatihanModel::getAllPelatihan($limit, $offset); }
function count_pelatihan() { return \App\Models\PelatihanModel::countPelatihan(); }
function get_all_materi($limit = null, $offset = 0) { return \App\Models\PelatihanModel::getAllMateri($limit, $offset); }
function count_materi() { return \App\Models\PelatihanModel::countMateri(); }
function add_materi(array $data) { return \App\Models\PelatihanModel::addMateri($data); }
function get_materi_by_id($id) { return \App\Models\PelatihanModel::getMateriById($id); }
function increment_materi_downloads($id) { return \App\Models\PelatihanModel::incrementMateriDownloads($id); }
function get_pelatihan_by_type($tipe) { return \App\Models\PelatihanModel::getPelatihanByType($tipe); }
function add_pelatihan(array $data) { return \App\Models\PelatihanModel::addPelatihan($data); }
function get_qna_pelatihan($pid, $lim = 20) { return \App\Models\PelatihanModel::getQnaPelatihan($pid, $lim); }
function add_qna_pelatihan($pid, $pert) { return \App\Models\PelatihanModel::addQnaPelatihan($pid, $pert); }

function get_all_dokumentasi_by_kategori($kategori, $limit = null, $offset = 0) { return \App\Models\DokumentasiModel::getAllByCategory($kategori, $limit, $offset); }
function count_dokumentasi_by_kategori($kategori) { return \App\Models\DokumentasiModel::countByCategory($kategori); }
function get_dokumentasi_by_id($id) { return \App\Models\DokumentasiModel::getById((int) $id); }
function add_dokumentasi(array $data) { return \App\Models\DokumentasiModel::add($data); }
function update_dokumentasi($id, array $data) { return \App\Models\DokumentasiModel::update((int) $id, $data); }
function delete_dokumentasi($id) { return \App\Models\DokumentasiModel::delete((int) $id); }

// Utils
function format_indo($data) { return \App\Utils\ViewHelper::formatIndo($data); }
function status_badge($status) { return \App\Utils\ViewHelper::statusBadge($status); }
function role_badge($role) { return \App\Utils\ViewHelper::roleBadge($role); }
function get_days_to_sensus() { return \App\Utils\ViewHelper::getDaysToSensus(); }
function validate_file_mime($tmp, $ext) { return \App\Utils\UploadHelper::validateMime($tmp, $ext); }

/**
 * Escape output agar view tidak perlu mengulang htmlspecialchars di setiap tempat.
 */
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input sebelum dipakai ulang di server atau disimpan.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = e($data);
    return $data;
}

/**
 * Flash message helpers tetap global karena sering digunakan di view
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
