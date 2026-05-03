<?php include 'category.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Category Baru</title>
</head>
<body>
    <h2>Tambah Category Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Nama Category</td>
                <td><input type="text" name="Category_Name" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Tipe Category</td>
                <td>
                    <select name="Category_Type" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="Income">Income (Penghasilan)</option>
                        <option value="Expense">Expense (Pengeluaran)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Icon Code</td>
                <td><input type="text" name="Icon_Code" required placeholder="contoh: ic_delivery_bot"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Category</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Category_Name = mysqli_real_escape_string($conn, $_POST['Category_Name']);
        $Category_Type = mysqli_real_escape_string($conn, $_POST['Category_Type']);
        $Icon_Code     = mysqli_real_escape_string($conn, $_POST['Icon_Code']);

        $sql = "INSERT INTO $table (Category_Name, Category_Type, Icon_Code) 
                VALUES ('$Category_Name', '$Category_Type', '$Icon_Code')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Category berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>