<?php
/**
 * database.php
 * Universal Database Connection Handler
 *
 * Mengelola pemilihan database secara otomatis berdasarkan:
 * 1. Session (pilihan user dari dashboard)
 * 2. Environment variable DB_MODE di .env
 * 3. Default: local (MySQL)
 *
 * Penggunaan:
 *   require_once '/path/to/config/database.php';
 *   $pdo = getDB();            // PDO connection
 *   $conn = getDBConn();       // Alias untuk kompatibilitas
 *   $mode = getDBMode();       // 'local' atau 'supabase'
 */

require_once __DIR__ . '/env_loader.php';
require_once __DIR__ . '/db_local.php';
require_once __DIR__ . '/db_supabase.php';

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kembalikan mode database aktif ('local' atau 'supabase').
 * Prioritas: session > .env > default 'local'
 */
function getDBMode(): string
{
    if (isset($_SESSION['db_mode'])) {
        return $_SESSION['db_mode'];
    }

    $mode = env('DB_MODE', 'local');
    return in_array($mode, ['local', 'supabase']) ? $mode : 'local';
}

/**
 * Kembalikan instance PDO sesuai mode aktif.
 * Menggunakan singleton agar koneksi tidak dibuat berulang.
 */
function getDB(): PDO
{
    static $connections = [];

    $mode = getDBMode();

    if (!isset($connections[$mode])) {
        $connections[$mode] = match ($mode) {
            'supabase' => getSupabaseConnection(),
            default    => getLocalConnection(),
        };
    }

    return $connections[$mode];
}

/**
 * Alias untuk getDB() — digunakan di halaman CRUD.
 */
function getDBConn(): PDO
{
    return getDB();
}

/**
 * Switch database mode dan simpan ke session.
 * Dipanggil dari endpoint AJAX atau form switch di dashboard.
 */
function switchDBMode(string $mode): bool
{
    if (!in_array($mode, ['local', 'supabase'])) {
        return false;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['db_mode'] = $mode;
    return true;
}

/**
 * Kembalikan label tampilan untuk mode saat ini.
 */
function getDBLabel(): string
{
    return match (getDBMode()) {
        'supabase' => 'Supabase (Cloud)',
        default    => 'MySQL Local',
    };
}
