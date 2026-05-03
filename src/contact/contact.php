<?php
// db_contact.php
$host = 'localhost';
$dbname = 'final-project-db2026';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Contact
$table       = 'Contact';
$primary_key = 'Contact_ID';
$title       = 'Data Contact';
?>