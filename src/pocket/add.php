<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pocket Baru</title>
</head>
<body>
    <h2>Tambah Pocket Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Nama Kantong</td>
                <td><input type="text" name="Pocket_Name" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Saldo Awal</td>
                <td><input type="number" name="Balance" step="0.01" value="0" required></td>
            </tr>
            <tr>
                <td>Maksimal Budget</td>
                <td><input type="number" name="Max_Budget" step="0.01" value="0"></td>
            </tr>
            <tr>
                <td>Tanggal Dibuat</td>
                <td><input type="datetime-local" name="Created_Date" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Pocket Baru</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Pocket_Name  = mysqli_real_escape_string($conn, $_POST['Pocket_Name']);
        $Balance      = floatval($_POST['Balance']);
        $Max_Budget   = floatval($_POST['Max_Budget']);
        $Created_Date = $_POST['Created_Date'];

        // Pocket_ID dihapus karena sudah AUTO_INCREMENT
        $sql = "INSERT INTO $table (Pocket_Name, Balance, Max_Budget, Created_Date) 
                VALUES ('$Pocket_Name', $Balance, $Max_Budget, '$Created_Date')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>
                    alert('Pocket baru berhasil ditambahkan!');
                    window.location='index.php';
                  </script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>