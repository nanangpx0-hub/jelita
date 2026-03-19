<?php
/**
 * Teknis & Administrasi > Surat Keputusan (SK)
 */
$sk_list = get_all_sk();
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Surat Keputusan</h1>
                <p class="text-orange-100 font-medium mt-1">Manajemen SK SE2026 BPS Kabupaten Jember</p>
            </div>
            <form method="POST" action="?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=sk&action=tambah-sk" enctype="multipart/form-data" class="flex items-center gap-2 bg-white/95 px-4 py-2.5 rounded-xl shadow-lg border border-slate-100">
                <?= csrf_field() ?>
                <input type="text" name="nomor_sk" placeholder="Nomor SK" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="text" name="judul" placeholder="Judul singkat" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="date" name="tanggal_sk" required class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                <input type="file" name="file_sk" accept=".pdf" class="text-[11px] text-slate-500">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-orange-700 transition-colors btn-animate">
                    <i class="fas fa-plus mr-1"></i> Simpan SK
                </button>
            </form>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Filter Bar -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-white flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Cari nomor SK atau judul..." class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <input type="date" class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <button class="bg-green-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-green-700 transition-colors"><i class="fas fa-file-pdf mr-1"></i> Export PDF</button>
        </div>

        <!-- SK Table -->
        <div class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-100">
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">#</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Nomor SK</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Judul</th>
                            <th class="text-left py-3 px-4 font-bold text-slate-500 uppercase text-xs">Tanggal</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs">Status</th>
                            <th class="text-center py-3 px-4 font-bold text-slate-500 uppercase text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sk_list as $i => $sk): ?>
                        <tr class="border-b border-slate-50 hover:bg-orange-50/50 transition-colors">
                            <td class="py-4 px-4 font-bold text-slate-400"><?= $i + 1 ?></td>
                            <td class="py-4 px-4 font-mono font-bold text-slate-700 text-xs"><?= e($sk['nomor_sk']) ?></td>
                            <td class="py-4 px-4 font-semibold text-slate-700"><?= e($sk['judul']) ?></td>
                            <td class="py-4 px-4 text-slate-600"><?= date('d M Y', strtotime($sk['tanggal_sk'])) ?></td>
                            <td class="py-4 px-4 text-center"><?= status_badge($sk['status']) ?></td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Preview"><i class="fas fa-eye text-xs"></i></button>
                                    <button class="w-8 h-8 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors" title="Download"><i class="fas fa-download text-xs"></i></button>
                                    <button class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors" title="Cetak"><i class="fas fa-print text-xs"></i></button>
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
