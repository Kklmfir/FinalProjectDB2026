<?php 
include 'db_subcategory.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Sub Category</title>
</head>
<body>
    <h2>Data Sub Category</h2>
    
    <p><a href="add.php">+ Tambah Sub Category Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Sub Category ID</th>
            <th>Category ID</th>
            <th>Nama Sub Category</th>
            <th>Notes</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Sub_Category_ID'] . "</td>";
            echo "<td>" . $row['Category_ID'] . "</td>";
            echo "<td>" . htmlspecialchars($row['Sub_Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Notes']) . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Sub_Category_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Sub_Category_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus sub category ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>