<?php
/**
 * db_local.php
 * Konfigurasi koneksi MySQL (lokal / phpMyAdmin)
 * Digunakan saat DB_MODE=local
 */

require_once __DIR__ . '/env_loader.php';

function getLocalConnection(): PDO
{
    $host     = env('MYSQL_HOST', 'localhost');
    $port     = env('MYSQL_PORT', '3306');
    $dbname   = env('MYSQL_DATABASE', 'final-project-db2026');
    $username = env('MYSQL_USERNAME', 'root');
    $password = env('MYSQL_PASSWORD', '');

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        // Tampilkan pesan error yang aman (tanpa mengekspos kredensial)
        die('<div style="color:red;padding:20px;font-family:sans-serif;">
            <strong>Database Error:</strong> Tidak dapat terhubung ke MySQL lokal.<br>
            Pastikan MySQL sudah berjalan dan konfigurasi .env sudah benar.
        </div>');
    }
}
