<?php
/**
 * File View: Beranda (Home)
 * Menampilkan Hero Section, Statistik Pra-Prelist Jember, dan Informasi Utama.
 */

// Memanggil fungsi untuk mendapatkan data muatan dari backend
$summary_stats = get_summary_stats();
$total_usaha = format_indo($summary_stats['total_usaha']);
$usaha_besar = format_indo($summary_stats['rincian']['ub']['jumlah']);
$usaha_menengah = format_indo($summary_stats['rincian']['um']['jumlah']);
$usaha_mikro = format_indo($summary_stats['rincian']['umk']['jumlah']);
?>

<main class="relative z-10 w-full overflow-hidden">
    <!-- HERO SECTION -->
    <section class="relative pt-16 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-center">
                
                <!-- Kolom Teks Kiri -->
                <div class="lg:col-span-7 mb-16 lg:mb-0 reveal active">
                    <div class="inline-flex items-center space-x-2 bg-white/20 text-white border border-white/30 px-4 py-1.5 rounded-full text-xs font-bold mb-8 backdrop-blur-sm shadow-sm">
                        <i class="fas fa-shield-alt animate-pulse text-orange-200"></i>
                        <span>Terintegrasi & Aman • Standar BPS RI</span>
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-black text-white leading-tight mb-6 drop-shadow-2xl">
                        Mendigitalkan <br/>
                        <span class="text-orange-200">Statistik Ekonomi.</span>
                    </h1>
                    
                    <div class="text-xl text-orange-50 mb-12 max-w-xl leading-relaxed font-semibold h-16">
                        <span id="typewriter"></span><span class="cursor"></span>
                    </div>
                    
                    <div class="flex flex-wrap gap-5">
                        <a href="?page=rekrutmen-petugas&sub=administrasi" class="group bg-white text-orange-600 px-8 py-4 rounded-2xl font-black text-lg flex items-center btn-animate shadow-xl shadow-orange-900/20">
                            Daftar Petugas 
                            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-2 transition-transform duration-300"></i>
                        </a>
                        <a href="?page=dashboard" class="bg-orange-900/20 backdrop-blur-md text-white border border-white/20 px-8 py-4 rounded-2xl font-black text-lg btn-animate">
                            E-Buku Saku KBLI
                        </a>
                    </div>
                </div>

                <!-- Kolom Kartu 3D Parallax Kanan -->
                <div class="hidden lg:block lg:col-span-5 perspective">
                    <div class="parallax-card bg-white/10 backdrop-blur-xl border border-white/20 rounded-[40px] p-10 text-white shadow-2xl relative overflow-hidden">
                        <!-- Ornamen Latar -->
                        <div class="absolute -top-10 -right-10 opacity-10">
                            <i class="fas fa-chart-bar text-[240px] animate-[spin_20s_linear_infinite]"></i>
                        </div>
                        
                        <h3 class="text-2xl font-black mb-1 drop-shadow-md">Target SE2026 Jember</h3>
                        <p class="text-orange-100 text-sm mb-8 opacity-90 italic font-medium">Data Pra-Prelist SBR 2026</p>
                        
                        <div class="space-y-5 relative z-10">
                            <!-- Total Usaha -->
                            <div class="bg-white p-6 rounded-3xl shadow-xl hover:scale-105 transition-transform duration-500 cursor-default">
                                <p class="text-[11px] uppercase font-black tracking-widest text-orange-600 mb-1">Total Unit Usaha</p>
                                <p class="text-4xl font-black text-slate-900 tracking-tighter"><?= $total_usaha ?></p>
                            </div>
                            
                            <!-- Breakdown Grid -->
                            <div class="grid grid-cols-2 gap-5">
                                <div class="bg-orange-500/30 p-5 rounded-3xl border border-white/20 hover:bg-orange-500/50 transition-colors cursor-default">
                                    <p class="text-[10px] uppercase font-bold text-orange-50">Usaha Besar</p>
                                    <p class="text-2xl font-black"><?= $usaha_besar ?></p>
                                </div>
                                <div class="bg-orange-500/30 p-5 rounded-3xl border border-white/20 hover:bg-orange-500/50 transition-colors cursor-default">
                                    <p class="text-[10px] uppercase font-bold text-orange-50">Menengah</p>
                                    <p class="text-2xl font-black"><?= $usaha_menengah ?></p>
                                </div>
                            </div>
                            
                            <!-- UMK Data -->
                            <div class="bg-white/10 p-4 rounded-2xl border border-white/10 flex justify-between items-center backdrop-blur-sm">
                                <span class="text-xs font-semibold text-orange-100">Dominasi Usaha Mikro Kecil (UMK)</span>
                                <span class="font-bold text-sm bg-orange-600 px-3 py-1 rounded-full"><?= $usaha_mikro ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FEATURES & INFO SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Fitur 1 -->
            <div class="reveal bg-white/95 backdrop-blur-md p-8 rounded-[32px] shadow-xl border border-white hover:-translate-y-2 transition-transform duration-500 group">
                <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 group-hover:shadow-lg group-hover:shadow-orange-200 transition-all duration-300">
                    <i class="fas fa-shield-alt text-2xl text-orange-600 group-hover:text-white transition-colors"></i>
                </div>
                <h4 class="font-black text-slate-900 mb-3 text-xl group-hover:text-orange-600 transition-colors">Integritas Data</h4>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">
                    Sistem dirancang dengan keamanan berlapis. Data responden dilindungi penuh oleh UU Statistik No. 16 Tahun 1997.
                </p>
            </div>

            <!-- Fitur 2 -->
            <div class="reveal bg-white/95 backdrop-blur-md p-8 rounded-[32px] shadow-xl border border-white hover:-translate-y-2 transition-transform duration-500 group" style="transition-delay: 100ms;">
                <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 group-hover:shadow-lg group-hover:shadow-orange-200 transition-all duration-300">
                    <i class="fas fa-laptop-code text-2xl text-orange-600 group-hover:text-white transition-colors"></i>
                </div>
                <h4 class="font-black text-slate-900 mb-3 text-xl group-hover:text-orange-600 transition-colors">Dashboard Cerdas</h4>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">
                    Pantau progres pencacahan lapangan secara *real-time* per kecamatan. Terintegrasi dengan Peta Potensi Jember.
                </p>
            </div>

            <!-- Fitur 3 -->
            <div class="reveal bg-white/95 backdrop-blur-md p-8 rounded-[32px] shadow-xl border border-white hover:-translate-y-2 transition-transform duration-500 group" style="transition-delay: 200ms;">
                <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 group-hover:shadow-lg group-hover:shadow-orange-200 transition-all duration-300">
                    <i class="fas fa-users text-2xl text-orange-600 group-hover:text-white transition-colors"></i>
                </div>
                <h4 class="font-black text-slate-900 mb-3 text-xl group-hover:text-orange-600 transition-colors">GARDA SE2026</h4>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">
                    Satuan tugas pendampingan langsung dari BPS Jember untuk menjaga kualitas dan ketahanan petugas lapangan.
                </p>
            </div>

        </div>
    </section>

    <!-- CALL TO ACTION (CTA) SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-32 reveal">
        <div class="bg-slate-900/10 backdrop-blur-xl rounded-[48px] p-10 md:p-14 border border-white/20 flex flex-col md:flex-row items-center justify-between gap-8 shadow-2xl">
            <div class="text-center md:text-left">
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-3 drop-shadow-sm">Siap Sukseskan SE2026?</h2>
                <p class="text-slate-700 font-bold opacity-90 text-lg">Mulai langkah Anda bersama BPS Kabupaten Jember hari ini.</p>
            </div>
            <a href="?page=rekrutmen-petugas&sub=administrasi" class="bg-slate-900 text-white px-10 py-5 rounded-2xl font-black text-lg hover:bg-orange-600 hover:shadow-xl hover:shadow-orange-500/30 transition-all duration-300 active:scale-95 flex items-center flex-shrink-0">
                Gabung Sekarang <i class="fas fa-play-circle ml-3 text-xl"></i>
            </a>
        </div>
    </section>
</main>