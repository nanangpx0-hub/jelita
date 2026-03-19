<?php
/**
 * Header — Mega-menu Navigation
 */
$current_page = isset($_GET['page']) ? $_GET['page'] : 'beranda';
$current_sub  = isset($_GET['sub'])  ? $_GET['sub']  : '';
$current_item = isset($_GET['item']) ? $_GET['item'] : '';
$logged_in = is_logged_in();
$user_role = get_user_role();
$user_name = get_user_name();

$has_access = static function (array $entry) use ($logged_in, $user_role): bool {
    if (($entry['auth'] ?? false) && !$logged_in) {
        return false;
    }

    if (isset($entry['roles']) && (! $logged_in || !in_array($user_role, $entry['roles'], true))) {
        return false;
    }

    return true;
};

$build_sub_url = static function (string $page, string $sub, array $submenu): string {
    $params = ['page' => $page, 'sub' => $sub];

    if (isset($submenu['items']) && is_array($submenu['items']) && $submenu['items'] !== []) {
        $params['item'] = array_key_first($submenu['items']);
    }

    return '?' . http_build_query($params);
};

// Menu structure
$menus = [
    'rekrutmen-petugas' => [
        'icon' => 'fa-user-plus', 'label' => 'Rekrutmen Petugas',
        'subs' => [
            'administrasi' => ['icon' => 'fa-file-alt', 'label' => 'Administrasi'],
            'alokasi-petugas-dan-wilayah'      => ['icon' => 'fa-map-marked-alt', 'label' => 'Alokasi Petugas dan Wilayah'],
            'pengumuman'   => ['icon' => 'fa-bullhorn', 'label' => 'Pengumuman'],
        ]
    ],
    'teknis-dan-administrasi' => [
        'icon' => 'fa-cogs', 'label' => 'Teknis dan Administrasi', 'auth' => true,
        'subs' => [
            'kelengkapan-administrasi' => [
                'icon' => 'fa-stamp', 'label' => 'Kelengkapan Administrasi', 'roles' => [ROLE_OPERATOR, ROLE_ADMIN],
                'items' => [
                    'sk'           => ['icon' => 'fa-stamp', 'label' => 'SK'],
                    'surat-masuk'  => ['icon' => 'fa-envelope-open', 'label' => 'Surat Masuk'],
                    'surat-keluar' => ['icon' => 'fa-paper-plane', 'label' => 'Surat Keluar'],
                    'memorandum'   => ['icon' => 'fa-sticky-note', 'label' => 'Memorandum dan Undangan'],
                    'laporan'      => ['icon' => 'fa-clipboard-list', 'label' => 'Laporan Kegiatan'],
                    'notulen'      => ['icon' => 'fa-pen-fancy', 'label' => 'Notulen Rapat'],
                ]
            ],
            'pelatihan' => [
                'icon' => 'fa-graduation-cap', 'label' => 'Pelatihan', 'roles' => [ROLE_PML, ROLE_PCL, ROLE_OPERATOR, ROLE_ADMIN],
                'items' => [
                    'online'       => ['icon' => 'fa-laptop', 'label' => 'Online'],
                    'offline'      => ['icon' => 'fa-chalkboard-teacher', 'label' => 'Offline'],
                    'materi'       => ['icon' => 'fa-file-lines', 'label' => 'Materi'],
                    'pelaksanaan'  => ['icon' => 'fa-clipboard-list', 'label' => 'Pelaksanaan'],
                ]
            ],
        ]
    ],
    'pengolahan' => [
        'icon' => 'fa-chart-bar', 'label' => 'Pengolahan', 'auth' => true,
        'subs' => [
            'anomaly'      => ['icon' => 'fa-exclamation-triangle', 'label' => 'Anomaly'],
            'monitoring'   => ['icon' => 'fa-tachometer-alt', 'label' => 'Monitoring'],
        ]
    ],
    'dokumentasi' => [
        'icon' => 'fa-images', 'label' => 'Dokumentasi', 'auth' => true,
        'subs' => [
            'pelatihan-online'  => ['icon' => 'fa-video', 'label' => 'Dok. Pelatihan Online'],
            'pelatihan-offline' => ['icon' => 'fa-camera', 'label' => 'Dok. Pelatihan Offline'],
            'rapat'             => ['icon' => 'fa-users', 'label' => 'Dok. Rapat'],
            'foto'              => ['icon' => 'fa-image', 'label' => 'Foto Kegiatan'],
        ]
    ],
];

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Sistem Informasi Sensus Ekonomi 2026 (SISE2026) BPS Kabupaten Jember.">
    <title><?= APP_NAME ?> | <?= BPS_OFFICE ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        orange: {
                            50: '#fff4ed', 100: '#ffe5d4', 200: '#ffc7a8',
                            500: '#ff6b35', 600: '#e85a2a', 900: '#7a2b13',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="se-gradient-bg min-h-screen text-slate-900 font-sans antialiased relative">

    <!-- Flash Messages -->
    <?php if ($flash): ?>
    <div id="flash-msg" class="fixed top-20 right-4 z-[999] max-w-sm animate-slide-in">
        <div class="<?= $flash['type'] === 'error' ? 'bg-red-500' : ($flash['type'] === 'success' ? 'bg-green-500' : 'bg-blue-500') ?> text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3">
            <i class="fas <?= $flash['type'] === 'error' ? 'fa-times-circle' : 'fa-check-circle' ?> text-lg"></i>
            <span class="font-bold text-sm"><?= htmlspecialchars($flash['message']) ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <script>setTimeout(() => { const f = document.getElementById('flash-msg'); if(f) f.remove(); }, 5000);</script>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-orange-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- Logo -->
                <a href="?page=beranda" class="flex items-center space-x-3 group cursor-pointer flex-shrink-0">
                    <div class="bg-orange-500 p-2 rounded-lg shadow-lg shadow-orange-200 group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-database text-white text-lg"></i>
                    </div>
                    <div>
                        <span class="font-bold text-lg block leading-none text-slate-900 tracking-tight">SISE2026</span>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest">BPS Jember</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-1">
                    <!-- Beranda -->
                    <a href="?page=beranda" class="nav-link px-3 py-2 text-sm font-bold rounded-lg transition-all <?= $current_page === 'beranda' ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:text-orange-600 hover:bg-orange-50' ?>">
                        <i class="fas fa-home mr-1"></i> Beranda
                    </a>

                    <?php foreach ($menus as $key => $menu):
                        $is_auth_menu = isset($menu['auth']) && $menu['auth'];
                        if ($is_auth_menu && !$logged_in) continue;
                        $is_active = ($current_page === $key);
                    ?>
                    <!-- Dropdown -->
                    <div class="relative group">
                        <button class="nav-link px-3 py-2 text-sm font-bold rounded-lg transition-all flex items-center gap-1 <?= $is_active ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:text-orange-600 hover:bg-orange-50' ?>">
                            <i class="fas <?= $menu['icon'] ?> text-xs"></i>
                            <span><?= $menu['label'] ?></span>
                            <i class="fas fa-chevron-down text-[8px] ml-0.5 group-hover:rotate-180 transition-transform"></i>
                        </button>
                        <!-- Dropdown Panel -->
                        <div class="dropdown-panel absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 min-w-[220px]">
                                <?php foreach ($menu['subs'] as $skey => $smenu):
                                    if (!$has_access($smenu)) continue;
                                    if (isset($smenu['items'])):
                                ?>
                                    <div class="relative group">
                                        <button class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-all text-slate-600 hover:bg-orange-50 hover:text-orange-600">
                                            <i class="fas <?= $smenu['icon'] ?> text-xs w-4 text-center"></i>
                                            <span><?= $smenu['label'] ?></span>
                                            <i class="fas fa-chevron-right text-[8px] ml-0.5"></i>
                                        </button>
                                        <div class="absolute left-full top-0 pl-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 min-w-[220px]">
                                                <?php foreach ($smenu['items'] as $ikey => $imenu): ?>
                                                <a href="?page=<?= $key ?>&sub=<?= $skey ?>&item=<?= $ikey ?>"
                                                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?= ($current_page === $key && $current_sub === $skey && $current_item === $ikey) ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                                                    <i class="fas <?= $imenu['icon'] ?> text-xs w-4 text-center"></i>
                                                    <span><?= $imenu['label'] ?></span>
                                                </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                <a href="<?= $build_sub_url($key, $skey, $smenu) ?>"
                                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all <?= ($current_page === $key && $current_sub === $skey) ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                                    <i class="fas <?= $smenu['icon'] ?> text-xs w-4 text-center"></i>
                                    <span><?= $smenu['label'] ?></span>
                                </a>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Dashboard -->
                    <a href="?page=dashboard" class="nav-link px-3 py-2 text-sm font-bold rounded-lg transition-all <?= $current_page === 'dashboard' ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:text-orange-600 hover:bg-orange-50' ?>">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-3">
                    <?php if ($logged_in): ?>
                    <div class="hidden sm:flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase leading-none"><?= strtoupper($user_role) ?></p>
                            <p class="text-xs font-bold text-slate-700 leading-tight"><?= htmlspecialchars($user_name) ?></p>
                        </div>
                        <div class="w-9 h-9 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-orange-600 text-sm"></i>
                        </div>
                    </div>
                    <a href="?page=logout" class="bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                    <?php else: ?>
                    <a href="?page=login" class="bg-slate-900 text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-orange-600 transition-colors">
                        <i class="fas fa-sign-in-alt mr-1"></i> Login
                    </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-lg hover:bg-orange-50 transition-colors focus:outline-none">
                        <i class="fas fa-bars text-slate-900 text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu-content" class="hidden lg:hidden bg-white border-t border-orange-100 shadow-xl max-h-[80vh] overflow-y-auto">
            <div class="p-4 space-y-1">
                <a href="?page=beranda" class="block px-4 py-3 rounded-xl text-sm font-bold <?= $current_page === 'beranda' ? 'bg-orange-50 text-orange-600' : 'text-slate-700 hover:bg-orange-50' ?>">
                    <i class="fas fa-home mr-2 w-5 text-center"></i> Beranda
                </a>

                <?php foreach ($menus as $key => $menu):
                    $is_auth_menu = isset($menu['auth']) && $menu['auth'];
                    if ($is_auth_menu && !$logged_in) continue;
                    $is_active = ($current_page === $key);
                ?>
                <div class="mobile-accordion">
                    <button class="mobile-acc-btn w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold <?= $is_active ? 'bg-orange-50 text-orange-600' : 'text-slate-700 hover:bg-orange-50' ?>">
                        <span><i class="fas <?= $menu['icon'] ?> mr-2 w-5 text-center"></i> <?= $menu['label'] ?></span>
                        <i class="fas fa-chevron-down text-[10px] transition-transform <?= $is_active ? 'rotate-180' : '' ?>"></i>
                    </button>
                    <div class="mobile-acc-panel <?= $is_active ? '' : 'hidden' ?> pl-8 py-1 space-y-1">
                        <?php foreach ($menu['subs'] as $skey => $smenu):
                            if (!$has_access($smenu)) continue;
                            if (isset($smenu['items'])):
                        ?>
                        <div class="px-4 py-2 text-[11px] font-black uppercase tracking-wider text-slate-400">
                            <i class="fas <?= $smenu['icon'] ?> mr-2 w-4 text-center text-xs"></i><?= $smenu['label'] ?>
                        </div>
                        <?php foreach ($smenu['items'] as $ikey => $imenu): ?>
                        <a href="?page=<?= $key ?>&sub=<?= $skey ?>&item=<?= $ikey ?>"
                           class="block px-6 py-2 rounded-lg text-sm font-medium <?= ($current_page === $key && $current_sub === $skey && $current_item === $ikey) ? 'text-orange-600 bg-orange-50/50' : 'text-slate-500 hover:text-orange-600' ?>">
                            <i class="fas <?= $imenu['icon'] ?> mr-2 w-4 text-center text-xs"></i> <?= $imenu['label'] ?>
                        </a>
                        <?php endforeach; else: ?>
                        <a href="<?= $build_sub_url($key, $skey, $smenu) ?>"
                           class="block px-4 py-2 rounded-lg text-sm font-medium <?= ($current_page === $key && $current_sub === $skey) ? 'text-orange-600 bg-orange-50/50' : 'text-slate-500 hover:text-orange-600' ?>">
                            <i class="fas <?= $smenu['icon'] ?> mr-2 w-4 text-center text-xs"></i> <?= $smenu['label'] ?>
                        </a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <a href="?page=dashboard" class="block px-4 py-3 rounded-xl text-sm font-bold <?= $current_page === 'dashboard' ? 'bg-orange-50 text-orange-600' : 'text-slate-700 hover:bg-orange-50' ?>">
                    <i class="fas fa-chart-line mr-2 w-5 text-center"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Sub-navigation bar for active menu -->
    <?php if (isset($menus[$current_page])): ?>
    <div class="bg-white/80 backdrop-blur-sm border-b border-orange-100 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-1 py-2 overflow-x-auto scrollbar-hide">
                <span class="text-xs font-bold text-slate-400 mr-2 flex-shrink-0">
                    <i class="fas <?= $menus[$current_page]['icon'] ?> mr-1"></i><?= $menus[$current_page]['label'] ?>:
                </span>
                <?php foreach ($menus[$current_page]['subs'] as $skey => $smenu):
                    if (!$has_access($smenu)) continue;
                ?>
                <a href="<?= $build_sub_url($current_page, $skey, $smenu) ?>"
                   class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?= $current_sub === $skey || (empty($current_sub) && $skey === array_key_first($menus[$current_page]['subs'])) ? 'bg-orange-500 text-white shadow-md shadow-orange-200' : 'text-slate-500 hover:bg-orange-50 hover:text-orange-600' ?>">
                    <i class="fas <?= $smenu['icon'] ?> mr-1"></i><?= $smenu['label'] ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
