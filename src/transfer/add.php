<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transfer.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Tambah Transfer'; $activePage='transfer'; $rootPath='../../'; $alertError='';
if (isset($_POST['submit'])) {
    $Source_Pocket_ID = sanitizeInt($_POST['Source_Pocket_ID']??0);
    $Target_Pocket_ID = sanitizeInt($_POST['Target_Pocket_ID']??0);
    $Transfer_Amount  = sanitizeFloat($_POST['Transfer_Amount']??0);
    $Transfer_Date    = sanitizeInput($_POST['Transfer_Date']??'');
    $sql = "INSERT INTO $table (Source_Pocket_ID, Target_Pocket_ID, Transfer_Amount, Transfer_Date) VALUES ($Source_Pocket_ID,$Target_Pocket_ID,$Transfer_Amount,'$Transfer_Date')";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Transfer berhasil dicatat!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Transfer</h1><nav class="mdg-breadcrumb"><a href="index.php">Transfer</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Transfer Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Source Pocket ID (Dari) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Source_Pocket_ID" value="<?= e($_POST['Source_Pocket_ID']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Target Pocket ID (Ke) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Target_Pocket_ID" value="<?= e($_POST['Target_Pocket_ID']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Jumlah Transfer (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Transfer_Amount" value="<?= e($_POST['Transfer_Amount']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Transfer <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" name="Transfer_Date" value="<?= e($_POST['Transfer_Date']??date('Y-m-d\TH:i')) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
