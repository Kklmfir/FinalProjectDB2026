<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/goal.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';
$pageTitle  = 'Goal';
$activePage = 'goal';
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
      <h1 class="page-title"><i class="fas fa-bullseye me-2 text-primary-mdg"></i>Goal</h1>
      <p class="page-subtitle">Kelola target tabungan dan goals</p>
    </div>
    <a href="add.php" class="btn-mdg-primary"><i class="fas fa-plus"></i> Tambah Goal</a>
  </div>
  <?php
require_once __DIR__ . '/../../config/bootstrap.php'; include $rootPath . 'components/alerts.php'; ?>
  <div class="mdg-table-wrapper">
    <div class="mdg-table-header"><h5><i class="fas fa-list me-2"></i>Data Goal</h5></div>
    <div class="table-responsive p-3">
      <table class="table mdg-table mdg-datatable">
        <thead><tr>
          <th>ID</th>
          <th>Nama Goal</th>
          <th>Target</th>
          <th>Saat Ini</th>
          <th>Deadline</th>
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
            <td><?= e($row['Goal_ID'] ?? '') ?></td>
            <td><?= e($row['Goal_Name'] ?? '') ?></td>
            <td><?= e($row['Target_Amount'] ?? '') ?></td>
            <td><?= e($row['Current_Amount'] ?? '') ?></td>
            <td><?= e($row['Deadline'] ?? '') ?></td>
            <td>
              <a href="edit.php?id=<?= (int)$row['Goal_ID'] ?>" class="btn-mdg-secondary" style="padding:.3rem .7rem;font-size:.78rem"><i class="fas fa-pen"></i> Edit</a>
              <a href="delete.php?id=<?= (int)$row['Goal_ID'] ?>" class="btn-mdg-danger" style="padding:.3rem .7rem;font-size:.78rem" onclick="return confirmDelete('Goal #<?= (int)$row['Goal_ID'] ?>')"><i class="fas fa-trash"></i> Hapus</a>
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
