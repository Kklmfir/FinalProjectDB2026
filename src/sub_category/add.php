<?php include 'db_subcategory.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Sub Category Baru</title>
</head>
<body>
    <h2>Tambah Sub Category Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Category ID</td>
                <td><input type="number" name="Category_ID" required></td>
            </tr>
            <tr>
                <td>Nama Sub Category</td>
                <td><input type="text" name="Sub_Name" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Notes / Keterangan</td>
                <td><textarea name="Notes" rows="4" cols="50"></textarea></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Sub Category</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Category_ID = intval($_POST['Category_ID']);
        $Sub_Name    = mysqli_real_escape_string($conn, $_POST['Sub_Name']);
        $Notes       = mysqli_real_escape_string($conn, $_POST['Notes']);

        $sql = "INSERT INTO $table (Category_ID, Sub_Name, Notes) 
                VALUES ($Category_ID, '$Sub_Name', '$Notes')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Sub Category berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>