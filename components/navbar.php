<?php
/**
 * navbar.php
 * Komponen top navigation bar
 *
 * Variabel yang dapat diset sebelum include:
 *   $pageTitle  - Judul halaman
 *   $rootPath   - Path ke root
 */

$pageTitle = $pageTitle ?? 'Dashboard';
$rootPath  = $rootPath  ?? '../';
?>
<!-- Top Navbar -->
<nav class="mdg-navbar">
    <!-- Hamburger Menu Button -->
    <button class="navbar-hamburger" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Page Title -->
    <div class="navbar-title">
        <h4 class="mb-0"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h4>
    </div>

    <!-- Right Actions -->
    <div class="navbar-actions">

        <!-- Database Mode Switcher -->
        <div class="db-switcher" id="dbSwitcher">
            <?php
            $dbConfigPath = __DIR__ . '/../config/database.php';
            if (file_exists($dbConfigPath)) {
                require_once $dbConfigPath;
                $currentMode = getDBMode();
            } else {
                $currentMode = 'local';
            }
            ?>
            <button class="db-switch-btn <?= $currentMode === 'local' ? 'active' : '' ?>"
                    data-mode="local" onclick="switchDatabase('local')" title="MySQL Local">
                <i class="fas fa-database"></i>
                <span class="d-none d-md-inline">Local</span>
            </button>
            <button class="db-switch-btn <?= $currentMode === 'supabase' ? 'active' : '' ?>"
                    data-mode="supabase" onclick="switchDatabase('supabase')" title="Supabase Cloud">
                <i class="fas fa-cloud"></i>
                <span class="d-none d-md-inline">Cloud</span>
            </button>
        </div>

        <!-- Home Link -->
        <a href="<?= $rootPath ?>main.php" class="navbar-action-btn" title="Dashboard">
            <i class="fas fa-home"></i>
        </a>

    </div>
</nav>
