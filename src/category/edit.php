<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/category.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Edit Kategori'; $activePage='category'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Category_ID']) ? (int)$_POST['Category_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
if (isset($_POST['update'])) {
    $Category_Name = mysqli_real_escape_string($conn, sanitizeInput($_POST['Category_Name']??''));
    $Category_Type = mysqli_real_escape_string($conn, sanitizeInput($_POST['Category_Type']??''));
    $Icon_Code     = mysqli_real_escape_string($conn, sanitizeInput($_POST['Icon_Code']??''));
    $sql = "UPDATE $table SET Category_Name='$Category_Name', Category_Type='$Category_Type', Icon_Code='$Icon_Code' WHERE $primary_key=$id";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Kategori berhasil diperbarui!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE $primary_key=$id"));
if (!$row) { header('Location: index.php'); exit; }
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Kategori</h1><nav class="mdg-breadcrumb"><a href="index.php">Kategori</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Kategori</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Category_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Nama Kategori <span class="text-danger">*</span></label><input type="text" class="form-control" name="Category_Name" value="<?= e($row['Category_Name']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Tipe Kategori <span class="text-danger">*</span></label>
        <select class="form-select" name="Category_Type" required>
          <option value="">-- Pilih Tipe --</option>
          <option value="Income" <?= ($row['Category_Type']==='Income')?'selected':'' ?>>Income (Pemasukan)</option>
          <option value="Expense" <?= ($row['Category_Type']==='Expense')?'selected':'' ?>>Expense (Pengeluaran)</option>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Icon Code <span class="text-danger">*</span></label><input type="text" class="form-control" name="Icon_Code" value="<?= e($row['Icon_Code']) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath.'components/footer.php'; ?></div></div>
