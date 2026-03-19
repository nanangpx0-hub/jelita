<?php
/**
 * File View: Rekrutmen
 * Menampilkan portal lowongan pekerjaan petugas SE2026 (PCL/PML).
 */

// Mengambil data lowongan dari fungsi di backend
$daftar_lowongan = get_all_lowongan();
?>

<main class="relative z-10 w-full overflow-hidden pb-20">
    
    <!-- HEADER REKRUTMEN -->
    <section class="pt-16 pb-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate-reveal-left">
        <div class="inline-flex items-center space-x-2 bg-white/20 text-white border border-white/30 px-4 py-1.5 rounded-full text-xs font-bold mb-6 backdrop-blur-sm shadow-sm">
            <i class="fas fa-graduation-cap text-orange-200"></i>
            <span>Prioritas Utama: Mahasiswa Jember</span>
        </div>
        <h1 class="text-5xl lg:text-6xl font-black text-white leading-tight mb-6 drop-shadow-2xl">
            Bergabunglah dengan <br/>
            <span class="text-orange-200">GARDA SE2026.</span>
        </h1>
        <p class="text-lg text-orange-50 max-w-2xl mx-auto leading-relaxed font-medium drop-shadow-sm">
            BPS Kabupaten Jember memanggil talenta muda berintegritas untuk menjadi bagian dari sejarah. Bersama kita catat potensi ekonomi daerah demi kemandirian bangsa.
        </p>
    </section>

    <!-- KONTEN UTAMA REKRUTMEN -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-md rounded-[48px] p-8 md:p-14 shadow-2xl border border-white">
            
            <!-- Bilah Pencarian -->
            <div class="relative mb-12 max-w-2xl mx-auto group">
                <i class="fas fa-search absolute left-6 top-1/2 transform -translate-y-1/2 text-slate-400 group-focus-within:text-orange-600 transition-colors text-lg"></i>
                <input 
                    type="text" 
                    id="search-lowongan"
                    placeholder="Cari kecamatan atau posisi (misal: PCL Sumbersari)..." 
                    class="w-full pl-16 pr-8 py-5 rounded-3xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-bold shadow-inner text-slate-700"
                >
            </div>

            <!-- Grid Lowongan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="lowongan-container">
                
                <?php if (empty($daftar_lowongan)): ?>
                    <!-- State Jika Data Kosong -->
                    <div class="col-span-full py-20 text-center">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box-open text-3xl text-slate-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700 mb-2">Belum Ada Lowongan</h3>
                        <p class="text-slate-500 font-medium">Saat ini belum ada pembukaan rekrutmen untuk wilayah Kabupaten Jember. Silakan periksa kembali nanti.</p>
                    </div>
                <?php else: ?>
                    <!-- Looping Data Lowongan -->
                    <?php foreach ($daftar_lowongan as $lowongan): 
                        // Menentukan gaya label berdasarkan tipe petugas
                        $badge_class = ($lowongan['tipe'] === 'PML') ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700';
                    ?>
                        <div class="lowongan-card reveal bg-white border border-slate-100 p-8 rounded-[32px] hover:border-orange-500 hover:shadow-2xl transition-all group relative overflow-hidden flex flex-col h-full">
                            
                            <!-- Header Card: Tipe & Kuota -->
                            <div class="flex justify-between items-start mb-6">
                                <span class="<?= $badge_class ?> px-4 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase shadow-sm">
                                    <?= htmlspecialchars($lowongan['tipe']) ?>
                                </span>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-400 uppercase">Kebutuhan</p>
                                    <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($lowongan['kuota']) ?> Orang</p>
                                </div>
                            </div>
                            
                            <!-- Info Utama -->
                            <h3 class="text-2xl font-black text-slate-900 mb-3 group-hover:text-orange-600 transition-colors leading-tight">
                                <?= htmlspecialchars($lowongan['posisi']) ?>
                            </h3>
                            <p class="location text-slate-500 font-bold text-sm mb-10 flex items-center">
                                <i class="fas fa-map-marker-alt w-4 h-4 mr-2 text-orange-500 group-hover:animate-bounce"></i> 
                                <?= htmlspecialchars($lowongan['wilayah']) ?>
                            </p>
                            
                            <!-- Footer Card: Deadline & Tombol -->
                            <div class="flex items-center justify-between mt-auto pt-6 border-t border-slate-50">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Batas Pendaftaran</p>
                                    <p class="text-xs font-bold text-slate-800">
                                        <?= date('d M Y', strtotime($lowongan['deadline'])) ?>
                                    </p>
                                </div>
                                <a href="?page=rekrutmen-petugas&sub=administrasi" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-black text-sm hover:bg-orange-600 transition-all shadow-lg btn-animate inline-flex items-center justify-center">
                                    Lamar
                                </a>
                            </div>
                            
                            <!-- Ornamen Hover -->
                            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-orange-50 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- Syarat dan Ketentuan Mini -->
            <div class="mt-16 bg-orange-50 rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 border border-orange-100">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-orange-500 text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-2">Informasi Tambahan</h4>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed">
                        Sesuai pedoman BPS, seluruh materi pelatihan akan disampaikan melalui <span class="font-bold text-slate-800">Video MicroLearning</span> dan dokumen Softcopy. Pastikan Anda memiliki perangkat (smartphone/laptop) yang memadai.
                    </p>
                </div>
            </div>

        </div>
    </section>
</main>