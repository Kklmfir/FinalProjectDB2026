<?php
require_once __DIR__ . '/goal.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Goal'; $activePage='goal'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Goal_ID']) ? (int)$_POST['Goal_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Pocket_ID     = sanitizeInt($_POST['Pocket_ID']??0);
    $Goal_Name     = mysqli_real_escape_string($conn, sanitizeInput($_POST['Goal_Name']??''));
    $Target_Amount = sanitizeFloat($_POST['Target_Amount']??0);
    $Current_Amount= sanitizeFloat($_POST['Current_Amount']??0);
    $Deadline      = sanitizeInput($_POST['Deadline']??'');
    $sql = "UPDATE $table SET Pocket_ID=$Pocket_ID, Goal_Name='$Goal_Name', Target_Amount=$Target_Amount, Current_Amount=$Current_Amount, Deadline='$Deadline' WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Goal berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Goal</h1><nav class="mdg-breadcrumb"><a href="index.php">Goal</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Goal</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Goal_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Pocket ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Pocket_ID" value="<?= e($row['Pocket_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Nama Goal <span class="text-danger">*</span></label><input type="text" class="form-control" name="Goal_Name" value="<?= e($row['Goal_Name']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Target Amount (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Target_Amount" value="<?= e($row['Target_Amount']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tabungan Saat Ini (Rp)</label><input type="number" class="form-control" name="Current_Amount" value="<?= e($row['Current_Amount']) ?>" step="0.01" min="0"></div>
      <div class="mdg-form-group"><label class="form-label">Deadline <span class="text-danger">*</span></label><input type="date" class="form-control" name="Deadline" value="<?= e($row['Deadline']) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
