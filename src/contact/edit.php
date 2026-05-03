<?php 
include 'contact.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Contact_ID']) ? intval($_POST['Contact_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Contact_Name   = mysqli_real_escape_string($conn, $_POST['Contact_Name']);
    $Phone_Number   = mysqli_real_escape_string($conn, $_POST['Phone_Number']);
    $Relation_Type  = mysqli_real_escape_string($conn, $_POST['Relation_Type']);

    $sql = "UPDATE $table SET 
                Contact_Name = '$Contact_Name',
                Phone_Number = '$Phone_Number',
                Relation_Type = '$Relation_Type'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Contact berhasil diupdate!');
                window.location='index.php';
              </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil data untuk edit
$sql = "SELECT * FROM $table WHERE $primary_key = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Data tidak ditemukan! ID: " . $id);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Contact</title>
</head>
<body>
    <h2>Edit Contact - ID: <?php echo $row['Contact_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Contact_ID" value="<?php echo $row['Contact_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Nama Kontak</td>
                <td><input type="text" name="Contact_Name" 
                    value="<?php echo htmlspecialchars($row['Contact_Name']); ?>" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Nomor Telepon</td>
                <td><input type="text" name="Phone_Number" 
                    value="<?php echo htmlspecialchars($row['Phone_Number']); ?>" required></td>
            </tr>
            <tr>
                <td>Jenis Hubungan</td>
                <td><input type="text" name="Relation_Type" 
                    value="<?php echo htmlspecialchars($row['Relation_Type']); ?>" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Contact</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>