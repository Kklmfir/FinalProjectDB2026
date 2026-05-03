<?php
require_once __DIR__ . '/debt_loan.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle='Tambah Hutang/Piutang'; $activePage='debt_loan'; $rootPath='../../'; $alertError='';
if (isset($_POST['submit'])) {
    $Contact_ID = sanitizeInt($_POST['Contact_ID']??0);
    $Pocket_ID  = sanitizeInt($_POST['Pocket_ID']??0);
    $Amount     = sanitizeFloat($_POST['Amount']??0);
    $Due_Date   = sanitizeInput($_POST['Due_Date']??'');
    $Status     = mysqli_real_escape_string($conn, sanitizeInput($_POST['Status']??'unpaid'));
    $sql = "INSERT INTO $table (Contact_ID, Pocket_ID, Amount, Due_Date, Status) VALUES ($Contact_ID,$Pocket_ID,$Amount,'$Due_Date','$Status')";
    if (mysqli_query($conn, $sql)) { $_SESSION['flash_success']='Hutang/Piutang berhasil ditambahkan!'; header('Location: index.php'); exit; }
    else { $alertError = 'Gagal: ' . mysqli_error($conn); }
}
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Hutang/Piutang</h1><nav class="mdg-breadcrumb"><a href="index.php">Hutang/Piutang</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Hutang/Piutang Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Contact ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Contact_ID" value="<?= e($_POST['Contact_ID']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Pocket ID <span class="text-danger">*</span></label><input type="number" class="form-control" name="Pocket_ID" value="<?= e($_POST['Pocket_ID']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Amount" value="<?= e($_POST['Amount']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label><input type="date" class="form-control" name="Due_Date" value="<?= e($_POST['Due_Date']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select" name="Status" required>
          <option value="unpaid">Belum Lunas</option>
          <option value="paid">Lunas</option>
          <option value="partial">Cicilan Aktif</option>
        </select>
      </div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
