<?php 
include 'db_category.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Category</title>
</head>
<body>
    <h2>Data Category</h2>
    
    <p><a href="add.php">+ Tambah Category Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Category ID</th>
            <th>Nama Category</th>
            <th>Tipe (Income/Expense)</th>
            <th>Icon Code</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Category_ID'] . "</td>";
            echo "<td>" . htmlspecialchars($row['Category_Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Category_Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Icon_Code']) . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Category_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Category_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus category ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>