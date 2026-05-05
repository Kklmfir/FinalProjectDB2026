<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/goal.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
$pageTitle='Tambah Goal'; $activePage='goal'; $rootPath='../../'; $alertError='';
$pdo = getDB();
$pocketOptions = getOptionsWithFormat($pdo, 'Pocket', 'Pocket_ID', 'Pocket_Name');
if (isset($_POST['submit'])) {
    $Pocket_ID     = sanitizeInt($_POST['Pocket_ID']??0);
    $Goal_Name     = sanitizeInput($_POST['Goal_Name']??'');
    $Target_Amount = sanitizeFloat($_POST['Target_Amount']??0);
    $Deadline_Date = sanitizeInput($_POST['Deadline_Date']??'');
    $errors = [];
    if ($Pocket_ID <= 0)       $errors[] = 'Pocket harus dipilih.';
    if (empty($Goal_Name))     $errors[] = 'Nama Goal tidak boleh kosong.';
    if ($Target_Amount <= 0)   $errors[] = 'Target Amount harus lebih dari 0.';
    if (empty($Deadline_Date)) $errors[] = 'Deadline tidak boleh kosong.';
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO $table (Pocket_ID, Goal_Name, Target_Amount, Deadline_Date) VALUES (?,?,?,?)");
            $stmt->execute([$Pocket_ID, $Goal_Name, $Target_Amount, $Deadline_Date]);
            $_SESSION['flash_success']='Goal berhasil ditambahkan!';
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
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Goal</h1><nav class="mdg-breadcrumb"><a href="index.php">Goal</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Goal Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Pocket <span class="text-danger">*</span></label>
        <select class="form-select" name="Pocket_ID" required>
          <?= renderOptions($pocketOptions, $_POST['Pocket_ID']??0, '-- Pilih Pocket --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Nama Goal <span class="text-danger">*</span></label><input type="text" class="form-control" name="Goal_Name" value="<?= e($_POST['Goal_Name']??'') ?>" placeholder="contoh: Beli Laptop" required></div>
      <div class="mdg-form-group"><label class="form-label">Target Amount (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Target_Amount" value="<?= e($_POST['Target_Amount']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Deadline <span class="text-danger">*</span></label><input type="date" class="form-control" name="Deadline_Date" value="<?= e($_POST['Deadline_Date']??'') ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
