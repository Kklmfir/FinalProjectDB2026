<?php 
include 'db_category.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Category_ID']) ? intval($_POST['Category_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Category_Name = mysqli_real_escape_string($conn, $_POST['Category_Name']);
    $Category_Type = mysqli_real_escape_string($conn, $_POST['Category_Type']);
    $Icon_Code     = mysqli_real_escape_string($conn, $_POST['Icon_Code']);

    $sql = "UPDATE $table SET 
                Category_Name = '$Category_Name',
                Category_Type = '$Category_Type',
                Icon_Code = '$Icon_Code'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Category berhasil diupdate!');
                window.location='index.php';
              </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil data untuk form
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
    <title>Edit Category</title>
</head>
<body>
    <h2>Edit Category - ID: <?php echo $row['Category_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Category_ID" value="<?php echo $row['Category_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Nama Category</td>
                <td><input type="text" name="Category_Name" 
                    value="<?php echo htmlspecialchars($row['Category_Name']); ?>" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Tipe Category</td>
                <td>
                    <select name="Category_Type" required>
                        <option value="Income"  <?php echo ($row['Category_Type']=='Income') ? 'selected' : ''; ?>>Income</option>
                        <option value="Expense" <?php echo ($row['Category_Type']=='Expense') ? 'selected' : ''; ?>>Expense</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Icon Code</td>
                <td><input type="text" name="Icon_Code" 
                    value="<?php echo htmlspecialchars($row['Icon_Code']); ?>" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Category</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>