<?php
namespace App\Utils;

use DateTime;

class ViewHelper {
    /**
     * Format angka ke format ribuan Indonesia.
     */
    public static function formatIndo($angka) {
        return number_format($angka, 0, ',', '.');
    }

    /**
     * Badge status dengan warna dan icon.
     */
    public static function statusBadge($status) {
        $map = [
            'pending'    => ['bg-yellow-100 text-yellow-700', 'fas fa-clock'],
            'verified'   => ['bg-green-100 text-green-700', 'fas fa-check-circle'],
            'rejected'   => ['bg-red-100 text-red-700', 'fas fa-times-circle'],
            'accepted'   => ['bg-blue-100 text-blue-700', 'fas fa-user-check'],
            'draft'      => ['bg-slate-100 text-slate-600', 'fas fa-edit'],
            'published'  => ['bg-green-100 text-green-700', 'fas fa-globe'],
            'archived'   => ['bg-gray-100 text-gray-500', 'fas fa-archive'],
            'baru'       => ['bg-blue-100 text-blue-700', 'fas fa-envelope'],
            'disposisi'  => ['bg-purple-100 text-purple-700', 'fas fa-share'],
            'proses'     => ['bg-orange-100 text-orange-700', 'fas fa-spinner'],
            'selesai'    => ['bg-green-100 text-green-700', 'fas fa-check'],
            'sent'       => ['bg-green-100 text-green-700', 'fas fa-paper-plane'],
            'scheduled'  => ['bg-blue-100 text-blue-700', 'fas fa-calendar'],
            'ongoing'    => ['bg-orange-100 text-orange-700', 'fas fa-play'],
            'completed'  => ['bg-green-100 text-green-700', 'fas fa-check-double'],
            'cancelled'  => ['bg-red-100 text-red-700', 'fas fa-ban'],
            'reported'   => ['bg-yellow-100 text-yellow-700', 'fas fa-exclamation-triangle'],
            'review'     => ['bg-purple-100 text-purple-700', 'fas fa-search'],
            'resolved'   => ['bg-green-100 text-green-700', 'fas fa-check-circle'],
            'active'     => ['bg-green-100 text-green-700', 'fas fa-play-circle'],
            'submitted'  => ['bg-blue-100 text-blue-700', 'fas fa-upload'],
            'approved'   => ['bg-green-100 text-green-700', 'fas fa-thumbs-up'],
            'revision'   => ['bg-orange-100 text-orange-700', 'fas fa-undo'],
            'upcoming'   => ['bg-blue-100 text-blue-700', 'fas fa-clock'],
        ];
        $cfg = $map[$status] ?? ['bg-slate-100 text-slate-600', 'fas fa-info-circle'];
        return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider ' . $cfg[0] . '"><i class="' . $cfg[1] . ' text-[10px]"></i>' . htmlspecialchars(ucfirst($status)) . '</span>';
    }

    /**
     * Badge role pengguna.
     */
    public static function roleBadge($role) {
        $map = [
            'admin'    => 'bg-red-100 text-red-700',
            'operator' => 'bg-purple-100 text-purple-700',
            'pml'      => 'bg-blue-100 text-blue-700',
            'pcl'      => 'bg-green-100 text-green-700',
        ];
        $cls = $map[$role] ?? 'bg-slate-100 text-slate-600';
        return '<span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider ' . $cls . '">' . strtoupper(htmlspecialchars($role)) . '</span>';
    }

    /**
     * Hitung sisa hari ke Sensus.
     */
    public static function getDaysToSensus() {
        $now = new DateTime();
        $start = new DateTime(START_DATE);
        if ($now > $start) return 0;
        $diff = $now->diff($start);
        return $diff->days;
    }
}
