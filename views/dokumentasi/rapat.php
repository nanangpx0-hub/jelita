<?php
$doc_config = [
    'category' => 'rapat',
    'sub' => 'rapat',
    'title' => 'Dokumentasi Rapat',
    'description' => 'Arsip notulen, bahan presentasi, dan foto kegiatan rapat koordinasi.',
    'icon' => 'fa-users',
    'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png',
    'accept_note' => 'Kategori ini menerima PDF, Word, dan gambar untuk notulen atau foto rapat.',
    'form_hint' => 'Unggah notulen atau dokumentasi rapat berikut tag agenda, unit, atau status tindak lanjut.',
    'empty_title' => 'Belum ada dokumentasi rapat',
];
require __DIR__ . '/_category_manager.php';
