<?php 
include 'db.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDG - Data Pocket</title>
</head>
<body>
    <h2>Data Pocket (Kantong Keuangan)</h2>
    
    <p><a href="add.php">+ Tambah Pocket Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Pocket ID</th>
            <th>Nama Kantong</th>
            <th>Saldo</th>
            <th>Maksimal Budget</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Pocket_ID'] . "</td>";
            echo "<td>" . htmlspecialchars($row['Pocket_Name']) . "</td>";
            echo "<td>Rp " . number_format($row['Balance'], 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($row['Max_Budget'], 0, ',', '.') . "</td>";
            echo "<td>" . $row['Created_Date'] . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Pocket_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Pocket_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus pocket ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>