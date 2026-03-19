<?php
/**
 * Pengolahan > Anomaly
 * Form pelaporan, upload bukti, workflow approval, dashboard tindak lanjut
 */
$anomaly_list = get_all_anomaly();
$status_counts = ['reported' => 0, 'review' => 0, 'resolved' => 0, 'rejected' => 0];
foreach ($anomaly_list as $a) {
    if (isset($status_counts[$a['status']])) {
        $status_counts[$a['status']]++;
    }
}
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Anomaly Report</h1>
                <p class="text-orange-100 font-medium mt-1">Pelaporan anomali, workflow approval, dan dashboard tindak lanjut</p>
            </div>
            <?php if (is_logged_in()): ?>
            <form method="POST" action="?page=pengolahan&sub=anomaly&action=lapor" class="bg-white/95 px-4 py-2.5 rounded-xl shadow-lg border border-slate-100 flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                <?= csrf_field() ?>
                <input type="text" name="judul" placeholder="Judul singkat anomali..." required class="flex-1 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-red-500">
                <input type="text" name="wilayah" placeholder="Wilayah (mis. Kec. Silo)" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-red-500">
                <button type="submit" class="bg-white text-red-600 px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg btn-animate border border-red-100">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Laporkan Anomali
                </button>
            </form>
            <?php else: ?>
            <div class="bg-white/80 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 border border-slate-100">
                <i class="fas fa-info-circle text-slate-400 mr-1"></i>Silakan login untuk melaporkan anomali.
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Dashboard Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 text-center">
                <p class="text-3xl font-black text-yellow-600"><?= $status_counts['reported'] ?></p>
                <p class="text-xs font-bold text-yellow-700 mt-1">Dilaporkan</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 text-center">
                <p class="text-3xl font-black text-purple-600"><?= $status_counts['review'] ?></p>
                <p class="text-xs font-bold text-purple-700 mt-1">Review</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
                <p class="text-3xl font-black text-green-600"><?= $status_counts['resolved'] ?></p>
                <p class="text-xs font-bold text-green-700 mt-1">Terselesaikan</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5 text-center">
                <p class="text-3xl font-black text-red-600"><?= $status_counts['rejected'] ?></p>
                <p class="text-xs font-bold text-red-700 mt-1">Ditolak</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-white flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Cari anomali..." class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <select class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
                <option value="">Semua Status</option><option>Reported</option><option>Review</option><option>Resolved</option>
            </select>
            <input type="date" class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <button class="bg-green-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-green-700"><i class="fas fa-file-excel mr-1"></i> Export</button>
        </div>

        <!-- Anomaly List -->
        <div class="space-y-4">
            <?php foreach ($anomaly_list as $a): ?>
            <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-6 shadow-xl border border-white hover:shadow-2xl transition-all group">
                <div class="flex flex-col md:flex-row gap-5 items-start">
                    <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-xl text-red-500"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-3">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-orange-600 transition-colors"><?= htmlspecialchars($a['judul']) ?></h3>
                                <p class="text-xs text-slate-400 font-bold mt-1">
                                    <i class="fas fa-map-marker-alt mr-1 text-orange-400"></i><?= htmlspecialchars($a['wilayah']) ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar mr-1"></i><?= date('d M Y', strtotime($a['tanggal'])) ?>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-user mr-1"></i><?= htmlspecialchars($a['pelapor']) ?>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <?= status_badge($a['status']) ?>
                                <button class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-100"><i class="fas fa-eye mr-1"></i> Detail</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
