<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/contact.php';
if (isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $sql = "DELETE FROM $table WHERE $primary_key = $id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['flash_success'] = 'Kontak berhasil dihapus.';
    } else {
        $_SESSION['flash_error'] = 'Gagal menghapus: ' . mysqli_error($conn);
    }
}
header('Location: index.php');
exit;
