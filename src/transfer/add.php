<?php include 'db_transfer.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Transfer Baru</title>
</head>
<body>
    <h2>Tambah Transfer Antar Kantong</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Source Pocket ID</td>
                <td><input type="number" name="Source_Pocket_ID" required></td>
            </tr>
            <tr>
                <td>Target Pocket ID</td>
                <td><input type="number" name="Target_Pocket_ID" required></td>
            </tr>
            <tr>
                <td>Jumlah Transfer (Rp)</td>
                <td><input type="number" name="Transfer_Amount" step="0.01" required></td>
            </tr>
            <tr>
                <td>Tanggal Transfer</td>
                <td><input type="datetime-local" name="Transfer_Date" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Transfer</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Source_Pocket_ID = intval($_POST['Source_Pocket_ID']);
        $Target_Pocket_ID = intval($_POST['Target_Pocket_ID']);
        $Transfer_Amount  = floatval($_POST['Transfer_Amount']);
        $Transfer_Date    = $_POST['Transfer_Date'];

        $sql = "INSERT INTO $table (Source_Pocket_ID, Target_Pocket_ID, Transfer_Amount, Transfer_Date) 
                VALUES ($Source_Pocket_ID, $Target_Pocket_ID, $Transfer_Amount, '$Transfer_Date')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Transfer berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>