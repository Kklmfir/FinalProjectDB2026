<?php 
include 'transfer.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Transfer_ID']) ? intval($_POST['Transfer_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Source_Pocket_ID = intval($_POST['Source_Pocket_ID']);
    $Target_Pocket_ID = intval($_POST['Target_Pocket_ID']);
    $Transfer_Amount  = floatval($_POST['Transfer_Amount']);
    $Transfer_Date    = $_POST['Transfer_Date'];

    $sql = "UPDATE $table SET 
                Source_Pocket_ID = $Source_Pocket_ID,
                Target_Pocket_ID = $Target_Pocket_ID,
                Transfer_Amount = $Transfer_Amount,
                Transfer_Date = '$Transfer_Date'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Transfer berhasil diupdate!');
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
    <title>Edit Transfer</title>
</head>
<body>
    <h2>Edit Transfer - ID: <?php echo $row['Transfer_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Transfer_ID" value="<?php echo $row['Transfer_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Source Pocket ID</td>
                <td><input type="number" name="Source_Pocket_ID" 
                    value="<?php echo $row['Source_Pocket_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Target Pocket ID</td>
                <td><input type="number" name="Target_Pocket_ID" 
                    value="<?php echo $row['Target_Pocket_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Jumlah Transfer (Rp)</td>
                <td><input type="number" name="Transfer_Amount" step="0.01" 
                    value="<?php echo $row['Transfer_Amount']; ?>" required></td>
            </tr>
            <tr>
                <td>Tanggal Transfer</td>
                <td><input type="datetime-local" name="Transfer_Date" 
                    value="<?php echo date('Y-m-d\TH:i', strtotime($row['Transfer_Date'])); ?>" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Transfer</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>