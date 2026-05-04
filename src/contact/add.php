<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/contact.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Tambah Kontak'; $activePage='contact'; $rootPath='../../'; $alertError='';
if (isset($_POST['submit'])) {
    $Contact_Name  = mysqli_real_escape_string($conn, sanitizeInput($_POST['Contact_Name']??''));
    $Phone_Number  = mysqli_real_escape_string($conn, sanitizeInput($_POST['Phone_Number']??''));
    $Relation_Type = mysqli_real_escape_string($conn, sanitizeInput($_POST['Relation_Type']??''));
    $sql = "INSERT INTO $table (Contact_Name, Phone_Number, Relation_Type) VALUES ('$Contact_Name','$Phone_Number','$Relation_Type')";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Kontak berhasil ditambahkan!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Kontak</h1><nav class="mdg-breadcrumb"><a href="index.php">Kontak</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Kontak Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Nama Kontak <span class="text-danger">*</span></label><input type="text" class="form-control" name="Contact_Name" value="<?= e($_POST['Contact_Name']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">No. Telepon <span class="text-danger">*</span></label><input type="text" class="form-control" name="Phone_Number" value="<?= e($_POST['Phone_Number']??'') ?>" placeholder="081234567890" required></div>
      <div class="mdg-form-group"><label class="form-label">Jenis Hubungan <span class="text-danger">*</span></label><input type="text" class="form-control" name="Relation_Type" value="<?= e($_POST['Relation_Type']??'') ?>" placeholder="contoh: Teman, Keluarga, Rekan Kerja" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
