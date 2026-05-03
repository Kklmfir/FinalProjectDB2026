<?php 
include 'pocket.php';

// Ambil ID dari GET (saat pertama kali buka form) atau dari POST (saat submit update)
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Pocket_ID']) ? intval($_POST['Pocket_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// === PROSES UPDATE (harus di paling atas sebelum ambil data) ===
if (isset($_POST['update'])) {
    $Pocket_Name  = mysqli_real_escape_string($conn, $_POST['Pocket_Name']);
    $Balance      = floatval($_POST['Balance']);
    $Max_Budget   = floatval($_POST['Max_Budget']);
    $Created_Date = $_POST['Created_Date'];

    $sql = "UPDATE $table SET 
                Pocket_Name = '$Pocket_Name',
                Balance = $Balance,
                Max_Budget = $Max_Budget,
                Created_Date = '$Created_Date'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data Pocket berhasil diupdate!');
                window.location='index.php';
              </script>";
        exit();
    } else {
        echo "Error Update: " . mysqli_error($conn);
        exit();
    }
}

// === AMBIL DATA UNTUK DITAMPILKAN DI FORM ===
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
    <title>Edit Pocket</title>
</head>
<body>
    <h2>Edit Pocket - ID: <?php echo $row['Pocket_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Pocket_ID" value="<?php echo $row['Pocket_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Nama Kantong</td>
                <td><input type="text" name="Pocket_Name" 
                    value="<?php echo htmlspecialchars($row['Pocket_Name']); ?>" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Saldo Saat Ini</td>
                <td><input type="number" name="Balance" step="0.01" 
                    value="<?php echo $row['Balance']; ?>" required></td>
            </tr>
            <tr>
                <td>Maksimal Budget</td>
                <td><input type="number" name="Max_Budget" step="0.01" 
                    value="<?php echo $row['Max_Budget']; ?>"></td>
            </tr>
            <tr>
                <td>Tanggal Dibuat</td>
                <td><input type="datetime-local" name="Created_Date" 
                    value="<?php echo date('Y-m-d\TH:i', strtotime($row['Created_Date'])); ?>" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">💾 Update Data</button>
                    <a href="index.php"> Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>