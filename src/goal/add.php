<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/goal.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Tambah Goal'; $activePage='goal'; $rootPath='../../'; $alertError='';
if (isset($_POST['submit'])) {
    $Pocket_ID     = sanitizeInt($_POST['Pocket_ID']??0);
    $Goal_Name     = mysqli_real_escape_string($conn, sanitizeInput($_POST['Goal_Name']??''));
    $Target_Amount = sanitizeFloat($_POST['Target_Amount']??0);
    $Current_Amount= sanitizeFloat($_POST['Current_Amount']??0);
    $Deadline      = sanitizeInput($_POST['Deadline']??'');
    $sql = "INSERT INTO $table (Pocket_ID, Goal_Name, Target_Amount, Current_Amount, Deadline) VALUES ($Pocket_ID,'$Goal_Name',$Target_Amount,$Current_Amount,'$Deadline')";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Goal berhasil ditambahkan!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Goal</h1><nav class="mdg-breadcrumb"><a href="index.php">Goal</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Goal Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Pocket ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Pocket_ID" value="<?= e($_POST['Pocket_ID']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Nama Goal <span class="text-danger">*</span></label><input type="text" class="form-control" name="Goal_Name" value="<?= e($_POST['Goal_Name']??'') ?>" placeholder="contoh: Beli Laptop" required></div>
      <div class="mdg-form-group"><label class="form-label">Target Amount (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Target_Amount" value="<?= e($_POST['Target_Amount']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tabungan Saat Ini (Rp)</label><input type="number" class="form-control" name="Current_Amount" value="<?= e($_POST['Current_Amount']??'0') ?>" step="0.01" min="0"></div>
      <div class="mdg-form-group"><label class="form-label">Deadline <span class="text-danger">*</span></label><input type="date" class="form-control" name="Deadline" value="<?= e($_POST['Deadline']??'') ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
