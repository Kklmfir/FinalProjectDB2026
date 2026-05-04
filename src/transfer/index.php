<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/transfer.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle  = 'Transfer';
$activePage = 'transfer';
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
      <h1 class="page-title"><i class="fas fa-arrow-right-arrow-left me-2 text-primary-mdg"></i>Transfer</h1>
      <p class="page-subtitle">Catat transfer antar pocket</p>
    </div>
    <a href="add.php" class="btn-mdg-primary"><i class="fas fa-plus"></i> Tambah Transfer</a>
  </div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/alerts.php'; ?>
  <div class="mdg-table-wrapper">
    <div class="mdg-table-header"><h5><i class="fas fa-list me-2"></i>Data Transfer</h5></div>
    <div class="table-responsive p-3">
      <table class="table mdg-table mdg-datatable">
        <thead><tr>
          <th>ID</th>
          <th>Source Pocket</th>
          <th>Target Pocket</th>
          <th>Jumlah</th>
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
            <td><?= e($row['Transfer_ID'] ?? '') ?></td>
            <td><?= e($row['Source_Pocket_ID'] ?? '') ?></td>
            <td><?= e($row['Target_Pocket_ID'] ?? '') ?></td>
            <td><?= e($row['Transfer_Amount'] ?? '') ?></td>
            <td><?= e($row['Transfer_Date'] ?? '') ?></td>
            <td>
              <a href="edit.php?id=<?= (int)$row['Transfer_ID'] ?>" class="btn-mdg-secondary" style="padding:.3rem .7rem;font-size:.78rem"><i class="fas fa-pen"></i> Edit</a>
              <a href="delete.php?id=<?= (int)$row['Transfer_ID'] ?>" class="btn-mdg-danger" style="padding:.3rem .7rem;font-size:.78rem" onclick="return confirmDelete('Transfer #<?= (int)$row['Transfer_ID'] ?>')"><i class="fas fa-trash"></i> Hapus</a>
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
