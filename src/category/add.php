<?php
/**
 * src/category/add.php — Tambah Kategori Baru
 * MySQLi with prepared statements
 */
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/category.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';

$pageTitle  = 'Tambah Kategori';
$activePage = 'category';
$rootPath   = '../../';
$alertError = '';

if (isset($_POST['submit'])) {
    // Validate input
    $Category_Name = sanitizeInput($_POST['Category_Name'] ?? '');
    $Category_Type = sanitizeInput($_POST['Category_Type'] ?? '');
    $Icon_Code     = sanitizeInput($_POST['Icon_Code'] ?? '');
    
    $errors = [];
    if (empty($Category_Name)) $errors[] = 'Nama kategori tidak boleh kosong.';
    if (empty($Category_Type)) $errors[] = 'Tipe kategori harus dipilih.';
    if (empty($Icon_Code)) $errors[] = 'Icon code tidak boleh kosong.';
    
    if (empty($errors)) {
        try {
            // Prepare statement
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO $table (Category_Name, Category_Type, Icon_Code) VALUES (?, ?, ?)"
            );
            
            if (!$stmt) {
                throw new Exception('Database prepare error: ' . mysqli_error($conn));
            }
            
            // Bind parameters (all strings)
            mysqli_stmt_bind_param(
                $stmt,
                'sss',
                $Category_Name,
                $Category_Type,
                $Icon_Code
            );
            
            // Execute
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Database execute error: ' . mysqli_error($conn));
            }
            
            mysqli_stmt_close($stmt);
            
            $_SESSION['flash_success'] = 'Kategori berhasil ditambahkan!';
            header('Location: index.php');
            exit;
            
        } catch (Throwable $e) {
            error_log('Category add error: ' . $e->getMessage());
            $alertError = 'Gagal menyimpan data. Silakan coba lagi.';
        }
    } else {
        $alertError = implode(' ', $errors);
    }
}

include $rootPath . 'components/header.php';
?>
<div class="mdg-layout">
<?php include $rootPath . 'components/sidebar.php'; ?>
<div class="mdg-main">
<?php include $rootPath . 'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="fas fa-plus-circle me-2 text-primary-mdg"></i>Tambah Kategori</h1>
            <nav class="mdg-breadcrumb"><a href="index.php">Kategori</a> / Tambah</nav>
        </div>
        <a href="index.php" class="btn-mdg-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    
    <?php include $rootPath . 'components/alerts.php'; ?>

    <div class="mdg-card" style="max-width:560px">
        <div class="mdg-card-header">
            <span class="mdg-card-title">Form Kategori Baru</span>
        </div>
        <div class="mdg-card-body">
            <form action="add.php" method="POST">
                <?= csrfField() ?>
                <div class="mdg-form-group">
                    <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="Category_Name"
                           value="<?= e($_POST['Category_Name'] ?? '') ?>" required>
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Tipe Kategori <span class="text-danger">*</span></label>
                    <select class="form-select" name="Category_Type" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="Income" <?= (($_POST['Category_Type'] ?? '') === 'Income') ? 'selected' : '' ?>>Income (Pemasukan)</option>
                        <option value="Expense" <?= (($_POST['Category_Type'] ?? '') === 'Expense') ? 'selected' : '' ?>>Expense (Pengeluaran)</option>
                    </select>
                </div>
                <div class="mdg-form-group">
                    <label class="form-label">Icon Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="Icon_Code"
                           value="<?= e($_POST['Icon_Code'] ?? '') ?>"
                           placeholder="contoh: ic_delivery_bot" required>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" name="submit" class="btn-mdg-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="index.php" class="btn-mdg-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</main>
<?php include $rootPath . 'components/footer.php'; ?>
</div>
</div>
