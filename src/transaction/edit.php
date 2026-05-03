<?php 
include 'transaction.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Transaction_ID']) ? intval($_POST['Transaction_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Pocket_ID      = intval($_POST['Pocket_ID']);
    $Category_ID    = intval($_POST['Category_ID']);
    $Amount         = floatval($_POST['Amount']);
    $System_Log     = $_POST['System_Log'];
    $Description    = mysqli_real_escape_string($conn, $_POST['Description']);
    $Warning_Status = intval($_POST['Warning_Status']);

    $sql = "UPDATE $table SET 
                Pocket_ID = $Pocket_ID,
                Category_ID = $Category_ID,
                Amount = $Amount,
                System_Log = '$System_Log',
                Description = '$Description',
                Warning_Status = $Warning_Status
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Transaction berhasil diupdate!');
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
    <title>Edit Transaction</title>
</head>
<body>
    <h2>Edit Transaction - ID: <?php echo $row['Transaction_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Transaction_ID" value="<?php echo $row['Transaction_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Pocket ID</td>
                <td><input type="number" name="Pocket_ID" 
                    value="<?php echo $row['Pocket_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Category ID</td>
                <td><input type="number" name="Category_ID" 
                    value="<?php echo $row['Category_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Jumlah (Rp)</td>
                <td><input type="number" name="Amount" step="0.01" 
                    value="<?php echo $row['Amount']; ?>" required></td>
            </tr>
            <tr>
                <td>Tanggal & Waktu</td>
                <td><input type="datetime-local" name="System_Log" 
                    value="<?php echo date('Y-m-d\TH:i', strtotime($row['System_Log'])); ?>" required></td>
            </tr>
            <tr>
                <td>Deskripsi</td>
                <td><textarea name="Description" rows="3" cols="50" required><?php echo htmlspecialchars($row['Description']); ?></textarea></td>
            </tr>
            <tr>
                <td>Warning Status</td>
                <td>
                    <select name="Warning_Status">
                        <option value="0" <?php echo ($row['Warning_Status']==0) ? 'selected' : ''; ?>>Tidak</option>
                        <option value="1" <?php echo ($row['Warning_Status']==1) ? 'selected' : ''; ?>>Ya (Warning)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Transaction</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>