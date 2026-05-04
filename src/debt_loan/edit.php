<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/debt_loan.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Hutang/Piutang'; $activePage='debt_loan'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Debt_ID']) ? (int)$_POST['Debt_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Contact_ID = sanitizeInt($_POST['Contact_ID']??0);
    $Pocket_ID  = sanitizeInt($_POST['Pocket_ID']??0);
    $Amount     = sanitizeFloat($_POST['Amount']??0);
    $Due_Date   = sanitizeInput($_POST['Due_Date']??'');
    $Status     = mysqli_real_escape_string($conn, sanitizeInput($_POST['Status']??'unpaid'));
    $sql = "UPDATE $table SET Contact_ID=$Contact_ID, Pocket_ID=$Pocket_ID, Amount=$Amount, Due_Date='$Due_Date', Status='$Status' WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Hutang/Piutang berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Hutang/Piutang</h1><nav class="mdg-breadcrumb"><a href="index.php">Hutang/Piutang</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Hutang/Piutang</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Debt_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Contact ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Contact_ID" value="<?= e($row['Contact_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Pocket ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Pocket_ID" value="<?= e($row['Pocket_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Amount" value="<?= e($row['Amount']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label><input type="date" class="form-control" name="Due_Date" value="<?= e($row['Due_Date']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select" name="Status" required>
          <option value="unpaid" <?= ($row['Status']==='unpaid')?'selected':'' ?>>Belum Lunas</option>
          <option value="paid"   <?= ($row['Status']==='paid')?'selected':'' ?>>Lunas</option>
          <option value="partial"<?= ($row['Status']==='partial')?'selected':'' ?>>Cicilan Aktif</option>
        </select>
      </div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
