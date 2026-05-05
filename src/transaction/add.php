<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transaction.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
$pageTitle='Tambah Transaksi'; $activePage='transaction'; $rootPath='../../'; $alertError='';
$pdo = getDB();
$pocketOptions   = getOptionsWithFormat($pdo, 'Pocket',   'Pocket_ID',   'Pocket_Name');
$categoryOptions = getOptionsWithFormat($pdo, 'Category', 'Category_ID', 'Category_Name');
if (isset($_POST['submit'])) {
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
            $stmt = $pdo->prepare("INSERT INTO $table (Pocket_ID, Category_ID, Amount, System_Log, Description, Warning_Status) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$Pocket_ID, $Category_ID, $Amount, $System_Log, $Description, $Warning_Status]);
            $_SESSION['flash_success']='Transaksi berhasil ditambahkan!';
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
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Transaksi</h1><nav class="mdg-breadcrumb"><a href="index.php">Transaksi</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Transaksi Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Pocket <span class="text-danger">*</span></label>
        <select class="form-select" name="Pocket_ID" required>
          <?= renderOptions($pocketOptions, $_POST['Pocket_ID']??0, '-- Pilih Pocket --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Kategori <span class="text-danger">*</span></label>
        <select class="form-select" name="Category_ID" required>
          <?= renderOptions($categoryOptions, $_POST['Category_ID']??0, '-- Pilih Kategori --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Amount" value="<?= e($_POST['Amount']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal &amp; Waktu <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" name="System_Log" value="<?= e($_POST['System_Log']??date('Y-m-d\TH:i')) ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">Deskripsi</label><textarea class="form-control" name="Description" rows="3"><?= e($_POST['Description']??'') ?></textarea></div>
      <div class="mdg-form-group"><label class="form-label">Warning Status</label>
        <select class="form-select" name="Warning_Status">
          <option value="0" <?= (($_POST['Warning_Status']??0)==0)?'selected':'' ?>>Normal</option>
          <option value="1" <?= (($_POST['Warning_Status']??0)==1)?'selected':'' ?>>Warning (Hampir Melewati Budget)</option>
        </select>
      </div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
