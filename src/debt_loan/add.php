<?php include 'debt_loan.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Debt/Loan Baru</title>
</head>
<body>
    <h2>Tambah Debt / Loan Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Contact ID</td>
                <td><input type="number" name="Contact_ID" required></td>
            </tr>
            <tr>
                <td>Pocket ID</td>
                <td><input type="number" name="Pocket_ID" required></td>
            </tr>
            <tr>
                <td>Jumlah (Rp)</td>
                <td><input type="number" name="Amount" step="0.01" required></td>
            </tr>
            <tr>
                <td>Tanggal Jatuh Tempo</td>
                <td><input type="date" name="Due_Date" required></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <select name="Status" required>
                        <option value="Belum Lunas">Belum Lunas</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Cicilan Aktif">Cicilan Aktif</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Debt/Loan</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Contact_ID = intval($_POST['Contact_ID']);
        $Pocket_ID  = intval($_POST['Pocket_ID']);
        $Amount     = floatval($_POST['Amount']);
        $Due_Date   = $_POST['Due_Date'];
        $Status     = mysqli_real_escape_string($conn, $_POST['Status']);

        $sql = "INSERT INTO $table (Contact_ID, Pocket_ID, Amount, Due_Date, Status) 
                VALUES ($Contact_ID, $Pocket_ID, $Amount, '$Due_Date', '$Status')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Debt/Loan berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>