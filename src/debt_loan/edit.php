<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/debt_loan.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
$pageTitle='Edit Hutang/Piutang'; $activePage='debt_loan'; $rootPath='../../'; $alertError='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['Debt_ID']) ? (int)$_POST['Debt_ID'] : 0);
if ($id <= 0) { header('Location: index.php'); exit; }
$pdo = getDB();
$contactOptions     = getOptionsWithFormat($pdo, 'Contact',      'Contact_ID',      'Contact_Name');
$pocketOptions      = getOptionsWithFormat($pdo, 'Pocket',       'Pocket_ID',       'Pocket_Name');
$subCategoryOptions = getOptionsWithFormat($pdo, 'Sub_Category', 'Sub_Category_ID', 'Sub_Name');
if (isset($_POST['update'])) {
    $Contact_ID    = sanitizeInt($_POST['Contact_ID']??0);
    $Pocket_ID     = sanitizeInt($_POST['Pocket_ID']??0);
    $Debt_Category = sanitizeInt($_POST['Debt_Category']??0);
    $Amount        = sanitizeFloat($_POST['Amount']??0);
    $Due_Date      = sanitizeInput($_POST['Due_Date']??'');
    $Status        = sanitizeInput($_POST['Status']??'unpaid');
    $errors = [];
    if ($Contact_ID <= 0)    $errors[] = 'Kontak harus dipilih.';
    if ($Pocket_ID <= 0)     $errors[] = 'Pocket harus dipilih.';
    if ($Debt_Category <= 0) $errors[] = 'Kategori Hutang harus dipilih.';
    if ($Amount <= 0)        $errors[] = 'Jumlah harus lebih dari 0.';
    if (empty($Due_Date))    $errors[] = 'Tanggal Jatuh Tempo tidak boleh kosong.';
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE $table SET Contact_ID=?, Pocket_ID=?, Debt_Category=?, Amount=?, Due_Date=?, Status=? WHERE $primary_key=?");
            $stmt->execute([$Contact_ID, $Pocket_ID, $Debt_Category, $Amount, $Due_Date, $Status, $id]);
            $_SESSION['flash_success']='Hutang/Piutang berhasil diperbarui!';
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
$sel_contact  = isset($_POST['Contact_ID'])    ? (int)$_POST['Contact_ID']    : (int)$row['Contact_ID'];
$sel_pocket   = isset($_POST['Pocket_ID'])     ? (int)$_POST['Pocket_ID']     : (int)$row['Pocket_ID'];
$sel_debt_cat = isset($_POST['Debt_Category']) ? (int)$_POST['Debt_Category'] : (int)$row['Debt_Category'];
include $rootPath . 'components/header.php'; ?>
<div class="mdg-layout"><?php include $rootPath.'components/sidebar.php'; ?><div class="mdg-main"><?php include $rootPath.'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-pen me-2 text-primary-mdg"></i>Edit Hutang/Piutang</h1><nav class="mdg-breadcrumb"><a href="index.php">Hutang/Piutang</a> / Edit #<?= $id ?></nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Edit Data Hutang/Piutang</span></div><div class="mdg-card-body">
    <form action="edit.php" method="POST"><?= csrfField() ?>
      <input type="hidden" name="Debt_ID" value="<?= $id ?>">
      <div class="mdg-form-group"><label class="form-label">Kontak <span class="text-danger">*</span></label>
        <select class="form-select" name="Contact_ID" required>
          <?= renderOptions($contactOptions, $sel_contact, '-- Pilih Kontak --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Pocket <span class="text-danger">*</span></label>
        <select class="form-select" name="Pocket_ID" required>
          <?= renderOptions($pocketOptions, $sel_pocket, '-- Pilih Pocket --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Kategori Hutang <span class="text-danger">*</span></label>
        <select class="form-select" name="Debt_Category" required>
          <?= renderOptions($subCategoryOptions, $sel_debt_cat, '-- Pilih Kategori Hutang --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Amount" value="<?= e(isset($_POST['Amount']) ? $_POST['Amount'] : $row['Amount']) ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label><input type="date" class="form-control" name="Due_Date" value="<?= e(isset($_POST['Due_Date']) ? $_POST['Due_Date'] : $row['Due_Date']) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Status <span class="text-danger">*</span></label>
        <?php $sel_status = isset($_POST['Status']) ? $_POST['Status'] : $row['Status']; ?>
        <select class="form-select" name="Status" required>
          <option value="unpaid"  <?= ($sel_status==='unpaid')  ?'selected':'' ?>>Belum Lunas</option>
          <option value="paid"    <?= ($sel_status==='paid')    ?'selected':'' ?>>Lunas</option>
          <option value="partial" <?= ($sel_status==='partial') ?'selected':'' ?>>Cicilan Aktif</option>
        </select>
      </div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="update" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan Perubahan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
