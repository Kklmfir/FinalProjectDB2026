<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/debt_loan.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle  = 'Hutang/Piutang';
$activePage = 'debt_loan';
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
      <h1 class="page-title"><i class="fas fa-hand-holding-dollar me-2 text-primary-mdg"></i>Hutang/Piutang</h1>
      <p class="page-subtitle">Kelola hutang dan piutang</p>
    </div>
    <a href="add.php" class="btn-mdg-primary"><i class="fas fa-plus"></i> Tambah Hutang/Piutang</a>
  </div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/alerts.php'; ?>
  <div class="mdg-table-wrapper">
    <div class="mdg-table-header"><h5><i class="fas fa-list me-2"></i>Data Hutang/Piutang</h5></div>
    <div class="table-responsive p-3">
      <table class="table mdg-table mdg-datatable">
        <thead><tr>
          <th>ID</th>
          <th>Contact ID</th>
          <th>Pocket ID</th>
          <th>Jumlah</th>
          <th>Jatuh Tempo</th>
          <th>Status</th>
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
            <td><?= e($row['Debt_ID'] ?? '') ?></td>
            <td><?= e($row['Contact_ID'] ?? '') ?></td>
            <td><?= e($row['Pocket_ID'] ?? '') ?></td>
            <td><?= e($row['Amount'] ?? '') ?></td>
            <td><?= e($row['Due_Date'] ?? '') ?></td>
            <td><?= e($row['Status'] ?? '') ?></td>
            <td>
              <a href="edit.php?id=<?= (int)$row['Debt_ID'] ?>" class="btn-mdg-secondary" style="padding:.3rem .7rem;font-size:.78rem"><i class="fas fa-pen"></i> Edit</a>
              <a href="delete.php?id=<?= (int)$row['Debt_ID'] ?>" class="btn-mdg-danger" style="padding:.3rem .7rem;font-size:.78rem" onclick="return confirmDelete('Hutang/Piutang #<?= (int)$row['Debt_ID'] ?>')"><i class="fas fa-trash"></i> Hapus</a>
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
