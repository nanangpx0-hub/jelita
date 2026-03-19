<?php
/**
 * Pelatihan > Online
 * Notulen & Rekaman, Undangan, Zoom, Daftar Hadir, QnA
 */
$pelatihan_online = get_pelatihan_by_type('online');
$current_online   = $pelatihan_online[0] ?? null;
$qna_list = [];
if ($current_online) {
    $qna_list = get_qna_pelatihan($current_online['id'], 20);
}
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-white drop-shadow-lg">Pelatihan Online</h1>
        <p class="text-orange-100 font-medium mt-1">Notulen, rekaman, zoom, daftar hadir, dan forum Q&A</p>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Pelatihan Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($pelatihan_online as $p):
                // Tampilkan fallback yang aman bila sesi daring belum punya tanggal selesai atau link Zoom.
                $tanggal_selesai = !empty($p['tanggal_selesai']) ? $p['tanggal_selesai'] : $p['tanggal_mulai'];
                $zoom_link = trim((string) ($p['zoom_link'] ?? ''));
            ?>
            <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-6 shadow-xl border border-white hover:shadow-2xl transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <?= status_badge($p['status']) ?>
                    <span class="text-xs font-bold text-slate-400"><i class="fas fa-users mr-1"></i><?= $p['peserta'] ?> peserta</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-3 group-hover:text-orange-600 transition-colors"><?= e($p['judul']) ?></h3>
                <div class="space-y-2 text-xs text-slate-500 font-medium mb-5">
                    <p><i class="fas fa-calendar w-4 text-orange-400"></i> <?= date('d M Y', strtotime($p['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($tanggal_selesai)) ?></p>
                    <p>
                        <i class="fas fa-video w-4 text-blue-400"></i>
                        <?php if ($zoom_link !== ''): ?>
                        <a href="<?= e($zoom_link) ?>" class="text-blue-600 hover:underline"><?= e($zoom_link) ?></a>
                        <?php else: ?>
                        <span class="text-slate-400">Link Zoom diumumkan menyusul</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button class="bg-blue-50 text-blue-600 py-2 rounded-xl text-[11px] font-bold hover:bg-blue-100 transition-colors"><i class="fas fa-video mr-1"></i> Rekaman</button>
                    <button class="bg-green-50 text-green-600 py-2 rounded-xl text-[11px] font-bold hover:bg-green-100 transition-colors"><i class="fas fa-clipboard-check mr-1"></i> Presensi</button>
                    <button class="bg-purple-50 text-purple-600 py-2 rounded-xl text-[11px] font-bold hover:bg-purple-100 transition-colors"><i class="fas fa-pen-fancy mr-1"></i> Notulen</button>
                    <button class="bg-orange-50 text-orange-600 py-2 rounded-xl text-[11px] font-bold hover:bg-orange-100 transition-colors"><i class="fas fa-envelope mr-1"></i> Undangan</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- QnA Forum -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center"><i class="fas fa-comments text-purple-600"></i></div>
                Forum Tanya Jawab (Q&A)
            </h2>
            <?php if ($current_online && is_logged_in()): ?>
            <form method="POST" action="?page=pelatihan&sub=online&action=ask" class="mb-6 flex gap-3">
                <?= csrf_field() ?>
                <input type="hidden" name="pelatihan_id" value="<?= (int)$current_online['id'] ?>">
                <input type="text" name="pertanyaan" placeholder="Tulis pertanyaan Anda..." required class="flex-1 px-4 py-3 rounded-xl bg-slate-50 border-2 border-slate-100 focus:border-purple-500 outline-none font-semibold text-slate-700">
                <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-purple-700 transition-colors btn-animate"><i class="fas fa-paper-plane mr-1"></i> Kirim</button>
            </form>
            <?php else: ?>
            <div class="mb-6 bg-slate-50 rounded-2xl p-4 border border-slate-100 text-sm text-slate-500 font-medium">
                <i class="fas fa-info-circle text-slate-400 mr-2"></i>Silakan login dan pastikan sudah ada jadwal pelatihan online untuk mengirim pertanyaan.
            </div>
            <?php endif; ?>
            <div class="space-y-4">
                <?php foreach ($qna_list as $q): ?>
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 hover:border-purple-200 transition-colors">
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center gap-1 flex-shrink-0">
                            <button class="text-slate-300 hover:text-purple-600"><i class="fas fa-chevron-up"></i></button>
                            <span class="text-sm font-black text-slate-600"><?= (int)($q['votes'] ?? 0) ?></span>
                            <button class="text-slate-300 hover:text-purple-600"><i class="fas fa-chevron-down"></i></button>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-800 mb-1"><?= e($q['pertanyaan']) ?></p>
                            <p class="text-xs text-slate-400 font-bold mb-3"><i class="fas fa-user mr-1"></i><?= e($q['user_nama'] ?? 'Peserta') ?></p>
                            <?php if (!empty($q['jawaban'])): ?>
                            <div class="bg-white rounded-xl p-3 border border-green-100">
                                <p class="text-xs font-bold text-green-600 mb-1"><i class="fas fa-check-circle mr-1"></i> Jawaban</p>
                                <p class="text-sm text-slate-600 font-medium"><?= e($q['jawaban']) ?></p>
                            </div>
                            <?php else: ?>
                            <p class="text-xs text-orange-500 font-bold"><i class="fas fa-clock mr-1"></i> Menunggu jawaban...</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
