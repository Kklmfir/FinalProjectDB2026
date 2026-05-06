<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/contact.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/counter_helper.php';
$pageTitle='Tambah Kontak'; $activePage='contact'; $rootPath='../../'; $alertError='';
$pdo = getDB();
if (isset($_POST['submit'])) {
    $Contact_Name  = sanitizeInput($_POST['Contact_Name']??'');
    $Phone_Number  = sanitizeInput($_POST['Phone_Number']??'');
    $Relation_Type = sanitizeInput($_POST['Relation_Type']??'');
    $errors = [];
    if (empty($Contact_Name))  $errors[] = 'Nama Kontak tidak boleh kosong.';
    if (empty($Phone_Number))  $errors[] = 'No. Telepon tidak boleh kosong.';
    if (empty($Relation_Type)) $errors[] = 'Jenis Hubungan tidak boleh kosong.';
    if (empty($errors)) {
        try {
            acquireSequentialIdAndInsert($pdo, 'contact', function(int $newId) use ($pdo, $Contact_Name, $Phone_Number, $Relation_Type) {
                $stmt = $pdo->prepare("INSERT INTO Contact (Contact_ID, Contact_Name, Phone_Number, Relation_Type) VALUES (?,?,?,?)");
                $stmt->execute([$newId, $Contact_Name, $Phone_Number, $Relation_Type]);
            });
            $_SESSION['flash_success']='Kontak berhasil ditambahkan!';
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
  <div class="page-header"><div><h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Kontak</h1><nav class="mdg-breadcrumb"><a href="index.php">Kontak</a> / Tambah</nav></div><a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a></div>
  <?php include $rootPath.'components/alerts.php'; ?>
  <div class="mdg-card" style="max-width:560px"><div class="mdg-card-header"><span class="mdg-card-title">Form Kontak Baru</span></div><div class="mdg-card-body">
    <form action="add.php" method="POST"><?= csrfField() ?>
      <div class="mdg-form-group"><label class="form-label">Nama Kontak <span class="text-danger">*</span></label><input type="text" class="form-control" name="Contact_Name" value="<?= e($_POST['Contact_Name']??'') ?>" required></div>
      <div class="mdg-form-group"><label class="form-label">No. Telepon <span class="text-danger">*</span></label><input type="text" class="form-control" name="Phone_Number" value="<?= e($_POST['Phone_Number']??'') ?>" placeholder="081234567890" required></div>
      <div class="mdg-form-group"><label class="form-label">Jenis Hubungan <span class="text-danger">*</span></label><input type="text" class="form-control" name="Relation_Type" value="<?= e($_POST['Relation_Type']??'') ?>" placeholder="contoh: Teman, Keluarga, Rekan Kerja" required></div>
      <div class="d-flex gap-2 mt-3"><button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn-mdg-secondary">Batal</a></div>
    </form>
  </div></div>
</main><?php include $rootPath.'components/footer.php'; ?></div></div>
