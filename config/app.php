<?php
/**
 * Application Configuration
 */

define('APP_NAME', 'Financial Management Dashboard');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'local');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// Base URL (adjust as needed)
define('BASE_URL', 'http://localhost/FinalProjectDB2026/');

// Paths
define('ROOT_PATH', __DIR__ . '/../');
define('ASSETS_PATH', BASE_URL . 'assets/');
define('SRC_PATH', ROOT_PATH . 'src/');

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 hour

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
?>