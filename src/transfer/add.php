<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transfer.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/dropdown_helper.php';
require_once __DIR__ . '/../../helpers/counter_helper.php';
$pageTitle='Tambah Transfer'; $activePage='transfer'; $rootPath='../../'; $alertError='';
$pdo = getDB();
$pocketOptions = getOptionsWithFormat($pdo, 'Pocket', 'Pocket_ID', 'Pocket_Name');
if (isset($_POST['submit'])) {
    $Source_Pocket_ID = sanitizeInt($_POST['Source_Pocket_ID']??0);
    $Target_Pocket_ID = sanitizeInt($_POST['Target_Pocket_ID']??0);
    $Transfer_Amount  = sanitizeFloat($_POST['Transfer_Amount']??0);
    $Transfer_Date    = sanitizeInput($_POST['Transfer_Date']??'');
    $errors = [];
    if ($Source_Pocket_ID <= 0) $errors[] = 'Pocket asal harus dipilih.';
    if ($Target_Pocket_ID <= 0) $errors[] = 'Pocket tujuan harus dipilih.';
    if ($Source_Pocket_ID > 0 && $Target_Pocket_ID > 0 && $Source_Pocket_ID === $Target_Pocket_ID)
        $errors[] = 'Source dan Target tidak boleh sama.';
    if ($Transfer_Amount <= 0)  $errors[] = 'Jumlah Transfer harus lebih dari 0.';
    if (empty($Transfer_Date))  $errors[] = 'Tanggal Transfer tidak boleh kosong.';
    if (empty($errors)) {
        try {
            acquireSequentialIdAndInsert($pdo, 'transfer', function(int $newId) use ($pdo, $Source_Pocket_ID, $Target_Pocket_ID, $Transfer_Amount, $Transfer_Date) {
                $stmt = $pdo->prepare("INSERT INTO Transfer (Transfer_ID, Source_Pocket_ID, Target_Pocket_ID, Transfer_Amount, Transfer_Date) VALUES (?,?,?,?,?)");
                $stmt->execute([$newId, $Source_Pocket_ID, $Target_Pocket_ID, $Transfer_Amount, $Transfer_Date]);
            });
            $_SESSION['flash_success']='Transfer berhasil dicatat!';
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
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Transfer</h1><nav class="mdg-breadcrumb"><a href="index.php">Transfer</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Transfer Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Pocket Asal (Dari) <span class="text-danger">*</span></label>
        <select class="form-select" name="Source_Pocket_ID" required>
          <?= renderOptions($pocketOptions, $_POST['Source_Pocket_ID']??0, '-- Pilih Pocket Asal --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Pocket Tujuan (Ke) <span class="text-danger">*</span></label>
        <select class="form-select" name="Target_Pocket_ID" required>
          <?= renderOptions($pocketOptions, $_POST['Target_Pocket_ID']??0, '-- Pilih Pocket Tujuan --') ?>
        </select>
      </div>
      <div class="mdg-form-group"><label class="form-label">Jumlah Transfer (Rp) <span class="text-danger">*</span></label><input type="number" class="form-control" name="Transfer_Amount" value="<?= e($_POST['Transfer_Amount']??'') ?>" step="0.01" min="0" required></div>
      <div class="mdg-form-group"><label class="form-label">Tanggal Transfer <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" name="Transfer_Date" value="<?= e($_POST['Transfer_Date']??date('Y-m-d\TH:i')) ?>" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
