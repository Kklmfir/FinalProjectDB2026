<?php
include 'db_debt.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM $table WHERE $primary_key = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Debt/Loan berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
}
?>