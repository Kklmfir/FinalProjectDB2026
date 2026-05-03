<?php
// db_transaction.php
$host = 'localhost';
$dbname = 'final-project-db2026';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Transactions
$table       = 'Transactions';
$primary_key = 'Transaction_ID';
$title       = 'Data Transactions';
?>