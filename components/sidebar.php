<?php
/**
 * sidebar.php
 * Komponen sidebar navigasi — digunakan di semua halaman
 *
 * Variabel yang dapat diset sebelum include:
 *   $activePage  - ID halaman aktif (string), misal: 'dashboard', 'pocket', 'budget'
 *   $rootPath    - Path ke root (string), misal: '../' atau '../../'
 */

$activePage = $activePage ?? '';
$rootPath   = $rootPath   ?? '../';

// Daftar menu navigasi
$navItems = [
    ['id' => 'dashboard',    'label' => 'Dashboard',      'icon' => 'fa-tachometer-alt', 'href' => $rootPath . 'main.php'],
    ['id' => 'pocket',       'label' => 'Pocket',         'icon' => 'fa-wallet',         'href' => $rootPath . 'src/pocket/index.php'],
    ['id' => 'transaction',  'label' => 'Transaksi',      'icon' => 'fa-exchange-alt',   'href' => $rootPath . 'src/transaction/index.php'],
    ['id' => 'budget',       'label' => 'Budget',         'icon' => 'fa-piggy-bank',     'href' => $rootPath . 'src/budget/index.php'],
    ['id' => 'goal',         'label' => 'Goal',           'icon' => 'fa-bullseye',       'href' => $rootPath . 'src/goal/index.php'],
    ['id' => 'category',     'label' => 'Kategori',       'icon' => 'fa-tags',           'href' => $rootPath . 'src/category/index.php'],
    ['id' => 'sub_category', 'label' => 'Sub Kategori',   'icon' => 'fa-tag',            'href' => $rootPath . 'src/sub_category/index.php'],
    ['id' => 'transfer',     'label' => 'Transfer',       'icon' => 'fa-arrow-right-arrow-left', 'href' => $rootPath . 'src/transfer/index.php'],
    ['id' => 'debt_loan',    'label' => 'Hutang/Piutang', 'icon' => 'fa-hand-holding-dollar', 'href' => $rootPath . 'src/debt_loan/index.php'],
    ['id' => 'contact',      'label' => 'Kontak',         'icon' => 'fa-address-book',   'href' => $rootPath . 'src/contact/index.php'],
];
?>
<!-- Sidebar -->
<nav id="mdgSidebar" class="mdg-sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">MDG</span>
            <span class="brand-tagline">My Dompet Gue</span>
        </div>
        <button class="sidebar-toggle-btn d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        <?php foreach ($navItems as $item): ?>
        <li class="sidebar-nav-item">
            <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
               class="sidebar-nav-link <?= $activePage === $item['id'] ? 'active' : '' ?>">
                <i class="fas <?= $item['icon'] ?> nav-icon"></i>
                <span class="nav-label"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- Database Mode Info di Sidebar -->
    <div class="sidebar-db-badge">
        <?php
        // Tampilkan status database jika config tersedia
        $dbConfigPath = __DIR__ . '/../config/database.php';
        if (file_exists($dbConfigPath)) {
            require_once $dbConfigPath;
            $currentMode = getDBMode();
        } else {
            $currentMode = 'local';
        }
        ?>
        <span class="db-badge <?= $currentMode === 'supabase' ? 'db-badge-cloud' : 'db-badge-local' ?>">
            <i class="fas <?= $currentMode === 'supabase' ? 'fa-cloud' : 'fa-database' ?>"></i>
            <?= $currentMode === 'supabase' ? 'Supabase' : 'MySQL Local' ?>
        </span>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <small>Final Project DB 2026</small><br>
        <small>President University</small>
    </div>
</nav>

<!-- Overlay untuk mobile -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>
