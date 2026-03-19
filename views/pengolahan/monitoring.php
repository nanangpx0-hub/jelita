<?php
/**
 * Pengolahan > Monitoring
 * Peta sebaran, progress capaian, alert otomatis, ekspor data
 */
$wilayah = get_all_wilayah();
$sektor = get_sektor_progress();
$total_target = array_sum(array_column($sektor, 'total'));
$avg_progress = count($sektor) > 0 ? round(array_sum(array_column($sektor, 'progres')) / count($sektor)) : 0;
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Monitoring Pengolahan</h1>
                <p class="text-orange-100 font-medium mt-1">Progress capaian, peta sebaran, dan alert otomatis</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-white/30 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-white/50 transition-colors"><i class="fas fa-sync-alt mr-1"></i> Refresh</button>
                <button class="bg-white text-green-600 px-4 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-green-50"><i class="fas fa-file-excel mr-1"></i> Export</button>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-orange-600"><?= $avg_progress ?>%</p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Rata-rata Progress</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-blue-600"><?= format_indo($total_target) ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Total Target</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-green-600"><?= count($wilayah) ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Wilayah Aktif</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-red-600">3</p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Alert Aktif</p>
            </div>
        </div>

        <!-- Progress Bars -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-6"><i class="fas fa-chart-bar text-orange-500 mr-2"></i> Progress Per Sektor</h2>
            <div class="space-y-6">
                <?php foreach ($sektor as $s):
                    $bar_color = $s['progres'] >= 80 ? 'bg-green-500' : ($s['progres'] >= 50 ? 'bg-orange-500' : 'bg-blue-500');
                ?>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <?php // Nama sektor ikut di-escape karena sumbernya bisa berpindah dari dummy ke database. ?>
                        <span class="text-sm font-bold text-slate-700"><?= e($s['sektor']) ?></span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-400">Target: <?= format_indo($s['total']) ?></span>
                            <span class="text-sm font-black text-slate-900 bg-slate-100 px-2 py-0.5 rounded"><?= $s['progres'] ?>%</span>
                        </div>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="<?= $bar_color ?> h-full rounded-full transition-all duration-1000" style="width: <?= $s['progres'] ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Alert -->
        <div class="bg-red-50 border border-red-200 rounded-[24px] p-6">
            <h3 class="text-lg font-bold text-red-700 mb-3"><i class="fas fa-bell mr-2"></i>Alert Aktif</h3>
            <div class="space-y-2">
                <div class="bg-white rounded-xl p-3 border border-red-100 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span class="text-sm font-medium text-slate-700">Kec. Silo — Progress di bawah 30%  target. Butuh tambahan 5 PCL.</span>
                    <span class="text-[10px] font-bold text-slate-400 ml-auto">2 jam lalu</span>
                </div>
                <div class="bg-white rounded-xl p-3 border border-red-100 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span class="text-sm font-medium text-slate-700">Kec. Tempurejo — Tidak ada update data selama 3 hari.</span>
                    <span class="text-[10px] font-bold text-slate-400 ml-auto">5 jam lalu</span>
                </div>
            </div>
        </div>
    </section>
</main>
