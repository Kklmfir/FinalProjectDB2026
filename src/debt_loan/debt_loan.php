<?php
// db_debt.php
$host = 'localhost';
$dbname = 'final-project-db2026';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Debt_Loan
$table       = 'Debt_Loan';
$primary_key = 'Debt_ID';
$title       = 'Data Debt / Loan';
?>