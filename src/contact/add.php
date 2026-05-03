<?php include 'contact.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Contact Baru</title>
</head>
<body>
    <h2>Tambah Contact Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Nama Kontak</td>
                <td><input type="text" name="Contact_Name" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Nomor Telepon</td>
                <td><input type="text" name="Phone_Number" required placeholder="contoh: 081234567890"></td>
            </tr>
            <tr>
                <td>Jenis Hubungan</td>
                <td><input type="text" name="Relation_Type" required placeholder="contoh: Teman, Rekan Kerja, Keluarga"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Contact</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Contact_Name   = mysqli_real_escape_string($conn, $_POST['Contact_Name']);
        $Phone_Number   = mysqli_real_escape_string($conn, $_POST['Phone_Number']);
        $Relation_Type  = mysqli_real_escape_string($conn, $_POST['Relation_Type']);

        $sql = "INSERT INTO $table (Contact_Name, Phone_Number, Relation_Type) 
                VALUES ('$Contact_Name', '$Phone_Number', '$Relation_Type')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Contact berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>