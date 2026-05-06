<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/budget.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
require_once __DIR__ . '/../../helpers/counter_helper.php';
$pageTitle='Tambah Budget'; $activePage='budget'; $rootPath='../../'; $alertError='';
$pdo = getDB();
$categoryOptions = getOptionsWithFormat($pdo, 'Category', 'Category_ID', 'Category_Name');
if (isset($_POST['submit'])) {
    $Category_ID   = sanitizeInt($_POST['Category_ID'] ?? 0);
    $Monthly_Limit = sanitizeFloat($_POST['Monthly_Limit'] ?? 0);
    $Start_Date    = sanitizeInput($_POST['Start_Date'] ?? '');
    $End_Date      = sanitizeInput($_POST['End_Date'] ?? '');
    $errors = [];
    if ($Category_ID <= 0)      $errors[] = 'Kategori harus dipilih.';
    if ($Monthly_Limit <= 0)    $errors[] = 'Batas Bulanan harus lebih dari 0.';
    if (empty($Start_Date))     $errors[] = 'Tanggal Mulai tidak boleh kosong.';
    if (empty($End_Date))       $errors[] = 'Tanggal Selesai tidak boleh kosong.';
    if (empty($errors) && strtotime($End_Date) < strtotime($Start_Date))
        $errors[] = 'Tanggal Selesai tidak boleh sebelum Tanggal Mulai.';
    if (empty($errors)) {
        try {
            acquireSequentialIdAndInsert($pdo, 'budget', function(int $newId) use ($pdo, $Category_ID, $Monthly_Limit, $Start_Date, $End_Date) {
                $stmt = $pdo->prepare("INSERT INTO Budget (Budget_ID, Category_ID, Monthly_Limit, Start_Date, End_Date) VALUES (?,?,?,?,?)");
                $stmt->execute([$newId, $Category_ID, $Monthly_Limit, $Start_Date, $End_Date]);
            });
            $_SESSION['flash_success']='Budget berhasil ditambahkan!';
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
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Budget</h1><nav class="mdg-breadcrumb"><a href="index.php">Budget</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Budget Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Kategori <span class="text-danger">*</span></label>
        <select class="form-select" name="Category_ID" required>
          <?= renderOptions($categoryOptions, $_POST['Category_ID']??0, '-- Pilih Kategori --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Batas Bulanan (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Monthly_Limit" value="<?= e($_POST['Monthly_Limit']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label><input type="date" class="form-control" name="Start_Date" value="<?= e($_POST['Start_Date']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label><input type="date" class="form-control" name="End_Date" value="<?= e($_POST['End_Date']??'') ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
