<?php
/**
 * src/pocket/edit.php — Edit Pocket
 */
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/pocket.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';

$pageTitle  = 'Edit Pocket';
$activePage = 'pocket';
$rootPath   = '../../';
$alertError = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Pocket_ID']) ? (int)$_POST['Pocket_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }

if (isset($_POST['update'])) {
    $Pocket_Name  = mysqli_real_escape_string($conn, sanitizeInput($_POST['Pocket_Name'] ?? ''));
    $Balance      = sanitizeFloat($_POST['Balance'] ?? 0);
    $Max_Budget   = sanitizeFloat($_POST['Max_Budget'] ?? 0);
    $Created_Date = sanitizeInput($_POST['Created_Date'] ?? '');

    $sql = "UPDATE $table SET
                Pocket_Name = '$Pocket_Name',
                Balance = $Balance,
                Max_Budget = $Max_Budget,
                Created_Date = '$Created_Date'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['flash_success'] = 'Data Pocket berhasil diperbarui!';
        header('Location: index.php');
        exit;
    } else {
        $alertError = 'Gagal memperbarui: ' . mysqli_error($conn);
    }
}

$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key = $id"));
if (!$row) { header('Location: index.php'); exit; }

include $rootPath . 'components/header.php';
?>
<div class="mdg-layout">
<?php include $rootPath . 'components/sidebar.php'; ?>
<div class="mdg-main">
<?php include $rootPath . 'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">

    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Pocket</h1>
            <nav class="mdg-breadcrumb"><a href="index.php">Pocket</a> / Edit #<?= $id ?></nav>
        </div>
        <a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <?php include $rootPath . 'components/alerts.php'; ?>

    <div class="mdg-card" style="max-width:560px">
        <div class="mdg-card-header">
            <span class="mdg-card-title">Edit Data Pocket</span>
        </div>
        <div class="mdg-card-body">
            <form action="edit.php" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="Pocket_ID" value="<?= $id ?>">
                <div class="mdg-form-group">
                    <label class="form-label">Nama Kantong <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="Pocket_Name"
                           value="<?= e($row['Pocket_Name']) ?>" required>
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Saldo (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="Balance"
                           value="<?= e($row['Balance']) ?>" step="0.01" min="0" required>
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Maksimal Budget (Rp)</label>
                    <input type="number" class="form-control" name="Max_Budget"
                           value="<?= e($row['Max_Budget']) ?>" step="0.01" min="0">
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Tanggal Dibuat <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" name="Created_Date"
                           value="<?= e(date('Y-m-d\TH:i', strtotime($row['Created_Date']))) ?>" required>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="update" class="btn-mdg-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
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