<?php
/**
 * Login Page
 */
?>
<main class="relative z-10 w-full overflow-hidden pb-20">
    <section class="min-h-[70vh] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">
            <div class="bg-white/95 backdrop-blur-md rounded-[40px] p-10 shadow-2xl border border-white">
                <!-- Header -->
                <div class="text-center mb-10">
                    <div class="w-20 h-20 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-orange-100">
                        <i class="fas fa-shield-alt text-3xl text-orange-600"></i>
                    </div>
                    <h1 class="text-3xl font-black text-slate-900 mb-2">Login Sistem</h1>
                    <p class="text-sm text-slate-500 font-medium"><?= APP_FULL_NAME ?></p>
                </div>

                <!-- Form -->
                <form method="POST" action="?page=login" class="space-y-6">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Username / NIP</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="username" required autocomplete="username"
                                   class="w-full pl-12 pr-4 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-bold text-slate-700"
                                   placeholder="Masukkan username...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="password" name="password" required id="login-password" autocomplete="current-password"
                                   class="w-full pl-12 pr-12 py-4 rounded-2xl bg-slate-50 border-2 border-slate-100 focus:border-orange-500 focus:bg-white outline-none transition-all font-bold text-slate-700"
                                   placeholder="Masukkan password...">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-600">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-orange-500 text-white py-4 rounded-2xl font-black text-lg hover:bg-orange-600 transition-all shadow-lg shadow-orange-200 btn-animate">
                        <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                    </button>
                </form>

                <!-- Info -->
                <div class="mt-8 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                    <p class="text-xs font-bold text-slate-500 mb-3"><i class="fas fa-info-circle text-orange-500 mr-1"></i> Demo Login:</p>
                    <div class="space-y-1.5 text-xs text-slate-600">
                        <p><span class="font-bold text-slate-700">Admin:</span> admin / password</p>
                        <p><span class="font-bold text-slate-700">Operator:</span> operator / password</p>
                        <p><span class="font-bold text-slate-700">PML:</span> pml / password</p>
                        <p><span class="font-bold text-slate-700">PCL:</span> pcl / password</p>
                        <p class="pt-2 text-[11px] text-slate-500">Akun non-admin tersedia setelah mengimpor <code>sql/seed_dummy_data.sql</code>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function togglePassword() {
    const p = document.getElementById('login-password');
    const i = document.getElementById('eye-icon');
    if (p.type === 'password') { p.type = 'text'; i.classList.replace('fa-eye', 'fa-eye-slash'); }
    else { p.type = 'password'; i.classList.replace('fa-eye-slash', 'fa-eye'); }
}
</script>
