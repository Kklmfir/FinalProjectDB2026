<?php
/**
 * bootstrap.php
 * Entry-point bootstrap — include this at the very top of every page.
 *
 * Responsibilities:
 *  1. Start the PHP session (before any output is sent).
 *  2. Load environment variables from .env via env_loader.php.
 *  3. Set the application timezone.
 *  4. Apply basic PHP ini safety defaults.
 */

// 1. Start session exactly once, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Load .env variables
require_once __DIR__ . '/env_loader.php';

// 3. Timezone
date_default_timezone_set('Asia/Jakarta');

// 4. Basic safety defaults
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
