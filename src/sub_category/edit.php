<?php 
include 'sub_category.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Sub_Category_ID']) ? intval($_POST['Sub_Category_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Category_ID = intval($_POST['Category_ID']);
    $Sub_Name    = mysqli_real_escape_string($conn, $_POST['Sub_Name']);
    $Notes       = mysqli_real_escape_string($conn, $_POST['Notes']);

    $sql = "UPDATE $table SET 
                Category_ID = $Category_ID,
                Sub_Name = '$Sub_Name',
                Notes = '$Notes'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Sub Category berhasil diupdate!');
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
    <title>Edit Sub Category</title>
</head>
<body>
    <h2>Edit Sub Category - ID: <?php echo $row['Sub_Category_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Sub_Category_ID" value="<?php echo $row['Sub_Category_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Category ID</td>
                <td><input type="number" name="Category_ID" 
                    value="<?php echo $row['Category_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Nama Sub Category</td>
                <td><input type="text" name="Sub_Name" 
                    value="<?php echo htmlspecialchars($row['Sub_Name']); ?>" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Notes / Keterangan</td>
                <td><textarea name="Notes" rows="4" cols="50"><?php echo htmlspecialchars($row['Notes']); ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Sub Category</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>