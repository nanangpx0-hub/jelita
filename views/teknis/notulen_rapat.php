<?php
/**
 * Teknis > Notulen Rapat
 */
$notulen = \App\Utils\DummyData::getNotulenRapat();
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Notulen Rapat</h1>
                <p class="text-orange-100 font-medium mt-1">Editor kolaboratif, penugasan tindak lanjut, distribusi otomatis</p>
            </div>
            <button class="bg-white text-orange-600 px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg btn-animate"><i class="fas fa-plus mr-2"></i> Buat Notulen</button>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <?php foreach ($notulen as $n): ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-6 md:p-8 shadow-xl border border-white hover:shadow-2xl transition-all group">
            <div class="flex flex-col md:flex-row gap-5 items-start">
                <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-pen-fancy text-xl text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-slate-900 group-hover:text-orange-600 transition-colors"><?= e($n['judul']) ?></h3>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-slate-500 font-bold">
                        <span><i class="fas fa-calendar text-indigo-400 mr-1"></i> <?= date('d M Y', strtotime($n['tanggal'])) ?></span>
                        <span><i class="fas fa-user-tie text-indigo-400 mr-1"></i> <?= e($n['pimpinan']) ?></span>
                        <span><i class="fas fa-users text-indigo-400 mr-1"></i> <?= $n['peserta'] ?> Peserta</span>
                        <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full"><i class="fas fa-tasks mr-1"></i> <?= $n['tindak_lanjut'] ?> Tindak Lanjut</span>
                    </div>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-indigo-100"><i class="fas fa-edit mr-1"></i> Edit</button>
                    <button class="bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-600"><i class="fas fa-share-alt mr-1"></i> Distribusi</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
</main>
