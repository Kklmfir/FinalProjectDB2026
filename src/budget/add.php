<?php include 'budget.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Budget Baru</title>
</head>
<body>
    <h2>Tambah Budget Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Category ID</td>
                <td><input type="number" name="Category_ID" required></td>
            </tr>
            <tr>
                <td>Batas Bulanan (Rp)</td>
                <td><input type="number" name="Monthly_Limit" step="0.01" required></td>
            </tr>
            <tr>
                <td>Tanggal Mulai</td>
                <td><input type="date" name="Start_Date" required></td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td><input type="date" name="End_Date" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Budget</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Category_ID    = intval($_POST['Category_ID']);
        $Monthly_Limit  = floatval($_POST['Monthly_Limit']);
        $Start_Date     = $_POST['Start_Date'];
        $End_Date       = $_POST['End_Date'];

        $sql = "INSERT INTO $table (Category_ID, Monthly_Limit, Start_Date, End_Date) 
                VALUES ($Category_ID, $Monthly_Limit, '$Start_Date', '$End_Date')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Budget berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>