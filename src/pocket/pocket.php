<?php
// db.php
$host = 'localhost';
$dbname = 'final-project-db2026';   // Sesuaikan jika nama database berbeda
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi tabel untuk CRUD Pocket
$table = 'Pocket';
$primary_key = 'Pocket_ID';
$columns = [
    'Pocket_ID'     => 'Pocket ID',
    'Pocket_Name'   => 'Nama Kantong',
    'Balance'       => 'Saldo',
    'Max_Budget'    => 'Maksimal Budget',
    'Created_Date'  => 'Tanggal Dibuat'
];
?>