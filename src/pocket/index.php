<?php
/**
 * src/pocket/index.php — Daftar Pocket
 */
require_once __DIR__ . '/pocket.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../helpers/security.php';

$pageTitle  = 'Pocket';
$activePage = 'pocket';
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
            <h1 class="page-title"><i class="fas fa-wallet me-2 text-primary-mdg"></i>Pocket (Kantong)</h1>
            <p class="page-subtitle">Kelola semua kantong keuangan Anda</p>
        </div>
        <a href="add.php" class="btn-mdg-primary">
            <i class="fas fa-plus"></i> Tambah Pocket
        </a>
    </div>

    <?php include $rootPath . 'components/alerts.php'; ?>

    <div class="mdg-table-wrapper">
        <div class="mdg-table-header">
            <h5><i class="fas fa-list me-2"></i>Data Pocket</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table mdg-table mdg-datatable" id="pocketTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kantong</th>
                        <th>Saldo</th>
                        <th>Maks. Budget</th>
                        <th>Tgl Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql    = "SELECT * FROM $table ORDER BY $primary_key ASC";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)):
                ?>
                    <tr>
                        <td><?= e($row['Pocket_ID']) ?></td>
                        <td><strong><?= e($row['Pocket_Name']) ?></strong></td>
                        <td><?= formatRupiah((float)$row['Balance']) ?></td>
                        <td><?= formatRupiah((float)$row['Max_Budget']) ?></td>
                        <td><?= formatDateShort($row['Created_Date'] ?? '') ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$row['Pocket_ID'] ?>"
                               class="btn-mdg-secondary" style="padding:.3rem .7rem;font-size:.78rem">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <a href="delete.php?id=<?= (int)$row['Pocket_ID'] ?>"
                               class="btn-mdg-danger" style="padding:.3rem .7rem;font-size:.78rem"
                               onclick="return confirmDelete('Pocket #<?= (int)$row['Pocket_ID'] ?>')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
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