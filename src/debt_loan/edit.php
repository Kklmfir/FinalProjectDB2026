<?php 
include 'debt_loan.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Debt_ID']) ? intval($_POST['Debt_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Contact_ID = intval($_POST['Contact_ID']);
    $Pocket_ID  = intval($_POST['Pocket_ID']);
    $Amount     = floatval($_POST['Amount']);
    $Due_Date   = $_POST['Due_Date'];
    $Status     = mysqli_real_escape_string($conn, $_POST['Status']);

    $sql = "UPDATE $table SET 
                Contact_ID = $Contact_ID,
                Pocket_ID = $Pocket_ID,
                Amount = $Amount,
                Due_Date = '$Due_Date',
                Status = '$Status'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Debt/Loan berhasil diupdate!');
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
    <title>Edit Debt/Loan</title>
</head>
<body>
    <h2>Edit Debt/Loan - ID: <?php echo $row['Debt_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Debt_ID" value="<?php echo $row['Debt_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Contact ID</td>
                <td><input type="number" name="Contact_ID" 
                    value="<?php echo $row['Contact_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Pocket ID</td>
                <td><input type="number" name="Pocket_ID" 
                    value="<?php echo $row['Pocket_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Jumlah (Rp)</td>
                <td><input type="number" name="Amount" step="0.01" 
                    value="<?php echo $row['Amount']; ?>" required></td>
            </tr>
            <tr>
                <td>Tanggal Jatuh Tempo</td>
                <td><input type="date" name="Due_Date" 
                    value="<?php echo $row['Due_Date']; ?>" required></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <select name="Status" required>
                        <option value="Belum Lunas" <?php echo ($row['Status']=='Belum Lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
                        <option value="Lunas" <?php echo ($row['Status']=='Lunas') ? 'selected' : ''; ?>>Lunas</option>
                        <option value="Cicilan Aktif" <?php echo ($row['Status']=='Cicilan Aktif') ? 'selected' : ''; ?>>Cicilan Aktif</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Debt/Loan</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>