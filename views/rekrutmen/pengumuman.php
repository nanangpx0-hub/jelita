<?php
/**
 * Rekrutmen > Pengumuman
 * Daftar pengumuman, jadwal, download PDF
 */
$pengumuman_list = get_all_pengumuman();
$tipe_icons = ['hasil_seleksi' => 'fa-trophy text-yellow-500', 'jadwal' => 'fa-calendar text-blue-500', 'info' => 'fa-info-circle text-green-500', 'umum' => 'fa-bullhorn text-orange-500'];
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl lg:text-4xl font-black text-white drop-shadow-lg">Pengumuman</h1>
        <p class="text-orange-100 font-medium mt-1">Informasi terbaru terkait rekrutmen petugas SE2026</p>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Filter -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-white flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Cari pengumuman..." class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <select class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
                <option value="">Semua Tipe</option>
                <option value="hasil_seleksi">Hasil Seleksi</option>
                <option value="jadwal">Jadwal</option>
                <option value="info">Informasi</option>
            </select>
            <input type="date" class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
        </div>

        <!-- Pengumuman Cards -->
        <div class="space-y-4">
            <?php foreach ($pengumuman_list as $p):
                $icon = $tipe_icons[$p['tipe']] ?? 'fa-bullhorn text-orange-500';
            ?>
            <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-6 md:p-8 shadow-xl border border-white hover:shadow-2xl transition-all group">
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-orange-50 transition-colors">
                        <i class="fas <?= $icon ?> text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-3 mb-3">
                            <div>
                                <h3 class="text-lg font-black text-slate-900 group-hover:text-orange-600 transition-colors"><?= htmlspecialchars($p['judul']) ?></h3>
                                <p class="text-xs font-bold text-slate-400 mt-1">
                                    <i class="fas fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($p['tanggal'])) ?>
                                    <span class="mx-2">•</span>
                                    <?= status_badge($p['tipe'] === 'hasil_seleksi' ? 'published' : 'upcoming') ?>
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button class="bg-orange-50 text-orange-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-100 transition-colors">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </button>
                                <button class="bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-colors">
                                    <i class="fas fa-download mr-1"></i> PDF
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 font-medium">Silakan unduh dokumen pengumuman resmi dalam format PDF untuk informasi lebih lengkap terkait <?= strtolower($p['judul']) ?>.</p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State if no results -->
        <?php if (empty($pengumuman_list)): ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-16 shadow-2xl border border-white text-center">
            <i class="fas fa-bullhorn text-6xl text-slate-200 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700 mb-2">Belum Ada Pengumuman</h3>
            <p class="text-slate-500 font-medium">Pengumuman terbaru akan ditampilkan di sini.</p>
        </div>
        <?php endif; ?>
    </section>
</main>
