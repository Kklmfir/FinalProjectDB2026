<?php 
include 'db_budget.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Budget_ID']) ? intval($_POST['Budget_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Category_ID    = intval($_POST['Category_ID']);
    $Monthly_Limit  = floatval($_POST['Monthly_Limit']);
    $Start_Date     = $_POST['Start_Date'];
    $End_Date       = $_POST['End_Date'];

    $sql = "UPDATE $table SET 
                Category_ID = $Category_ID,
                Monthly_Limit = $Monthly_Limit,
                Start_Date = '$Start_Date',
                End_Date = '$End_Date'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Budget berhasil diupdate!');
                window.location='index.php';
              </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil data existing
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
    <title>Edit Budget</title>
</head>
<body>
    <h2>Edit Budget - ID: <?php echo $row['Budget_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Budget_ID" value="<?php echo $row['Budget_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Category ID</td>
                <td><input type="number" name="Category_ID" 
                    value="<?php echo $row['Category_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Batas Bulanan (Rp)</td>
                <td><input type="number" name="Monthly_Limit" step="0.01" 
                    value="<?php echo $row['Monthly_Limit']; ?>" required></td>
            </tr>
            <tr>
                <td>Tanggal Mulai</td>
                <td><input type="date" name="Start_Date" 
                    value="<?php echo $row['Start_Date']; ?>" required></td>
            </tr>
            <tr>
                <td>Tanggal Selesai</td>
                <td><input type="date" name="End_Date" 
                    value="<?php echo $row['End_Date']; ?>" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Budget</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>