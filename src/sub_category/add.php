<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/sub_category.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
require_once __DIR__ . '/../../helpers/counter_helper.php';
$pageTitle='Tambah Sub Kategori'; $activePage='sub_category'; $rootPath='../../'; $alertError='';
$pdo = getDB();
$categoryOptions = getOptionsWithFormat($pdo, 'Category', 'Category_ID', 'Category_Name');
if (isset($_POST['submit'])) {
    $Category_ID = sanitizeInt($_POST['Category_ID']??0);
    $Sub_Name    = sanitizeInput($_POST['Sub_Name']??'');
    $Notes       = sanitizeInput($_POST['Notes']??'');
    $errors = [];
    if ($Category_ID <= 0) $errors[] = 'Kategori harus dipilih.';
    if (empty($Sub_Name))  $errors[] = 'Nama Sub Kategori tidak boleh kosong.';
    if (empty($errors)) {
        try {
            acquireSequentialIdAndInsert($pdo, 'sub_category', function(int $newId) use ($pdo, $Category_ID, $Sub_Name, $Notes) {
                $stmt = $pdo->prepare("INSERT INTO Sub_Category (Sub_Category_ID, Category_ID, Sub_Name, Notes) VALUES (?,?,?,?)");
                $stmt->execute([$newId, $Category_ID, $Sub_Name, $Notes]);
            });
            $_SESSION['flash_success']='Sub Kategori berhasil ditambahkan!';
            header('Location: index.php'); exit;
        } catch (Exception $e) {
            $alertError = 'Gagal menyimpan data. Silakan coba lagi.';
        }
    } else {
        $alertError = implode(' ', $errors);
    }
}
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Sub Kategori</h1><nav class="mdg-breadcrumb"><a href="index.php">Sub Kategori</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Sub Kategori Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Kategori <span class="text-danger">*</span></label>
        <select class="form-select" name="Category_ID" required>
          <?= renderOptions($categoryOptions, $_POST['Category_ID']??0, '-- Pilih Kategori --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Nama Sub Kategori <span class="text-danger">*</span></label><input type="text" class="form-control" name="Sub_Name" value="<?= e($_POST['Sub_Name']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Catatan</label><textarea class="form-control" name="Notes" rows="3"><?= e($_POST['Notes']??'') ?></textarea></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
