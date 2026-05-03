<?php
require_once __DIR__ . '/sub_category.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle  = 'Sub Kategori';
$activePage = 'sub_category';
$rootPath   = '../../';
include $rootPath . 'components/header.php';
?>
<div class="mdg-layout">
<?php include $rootPath . 'components/sidebar.php'; ?>
<div class="mdg-main">
<?php include $rootPath . 'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header">
    <div>
      <h1 class="page-title"><i class="fas fa-tag me-2 text-primary-mdg"></i>Sub Kategori</h1>
      <p class="page-subtitle">Kelola sub kategori transaksi</p>
    </div>
    <a href="add.php" class="btn-mdg-primary"><i class="fas fa-plus"></i> Tambah Sub Kategori</a>
  </div>
  <?php include $rootPath . 'components/alerts.php'; ?>
  <div class="mdg-table-wrapper">
    <div class="mdg-table-header"><h5><i class="fas fa-list me-2"></i>Data Sub Kategori</h5></div>
    <div class="table-responsive p-3">
      <table class="table mdg-table mdg-datatable">
        <thead><tr>
          <th>ID</th>
          <th>Nama Sub Kategori</th>
          <th>Category ID</th>
          <th>Catatan</th>
          <th>Aksi</th>
        </tr></thead>
        <tbody>
        <?php
        $sql    = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)):
        ?>
          <tr>
            <td><?= e($row['Sub_Category_ID'] ?? '') ?></td>
            <td><?= e($row['Sub_Name'] ?? '') ?></td>
            <td><?= e($row['Category_ID'] ?? '') ?></td>
            <td><?= e($row['Notes'] ?? '') ?></td>
            <td>
              <a href="edit.php?id=<?= (int)$row['Sub_Category_ID'] ?>" class="btn-mdg-secondary" style="padding:.3rem .7rem;font-size:.78rem"><i class="fas fa-pen"></i> Edit</a>
              <a href="delete.php?id=<?= (int)$row['Sub_Category_ID'] ?>" class="btn-mdg-danger" style="padding:.3rem .7rem;font-size:.78rem" onclick="return confirmDelete('Sub Kategori #<?= (int)$row['Sub_Category_ID'] ?>')"><i class="fas fa-trash"></i> Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php include $rootPath . 'components/footer.php'; ?>
</div>
</div>
