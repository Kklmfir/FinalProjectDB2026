<?php
// db_transfer.php
$host = 'localhost';
$dbname = 'final-project-db2026';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Transfer
$table       = 'Transfer';
$primary_key = 'Transfer_ID';
$title       = 'Data Transfer';
?>