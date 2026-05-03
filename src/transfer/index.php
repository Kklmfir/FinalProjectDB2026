<?php 
include 'db_transfer.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Transfer</title>
</head>
<body>
    <h2>Data Transfer Antar Kantong</h2>
    
    <p><a href="add.php">+ Tambah Transfer Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Transfer ID</th>
            <th>Source Pocket ID</th>
            <th>Target Pocket ID</th>
            <th>Jumlah Transfer</th>
            <th>Tanggal Transfer</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key DESC";   // DESC agar yang terbaru di atas
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Transfer_ID'] . "</td>";
            echo "<td>" . $row['Source_Pocket_ID'] . "</td>";
            echo "<td>" . $row['Target_Pocket_ID'] . "</td>";
            echo "<td>Rp " . number_format($row['Transfer_Amount'], 0, ',', '.') . "</td>";
            echo "<td>" . $row['Transfer_Date'] . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Transfer_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Transfer_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus transfer ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>