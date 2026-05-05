<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/sub_category.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
$pageTitle='Edit Sub Kategori'; $activePage='sub_category'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Sub_Category_ID']) ? (int)$_POST['Sub_Category_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
$pdo = getDB();
$categoryOptions = getOptionsWithFormat($pdo, 'Category', 'Category_ID', 'Category_Name');
if (isset($_POST['update'])) {
    $Category_ID = sanitizeInt($_POST['Category_ID']??0);
    $Sub_Name    = sanitizeInput($_POST['Sub_Name']??'');
    $Notes       = sanitizeInput($_POST['Notes']??'');
    $errors = [];
    if ($Category_ID <= 0) $errors[] = 'Kategori harus dipilih.';
    if (empty($Sub_Name))  $errors[] = 'Nama Sub Kategori tidak boleh kosong.';
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE $table SET Category_ID=?, Sub_Name=?, Notes=? WHERE $primary_key=?");
            $stmt->execute([$Category_ID, $Sub_Name, $Notes, $id]);
            $_SESSION['flash_success']='Sub Kategori berhasil diperbarui!';
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
$sel_category = isset($_POST['Category_ID']) ? (int)$_POST['Category_ID'] : (int)$row['Category_ID'];
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Sub Kategori</h1><nav class="mdg-breadcrumb"><a href="index.php">Sub Kategori</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Sub Kategori</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Sub_Category_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Kategori <span class="text-danger">*</span></label>
        <select class="form-select" name="Category_ID" required>
          <?= renderOptions($categoryOptions, $sel_category, '-- Pilih Kategori --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Nama Sub Kategori <span class="text-danger">*</span></label><input type="text" class="form-control" name="Sub_Name" value="<?= e(isset($_POST['Sub_Name']) ? $_POST['Sub_Name'] : $row['Sub_Name']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Catatan</label><textarea class="form-control" name="Notes" rows="3"><?= e(isset($_POST['Notes']) ? $_POST['Notes'] : $row['Notes']) ?></textarea></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
