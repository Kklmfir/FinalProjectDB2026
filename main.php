<?php
/**
 * main.php — Dashboard Utama MDG App
 * Modern Fintech Dashboard
 */

// ── Bootstrap config & helpers ────────────────────────────
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/security.php';

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
$rootPath   = './';

// ── Ambil data dari database ───────────────────────────────
try {
    $pdo = getDB();

    // Total saldo semua pocket
    $totalBalance = (float)($pdo->query("SELECT COALESCE(SUM(Balance), 0) FROM Pocket")->fetchColumn() ?? 0);

    // Total income bulan ini
    $totalIncome = (float)($pdo->query("
        SELECT COALESCE(SUM(Amount), 0) FROM Transactions
        WHERE Type = 'income'
          AND MONTH(Transaction_Date) = MONTH(CURDATE())
          AND YEAR(Transaction_Date) = YEAR(CURDATE())
    ")->fetchColumn() ?? 0);

    // Total expense bulan ini
    $totalExpense = (float)($pdo->query("
        SELECT COALESCE(SUM(Amount), 0) FROM Transactions
        WHERE Type = 'expense'
          AND MONTH(Transaction_Date) = MONTH(CURDATE())
          AND YEAR(Transaction_Date) = YEAR(CURDATE())
    ")->fetchColumn() ?? 0);

    // Jumlah pocket
    $totalPockets = (int)($pdo->query("SELECT COUNT(*) FROM Pocket")->fetchColumn() ?? 0);

    // Daftar pocket (5 teratas)
    $pockets = $pdo->query("SELECT * FROM Pocket ORDER BY Balance DESC LIMIT 5")->fetchAll();

    // Goals dengan progress
    $goals = $pdo->query("
        SELECT Goal_ID, Goal_Name, Target_Amount, Current_Amount, Deadline
        FROM Goal
        ORDER BY
            CASE WHEN Target_Amount > 0 THEN Current_Amount / Target_Amount ELSE 0 END DESC
        LIMIT 5
    ")->fetchAll();

    // Transaksi terbaru
    $recentTx = $pdo->query("
        SELECT t.Transaction_ID, t.Amount, t.Type, t.Description,
               t.Transaction_Date, c.Category_Name
        FROM Transactions t
        LEFT JOIN Category c ON t.Category_ID = c.Category_ID
        ORDER BY t.Transaction_Date DESC, t.Transaction_ID DESC
        LIMIT 8
    ")->fetchAll();

    // Data chart: transaksi 6 bulan terakhir (income & expense)
    $chartData = $pdo->query("
        SELECT
            DATE_FORMAT(Transaction_Date, '%b %Y') AS month_label,
            DATE_FORMAT(Transaction_Date, '%Y-%m') AS month_key,
            SUM(CASE WHEN Type = 'income'  THEN Amount ELSE 0 END) AS income,
            SUM(CASE WHEN Type = 'expense' THEN Amount ELSE 0 END) AS expense
        FROM Transactions
        WHERE Transaction_Date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month_key, month_label
        ORDER BY month_key ASC
    ")->fetchAll();

    // Top 5 kategori pengeluaran bulan ini
    $topCategories = $pdo->query("
        SELECT c.Category_Name, SUM(t.Amount) AS total
        FROM Transactions t
        JOIN Category c ON t.Category_ID = c.Category_ID
        WHERE t.Type = 'expense'
          AND MONTH(t.Transaction_Date) = MONTH(CURDATE())
          AND YEAR(t.Transaction_Date)  = YEAR(CURDATE())
        GROUP BY c.Category_ID, c.Category_Name
        ORDER BY total DESC
        LIMIT 5
    ")->fetchAll();

    // Aktif hutang/piutang
    $activeDebt = (int)($pdo->query("SELECT COUNT(*) FROM Debt_Loan WHERE Status = 'unpaid'")->fetchColumn() ?? 0);

    $dbError = false;

} catch (Exception $e) {
    // Jika database belum tersambung, tampilkan mode demo
    $dbError      = true;
    $totalBalance = 0;
    $totalIncome  = 0;
    $totalExpense = 0;
    $totalPockets = 0;
    $pockets      = [];
    $goals        = [];
    $recentTx     = [];
    $chartData    = [];
    $topCategories= [];
    $activeDebt   = 0;
}

// ── Siapkan data untuk chart ───────────────────────────────
$chartLabels  = array_column($chartData, 'month_label');
$chartIncome  = array_map('floatval', array_column($chartData, 'income'));
$chartExpense = array_map('floatval', array_column($chartData, 'expense'));

$catLabels = array_column($topCategories, 'Category_Name');
$catValues = array_map('floatval', array_column($topCategories, 'total'));

// Encode untuk JSON aman
$chartLabelsJson  = json_encode($chartLabels,  JSON_UNESCAPED_UNICODE);
$chartIncomeJson  = json_encode($chartIncome);
$chartExpenseJson = json_encode($chartExpense);
$catLabelsJson    = json_encode($catLabels, JSON_UNESCAPED_UNICODE);
$catValuesJson    = json_encode($catValues);

include __DIR__ . '/components/header.php';
?>

<!-- ── Layout Wrapper ───────────────────────────────────────── -->
<div class="mdg-layout">

<?php include __DIR__ . '/components/sidebar.php'; ?>

<div class="mdg-main">
<?php include __DIR__ . '/components/navbar.php'; ?>

<!-- ── Main Content ─────────────────────────────────────────── -->
<main class="mdg-content animate-fade-in">

    <?php if ($dbError): ?>
    <div class="alert mdg-alert alert-warning mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Database belum terhubung.</strong>
        Pastikan MySQL berjalan dan konfigurasi <code>.env</code> sudah benar.
        Tampilan di bawah menggunakan data demo.
    </div>
    <?php endif; ?>

    <?php include __DIR__ . '/components/alerts.php'; ?>

    <!-- ── Page Header ─────────────────────────────────────── -->
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title">Dashboard Keuangan</h1>
            <p class="page-subtitle">Selamat datang di MDG — My Dompet Gue</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="./src/transaction/add.php" class="btn-mdg-primary">
                <i class="fas fa-plus"></i> Transaksi Baru
            </a>
            <a href="./src/pocket/add.php" class="btn-mdg-secondary">
                <i class="fas fa-wallet"></i> Pocket Baru
            </a>
        </div>
    </div>

    <!-- ── Hero Balance Card ──────────────────────────────── -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="hero-card h-100">
                <div style="position:relative;z-index:1">
                    <p class="hero-label"><i class="fas fa-wallet me-1"></i> Total Saldo Semua Pocket</p>
                    <h2 class="hero-amount"><?= formatRupiah($totalBalance) ?></h2>
                    <p class="hero-sub"><?= $totalPockets ?> pocket aktif &bull; <?= date('F Y') ?></p>
                    <div class="mt-3 d-flex gap-2 flex-wrap">
                        <span class="hero-badge">
                            <i class="fas fa-arrow-down"></i>
                            +<?= formatRupiah($totalIncome) ?>
                        </span>
                        <span class="hero-badge">
                            <i class="fas fa-arrow-up"></i>
                            -<?= formatRupiah($totalExpense) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="col-12 col-lg-6">
            <div class="row g-3 h-100">
                <div class="col-6">
                    <div class="stat-card h-100">
                        <div class="stat-icon stat-icon-green">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Pemasukan</div>
                            <div class="stat-value"><?= formatRupiah($totalIncome) ?></div>
                            <div class="stat-sub">Bulan ini</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card h-100">
                        <div class="stat-icon stat-icon-red">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Pengeluaran</div>
                            <div class="stat-value"><?= formatRupiah($totalExpense) ?></div>
                            <div class="stat-sub">Bulan ini</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card h-100">
                        <div class="stat-icon stat-icon-blue">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Pocket</div>
                            <div class="stat-value"><?= $totalPockets ?></div>
                            <div class="stat-sub">Kantong aktif</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card h-100">
                        <div class="stat-icon stat-icon-gold">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Hutang/Piutang</div>
                            <div class="stat-value"><?= $activeDebt ?></div>
                            <div class="stat-sub">Belum lunas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Quick Actions ──────────────────────────────────── -->
    <div class="mdg-card mb-4">
        <div class="mdg-card-header">
            <span class="mdg-card-title"><i class="fas fa-bolt me-2 text-primary-mdg"></i>Aksi Cepat</span>
        </div>
        <div class="mdg-card-body">
            <div class="quick-actions">
                <a href="./src/transaction/add.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#d1fae5;color:#059669">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <span class="quick-action-label">Tambah Transaksi</span>
                </a>
                <a href="./src/pocket/index.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#dbeafe;color:#2563eb">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span class="quick-action-label">Kelola Pocket</span>
                </a>
                <a href="./src/transfer/add.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#fef3c7;color:#d97706">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                    </div>
                    <span class="quick-action-label">Transfer</span>
                </a>
                <a href="./src/budget/add.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#ede9fe;color:#7c3aed">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <span class="quick-action-label">Set Budget</span>
                </a>
                <a href="./src/goal/add.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#fee2e2;color:#dc2626">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <span class="quick-action-label">Tambah Goal</span>
                </a>
                <a href="./src/debt_loan/add.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#cffafe;color:#0891b2">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <span class="quick-action-label">Catat Hutang</span>
                </a>
                <a href="./src/category/index.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#fce7f3;color:#be185d">
                        <i class="fas fa-tags"></i>
                    </div>
                    <span class="quick-action-label">Kategori</span>
                </a>
                <a href="./src/contact/index.php" class="quick-action-btn">
                    <div class="quick-action-icon" style="background:#f0fdf4;color:#16a34a">
                        <i class="fas fa-address-book"></i>
                    </div>
                    <span class="quick-action-label">Kontak</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ── Charts Row ─────────────────────────────────────── -->
    <div class="row g-3 mb-4">
        <!-- Trend Chart -->
        <div class="col-12 col-xl-8">
            <div class="mdg-card h-100">
                <div class="mdg-card-header">
                    <span class="mdg-card-title"><i class="fas fa-chart-line me-2 text-primary-mdg"></i>Tren Keuangan 6 Bulan</span>
                </div>
                <div class="mdg-card-body">
                    <?php if (empty($chartData)): ?>
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h5>Belum ada data transaksi</h5>
                        <p>Mulai tambahkan transaksi untuk melihat grafik tren keuangan Anda.</p>
                    </div>
                    <?php else: ?>
                    <div class="chart-container" style="height:280px">
                        <canvas id="trendChart"></canvas>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Category Pie Chart -->
        <div class="col-12 col-xl-4">
            <div class="mdg-card h-100">
                <div class="mdg-card-header">
                    <span class="mdg-card-title"><i class="fas fa-chart-pie me-2 text-primary-mdg"></i>Pengeluaran per Kategori</span>
                </div>
                <div class="mdg-card-body">
                    <?php if (empty($topCategories)): ?>
                    <div class="empty-state">
                        <i class="fas fa-chart-pie"></i>
                        <h5>Belum ada data</h5>
                        <p>Tambahkan transaksi pengeluaran bulan ini.</p>
                    </div>
                    <?php else: ?>
                    <div class="chart-container" style="height:240px">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Goals & Pockets Row ────────────────────────────── -->
    <div class="row g-3 mb-4">
        <!-- Goal Progress -->
        <div class="col-12 col-md-6">
            <div class="mdg-card h-100">
                <div class="mdg-card-header">
                    <span class="mdg-card-title"><i class="fas fa-bullseye me-2 text-primary-mdg"></i>Progress Goal</span>
                    <a href="./src/goal/index.php" class="btn-mdg-secondary" style="padding:.3rem .75rem;font-size:.75rem">
                        Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="mdg-card-body">
                    <?php if (empty($goals)): ?>
                    <div class="empty-state">
                        <i class="fas fa-bullseye"></i>
                        <h5>Belum ada goal</h5>
                        <p>
                            <a href="./src/goal/add.php" class="btn-mdg-primary" style="font-size:.8rem;padding:.45rem 1rem">
                                + Tambah Goal
                            </a>
                        </p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($goals as $goal):
                        $pct = calcPercent((float)$goal['Current_Amount'], (float)$goal['Target_Amount']);
                        $color = $pct >= 100 ? 'green' : ($pct >= 75 ? 'blue' : ($pct >= 50 ? 'gold' : 'red'));
                    ?>
                    <div class="goal-item">
                        <div class="goal-info">
                            <span class="goal-name"><?= e($goal['Goal_Name']) ?></span>
                            <span class="goal-pct"><?= $pct ?>%</span>
                        </div>
                        <div class="progress-mdg">
                            <div class="progress-fill progress-fill-<?= $color ?>"
                                 data-width="<?= $pct ?>"
                                 style="width:<?= $pct ?>%"></div>
                        </div>
                        <div class="goal-amounts">
                            <span class="goal-current"><?= formatRupiah((float)$goal['Current_Amount']) ?></span>
                            <span class="goal-target"><?= formatRupiah((float)$goal['Target_Amount']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pocket Overview -->
        <div class="col-12 col-md-6">
            <div class="mdg-card h-100">
                <div class="mdg-card-header">
                    <span class="mdg-card-title"><i class="fas fa-wallet me-2 text-primary-mdg"></i>Pocket Saya</span>
                    <a href="./src/pocket/index.php" class="btn-mdg-secondary" style="padding:.3rem .75rem;font-size:.75rem">
                        Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="mdg-card-body">
                    <?php if (empty($pockets)): ?>
                    <div class="empty-state">
                        <i class="fas fa-wallet"></i>
                        <h5>Belum ada pocket</h5>
                        <p>
                            <a href="./src/pocket/add.php" class="btn-mdg-primary" style="font-size:.8rem;padding:.45rem 1rem">
                                + Tambah Pocket
                            </a>
                        </p>
                    </div>
                    <?php else: ?>
                    <div class="row g-2">
                        <?php
                        $pocketGradients = [
                            'linear-gradient(135deg,#059669,#0d9488)',
                            'linear-gradient(135deg,#2563eb,#7c3aed)',
                            'linear-gradient(135deg,#d97706,#dc2626)',
                            'linear-gradient(135deg,#0891b2,#0d9488)',
                            'linear-gradient(135deg,#be185d,#7c3aed)',
                        ];
                        foreach ($pockets as $i => $pocket):
                            $grad = $pocketGradients[$i % count($pocketGradients)];
                        ?>
                        <div class="col-6">
                            <div class="pocket-card" style="background:<?= $grad ?>">
                                <div class="pocket-card-name"><?= e($pocket['Pocket_Name']) ?></div>
                                <div class="pocket-card-balance"><?= formatRupiah((float)$pocket['Balance']) ?></div>
                                <div class="pocket-card-meta">
                                    Maks: <?= formatRupiah((float)$pocket['Max_Budget']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Recent Transactions ────────────────────────────── -->
    <div class="mdg-card mb-4">
        <div class="mdg-card-header">
            <span class="mdg-card-title"><i class="fas fa-history me-2 text-primary-mdg"></i>Transaksi Terbaru</span>
            <a href="./src/transaction/index.php" class="btn-mdg-secondary" style="padding:.3rem .75rem;font-size:.75rem">
                Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="mdg-card-body" style="padding-top:.75rem;padding-bottom:.75rem">
            <?php if (empty($recentTx)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <h5>Belum ada transaksi</h5>
                <p>
                    <a href="./src/transaction/add.php" class="btn-mdg-primary" style="font-size:.8rem;padding:.45rem 1rem">
                        + Tambah Transaksi
                    </a>
                </p>
            </div>
            <?php else: ?>
            <?php foreach ($recentTx as $tx):
                $type = strtolower($tx['Type'] ?? 'expense');
                $iconClass = $type === 'income' ? 'fa-arrow-down' : ($type === 'transfer' ? 'fa-exchange-alt' : 'fa-arrow-up');
                $iconBg = $type === 'income' ? 'background:#d1fae5;color:#059669' : ($type === 'transfer' ? 'background:#dbeafe;color:#2563eb' : 'background:#fee2e2;color:#dc2626');
                $amountClass = "amount-{$type}";
                $sign = $type === 'income' ? '+' : '-';
            ?>
            <div class="transaction-item">
                <div class="transaction-icon-wrap" style="<?= $iconBg ?>">
                    <i class="fas <?= $iconClass ?>"></i>
                </div>
                <div class="transaction-info">
                    <div class="transaction-name"><?= e($tx['Description'] ?: ($tx['Category_Name'] ?: 'Transaksi')) ?></div>
                    <div class="transaction-meta">
                        <?= e($tx['Category_Name'] ?? '-') ?> &bull;
                        <?= formatDateShort($tx['Transaction_Date'] ?? '') ?>
                    </div>
                </div>
                <div class="transaction-amount <?= $amountClass ?>">
                    <?= $sign ?><?= formatRupiah((float)$tx['Amount']) ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Database Status Card ───────────────────────────── -->
    <div class="mdg-card mb-4">
        <div class="mdg-card-header">
            <span class="mdg-card-title"><i class="fas fa-database me-2 text-primary-mdg"></i>Status Database</span>
        </div>
        <div class="mdg-card-body">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <span class="db-badge <?= getDBMode() === 'supabase' ? 'db-badge-cloud' : 'db-badge-local' ?>"
                          style="font-size:.8rem;padding:.4rem 1rem">
                        <i class="fas <?= getDBMode() === 'supabase' ? 'fa-cloud' : 'fa-database' ?>"></i>
                        Mode Aktif: <?= getDBLabel() ?>
                    </span>
                </div>
                <div class="col">
                    <small class="text-muted">
                        Ganti mode database menggunakan tombol <strong>Local / Cloud</strong> di navbar atas.
                        Pilihan akan tersimpan untuk sesi ini.
                    </small>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <button onclick="switchDatabase('local')"
                                class="btn-mdg-secondary"
                                style="padding:.4rem 1rem;font-size:.8rem">
                            <i class="fas fa-database me-1"></i> MySQL Local
                        </button>
                        <button onclick="switchDatabase('supabase')"
                                class="btn-mdg-secondary"
                                style="padding:.4rem 1rem;font-size:.8rem">
                            <i class="fas fa-cloud me-1"></i> Supabase
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<?php
$inlineJs = "
document.addEventListener('DOMContentLoaded', function() {
    // Trend Chart
    createTrendChart('trendChart', {$chartLabelsJson}, {$chartIncomeJson}, {$chartExpenseJson});
    // Category Donut Chart
    createCategoryChart('categoryChart', {$catLabelsJson}, {$catValuesJson});
});
";

include __DIR__ . '/components/footer.php';
?>

</div><!-- .mdg-main -->
</div><!-- .mdg-layout -->
