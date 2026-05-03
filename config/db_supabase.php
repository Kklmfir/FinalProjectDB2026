<?php
/**
 * db_supabase.php
 * Konfigurasi koneksi Supabase PostgreSQL (cloud)
 * Digunakan saat DB_MODE=supabase
 */

require_once __DIR__ . '/env_loader.php';

function getSupabaseConnection(): PDO
{
    $host     = env('SUPABASE_HOST', '');
    $port     = env('SUPABASE_PORT', '5432');
    $dbname   = env('SUPABASE_DB', 'postgres');
    $username = env('SUPABASE_USERNAME', 'postgres');
    $password = env('SUPABASE_PASSWORD', '');

    if (empty($host)) {
        die('<div style="color:red;padding:20px;font-family:sans-serif;">
            <strong>Supabase Error:</strong> SUPABASE_HOST belum dikonfigurasi di file .env.<br>
            Silakan isi konfigurasi Supabase atau gunakan mode lokal (DB_MODE=local).
        </div>');
    }

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die('<div style="color:red;padding:20px;font-family:sans-serif;">
            <strong>Supabase Error:</strong> Tidak dapat terhubung ke Supabase PostgreSQL.<br>
            Periksa kembali konfigurasi SUPABASE_* di file .env.
        </div>');
    }
}
