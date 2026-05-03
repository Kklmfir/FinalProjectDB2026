<?php
require_once __DIR__ . '/budget.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Budget'; $activePage='budget'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Budget_ID']) ? (int)$_POST['Budget_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Category_ID   = sanitizeInt($_POST['Category_ID'] ?? 0);
    $Monthly_Limit = sanitizeFloat($_POST['Monthly_Limit'] ?? 0);
    $Start_Date    = sanitizeInput($_POST['Start_Date'] ?? '');
    $End_Date      = sanitizeInput($_POST['End_Date'] ?? '');
    $sql = "UPDATE $table SET Category_ID=$Category_ID, Monthly_Limit=$Monthly_Limit, Start_Date='$Start_Date', End_Date='$End_Date' WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Budget berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Budget</h1><nav class="mdg-breadcrumb"><a href="index.php">Budget</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Budget</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Budget_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Category ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Category_ID" value="<?= e($row['Category_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Batas Bulanan (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Monthly_Limit" value="<?= e($row['Monthly_Limit']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label><input type="date" class="form-control" name="Start_Date" value="<?= e($row['Start_Date']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label><input type="date" class="form-control" name="End_Date" value="<?= e($row['End_Date']) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
