<?php include 'goal.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Goal Baru</title>
</head>
<body>
    <h2>Tambah Goal Baru</h2>
    
    <form action="add.php" method="POST">
        <table cellpadding="8">
            <tr>
                <td>Pocket ID</td>
                <td><input type="number" name="Pocket_ID" required></td>
            </tr>
            <tr>
                <td>Nama Goal</td>
                <td><input type="text" name="Goal_Name" required style="width:350px;"></td>
            </tr>
            <tr>
                <td>Target Amount (Rp)</td>
                <td><input type="number" name="Target_Amount" step="0.01" required></td>
            </tr>
            <tr>
                <td>Deadline Date</td>
                <td><input type="date" name="Deadline_Date" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="submit">Simpan Goal</button>
                    <a href="index.php">Batal</a>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $Pocket_ID      = intval($_POST['Pocket_ID']);
        $Goal_Name      = mysqli_real_escape_string($conn, $_POST['Goal_Name']);
        $Target_Amount  = floatval($_POST['Target_Amount']);
        $Deadline_Date  = $_POST['Deadline_Date'];

        $sql = "INSERT INTO $table (Pocket_ID, Goal_Name, Target_Amount, Deadline_Date) 
                VALUES ($Pocket_ID, '$Goal_Name', $Target_Amount, '$Deadline_Date')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Goal berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>