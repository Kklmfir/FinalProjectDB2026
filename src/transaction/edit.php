<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transaction.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Transaksi'; $activePage='transaction'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Transaction_ID']) ? (int)$_POST['Transaction_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Pocket_ID      = sanitizeInt($_POST['Pocket_ID']??0);
    $Category_ID    = sanitizeInt($_POST['Category_ID']??0);
    $Amount         = sanitizeFloat($_POST['Amount']??0);
    $System_Log     = sanitizeInput($_POST['System_Log']??'');
    $Description    = mysqli_real_escape_string($conn, sanitizeInput($_POST['Description']??''));
    $Warning_Status = sanitizeInt($_POST['Warning_Status']??0);
    $sql = "UPDATE $table SET Pocket_ID=$Pocket_ID, Category_ID=$Category_ID, Amount=$Amount, System_Log='$System_Log', Description='$Description', Warning_Status=$Warning_Status WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Transaksi berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Transaksi</h1><nav class="mdg-breadcrumb"><a href="index.php">Transaksi</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Transaksi</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Transaction_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Pocket ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Pocket_ID" value="<?= e($row['Pocket_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Category ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Category_ID" value="<?= e($row['Category_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Amount" value="<?= e($row['Amount']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal &amp; Waktu <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" name="System_Log" value="<?= e(date('Y-m-d\TH:i', strtotime($row['System_Log']??'now'))) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Deskripsi</label><textarea class="form-control" name="Description" rows="3"><?= e($row['Description']) ?></textarea></div>
      <div class="mdg-form-group"><label class="form-label">Warning Status</label>
        <select class="form-select" name="Warning_Status">
          <option value="0" <?= ($row['Warning_Status']==0)?'selected':'' ?>>Normal</option>
          <option value="1" <?= ($row['Warning_Status']==1)?'selected':'' ?>>Warning</option>
        </select>
      </div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
