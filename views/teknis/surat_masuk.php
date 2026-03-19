<?php
/**
 * Teknis > Surat Masuk
 */
$surat_list = get_all_surat_masuk();
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Surat Masuk</h1>
                <p class="text-orange-100 font-medium mt-1">Registrasi, disposisi digital, dan tracking surat masuk</p>
            </div>
            <form method="POST" action="?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=surat-masuk&action=tambah-surat" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 bg-white/95 px-4 py-2.5 rounded-xl shadow-lg border border-slate-100">
                <?= csrf_field() ?>
                <input type="text" name="nomor_surat" placeholder="No. Surat" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="text" name="pengirim" placeholder="Pengirim" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="text" name="perihal" placeholder="Perihal singkat" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="date" name="tanggal_surat" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="date" name="tanggal_terima" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="file" name="file_surat" accept=".pdf" class="text-[11px] text-slate-500">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-orange-700 transition-colors btn-animate">
                    <i class="fas fa-plus mr-1"></i> Simpan
                </button>
            </form>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php
            $counts = ['baru' => 0, 'disposisi' => 0, 'proses' => 0, 'selesai' => 0];
            foreach ($surat_list as $s) {
                if (isset($counts[$s['status']])) {
                    $counts[$s['status']]++;
                }
            }
            $labels = ['baru' => ['Baru', 'fa-envelope', 'blue'], 'disposisi' => ['Disposisi', 'fa-share', 'purple'], 'proses' => ['Proses', 'fa-spinner', 'orange'], 'selesai' => ['Selesai', 'fa-check', 'green']];
            foreach ($labels as $k => $l): ?>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-<?= $l[2] ?>-100 rounded-xl flex items-center justify-center"><i class="fas <?= $l[1] ?> text-<?= $l[2] ?>-600"></i></div>
                    <div>
                        <p class="text-2xl font-black text-slate-900"><?= $counts[$k] ?></p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase"><?= $l[0] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Filter -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-white flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Cari nomor surat atau pengirim..." class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <select class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
                <option value="">Semua Status</option>
                <option>Baru</option><option>Disposisi</option><option>Proses</option><option>Selesai</option>
            </select>
            <input type="date" class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <button class="bg-green-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-green-700"><i class="fas fa-file-excel mr-1"></i> Export</button>
        </div>

        <!-- Table -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-100">
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">No. Surat</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Pengirim</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Perihal</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Tgl Terima</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs">Status</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surat_list as $s): ?>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50 transition-colors">
                            <td class="py-4 px-4 font-mono font-bold text-slate-700 text-xs"><?= e($s['nomor_surat']) ?></td>
                            <td class="py-4 px-4 font-semibold text-slate-700"><?= e($s['pengirim']) ?></td>
                            <td class="py-4 px-4 text-slate-600 max-w-[200px] truncate"><?= e($s['perihal']) ?></td>
                            <td class="py-4 px-4 text-slate-600"><?= date('d M Y', strtotime($s['tanggal_terima'])) ?></td>
                            <td class="py-4 px-4 text-center"><?= status_badge($s['status']) ?></td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100" title="Detail"><i class="fas fa-eye text-xs"></i></button>
                                    <button class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100" title="Disposisi"><i class="fas fa-share text-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
