<?php
// category.php — Konfigurasi database untuk modul Category
$_envLoader = dirname(__DIR__, 2) . '/config/env_loader.php';
if (file_exists($_envLoader)) {
    require_once $_envLoader;
    $host     = env('MYSQL_HOST', 'localhost');
    $port     = (int) env('MYSQL_PORT', '3306');
    $dbname   = env('MYSQL_DATABASE', 'final-project-db2026');
    $username = env('MYSQL_USERNAME', 'root');
    $password = env('MYSQL_PASSWORD', '');
} else {
    $host = 'localhost'; $port = 3306;
    $dbname = 'final-project-db2026';
    $username = 'root'; $password = '';
}

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Konfigurasi untuk tabel Category
$table       = 'Category';
$primary_key = 'Category_ID';
$title       = 'Data Category';
?>