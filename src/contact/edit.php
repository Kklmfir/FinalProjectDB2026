<?php
require_once __DIR__ . '/contact.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Kontak'; $activePage='contact'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Contact_ID']) ? (int)$_POST['Contact_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Contact_Name  = mysqli_real_escape_string($conn, sanitizeInput($_POST['Contact_Name']??''));
    $Phone_Number  = mysqli_real_escape_string($conn, sanitizeInput($_POST['Phone_Number']??''));
    $Relation_Type = mysqli_real_escape_string($conn, sanitizeInput($_POST['Relation_Type']??''));
    $sql = "UPDATE $table SET Contact_Name='$Contact_Name', Phone_Number='$Phone_Number', Relation_Type='$Relation_Type' WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Kontak berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Kontak</h1><nav class="mdg-breadcrumb"><a href="index.php">Kontak</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Kontak</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Contact_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Nama Kontak <span class="text-danger">*</span></label><input type="text" class="form-control" name="Contact_Name" value="<?= e($row['Contact_Name']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">No. Telepon <span class="text-danger">*</span></label><input type="text" class="form-control" name="Phone_Number" value="<?= e($row['Phone_Number']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Jenis Hubungan <span class="text-danger">*</span></label><input type="text" class="form-control" name="Relation_Type" value="<?= e($row['Relation_Type']) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
