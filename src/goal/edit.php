<?php 
include 'goal.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['Goal_ID']) ? intval($_POST['Goal_ID']) : 0);

if ($id <= 0) {
    die("ID tidak valid!");
}

// Proses Update
if (isset($_POST['update'])) {
    $Pocket_ID      = intval($_POST['Pocket_ID']);
    $Goal_Name      = mysqli_real_escape_string($conn, $_POST['Goal_Name']);
    $Target_Amount  = floatval($_POST['Target_Amount']);
    $Deadline_Date  = $_POST['Deadline_Date'];

    $sql = "UPDATE $table SET 
                Pocket_ID = $Pocket_ID,
                Goal_Name = '$Goal_Name',
                Target_Amount = $Target_Amount,
                Deadline_Date = '$Deadline_Date'
            WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Goal berhasil diupdate!');
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
    <title>Edit Goal</title>
</head>
<body>
    <h2>Edit Goal - ID: <?php echo $row['Goal_ID']; ?></h2>
    
    <form action="edit.php" method="POST">
        <input type="hidden" name="Goal_ID" value="<?php echo $row['Goal_ID']; ?>">

        <table cellpadding="8">
            <tr>
                <td>Pocket ID</td>
                <td><input type="number" name="Pocket_ID" 
                    value="<?php echo $row['Pocket_ID']; ?>" required></td>
            </tr>
            <tr>
                <td>Nama Goal</td>
                <td><input type="text" name="Goal_Name" 
                    value="<?php echo htmlspecialchars($row['Goal_Name']); ?>" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Target Amount (Rp)</td>
                <td><input type="number" name="Target_Amount" step="0.01" 
                    value="<?php echo $row['Target_Amount']; ?>" required></td>
            </tr>
            <tr>
                <td>Deadline Date</td>
                <td><input type="date" name="Deadline_Date" 
                    value="<?php echo $row['Deadline_Date']; ?>" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="update">Update Goal</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>