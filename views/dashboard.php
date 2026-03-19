<?php
/**
 * File View: Dashboard
 * Menampilkan visualisasi data ekonomi dan progres pendataan SE2026 Jember.
 */

// Mengambil data dari fungsi di backend
$summary_stats = get_summary_stats();
$sektor_progress = get_sektor_progress();

// Kalkulasi persentase komposisi (untuk visualisasi)
$total = $summary_stats['total_usaha'];
$pct_ub = round(($summary_stats['rincian']['ub']['jumlah'] / $total) * 100, 2);
$pct_um = round(($summary_stats['rincian']['um']['jumlah'] / $total) * 100, 2);
$pct_umk = round(($summary_stats['rincian']['umk']['jumlah'] / $total) * 100, 2);
?>

<main class="relative z-10 w-full overflow-hidden pb-20">
    
    <!-- HEADER DASHBOARD -->
    <section class="pt-16 pb-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-reveal-left">
        <div class="inline-flex items-center space-x-2 bg-white/20 text-white border border-white/30 px-4 py-1.5 rounded-full text-xs font-bold mb-6 backdrop-blur-sm shadow-sm">
            <i class="fas fa-chart-line text-orange-200"></i>
            <span>Monitoring Real-time Muatan Jember</span>
        </div>
        <h1 class="text-4xl lg:text-5xl font-black text-white leading-tight mb-4 drop-shadow-2xl">
            Visualisasi Data <br/>
            <span class="text-orange-200">Ekonomi Sektoral.</span>
        </h1>
        <p class="text-lg text-orange-50 max-w-2xl mx-auto leading-relaxed font-medium drop-shadow-sm">
            Pantau progres pendataan dan struktur demografi usaha Kabupaten Jember untuk mendukung pengambilan kebijakan berbasis data yang akurat.
        </p>
    </section>

    <!-- KONTEN DASHBOARD -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Toolbar Aksi -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4 reveal">
            <div class="bg-white/90 backdrop-blur-md px-6 py-3 rounded-2xl shadow-sm border border-white/50 w-full sm:w-auto">
                <p class="text-sm font-bold text-slate-700">
                    <i class="fas fa-sync-alt text-orange-500 mr-2 animate-spin-slow"></i> 
                    Update Terakhir: <span class="text-slate-900"><?= date('d M Y, H:i') ?> WIB</span>
                </p>
            </div>
            <div class="flex space-x-3 w-full sm:w-auto">
                <button class="flex-1 sm:flex-none bg-white/90 backdrop-blur border border-white px-4 py-3 rounded-xl shadow-sm hover:bg-orange-50 transition-colors text-slate-600 hover:text-orange-600 font-bold text-sm">
                    <i class="fas fa-filter mr-2"></i> Filter Wilayah
                </button>
                <button class="flex-1 sm:flex-none bg-slate-900 text-white px-4 py-3 rounded-xl shadow-lg hover:bg-orange-600 transition-colors font-bold text-sm btn-animate">
                    <i class="fas fa-file-export mr-2"></i> Ekspor CSV
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Panel Kiri: Progres Sektoral -->
            <div class="lg:col-span-2 reveal">
                <div class="bg-white/95 backdrop-blur-md p-8 md:p-10 rounded-[40px] shadow-2xl border border-white h-full">
                    <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center">
                        <i class="fas fa-tasks w-8 h-8 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center mr-4"></i>
                        Progres Pendataan Sektoral
                    </h3>
                    
                    <div class="space-y-8">
                        <?php foreach ($sektor_progress as $sektor): 
                            // Menentukan warna progress bar berdasarkan persentase
                            $color_class = 'bg-orange-500';
                            if ($sektor['progres'] >= 80) $color_class = 'bg-green-500';
                            elseif ($sektor['progres'] < 50) $color_class = 'bg-blue-500';
                        ?>
                            <div class="group">
                                <div class="flex justify-between text-sm font-bold mb-3">
                                    <span class="text-slate-700 group-hover:text-orange-600 transition-colors">
                                        <?= htmlspecialchars($sektor['sektor']) ?>
                                    </span>
                                    <span class="text-slate-900 bg-slate-100 px-3 py-1 rounded-lg">
                                        <?= $sektor['progres'] ?>%
                                    </span>
                                </div>
                                <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                    <div class="<?= $color_class ?> h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden" 
                                         style="width: <?= $sektor['progres'] ?>%;">
                                        <!-- Efek kilap pada progress bar -->
                                        <div class="absolute top-0 left-0 w-full h-full bg-white/20"></div>
                                    </div>
                                </div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mt-2 text-right">
                                    Target: <?= format_indo($sektor['total']) ?> Usaha
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Panel Kanan: Komposisi Usaha -->
            <div class="lg:col-span-1 reveal" style="transition-delay: 100ms;">
                <div class="bg-slate-900 text-white p-8 md:p-10 rounded-[40px] shadow-2xl relative overflow-hidden h-full flex flex-col">
                    <!-- Ornamen Background -->
                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-orange-600 rounded-full blur-[80px] opacity-30"></div>
                    
                    <h3 class="text-xl font-black mb-10 flex items-center relative z-10">
                        <i class="fas fa-chart-pie w-8 h-8 bg-white/10 text-orange-400 rounded-xl flex items-center justify-center mr-4"></i>
                        Struktur Demografi
                    </h3>
                    
                    <!-- CSS Donut Chart (Representasi Statis) -->
                    <div class="flex justify-center mb-10 relative z-10">
                        <div class="relative w-48 h-48 rounded-full flex items-center justify-center" 
                             style="background: conic-gradient(#10B981 <?= $pct_umk ?>%, #F59E0B 0 <?= $pct_umk + $pct_um ?>%, #3B82F6 0 100%);">
                            <!-- Inner circle for Donut effect -->
                            <div class="absolute w-36 h-36 bg-slate-900 rounded-full flex flex-col items-center justify-center shadow-inner">
                                <span class="text-3xl font-black text-white"><?= format_indo($total) ?></span>
                                <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1">Total Unit</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Legenda -->
                    <div class="space-y-4 relative z-10 mt-auto">
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="flex items-center text-sm font-bold">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full mr-3 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                                <span class="text-slate-300">UMK</span>
                            </div>
                            <span class="font-black text-emerald-400"><?= $pct_umk ?>%</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="flex items-center text-sm font-bold">
                                <div class="w-3 h-3 bg-amber-500 rounded-full mr-3 shadow-[0_0_10px_rgba(245,158,11,0.5)]"></div>
                                <span class="text-slate-300">Menengah</span>
                            </div>
                            <span class="font-black text-amber-400"><?= $pct_um ?>%</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="flex items-center text-sm font-bold">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                                <span class="text-slate-300">Besar</span>
                            </div>
                            <span class="font-black text-blue-400"><?= $pct_ub ?>%</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Banner Ekstra Bawah -->
        <div class="mt-8 bg-white/95 backdrop-blur-md rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 border border-white shadow-xl reveal">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                    <i class="fas fa-lightbulb text-orange-500 text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900">Optimalisasi Data dengan Aplikasi E-Buku Saku KBLI</h4>
                    <p class="text-sm text-slate-500 font-medium">Pastikan klasifikasi lapangan usaha unik di Jember dikodekan dengan benar oleh petugas lapangan.</p>
                </div>
            </div>
            <button class="bg-orange-50 text-orange-600 border border-orange-200 px-6 py-3 rounded-xl font-bold text-sm hover:bg-orange-600 hover:text-white transition-all whitespace-nowrap btn-animate">
                Buka E-Buku Saku
            </button>
        </div>

    </section>
</main>