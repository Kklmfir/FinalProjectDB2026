<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/budget.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle  = 'Budget';
$activePage = 'budget';
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
      <h1 class="page-title"><i class="fas fa-piggy-bank me-2 text-primary-mdg"></i>Budget</h1>
      <p class="page-subtitle">Kelola batas budget bulanan</p>
    </div>
    <a href="add.php" class="btn-mdg-primary"><i class="fas fa-plus"></i> Tambah Budget</a>
  </div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/alerts.php'; ?>
  <div class="mdg-table-wrapper">
    <div class="mdg-table-header"><h5><i class="fas fa-list me-2"></i>Data Budget</h5></div>
    <div class="table-responsive p-3">
      <table class="table mdg-table mdg-datatable">
        <thead><tr>
          <th>ID</th>
          <th>Category ID</th>
          <th>Batas Bulanan</th>
          <th>Tgl Mulai</th>
          <th>Tgl Selesai</th>
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
            <td><?= e($row['Budget_ID'] ?? '') ?></td>
            <td><?= e($row['Category_ID'] ?? '') ?></td>
            <td><?= e($row['Monthly_Limit'] ?? '') ?></td>
            <td><?= e($row['Start_Date'] ?? '') ?></td>
            <td><?= e($row['End_Date'] ?? '') ?></td>
            <td>
              <a href="edit.php?id=<?= (int)$row['Budget_ID'] ?>" class="btn-mdg-secondary" style="padding:.3rem .7rem;font-size:.78rem"><i class="fas fa-pen"></i> Edit</a>
              <a href="delete.php?id=<?= (int)$row['Budget_ID'] ?>" class="btn-mdg-danger" style="padding:.3rem .7rem;font-size:.78rem" onclick="return confirmDelete('Budget #<?= (int)$row['Budget_ID'] ?>')"><i class="fas fa-trash"></i> Hapus</a>
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
