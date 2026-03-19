<?php
/**
 * File Footer Utama
 * Memuat bagian bawah halaman, informasi hak cipta, kontak, dan penutup tag HTML.
 */
?>
    <footer class="relative z-10 bg-slate-900 text-white pt-16 pb-8 border-t border-white/20 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                
                <!-- Brand & Deskripsi -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-6 group cursor-pointer">
                        <div class="bg-orange-600 p-2 rounded-lg shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <i class="fas fa-database text-white text-xl"></i>
                        </div>
                        <span class="font-black text-white tracking-tighter text-2xl uppercase">SISE2026 JEMBER</span>
                    </div>
                    <p class="text-slate-400 max-w-md mb-8 leading-relaxed font-medium">
                        Portal digital resmi Badan Pusat Statistik Kabupaten Jember untuk manajemen pelaksanaan Sensus Ekonomi 2026. Kami berkomitmen menyajikan data yang akurat, transparan, dan profesional.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-white/5 border border-white/10 rounded-full flex items-center justify-center hover:bg-orange-600 transition-all duration-300 text-slate-300 hover:text-white">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/5 border border-white/10 rounded-full flex items-center justify-center hover:bg-orange-600 transition-all duration-300 text-slate-300 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/5 border border-white/10 rounded-full flex items-center justify-center hover:bg-orange-600 transition-all duration-300 text-slate-300 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                </div>

                <!-- Tautan Penting -->
                <div>
                    <h5 class="font-bold mb-6 text-sm uppercase tracking-widest text-slate-500">Tautan Penting</h5>
                    <ul class="space-y-4 text-slate-400 text-sm font-medium">
                        <li><a href="https://bps.go.id" target="_blank" class="hover:text-orange-400 transition-colors flex items-center"><i class="fas fa-angle-right mr-2 text-orange-600"></i> BPS Republik Indonesia</a></li>
                        <li><a href="https://jatim.bps.go.id" target="_blank" class="hover:text-orange-400 transition-colors flex items-center"><i class="fas fa-angle-right mr-2 text-orange-600"></i> BPS Provinsi Jawa Timur</a></li>
                        <li><a href="https://jemberkab.bps.go.id" target="_blank" class="hover:text-orange-400 transition-colors flex items-center"><i class="fas fa-angle-right mr-2 text-orange-600"></i> BPS Kabupaten Jember</a></li>
                        <li><a href="https://data.go.id/" target="_blank" class="hover:text-orange-400 transition-colors flex items-center"><i class="fas fa-angle-right mr-2 text-orange-600"></i> Portal Satu Data</a></li>
                    </ul>
                </div>

                <!-- Kontak Kami -->
                <div>
                    <h5 class="font-bold mb-6 text-sm uppercase tracking-widest text-slate-500">Kantor Kami</h5>
                    <div class="space-y-4 text-slate-400 text-sm">
                        <div class="flex items-start group">
                            <i class="fas fa-map-marker-alt w-5 h-5 mr-3 text-orange-500 flex-shrink-0 mt-1 group-hover:animate-bounce"></i>
                            <p class="leading-relaxed">Jl. Cendrawasih No. 20 Jember,<br>Jawa Timur,<br>Indonesia 68121</p>
                        </div>
                        <div class="flex items-center group">
                            <i class="fas fa-phone-alt w-5 h-5 mr-3 text-orange-500 flex-shrink-0 group-hover:text-white transition-colors"></i>
                            <p>(62-331) 487642</p>
                        </div>
                        <div class="flex items-center group">
                            <i class="fas fa-fax w-5 h-5 mr-3 text-orange-500 flex-shrink-0 group-hover:text-white transition-colors"></i>
                            <p>(62-331) 427533</p>
                        </div>
                        <div class="flex items-start group">
                            <i class="fas fa-envelope w-5 h-5 mr-3 text-orange-500 flex-shrink-0 mt-1 group-hover:animate-bounce"></i>
                            <p class="leading-relaxed">bps3509@bps.go.id</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Bawah & Hak Cipta -->
            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center text-[10px] font-bold text-slate-500 tracking-widest uppercase">
                <div class="flex flex-col md:flex-row items-center gap-2 mb-4 md:mb-0">
                    <p>&copy; <?= defined('SE_YEAR') ? SE_YEAR : '2026' ?> <?= defined('BPS_OFFICE') ? BPS_OFFICE : 'BPS KABUPATEN JEMBER' ?> | SATGAS GARDA SE2026. ALL RIGHTS RESERVED.</p>
                    <span class="hidden md:inline text-slate-600">|</span>
                    <p class="text-slate-600">DEV: NANANG PAMUNGKAS</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-white transition-colors">Keamanan Data</a>
                    <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Injeksi File Interaktivitas JavaScript -->
    <script src="assets/js/app.js"></script>

</body>
</html>