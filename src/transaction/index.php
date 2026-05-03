<?php 
include 'transaction.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Transactions</title>
</head>
<body>
    <h2>Data Transactions (Semua Transaksi)</h2>
    
    <p><a href="add.php">+ Tambah Transaction Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Transaction ID</th>
            <th>Pocket ID</th>
            <th>Category ID</th>
            <th>Jumlah</th>
            <th>Tanggal Transaksi</th>
            <th>Deskripsi</th>
            <th>Warning</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key DESC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $warning = $row['Warning_Status'] ? '<span style="color:red;">⚠️ Ya</span>' : 'Tidak';
            echo "<tr>";
            echo "<td>" . $row['Transaction_ID'] . "</td>";
            echo "<td>" . $row['Pocket_ID'] . "</td>";
            echo "<td>" . $row['Category_ID'] . "</td>";
            echo "<td>Rp " . number_format($row['Amount'], 0, ',', '.') . "</td>";
            echo "<td>" . $row['System_Log'] . "</td>";
            echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
            echo "<td>" . $warning . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Transaction_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Transaction_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus transaksi ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>