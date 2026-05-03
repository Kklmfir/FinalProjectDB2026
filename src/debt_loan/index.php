<?php 
include 'debt_loan.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Debt/Loan</title>
</head>
<body>
    <h2>Data Debt / Loan (Hutang & Piutang)</h2>
    
    <p><a href="add.php">+ Tambah Debt/Loan Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Debt ID</th>
            <th>Contact ID</th>
            <th>Pocket ID</th>
            <th>Jumlah</th>
            <th>Tanggal Jatuh Tempo</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key DESC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Debt_ID'] . "</td>";
            echo "<td>" . $row['Contact_ID'] . "</td>";
            echo "<td>" . $row['Pocket_ID'] . "</td>";
            echo "<td>Rp " . number_format($row['Amount'], 0, ',', '.') . "</td>";
            echo "<td>" . $row['Due_Date'] . "</td>";
            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Debt_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Debt_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>