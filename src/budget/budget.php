<?php
// db_budget.php
$host = 'localhost';
$dbname = 'final-project-db2026';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Budget
$table       = 'Budget';
$primary_key = 'Budget_ID';
$title       = 'Data Budget';
?>