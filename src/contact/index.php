<?php 
include 'contact.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MDG - Data Contact</title>
</head>
<body>
    <h2>Data Contact (Daftar Kontak)</h2>
    
    <p><a href="add.php">+ Tambah Contact Baru</a></p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th>Contact ID</th>
            <th>Nama Kontak</th>
            <th>Nomor Telepon</th>
            <th>Jenis Hubungan</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM $table ORDER BY $primary_key ASC";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Contact_ID'] . "</td>";
            echo "<td>" . htmlspecialchars($row['Contact_Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Phone_Number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Relation_Type']) . "</td>";
            echo "<td>
                    <a href='edit.php?id=" . $row['Contact_ID'] . "'>Edit</a> | 
                    <a href='delete.php?id=" . $row['Contact_ID'] . "' 
                       onclick=\"return confirm('Yakin ingin menghapus contact ini?')\">Hapus</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <br>
    <a href="index.php">Refresh</a>
</body>
</html>