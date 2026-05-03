<?php 
include 'db_budget.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Budget</title>
</head>
<body>
    <h2>Data Budget</h2>
    
    <p><a href="add.php">+ Tambah Budget Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Budget ID</th>
            <th>Category ID</th>
            <th>Batas Bulanan</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Budget_ID'] . "</td>";
            echo "<td>" . $row['Category_ID'] . "</td>";
            echo "<td>Rp " . number_format($row['Monthly_Limit'], 0, ',', '.') . "</td>";
            echo "<td>" . $row['Start_Date'] . "</td>";
            echo "<td>" . $row['End_Date'] . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Budget_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Budget_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus budget ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>