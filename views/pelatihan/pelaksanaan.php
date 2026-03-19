<?php
/**
 * Pelatihan > Pelaksanaan
 * Surat Tugas, Visum, KBLI/KBKI, Jadwal, Laporan, Username FASIH, Monitoring
 */
$sub_tabs = [
    'surat-tugas'  => ['Surat Tugas', 'fa-file-signature'],
    'visum'        => ['Visum', 'fa-stamp'],
    'kbli-kbki'    => ['KBLI & KBKI', 'fa-search'],
    'jadwal'       => ['Jadwal Pertemuan', 'fa-calendar-alt'],
    'laporan'      => ['Laporan', 'fa-chart-bar'],
    'fasih'        => ['Username FASIH', 'fa-key'],
    'monitoring'   => ['Monitoring', 'fa-tachometer-alt'],
];
$active_sub = isset($_GET['tab']) ? $_GET['tab'] : 'surat-tugas';
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-white drop-shadow-lg">Pelaksanaan</h1>
        <p class="text-orange-100 font-medium mt-1">Surat Tugas, Visum, KBLI/KBKI, Jadwal, Laporan, Monitoring</p>
    </section>

    <!-- Sub Tabs -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <?php foreach ($sub_tabs as $tk => $tv): ?>
            <a href="?page=pelatihan&sub=pelaksanaan&tab=<?= $tk ?>"
               class="flex-shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold transition-all <?= $active_sub === $tk ? 'bg-white text-orange-600 shadow-lg' : 'bg-white/30 text-white hover:bg-white/50' ?>">
                <i class="fas <?= $tv[1] ?> mr-1.5"></i><?= $tv[0] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($active_sub === 'surat-tugas'): ?>
        <!-- Surat Tugas -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-slate-900"><i class="fas fa-file-signature text-orange-500 mr-2"></i> Surat Tugas</h2>
                <button class="bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 btn-animate"><i class="fas fa-plus mr-1"></i> Generate ST</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b-2 border-slate-100">
                        <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">No. Surat</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Petugas</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Wilayah</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Tanggal</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs">QR Code</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs">Status</th>
                    </tr></thead>
                    <tbody>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50">
                            <td class="py-3 px-4 font-mono text-xs font-bold">ST/001/SE2026/2026</td>
                            <td class="py-3 px-4 font-semibold">Ahmad Fauzi</td>
                            <td class="py-3 px-4 text-slate-600">Kec. Sumbersari</td>
                            <td class="py-3 px-4 text-slate-600">01 Mei 2026</td>
                            <td class="py-3 px-4 text-center"><span class="bg-slate-100 px-2 py-1 rounded text-[10px] font-bold">QR-001</span></td>
                            <td class="py-3 px-4 text-center"><?= status_badge('active') ?></td>
                        </tr>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50">
                            <td class="py-3 px-4 font-mono text-xs font-bold">ST/002/SE2026/2026</td>
                            <td class="py-3 px-4 font-semibold">Siti Nurjanah</td>
                            <td class="py-3 px-4 text-slate-600">Kec. Patrang</td>
                            <td class="py-3 px-4 text-slate-600">01 Mei 2026</td>
                            <td class="py-3 px-4 text-center"><span class="bg-slate-100 px-2 py-1 rounded text-[10px] font-bold">QR-002</span></td>
                            <td class="py-3 px-4 text-center"><?= status_badge('active') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($active_sub === 'kbli-kbki'): ?>
        <!-- KBLI & KBKI Search -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-6"><i class="fas fa-search text-orange-500 mr-2"></i> Pencarian KBLI & KBKI</h2>
            <div class="flex gap-3 mb-8">
                <select class="px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 font-bold text-sm focus:border-orange-500 outline-none">
                    <option>KBLI 2020</option><option>KBKI 2015</option>
                </select>
                <input type="text" placeholder="Cari kode atau deskripsi lapangan usaha..." class="flex-1 px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 outline-none font-semibold text-slate-700">
                <button class="bg-orange-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-orange-600 btn-animate"><i class="fas fa-search mr-1"></i> Cari</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b-2 border-slate-100">
                        <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Kode</th>
                        <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Deskripsi</th>
                        <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs">Aksi</th>
                    </tr></thead>
                    <tbody>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50"><td class="py-3 px-4 font-mono font-bold text-orange-600">47111</td><td class="py-3 px-4">Perdagangan Eceran Berbagai Macam Barang yang Utamanya Makanan</td><td class="py-3 px-4 text-center"><button class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"><i class="fas fa-eye text-xs"></i></button></td></tr>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50"><td class="py-3 px-4 font-mono font-bold text-orange-600">56101</td><td class="py-3 px-4">Restoran dan Rumah Makan</td><td class="py-3 px-4 text-center"><button class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"><i class="fas fa-eye text-xs"></i></button></td></tr>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50"><td class="py-3 px-4 font-mono font-bold text-orange-600">10710</td><td class="py-3 px-4">Industri Roti dan Kue</td><td class="py-3 px-4 text-center"><button class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"><i class="fas fa-eye text-xs"></i></button></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex gap-2">
                <button class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-100"><i class="fas fa-file-pdf mr-1"></i> Preview PDF</button>
                <button class="bg-green-50 text-green-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-100"><i class="fas fa-file-excel mr-1"></i> Export Excel</button>
            </div>
        </div>

        <?php elseif ($active_sub === 'jadwal'): ?>
        <!-- Jadwal Pertemuan Calendar -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-slate-900"><i class="fas fa-calendar-alt text-orange-500 mr-2"></i> Jadwal Pertemuan</h2>
                <button class="bg-orange-500 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 btn-animate"><i class="fas fa-plus mr-1"></i> Jadwal Baru</button>
            </div>
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 mb-6">
                <?php foreach (['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day): ?>
                <div class="text-center text-xs font-bold text-slate-400 py-2"><?= $day ?></div>
                <?php endforeach;
                for ($i = 1; $i <= 31; $i++):
                    $has_event = in_array($i, [5, 12, 15, 20, 25]);
                ?>
                <div class="text-center py-3 rounded-xl text-sm font-bold cursor-pointer hover:bg-orange-50 transition-colors <?= $has_event ? 'bg-orange-100 text-orange-700' : 'text-slate-600' ?> <?= $i === 12 ? 'ring-2 ring-orange-500' : '' ?>">
                    <?= $i ?>
                    <?php if ($has_event): ?><div class="w-1.5 h-1.5 bg-orange-500 rounded-full mx-auto mt-1"></div><?php endif; ?>
                </div>
                <?php endfor; ?>
            </div>
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <p class="text-xs font-bold text-slate-400 mb-2">Agenda 12 Maret 2026:</p>
                <p class="text-sm font-bold text-slate-700"><i class="fas fa-circle text-orange-500 text-[6px] mr-2"></i>Rapat Koordinasi Petugas — 09:00 WIB, Aula BPS</p>
            </div>
        </div>

        <?php elseif ($active_sub === 'monitoring'): ?>
        <!-- Monitoring Dashboard -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-6"><i class="fas fa-tachometer-alt text-orange-500 mr-2"></i> Monitoring Real-time</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-green-50 rounded-2xl p-6 border border-green-100 text-center">
                    <p class="text-4xl font-black text-green-600">87%</p>
                    <p class="text-xs font-bold text-green-700 mt-1">Kehadiran Rata-rata</p>
                </div>
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 text-center">
                    <p class="text-4xl font-black text-blue-600">245</p>
                    <p class="text-xs font-bold text-blue-700 mt-1">Petugas Aktif</p>
                </div>
                <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100 text-center">
                    <p class="text-4xl font-black text-orange-600">12</p>
                    <p class="text-xs font-bold text-orange-700 mt-1">Alert Keterlambatan</p>
                </div>
            </div>
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-center">
                <i class="fas fa-chart-area text-4xl text-slate-200 mb-3"></i>
                <p class="text-sm font-bold text-slate-400">Grafik kehadiran harian ditampilkan di sini</p>
            </div>
        </div>

        <?php else: ?>
        <!-- Default: Info Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-4"><i class="fas <?= $sub_tabs[$active_sub][1] ?? 'fa-info' ?> text-orange-500 mr-2"></i> <?= $sub_tabs[$active_sub][0] ?? 'Info' ?></h2>
            <div class="bg-slate-50 rounded-2xl p-8 border border-slate-100 text-center">
                <i class="fas fa-tools text-5xl text-slate-200 mb-4"></i>
                <h3 class="text-lg font-bold text-slate-600 mb-2">Modul <?= $sub_tabs[$active_sub][0] ?? '' ?></h3>
                <p class="text-sm text-slate-400 font-medium">Fitur ini sedang dalam pengembangan dan akan segera tersedia.</p>
            </div>
        </div>
        <?php endif; ?>
    </section>
</main>
