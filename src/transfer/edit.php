<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transfer.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Transfer'; $activePage='transfer'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Transfer_ID']) ? (int)$_POST['Transfer_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Source_Pocket_ID = sanitizeInt($_POST['Source_Pocket_ID']??0);
    $Target_Pocket_ID = sanitizeInt($_POST['Target_Pocket_ID']??0);
    $Transfer_Amount  = sanitizeFloat($_POST['Transfer_Amount']??0);
    $Transfer_Date    = sanitizeInput($_POST['Transfer_Date']??'');
    $sql = "UPDATE $table SET Source_Pocket_ID=$Source_Pocket_ID, Target_Pocket_ID=$Target_Pocket_ID, Transfer_Amount=$Transfer_Amount, Transfer_Date='$Transfer_Date' WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Transfer berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Transfer</h1><nav class="mdg-breadcrumb"><a href="index.php">Transfer</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Transfer</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Transfer_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Source Pocket ID (Dari) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Source_Pocket_ID" value="<?= e($row['Source_Pocket_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Target Pocket ID (Ke) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Target_Pocket_ID" value="<?= e($row['Target_Pocket_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Jumlah Transfer (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Transfer_Amount" value="<?= e($row['Transfer_Amount']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Transfer <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" name="Transfer_Date" value="<?= e(date('Y-m-d\TH:i', strtotime($row['Transfer_Date']??'now'))) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
