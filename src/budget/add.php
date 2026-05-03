<?php
require_once __DIR__ . '/budget.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Tambah Budget'; $activePage='budget'; $rootPath='../../'; $alertError='';
if (isset($_POST['submit'])) {
    $Category_ID   = sanitizeInt($_POST['Category_ID'] ?? 0);
    $Monthly_Limit = sanitizeFloat($_POST['Monthly_Limit'] ?? 0);
    $Start_Date    = sanitizeInput($_POST['Start_Date'] ?? '');
    $End_Date      = sanitizeInput($_POST['End_Date'] ?? '');
    $sql = "INSERT INTO $table (Category_ID, Monthly_Limit, Start_Date, End_Date) VALUES ($Category_ID, $Monthly_Limit, '$Start_Date', '$End_Date')";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Budget berhasil ditambahkan!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Budget</h1><nav class="mdg-breadcrumb"><a href="index.php">Budget</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Budget Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Category ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Category_ID" value="<?= e($_POST['Category_ID']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Batas Bulanan (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Monthly_Limit" value="<?= e($_POST['Monthly_Limit']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label><input type="date" class="form-control" name="Start_Date" value="<?= e($_POST['Start_Date']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label><input type="date" class="form-control" name="End_Date" value="<?= e($_POST['End_Date']??'') ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
