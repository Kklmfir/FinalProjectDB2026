<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transaction.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
$pageTitle='Edit Transaksi'; $activePage='transaction'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Transaction_ID']) ? (int)$_POST['Transaction_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
$pdo = getDB();
$pocketOptions   = getOptionsWithFormat($pdo, 'Pocket',   'Pocket_ID',   'Pocket_Name');
$categoryOptions = getOptionsWithFormat($pdo, 'Category', 'Category_ID', 'Category_Name');
if (isset($_POST['update'])) {
    $Pocket_ID      = sanitizeInt($_POST['Pocket_ID']??0);
    $Category_ID    = sanitizeInt($_POST['Category_ID']??0);
    $Amount         = sanitizeFloat($_POST['Amount']??0);
    $System_Log     = sanitizeInput($_POST['System_Log']??'');
    $Description    = sanitizeInput($_POST['Description']??'');
    $Warning_Status = sanitizeInt($_POST['Warning_Status']??0);
    $errors = [];
    if ($Pocket_ID <= 0)    $errors[] = 'Pocket harus dipilih.';
    if ($Category_ID <= 0)  $errors[] = 'Kategori harus dipilih.';
    if ($Amount <= 0)       $errors[] = 'Jumlah harus lebih dari 0.';
    if (empty($System_Log)) $errors[] = 'Tanggal & Waktu tidak boleh kosong.';
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE $table SET Pocket_ID=?, Category_ID=?, Amount=?, System_Log=?, Description=?, Warning_Status=? WHERE $primary_key=?");
            $stmt->execute([$Pocket_ID, $Category_ID, $Amount, $System_Log, $Description, $Warning_Status, $id]);
            $_SESSION['flash_success']='Transaksi berhasil diperbarui!';
            header('Location: index.php'); exit;
        } catch (Exception $e) {
            $alertError = 'Gagal memperbarui data. Silakan coba lagi.';
        }
    } else {
        $alertError = implode(' ', $errors);
    }
}
try {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $primary_key=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $row = null;
}
if (!$row) { header('Location: index.php'); exit; }
$sel_pocket   = isset($_POST['Pocket_ID'])   ? (int)$_POST['Pocket_ID']   : (int)$row['Pocket_ID'];
$sel_category = isset($_POST['Category_ID']) ? (int)$_POST['Category_ID'] : (int)$row['Category_ID'];
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Transaksi</h1><nav class="mdg-breadcrumb"><a href="index.php">Transaksi</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Transaksi</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Transaction_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Pocket <span class="text-danger">*</span></label>
        <select class="form-select" name="Pocket_ID" required>
          <?= renderOptions($pocketOptions, $sel_pocket, '-- Pilih Pocket --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Kategori <span class="text-danger">*</span></label>
        <select class="form-select" name="Category_ID" required>
          <?= renderOptions($categoryOptions, $sel_category, '-- Pilih Kategori --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Amount" value="<?= e(isset($_POST['Amount']) ? $_POST['Amount'] : $row['Amount']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal &amp; Waktu <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" name="System_Log" value="<?= e(isset($_POST['System_Log']) ? $_POST['System_Log'] : date('Y-m-d\TH:i', strtotime($row['System_Log']??'now'))) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Deskripsi</label><textarea class="form-control" name="Description" rows="3"><?= e(isset($_POST['Description']) ? $_POST['Description'] : $row['Description']) ?></textarea></div>
      <div class="mdg-form-group"><label class="form-label">Warning Status</label>
        <?php $ws = isset($_POST['Warning_Status']) ? (int)$_POST['Warning_Status'] : (int)$row['Warning_Status']; ?>
        <select class="form-select" name="Warning_Status">
          <option value="0" <?= ($ws==0)?'selected':'' ?>>Normal</option>
          <option value="1" <?= ($ws==1)?'selected':'' ?>>Warning</option>
        </select>
      </div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
