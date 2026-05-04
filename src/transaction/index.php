<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transaction.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle  = 'Transaksi';
$activePage = 'transaction';
$rootPath   = '../../';
include $rootPath . 'components/header.php';
?>
<div class="mdg-layout">
<?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/sidebar.php'; ?>
<div class="mdg-main">
<?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/navbar.php'; ?>
<main class="mdg-content animate-fade-in">
  <div class="page-header">
    <div>
      <h1 class="page-title"><i class="fas fa-exchange-alt me-2 text-primary-mdg"></i>Transaksi</h1>
      <p class="page-subtitle">Catat semua transaksi keuangan</p>
    </div>
    <a href="add.php" class="btn-mdg-primary"><i class="fas fa-plus"></i> Tambah Transaksi</a>
  </div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/alerts.php'; ?>
  <div class="mdg-table-wrapper">
    <div class="mdg-table-header"><h5><i class="fas fa-list me-2"></i>Data Transaksi</h5></div>
    <div class="table-responsive p-3">
      <table class="table mdg-table mdg-datatable">
        <thead><tr>
          <th>ID</th>
          <th>Pocket ID</th>
          <th>Category ID</th>
          <th>Jumlah</th>
          <th>Deskripsi</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr></thead>
        <tbody>
        <?php
require_once __DIR__ . '/../../config/bootstrap.php';
        $sql    = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)):
        ?>
          <tr>
            <td><?= e($row['Transaction_ID'] ?? '') ?></td>
            <td><?= e($row['Pocket_ID'] ?? '') ?></td>
            <td><?= e($row['Category_ID'] ?? '') ?></td>
            <td><?= e($row['Amount'] ?? '') ?></td>
            <td><?= e($row['Description'] ?? '') ?></td>
            <td><?= e($row['System_Log'] ?? '') ?></td>
            <td>
              <a href="edit.php?id=<?= (int)$row['Transaction_ID'] ?>" class="btn-mdg-secondary" style="padding:.3rem .7rem;font-size:.78rem"><i class="fas fa-pen"></i> Edit</a>
              <a href="delete.php?id=<?= (int)$row['Transaction_ID'] ?>" class="btn-mdg-danger" style="padding:.3rem .7rem;font-size:.78rem" onclick="return confirmDelete('Transaksi #<?= (int)$row['Transaction_ID'] ?>')"><i class="fas fa-trash"></i> Hapus</a>
            </td>
          </tr>
        <?php
require_once __DIR__ . '/../../config/bootstrap.php'; endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/footer.php'; ?>
</div>
</div>
