<?php
/**
 * Rekrutmen > Administrasi
 * Form pendaftaran, upload dokumen, status berkas, jadwal seleksi
 */
// DummyData menyatukan jadwal demo agar view, seed, dan dokumentasi tidak drift.
$jadwal_seleksi = \App\Utils\DummyData::getRecruitmentSchedule();
$wilayah_options = get_all_wilayah();

$status_keyword = isset($_GET['status_q']) ? trim($_GET['status_q']) : '';
$status_result = null;
if ($status_keyword !== '') {
    $status_result = get_pendaftaran_status($status_keyword);
}
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <!-- Header -->
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl lg:text-4xl font-black text-white drop-shadow-lg">Pendaftaran Petugas</h1>
                <p class="text-orange-100 font-medium mt-1">Daftarkan diri Anda sebagai PCL/PML Sensus Ekonomi 2026</p>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Form Pendaftaran -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 md:p-10 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center"><i class="fas fa-user-plus text-orange-600"></i></div>
                Formulir Pendaftaran
            </h2>
            <form class="grid grid-cols-1 md:grid-cols-2 gap-6" method="POST" action="?page=rekrutmen-petugas&sub=administrasi&action=daftar" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" required placeholder="Sesuai KTP" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-semibold text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIK (16 digit) *</label>
                    <input type="text" name="nik" required maxlength="16" pattern="[0-9]{16}" placeholder="3509xxxxxxxxxx" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-semibold text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email *</label>
                    <input type="email" name="email" required placeholder="email@contoh.com" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-semibold text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. HP / WhatsApp *</label>
                    <input type="tel" name="no_hp" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-semibold text-slate-700">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Lengkap *</label>
                    <textarea rows="2" name="alamat" required placeholder="Alamat domisili saat ini" class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-semibold text-slate-700 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Posisi yang Dilamar *</label>
                    <select name="posisi" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-semibold text-slate-700">
                        <option value="">-- Pilih Posisi --</option>
                        <option value="PCL">Petugas Pencacah Lapangan (PCL)</option>
                        <option value="PML">Petugas Pemeriksa Lapangan (PML)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wilayah Pilihan *</label>
                    <select name="wilayah" required class="w-full px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-semibold text-slate-700">
                        <option value="">-- Pilih Kecamatan --</option>
                        <?php foreach ($wilayah_options as $wilayah): ?>
                        <option value="<?= e($wilayah['nama_kecamatan']) ?>"><?= e($wilayah['nama_kecamatan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Upload Dokumen -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Upload Dokumen Persyaratan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <label class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-orange-300 transition-colors cursor-pointer group">
                            <i class="fas fa-id-card text-2xl text-slate-300 group-hover:text-orange-500 mb-2 transition-colors"></i>
                            <p class="text-xs font-bold text-slate-500">KTP</p>
                            <p class="text-[10px] text-slate-400 mt-1">PDF/JPG/PNG maks. 5MB</p>
                            <input type="file" name="dok_ktp" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                        <label class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-orange-300 transition-colors cursor-pointer group">
                            <i class="fas fa-graduation-cap text-2xl text-slate-300 group-hover:text-orange-500 mb-2 transition-colors"></i>
                            <p class="text-xs font-bold text-slate-500">Ijazah Terakhir</p>
                            <p class="text-[10px] text-slate-400 mt-1">PDF/JPG/PNG maks. 5MB</p>
                            <input type="file" name="dok_ijazah" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                        </label>
                        <label class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-orange-300 transition-colors cursor-pointer group">
                            <i class="fas fa-portrait text-2xl text-slate-300 group-hover:text-orange-500 mb-2 transition-colors"></i>
                            <p class="text-xs font-bold text-slate-500">Pas Foto 4x6</p>
                            <p class="text-[10px] text-slate-400 mt-1">JPG/PNG maks. 5MB</p>
                            <input type="file" name="dok_foto" class="hidden" accept=".jpg,.jpeg,.png">
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-end gap-3">
                    <button type="reset" class="px-6 py-3 rounded-xl border-2 border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition-colors">Reset</button>
                    <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded-xl font-bold hover:bg-orange-600 transition-all shadow-lg shadow-orange-200 btn-animate">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Pendaftaran
                    </button>
                </div>
            </form>
        </div>

        <!-- Status Kelengkapan Berkas -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 md:p-10 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"><i class="fas fa-tasks text-blue-600"></i></div>
                Cek Status Kelengkapan Berkas
            </h2>
            <form method="GET" action="" class="flex flex-col sm:flex-row gap-4">
                <input type="hidden" name="page" value="rekrutmen-petugas">
                <input type="hidden" name="sub" value="administrasi">
                <input type="text" name="status_q" value="<?= e($status_keyword) ?>" placeholder="Masukkan NIK atau Email Pendaftaran..." class="flex-1 px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-blue-500 focus:bg-white outline-none transition-all font-semibold text-slate-700">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors btn-animate">
                    <i class="fas fa-search mr-2"></i> Cek Status
                </button>
            </form>
            <div class="mt-6 bg-slate-50 rounded-2xl p-6 border border-slate-100">
                <?php if ($status_keyword === ''): ?>
                    <p class="text-sm text-slate-500 text-center font-medium"><i class="fas fa-info-circle text-slate-400 mr-2"></i>Masukkan NIK atau email untuk melihat status pendaftaran Anda.</p>
                <?php elseif (!$status_result): ?>
                    <p class="text-sm text-red-500 text-center font-medium"><i class="fas fa-times-circle text-red-400 mr-2"></i>Data pendaftaran dengan NIK/Email tersebut tidak ditemukan.</p>
                <?php else: ?>
                    <div class="text-sm text-slate-700 space-y-2">
                        <p class="font-bold text-slate-900"><?= e($status_result['nama_lengkap']) ?></p>
                        <p><span class="font-semibold">NIK:</span> <?= e($status_result['nik']) ?></p>
                        <p><span class="font-semibold">Email:</span> <?= e($status_result['email']) ?></p>
                        <p><span class="font-semibold">Posisi:</span> <?= e($status_result['posisi']) ?> &bull; <span class="font-semibold">Wilayah:</span> <?= e($status_result['wilayah']) ?></p>
                        <p><span class="font-semibold">Status:</span> <?= status_badge($status_result['status']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Jadwal Seleksi -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 md:p-10 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center"><i class="fas fa-calendar-alt text-green-600"></i></div>
                Jadwal Seleksi SE2026
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-100">
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Tanggal</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Kegiatan</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal_seleksi as $j): ?>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-700"><?= date('d M Y', strtotime($j['tanggal'])) ?></td>
                            <td class="py-3 px-4 text-slate-600 font-medium"><?= e($j['kegiatan']) ?></td>
                            <td class="py-3 px-4"><?= status_badge($j['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
