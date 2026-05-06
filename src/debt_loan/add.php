<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/debt_loan.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
require_once __DIR__ . '/../../helpers/counter_helper.php';
$pageTitle='Tambah Hutang/Piutang'; $activePage='debt_loan'; $rootPath='../../'; $alertError='';
$pdo = getDB();
$contactOptions     = getOptionsWithFormat($pdo, 'Contact',      'Contact_ID',      'Contact_Name');
$pocketOptions      = getOptionsWithFormat($pdo, 'Pocket',       'Pocket_ID',       'Pocket_Name');
$subCategoryOptions = getOptionsWithFormat($pdo, 'Sub_Category', 'Sub_Category_ID', 'Sub_Name');
if (isset($_POST['submit'])) {
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
            acquireSequentialIdAndInsert($pdo, 'debt_loan', function(int $newId) use ($pdo, $Contact_ID, $Pocket_ID, $Debt_Category, $Amount, $Due_Date, $Status) {
                $stmt = $pdo->prepare("INSERT INTO Debt_Loan (Debt_ID, Contact_ID, Pocket_ID, Debt_Category, Amount, Due_Date, Status) VALUES (?,?,?,?,?,?,?)");
                $stmt->execute([$newId, $Contact_ID, $Pocket_ID, $Debt_Category, $Amount, $Due_Date, $Status]);
            });
            $_SESSION['flash_success']='Hutang/Piutang berhasil ditambahkan!';
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
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Hutang/Piutang</h1><nav class="mdg-breadcrumb"><a href="index.php">Hutang/Piutang</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Hutang/Piutang Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Kontak <span class="text-danger">*</span></label>
        <select class="form-select" name="Contact_ID" required>
          <?= renderOptions($contactOptions, $_POST['Contact_ID']??0, '-- Pilih Kontak --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Pocket <span class="text-danger">*</span></label>
        <select class="form-select" name="Pocket_ID" required>
          <?= renderOptions($pocketOptions, $_POST['Pocket_ID']??0, '-- Pilih Pocket --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Kategori Hutang <span class="text-danger">*</span></label>
        <select class="form-select" name="Debt_Category" required>
          <?= renderOptions($subCategoryOptions, $_POST['Debt_Category']??0, '-- Pilih Kategori Hutang --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Amount" value="<?= e($_POST['Amount']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label><input type="date" class="form-control" name="Due_Date" value="<?= e($_POST['Due_Date']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select" name="Status" required>
          <option value="unpaid"  <?= (($_POST['Status']??'unpaid')==='unpaid')  ?'selected':'' ?>>Belum Lunas</option>
          <option value="paid"    <?= (($_POST['Status']??'')==='paid')    ?'selected':'' ?>>Lunas</option>
          <option value="partial" <?= (($_POST['Status']??'')==='partial') ?'selected':'' ?>>Cicilan Aktif</option>
        </select>
      </div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
