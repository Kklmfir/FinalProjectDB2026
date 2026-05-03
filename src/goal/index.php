<?php 
include 'db_goal.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Goal</title>
</head>
<body>
    <h2>Data Goal (Target Tabungan)</h2>
    
    <p><a href="add.php">+ Tambah Goal Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Goal ID</th>
            <th>Pocket ID</th>
            <th>Nama Goal</th>
            <th>Target Amount</th>
            <th>Deadline</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Goal_ID'] . "</td>";
            echo "<td>" . $row['Pocket_ID'] . "</td>";
            echo "<td>" . htmlspecialchars($row['Goal_Name']) . "</td>";
            echo "<td>Rp " . number_format($row['Target_Amount'], 0, ',', '.') . "</td>";
            echo "<td>" . $row['Deadline_Date'] . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Goal_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Goal_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus goal ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>