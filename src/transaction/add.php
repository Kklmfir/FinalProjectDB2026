<?php include 'db_transaction.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Transaction Baru</title>
</head>
<body>
    <h2>Tambah Transaction Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Pocket ID</td>
                <td><input type="number" name="Pocket_ID" required></td>
            </tr>
            <tr>
                <td>Category ID</td>
                <td><input type="number" name="Category_ID" required></td>
            </tr>
            <tr>
                <td>Jumlah (Rp)</td>
                <td><input type="number" name="Amount" step="0.01" required></td>
            </tr>
            <tr>
                <td>Tanggal & Waktu</td>
                <td><input type="datetime-local" name="System_Log" required></td>
            </tr>
            <tr>
                <td>Deskripsi</td>
                <td><textarea name="Description" rows="3" cols="50" required></textarea></td>
            </tr>
            <tr>
                <td>Warning Status</td>
                <td>
                    <select name="Warning_Status">
                        <option value="0">Tidak</option>
                        <option value="1">Ya (Warning)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Transaction</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Pocket_ID      = intval($_POST['Pocket_ID']);
        $Category_ID    = intval($_POST['Category_ID']);
        $Amount         = floatval($_POST['Amount']);
        $System_Log     = $_POST['System_Log'];
        $Description    = mysqli_real_escape_string($conn, $_POST['Description']);
        $Warning_Status = intval($_POST['Warning_Status']);

        $sql = "INSERT INTO $table (Pocket_ID, Category_ID, Amount, System_Log, Description, Warning_Status) 
                VALUES ($Pocket_ID, $Category_ID, $Amount, '$System_Log', '$Description', $Warning_Status)";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Transaction berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>