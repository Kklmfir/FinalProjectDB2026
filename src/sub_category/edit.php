<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/sub_category.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Sub Kategori'; $activePage='sub_category'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Sub_Category_ID']) ? (int)$_POST['Sub_Category_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Category_ID = sanitizeInt($_POST['Category_ID']??0);
    $Sub_Name    = mysqli_real_escape_string($conn, sanitizeInput($_POST['Sub_Name']??''));
    $Notes       = mysqli_real_escape_string($conn, sanitizeInput($_POST['Notes']??''));
    $sql = "UPDATE $table SET Category_ID=$Category_ID, Sub_Name='$Sub_Name', Notes='$Notes' WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Sub Kategori berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Sub Kategori</h1><nav class="mdg-breadcrumb"><a href="index.php">Sub Kategori</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Sub Kategori</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Sub_Category_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Category ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Category_ID" value="<?= e($row['Category_ID']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Nama Sub Kategori <span class="text-danger">*</span></label><input type="text" class="form-control" name="Sub_Name" value="<?= e($row['Sub_Name']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Catatan</label><textarea class="form-control" name="Notes" rows="3"><?= e($row['Notes']) ?></textarea></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
