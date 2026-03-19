<?php
/**
 * Teknis > Memorandum & Undangan
 */
$memo_list = get_all_memorandum();
$edit_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$edit_memo = $edit_id > 0 ? get_memorandum_by_id($edit_id) : null;
$is_edit_mode = is_array($edit_memo);
$memo_form = [
    'nomor' => $edit_memo['nomor'] ?? '',
    'tipe' => $edit_memo['tipe'] ?? 'memo',
    'judul' => $edit_memo['judul'] ?? '',
    'konten' => $edit_memo['konten'] ?? '',
    'tanggal' => $edit_memo['tanggal'] ?? date('Y-m-d'),
    'waktu' => isset($edit_memo['waktu']) && $edit_memo['waktu'] !== '-' ? substr((string) $edit_memo['waktu'], 0, 5) : '',
    'tempat' => isset($edit_memo['tempat']) && $edit_memo['tempat'] !== '-' ? $edit_memo['tempat'] : '',
    'distribusi_email' => !empty($edit_memo['distribusi_email']),
    'distribusi_sms' => !empty($edit_memo['distribusi_sms']),
];
$memo_count = count(array_filter($memo_list, static function ($memo) { return ($memo['tipe'] ?? '') === 'memo'; }));
$undangan_count = count(array_filter($memo_list, static function ($memo) { return ($memo['tipe'] ?? '') === 'undangan'; }));
$email_count = count(array_filter($memo_list, static function ($memo) { return !empty($memo['distribusi_email']); }));
$sms_count = count(array_filter($memo_list, static function ($memo) { return !empty($memo['distribusi_sms']); }));
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="pt-12 pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-white drop-shadow-lg">Memorandum & Undangan</h1>
                <p class="text-orange-100 font-medium mt-1">CRUD memorandum, distribusi internal, dan pelacakan konfirmasi kehadiran</p>
            </div>
            <div class="flex gap-2">
                <a href="#memo-form" class="bg-white text-orange-600 px-4 py-2.5 rounded-xl font-bold text-sm shadow-lg btn-animate">
                    <i class="fas fa-sticky-note mr-1"></i> <?= $is_edit_mode ? 'Edit Memorandum' : 'Buat Memorandum' ?>
                </a>
                <?php if ($is_edit_mode): ?>
                <a href="?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=memorandum" class="bg-white text-slate-700 px-4 py-2.5 rounded-xl font-bold text-sm shadow-lg btn-animate">
                    <i class="fas fa-rotate-left mr-1"></i> Batal Edit
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-yellow-600"><?= $memo_count ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Memo Internal</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-blue-600"><?= $undangan_count ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Undangan</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-green-600"><?= $email_count ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Distribusi Email</p>
            </div>
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-5 shadow-xl border border-white">
                <p class="text-3xl font-black text-purple-600"><?= $sms_count ?></p>
                <p class="text-xs font-bold text-slate-500 uppercase mt-1">Distribusi SMS</p>
            </div>
        </div>

        <div id="memo-form" class="bg-white/95 backdrop-blur-md rounded-[32px] p-8 shadow-2xl border border-white">
            <div class="flex flex-col lg:flex-row justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-black text-slate-900"><?= $is_edit_mode ? 'Edit Memorandum' : 'Buat Memorandum Baru' ?></h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Form ini menyimpan memo dan undangan langsung ke tabel `memorandum`.</p>
                </div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                    <?= $is_edit_mode ? 'Mode Edit' : 'Mode Tambah' ?>
                </div>
            </div>

            <form method="POST" action="?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=memorandum&action=<?= $is_edit_mode ? 'update-memorandum&id=' . (int) $edit_memo['id'] : 'tambah-memorandum' ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <input type="text" name="nomor" value="<?= e($memo_form['nomor']) ?>" placeholder="Nomor memorandum" required class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                    <select name="tipe" class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                        <option value="memo" <?= $memo_form['tipe'] === 'memo' ? 'selected' : '' ?>>Memorandum</option>
                        <option value="undangan" <?= $memo_form['tipe'] === 'undangan' ? 'selected' : '' ?>>Undangan</option>
                    </select>
                    <input type="date" name="tanggal" value="<?= e($memo_form['tanggal']) ?>" required class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                    <input type="time" name="waktu" value="<?= e($memo_form['waktu']) ?>" class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="judul" value="<?= e($memo_form['judul']) ?>" placeholder="Judul memorandum atau undangan" required class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                    <input type="text" name="tempat" value="<?= e($memo_form['tempat']) ?>" placeholder="Tempat / lokasi (opsional)" class="px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold bg-slate-50 focus:outline-none focus:border-orange-500">
                </div>
                <textarea name="konten" rows="5" placeholder="Isi memorandum, agenda, atau instruksi distribusi" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-medium bg-slate-50 focus:outline-none focus:border-orange-500"><?= e($memo_form['konten']) ?></textarea>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex flex-wrap gap-4 text-sm font-semibold text-slate-600">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="distribusi_email" value="1" <?= $memo_form['distribusi_email'] ? 'checked' : '' ?> class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                            Distribusi Email
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="distribusi_sms" value="1" <?= $memo_form['distribusi_sms'] ? 'checked' : '' ?> class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                            Distribusi SMS
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($is_edit_mode): ?>
                        <a href="?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=memorandum" class="px-4 py-3 rounded-xl text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Batal</a>
                        <?php endif; ?>
                        <button type="submit" class="px-5 py-3 rounded-xl text-sm font-bold bg-orange-600 text-white hover:bg-orange-700 transition-colors btn-animate">
                            <i class="fas <?= $is_edit_mode ? 'fa-save' : 'fa-plus' ?> mr-1"></i><?= $is_edit_mode ? 'Simpan Perubahan' : 'Simpan Memorandum' ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($memo_list)): ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-8 shadow-xl border border-white text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-sticky-note text-2xl text-orange-500"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900">Belum ada memorandum</h3>
            <p class="text-sm text-slate-500 font-medium mt-2">Tambahkan memorandum pertama untuk mulai mengelola distribusi administrasi internal.</p>
        </div>
        <?php endif; ?>

        <?php foreach ($memo_list as $m): ?>
        <?php
        $content_preview = trim((string) ($m['konten'] ?? ''));
        if ($content_preview !== '' && strlen($content_preview) > 180) {
            $content_preview = substr($content_preview, 0, 177) . '...';
        }
        ?>
        <div class="bg-white/95 backdrop-blur-md rounded-[24px] p-6 md:p-8 shadow-xl border border-white hover:shadow-2xl transition-all group">
            <div class="flex flex-col md:flex-row gap-5 items-start">
                <div class="w-12 h-12 <?= $m['tipe'] === 'undangan' ? 'bg-blue-100' : 'bg-yellow-100' ?> rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas <?= $m['tipe'] === 'undangan' ? 'fa-envelope-open text-blue-600' : 'fa-sticky-note text-yellow-600' ?> text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-3">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 group-hover:text-orange-600 transition-colors"><?= e($m['judul']) ?></h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">
                                <span class="font-mono"><?= e($m['nomor']) ?></span>
                                <span class="mx-2">•</span>
                                <i class="fas fa-calendar mr-1"></i><?= date('d M Y', strtotime($m['tanggal'])) ?>
                                <?php if ($m['waktu'] !== '-'): ?>
                                <span class="mx-2">•</span><i class="fas fa-clock mr-1"></i><?= e($m['waktu']) ?> WIB
                                <?php endif; ?>
                                <?php if ($m['tempat'] !== '-'): ?>
                                <span class="mx-2">•</span><i class="fas fa-map-marker-alt mr-1"></i><?= e($m['tempat']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <?php if ($m['tipe'] === 'undangan'): ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fas fa-check mr-1"></i><?= (int) $m['konfirmasi'] ?> Konfirmasi</span>
                            <?php endif; ?>
                            <?php if (!empty($m['distribusi_email'])): ?>
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fas fa-envelope mr-1"></i>Email</span>
                            <?php endif; ?>
                            <?php if (!empty($m['distribusi_sms'])): ?>
                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fas fa-comment-dots mr-1"></i>SMS</span>
                            <?php endif; ?>
                            <a href="?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=memorandum&edit=<?= (int) $m['id'] ?>" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-100">
                                <i class="fas fa-pen mr-1"></i> Edit
                            </a>
                            <form method="POST" action="?page=teknis-dan-administrasi&sub=kelengkapan-administrasi&item=memorandum&action=hapus-memorandum&id=<?= (int) $m['id'] ?>" onsubmit="return confirm('Hapus memorandum ini?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-100">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php if ($content_preview !== ''): ?>
                    <div class="mt-4 bg-slate-50 rounded-2xl px-4 py-3 text-sm text-slate-600 font-medium leading-relaxed">
                        <?= nl2br(e($content_preview)) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
</main>
