<?php
/**
 * Teknis > Laporan Kegiatan
 */
$laporan = \App\Utils\DummyData::getLaporanKegiatan();
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Laporan Kegiatan</h1>
                <p class="text-orange-100 font-medium mt-1">Form isian, lampiran foto, persetujuan atasan, ekspor PDF/Excel</p>
            </div>
            <button class="bg-white text-orange-600 px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg btn-animate"><i class="fas fa-plus mr-2"></i> Buat Laporan</button>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-white flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Cari laporan..." class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <select class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
                <option value="">Semua Status</option><option>Draft</option><option>Submitted</option><option>Approved</option>
            </select>
            <button class="bg-green-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-green-700"><i class="fas fa-file-excel mr-1"></i> Export</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($laporan as $l): ?>
            <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-6 shadow-xl border border-white hover:shadow-2xl transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <?= status_badge($l['status']) ?>
                    <button class="w-8 h-8 bg-slate-50 rounded-lg hover:bg-orange-50 text-slate-400 hover:text-orange-600 transition-colors"><i class="fas fa-ellipsis-v text-xs"></i></button>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 group-hover:text-orange-600 transition-colors"><?= e($l['judul']) ?></h3>
                <div class="space-y-2 text-xs text-slate-500 font-medium mb-6">
                    <p><i class="fas fa-calendar w-4 text-orange-400"></i> <?= date('d M Y', strtotime($l['tanggal'])) ?></p>
                    <p><i class="fas fa-map-marker-alt w-4 text-orange-400"></i> <?= e($l['lokasi']) ?></p>
                    <p><i class="fas fa-image w-4 text-orange-400"></i> 3 Foto Lampiran</p>
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 bg-orange-50 text-orange-600 py-2 rounded-xl text-xs font-bold hover:bg-orange-100"><i class="fas fa-eye mr-1"></i> Lihat</button>
                    <button class="flex-1 bg-slate-900 text-white py-2 rounded-xl text-xs font-bold hover:bg-orange-600"><i class="fas fa-file-pdf mr-1"></i> PDF</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
