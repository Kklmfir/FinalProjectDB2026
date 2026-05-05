<?php
/**
 * bootstrap.php
 * Entry-point bootstrap — include this at the very top of every page.
 *
 * Responsibilities:
 *  1. Start the PHP session (before any output is sent).
 *  2. Load environment variables from .env via env_loader.php.
 *  3. Set the application timezone.
 *  4. Load database connection (MySQLi).
 *  5. Apply basic PHP ini safety defaults.
 */

// 1. Start session exactly once, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Load .env variables
require_once __DIR__ . '/env_loader.php';

// 3. Timezone
date_default_timezone_set('Asia/Jakarta');

// 4. Load MySQLi connection
require_once __DIR__ . '/db_mysqli.php';

// Get connection and make globally available
try {
    $conn = getMySQLiConnection();
} catch (Exception $e) {
    // Connection error - log and show user-friendly message
    error_log("Fatal Error: " . $e->getMessage());
    die(
        '<div style="color:#dc2626;padding:20px;font-family:sans-serif;background:#fee2e2;border:1px solid #fca5a5;border-radius:6px;margin:20px;">' .
        '<strong>Sistem Error</strong><br>' .
        'Database tidak dapat dihubungkan. Silakan hubungi administrator.' .
        '</div>'
    );
}

// 5. Basic safety defaults
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
