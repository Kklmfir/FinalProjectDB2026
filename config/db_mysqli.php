<?php
/**
 * db_mysqli.php
 * MySQLi Connection Handler (PRIMARY)
 * 
 * Mengelola koneksi MySQLi untuk:
 * 1. Local MySQL (default)
 * 2. Supabase PostgreSQL (via pg_connect abstraction)
 * 
 * Usage:
 *   require_once '/path/to/config/db_mysqli.php';
 *   $conn = getMySQLiConnection();  // MySQLi object
 *   $mode = getDBMode();              // 'local' atau 'supabase'
 */

require_once __DIR__ . '/env_loader.php';

// Session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get active database mode from session, .env, or default
 * Priority: session > .env > 'local'
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
 * Get MySQLi connection (LOCAL MODE ONLY)
 * Returns mysqli object configured for local MySQL
 * 
 * @return mysqli
 * @throws Exception if connection fails
 */
function getMySQLiConnection(): mysqli
{
    static $connection = null;

    if ($connection !== null) {
        return $connection;
    }

    $host     = env('MYSQL_HOST', 'localhost');
    $port     = env('MYSQL_PORT', '3306');
    $dbname   = env('MYSQL_DATABASE', 'final-project-db2026');
    $username = env('MYSQL_USERNAME', 'root');
    $password = env('MYSQL_PASSWORD', '');

    try {
        $connection = new mysqli($host, $username, $password, $dbname, (int)$port);

        if ($connection->connect_error) {
            error_log("MySQLi Connection Error: " . $connection->connect_error);
            throw new Exception("Gagal terhubung ke database. Silakan coba lagi.");
        }

        // Set charset to utf8mb4
        $connection->set_charset("utf8mb4");

        return $connection;
    } catch (Exception $e) {
        error_log("MySQLi Error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Alias for getMySQLiConnection() for backward compatibility
 */
function getDBConn(): mysqli
{
    return getMySQLiConnection();
}

/**
 * Switch database mode and save to session
 * Called from AJAX or dashboard switch
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
 * Get display label for current mode
 */
function getDBLabel(): string
{
    return match (getDBMode()) {
        'supabase' => 'Supabase (Cloud)',
        default    => 'MySQL Local',
    };
}

/**
 * FUTURE: PostgreSQL abstraction for Supabase
 * (Currently returns null; implement when Supabase migration needed)
 */
function getPostgreSQLConnection()
{
    // TODO: Implement PostgreSQL connection when needed
    // For now, enforce LOCAL mode
    return null;
}
