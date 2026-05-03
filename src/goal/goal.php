<?php
// db_goal.php
$host = 'localhost';
$dbname = 'final-project-db2026';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Goal
$table       = 'Goal';
$primary_key = 'Goal_ID';
$title       = 'Data Goal';
?>