<?php
/**
 * src/pocket/add.php — Tambah Pocket Baru
 */
require_once __DIR__ . '/pocket.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';

$pageTitle  = 'Tambah Pocket';
$activePage = 'pocket';
$rootPath   = '../../';
$alertError = '';

if (isset($_POST['submit'])) {
    $Pocket_Name  = mysqli_real_escape_string($conn, sanitizeInput($_POST['Pocket_Name'] ?? ''));
    $Balance      = sanitizeFloat($_POST['Balance'] ?? 0);
    $Max_Budget   = sanitizeFloat($_POST['Max_Budget'] ?? 0);
    $Created_Date = sanitizeInput($_POST['Created_Date'] ?? '');

    $sql = "INSERT INTO $table (Pocket_Name, Balance, Max_Budget, Created_Date)
            VALUES ('$Pocket_Name', $Balance, $Max_Budget, '$Created_Date')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['flash_success'] = 'Pocket baru berhasil ditambahkan!';
        header('Location: index.php');
        exit;
    } else {
        $alertError = 'Gagal menyimpan: ' . mysqli_error($conn);
    }
}

include $rootPath . 'components/header.php';
?>
<div class="mdg-layout">
<?php include $rootPath . 'components/sidebar.php'; ?>
<div class="mdg-main">
<?php include $rootPath . 'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">

    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Pocket</h1>
            <nav class="mdg-breadcrumb"><a href="index.php">Pocket</a> / Tambah</nav>
        </div>
        <a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <?php include $rootPath . 'components/alerts.php'; ?>

    <div class="mdg-card" style="max-width:560px">
        <div class="mdg-card-header">
            <span class="mdg-card-title">Form Pocket Baru</span>
        </div>
        <div class="mdg-card-body">
            <form action="add.php" method="POST">
                <?= csrfField() ?>
                <div class="mdg-form-group">
                    <label class="form-label">Nama Kantong <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="Pocket_Name"
                           value="<?= e($_POST['Pocket_Name'] ?? '') ?>"
                           placeholder="contoh: Tabungan Utama" required>
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Saldo Awal (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="Balance"
                           value="<?= e($_POST['Balance'] ?? '0') ?>" step="0.01" min="0" required>
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Maksimal Budget (Rp)</label>
                    <input type="number" class="form-control" name="Max_Budget"
                           value="<?= e($_POST['Max_Budget'] ?? '0') ?>" step="0.01" min="0">
                    <div class="form-text-hint">Batas maksimum saldo pada kantong ini (opsional).</div>
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Tanggal Dibuat <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" name="Created_Date"
                           value="<?= e($_POST['Created_Date'] ?? date('Y-m-d\TH:i')) ?>" required>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="submit" class="btn-mdg-primary">
                        <i class="fas fa-save"></i> Simpan Pocket
                    </button>
                    <a href="index.php" class="btn-mdg-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</main>
<?php include $rootPath . 'components/footer.php'; ?>
</div>
</div>