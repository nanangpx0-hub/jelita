<?php
/**
 * Pelatihan > Offline
 * Daftar hadir manual, evaluasi, sertifikat
 */
$pelatihan_offline = get_pelatihan_by_type('offline');
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-white drop-shadow-lg">Pelatihan Offline</h1>
        <p class="text-orange-100 font-medium mt-1">Administrasi pelatihan tatap muka, daftar hadir, evaluasi, sertifikat</p>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <?php foreach ($pelatihan_offline as $p): ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-black text-slate-900"><?= e($p['judul']) ?></h2>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-slate-500 font-bold">
                        <span><i class="fas fa-calendar text-orange-400 mr-1"></i><?= date('d M Y', strtotime($p['tanggal_mulai'])) ?></span>
                        <?php if (!empty($p['tempat'])): ?>
                        <span><i class="fas fa-map-marker-alt text-orange-400 mr-1"></i><?= e($p['tempat']) ?></span>
                        <?php endif; ?>
                        <span><i class="fas fa-users text-orange-400 mr-1"></i><?= (int)($p['peserta'] ?? 0) ?> Peserta</span>
                        <?= status_badge($p['status']) ?>
                    </div>
                </div>
            </div>

            <!-- Action Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 hover:shadow-lg transition-all cursor-pointer group">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-blue-600 transition-colors">
                        <i class="fas fa-clipboard-list text-xl text-blue-600 group-hover:text-white transition-colors"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1">Daftar Hadir</h4>
                    <p class="text-xs text-slate-500 font-medium">Input kehadiran manual peserta pelatihan</p>
                </div>
                <div class="bg-green-50 rounded-2xl p-6 border border-green-100 hover:shadow-lg transition-all cursor-pointer group">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-green-600 transition-colors">
                        <i class="fas fa-star text-xl text-green-600 group-hover:text-white transition-colors"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1">Formulir Evaluasi</h4>
                    <p class="text-xs text-slate-500 font-medium">Evaluasi pelaksanaan dan penilaian instruktur</p>
                </div>
                <div class="bg-purple-50 rounded-2xl p-6 border border-purple-100 hover:shadow-lg transition-all cursor-pointer group">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-purple-600 transition-colors">
                        <i class="fas fa-award text-xl text-purple-600 group-hover:text-white transition-colors"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-1">Sertifikat</h4>
                    <p class="text-xs text-slate-500 font-medium">Generate dan distribusi sertifikat peserta</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($pelatihan_offline)): ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-16 shadow-2xl border border-white text-center">
            <i class="fas fa-chalkboard-teacher text-6xl text-slate-200 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700">Belum ada pelatihan offline terjadwal</h3>
        </div>
        <?php endif; ?>
    </section>
</main>
