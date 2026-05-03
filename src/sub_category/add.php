<?php
require_once __DIR__ . '/sub_category.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Tambah Sub Kategori'; $activePage='sub_category'; $rootPath='../../'; $alertError='';
if (isset($_POST['submit'])) {
    $Category_ID = sanitizeInt($_POST['Category_ID']??0);
    $Sub_Name    = mysqli_real_escape_string($conn, sanitizeInput($_POST['Sub_Name']??''));
    $Notes       = mysqli_real_escape_string($conn, sanitizeInput($_POST['Notes']??''));
    $sql = "INSERT INTO $table (Category_ID, Sub_Name, Notes) VALUES ($Category_ID,'$Sub_Name','$Notes')";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Sub Kategori berhasil ditambahkan!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Sub Kategori</h1><nav class="mdg-breadcrumb"><a href="index.php">Sub Kategori</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Sub Kategori Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Category ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Category_ID" value="<?= e($_POST['Category_ID']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Nama Sub Kategori <span class="text-danger">*</span></label><input type="text" class="form-control" name="Sub_Name" value="<?= e($_POST['Sub_Name']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Catatan</label><textarea class="form-control" name="Notes" rows="3"><?= e($_POST['Notes']??'') ?></textarea></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
