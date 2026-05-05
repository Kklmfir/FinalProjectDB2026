<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/goal.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
$pageTitle='Edit Goal'; $activePage='goal'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Goal_ID']) ? (int)$_POST['Goal_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
$pdo = getDB();
$pocketOptions = getOptionsWithFormat($pdo, 'Pocket', 'Pocket_ID', 'Pocket_Name');
if (isset($_POST['update'])) {
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
            $stmt = $pdo->prepare("UPDATE $table SET Pocket_ID=?, Goal_Name=?, Target_Amount=?, Deadline_Date=? WHERE $primary_key=?");
            $stmt->execute([$Pocket_ID, $Goal_Name, $Target_Amount, $Deadline_Date, $id]);
            $_SESSION['flash_success']='Goal berhasil diperbarui!';
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
$sel_pocket = isset($_POST['Pocket_ID']) ? (int)$_POST['Pocket_ID'] : (int)$row['Pocket_ID'];
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Goal</h1><nav class="mdg-breadcrumb"><a href="index.php">Goal</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Goal</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Goal_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Pocket <span class="text-danger">*</span></label>
        <select class="form-select" name="Pocket_ID" required>
          <?= renderOptions($pocketOptions, $sel_pocket, '-- Pilih Pocket --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Nama Goal <span class="text-danger">*</span></label><input type="text" class="form-control" name="Goal_Name" value="<?= e(isset($_POST['Goal_Name']) ? $_POST['Goal_Name'] : $row['Goal_Name']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Target Amount (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Target_Amount" value="<?= e(isset($_POST['Target_Amount']) ? $_POST['Target_Amount'] : $row['Target_Amount']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Deadline <span class="text-danger">*</span></label><input type="date" class="form-control" name="Deadline_Date" value="<?= e(isset($_POST['Deadline_Date']) ? $_POST['Deadline_Date'] : $row['Deadline_Date']) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
