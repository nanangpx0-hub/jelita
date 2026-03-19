<?php
/**
 * Pelatihan > Materi & Bahan Tayang
 * Repository file, versi PDF & PPT, akses download berbasis role
 */
$materi_list = get_all_materi();
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Materi & Bahan Tayang</h1>
                <p class="text-orange-100 font-medium mt-1">Repository file pelatihan SE2026 — PDF, PPTX, XLSX</p>
            </div>
            <?php if (has_role([ROLE_ADMIN, ROLE_OPERATOR])): ?>
            <details class="bg-white/90 px-4 py-3 rounded-xl shadow-lg border border-slate-100">
                <summary class="cursor-pointer text-sm font-bold text-orange-600 flex items-center gap-2">
                    <i class="fas fa-upload"></i>
                    Upload Materi
                </summary>
                <form method="POST" action="?page=pelatihan&sub=materi&action=upload" enctype="multipart/form-data" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?= csrf_field() ?>
                    <input type="text" name="judul" placeholder="Judul materi" required class="px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                    <input type="text" name="kategori" placeholder="Kategori (mis. Referensi)" required class="px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                    <select name="tipe" required class="px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                        <option value="">Format file</option>
                        <option value="PDF">PDF</option>
                        <option value="PPT">PPT</option>
                        <option value="PPTX">PPTX</option>
                        <option value="XLS">XLS</option>
                        <option value="XLSX">XLSX</option>
                        <option value="MP4">MP4</option>
                    </select>
                    <input type="file" name="file_materi" accept=".pdf,.ppt,.pptx,.xls,.xlsx,.mp4" required class="text-[11px] text-slate-500">
                    <button type="submit" class="col-span-full bg-orange-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-orange-700 transition-colors">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Kirim Materi
                    </button>
                </form>
            </details>
            <?php endif; ?>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Filter -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-white flex flex-col sm:flex-row gap-3">
            <input type="text" placeholder="Cari materi..." class="flex-1 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
            <select class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
                <option value="">Semua Kategori</option>
                <option>Pedoman</option><option>Pelatihan</option><option>Referensi</option><option>Template</option><option>Video</option>
            </select>
            <select class="px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold focus:outline-none focus:border-orange-500">
                <option value="">Semua Format</option><option>PDF</option><option>PPTX</option><option>XLSX</option><option>MP4</option>
            </select>
        </div>

        <!-- Grid -->
        <?php if (empty($materi_list)): ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-10 shadow-xl border border-white text-center">
            <i class="fas fa-folder-open text-5xl text-slate-200 mb-4"></i>
            <h3 class="text-lg font-bold text-slate-700">Belum ada materi terpublikasi</h3>
            <p class="text-sm text-slate-500 font-medium mt-2">Unggah materi pertama untuk mulai menguji modul download.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($materi_list as $m): ?>
            <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-6 shadow-xl border border-white hover:shadow-2xl transition-all group">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <i class="fas <?= e($m['icon']) ?> text-2xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-900 group-hover:text-orange-600 transition-colors truncate"><?= e($m['judul']) ?></h3>
                        <?php $size_label = isset($m['file_size']) && $m['file_size'] > 0 ? round($m['file_size'] / 1024 / 1024, 1) . ' MB' : '—'; ?>
                        <p class="text-xs text-slate-400 font-bold mt-1"><?= e($m['kategori']) ?> • <?= e($m['tipe']) ?> • <?= $size_label ?></p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-bold"><i class="fas fa-download mr-1"></i><?= (int)($m['downloads'] ?? 0) ?> unduhan</span>
                    <a href="?page=pelatihan&sub=materi&action=download&id=<?= (int)$m['id'] ?>" class="bg-orange-500 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-colors">
                        <i class="fas fa-download mr-1"></i> Download
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
</main>
