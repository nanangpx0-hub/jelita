<?php
/**
 * Reusable manager for documentation categories.
 *
 * Variabel wajib:
 * - $doc_config['category']
 * - $doc_config['title']
 * - $doc_config['description']
 * - $doc_config['icon']
 * - $doc_config['accept']
 * - $doc_config['form_hint']
 * - $doc_config['empty_title']
 */
$documents = get_all_dokumentasi_by_kategori($doc_config['category']);
$edit_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$edit_doc = $edit_id > 0 ? get_dokumentasi_by_id($edit_id) : null;
if ($edit_doc && ($edit_doc['kategori'] ?? '') !== $doc_config['category']) {
    $edit_doc = null;
}
$is_edit_mode = is_array($edit_doc);
$doc_form = [
    'judul' => $edit_doc['judul'] ?? '',
    'tanggal' => $edit_doc['tanggal'] ?? date('Y-m-d'),
    'deskripsi' => $edit_doc['deskripsi'] ?? '',
    'tags' => $edit_doc ? implode(', ', $edit_doc['tags'] ?? []) : '',
    'watermark' => !empty($edit_doc['watermark']),
];
$count_total = count($documents);
$count_watermark = count(array_filter($documents, static function ($doc) { return !empty($doc['watermark']); }));
$count_with_tags = count(array_filter($documents, static function ($doc) { return !empty($doc['tags']); }));
$count_downloadable = count(array_filter($documents, static function ($doc) { return !empty($doc['file_path']); }));
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg"><?= e($doc_config['title']) ?></h1>
                <p class="text-orange-100 font-medium mt-1"><?= e($doc_config['description']) ?></p>
            </div>
            <?php if (has_role([ROLE_ADMIN, ROLE_OPERATOR])): ?>
            <div class="flex gap-2">
                <a href="#doc-form" class="bg-white text-orange-600 px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg btn-animate">
                    <i class="fas <?= e($doc_config['icon']) ?> mr-2"></i><?= $is_edit_mode ? 'Edit Dokumentasi' : 'Tambah Dokumentasi' ?>
                </a>
                <?php if ($is_edit_mode): ?>
                <a href="?page=dokumentasi&sub=<?= e($doc_config['sub']) ?>" class="bg-white text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg btn-animate">
                    <i class="fas fa-rotate-left mr-2"></i>Batal Edit
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-orange-600"><?= $count_total ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Total Dokumen</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-blue-600"><?= $count_downloadable ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Siap Download</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-green-600"><?= $count_with_tags ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Bertag</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-purple-600"><?= $count_watermark ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Watermark</p>
            </div>
        </div>

        <?php if (has_role([ROLE_ADMIN, ROLE_OPERATOR])): ?>
        <div id="doc-form" class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <div class="flex flex-col lg:flex-row justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-black text-slate-900"><?= $is_edit_mode ? 'Edit Entri Dokumentasi' : 'Tambah Entri Dokumentasi' ?></h2>
                    <p class="text-sm text-slate-500 font-medium mt-1"><?= e($doc_config['form_hint']) ?></p>
                </div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider"><?= $is_edit_mode ? 'Mode Edit' : 'Mode Tambah' ?></div>
            </div>

            <form method="POST" action="?page=dokumentasi&sub=<?= e($doc_config['sub']) ?>&action=<?= $is_edit_mode ? 'update&id=' . (int) $edit_doc['id'] : 'tambah' ?>" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="judul" value="<?= e($doc_form['judul']) ?>" placeholder="Judul dokumentasi" required class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                    <input type="date" name="tanggal" value="<?= e($doc_form['tanggal']) ?>" required class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="tags" value="<?= e($doc_form['tags']) ?>" placeholder="Tag dipisahkan koma, mis. capi, batch-1, korwil" class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold text-slate-600">
                        <input type="checkbox" name="watermark" value="1" <?= $doc_form['watermark'] ? 'checked' : '' ?> class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        Tandai file dengan watermark/logistik visual
                    </label>
                </div>
                <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat dokumentasi, lokasi, atau konteks kegiatan" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium bg-slate-50 focus:outline-none focus:border-orange-500"><?= e($doc_form['deskripsi']) ?></textarea>
                <div class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
                    <div class="flex-1">
                        <input type="file" name="file_dokumentasi" accept="<?= e($doc_config['accept']) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                        <p class="text-xs text-slate-400 font-bold mt-2"><?= e($doc_config['accept_note']) ?></p>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($is_edit_mode): ?>
                        <a href="?page=dokumentasi&sub=<?= e($doc_config['sub']) ?>" class="px-4 py-3 rounded-xl text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Batal</a>
                        <?php endif; ?>
                        <button type="submit" class="px-5 py-3 rounded-xl text-sm font-bold bg-orange-600 text-white hover:bg-orange-700 transition-colors btn-animate">
                            <i class="fas <?= $is_edit_mode ? 'fa-save' : 'fa-plus' ?> mr-1"></i><?= $is_edit_mode ? 'Simpan Perubahan' : 'Simpan Dokumentasi' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if (empty($documents)): ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-8 shadow-xl border border-white text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas <?= e($doc_config['icon']) ?> text-2xl text-orange-500"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900"><?= e($doc_config['empty_title']) ?></h3>
            <p class="text-sm text-slate-500 font-medium mt-2">Tambahkan dokumentasi baru untuk mulai mengarsipkan aktivitas kategori ini.</p>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($documents as $doc): ?>
            <?php $descriptionPreview = trim((string) ($doc['deskripsi'] ?? '')); ?>
            <div class="bg-white/95 backdrop-blur-md rounded-[24px] overflow-hidden shadow-xl border border-white hover:shadow-2xl transition-all group">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas <?= e($doc['icon']) ?> text-2xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-black text-slate-900 group-hover:text-orange-600 transition-colors truncate"><?= e($doc['judul']) ?></h3>
                                <p class="text-xs text-slate-400 font-bold mt-1">
                                    <i class="fas fa-calendar mr-1"></i><?= date('d M Y', strtotime($doc['tanggal'])) ?>
                                    <span class="mx-2">•</span><?= e($doc['file_type']) ?>
                                    <span class="mx-2">•</span><?= e($doc['size_label']) ?>
                                </p>
                            </div>
                        </div>
                        <?php if (!empty($doc['watermark'])): ?>
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold flex-shrink-0">
                            <i class="fas fa-shield-alt mr-1"></i>Watermark
                        </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($descriptionPreview !== ''): ?>
                    <div class="mt-4 bg-slate-50 rounded-2xl px-4 py-3 text-sm text-slate-600 font-medium leading-relaxed">
                        <?= nl2br(e($descriptionPreview)) ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($doc['tags'])): ?>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php foreach ($doc['tags'] as $tag): ?>
                        <span class="bg-orange-50 text-orange-700 px-3 py-1 rounded-full text-xs font-bold"><?= e($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <?php if (!empty($doc['file_path'])): ?>
                        <a href="?page=dokumentasi&sub=<?= e($doc_config['sub']) ?>&action=download&id=<?= (int) $doc['id'] ?>" class="bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-colors">
                            <i class="fas fa-download mr-1"></i>Download
                        </a>
                        <?php endif; ?>
                        <?php if (has_role([ROLE_ADMIN, ROLE_OPERATOR])): ?>
                        <a href="?page=dokumentasi&sub=<?= e($doc_config['sub']) ?>&edit=<?= (int) $doc['id'] ?>" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-100 transition-colors">
                            <i class="fas fa-pen mr-1"></i>Edit
                        </a>
                        <form method="POST" action="?page=dokumentasi&sub=<?= e($doc_config['sub']) ?>&action=hapus&id=<?= (int) $doc['id'] ?>" onsubmit="return confirm('Hapus dokumentasi ini?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
