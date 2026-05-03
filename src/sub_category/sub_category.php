<?php
// db_subcategory.php
$host = 'localhost';
$dbname = 'final-project-db2026';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Sub_Category
$table       = 'Sub_Category';
$primary_key = 'Sub_Category_ID';
$title       = 'Data Sub Category';
?>