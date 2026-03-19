<?php
/**
 * Rekrutmen > Alokasi Petugas & Wilayah
 * Peta interaktif, daftar wilayah, kebutuhan per area
 */
$wilayah = get_all_wilayah();
$total_pcl_need = array_sum(array_column($wilayah, 'kebutuhan_pcl'));
$total_pml_need = array_sum(array_column($wilayah, 'kebutuhan_pml'));
$total_pcl_fill = array_sum(array_column($wilayah, 'terisi_pcl'));
$total_pml_fill = array_sum(array_column($wilayah, 'terisi_pml'));
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl lg:text-4xl font-black text-white drop-shadow-lg">Alokasi Petugas & Wilayah</h1>
        <p class="text-orange-100 font-medium mt-1">Peta distribusi kebutuhan petugas SE2026 Kabupaten Jember</p>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-orange-600"><?= $total_pcl_need ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Kebutuhan PCL</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-blue-600"><?= $total_pml_need ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Kebutuhan PML</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-green-600"><?= $total_pcl_fill ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">PCL Terisi</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 shadow-xl border border-white text-center">
                <p class="text-3xl font-black text-emerald-600"><?= $total_pml_fill ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">PML Terisi</p>
            </div>
        </div>

        <!-- Peta Interaktif -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-6 md:p-8 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center"><i class="fas fa-map-marked-alt text-orange-600"></i></div>
                Peta Penugasan Wilayah
            </h2>
            <div id="map-container" class="w-full h-[400px] rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden relative">
                <div class="absolute inset-0 flex items-center justify-center bg-slate-50">
                    <div class="text-center">
                        <i class="fas fa-map text-6xl text-slate-200 mb-4"></i>
                        <p class="text-sm font-bold text-slate-400">Peta Interaktif Kabupaten Jember</p>
                        <p class="text-xs text-slate-300 mt-1">Leaflet.js + OpenStreetMap — 31 Kecamatan</p>
                        <div class="mt-4 grid grid-cols-3 gap-2 max-w-xs mx-auto">
                            <?php foreach (array_slice($wilayah, 0, 6) as $w): ?>
                            <div class="bg-white rounded-lg p-2 shadow-sm border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-600"><?= e($w['nama_kecamatan']) ?></p>
                                <p class="text-[9px] text-slate-400">PCL: <?= $w['terisi_pcl'] ?>/<?= $w['kebutuhan_pcl'] ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Wilayah Kerja -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 md:p-10 shadow-2xl border border-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"><i class="fas fa-list text-blue-600"></i></div>
                    Daftar Wilayah Kerja
                </h2>
                <div class="flex gap-2">
                    <input type="text" placeholder="Cari kecamatan..." class="px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
                    <button class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-100">
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Kecamatan</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Kebutuhan PCL</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Terisi PCL</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Kebutuhan PML</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Terisi PML</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wilayah as $w):
                            $pct = ($w['kebutuhan_pcl'] > 0) ? round(($w['terisi_pcl'] / $w['kebutuhan_pcl']) * 100) : 0;
                            $status = $pct >= 100 ? 'completed' : ($pct >= 50 ? 'ongoing' : 'upcoming');
                        ?>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-700"><i class="fas fa-map-pin text-orange-400 mr-2"></i><?= e($w['nama_kecamatan']) ?></td>
                            <td class="py-3 px-4 text-center font-bold text-slate-600"><?= $w['kebutuhan_pcl'] ?></td>
                            <td class="py-3 px-4 text-center font-bold text-green-600"><?= $w['terisi_pcl'] ?></td>
                            <td class="py-3 px-4 text-center font-bold text-slate-600"><?= $w['kebutuhan_pml'] ?></td>
                            <td class="py-3 px-4 text-center font-bold text-green-600"><?= $w['terisi_pml'] ?></td>
                            <td class="py-3 px-4 text-center">
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full <?= $pct >= 100 ? 'bg-green-500' : ($pct >= 50 ? 'bg-orange-500' : 'bg-blue-500') ?>" style="width: <?= min($pct, 100) ?>%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400"><?= $pct ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
