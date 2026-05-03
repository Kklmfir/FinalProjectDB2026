<?php
/**
 * Helper Functions
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = 'IDR') {
    return $currency . ' ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date
 */
function formatDate($date, $format = 'd M Y') {
    return date($format, strtotime($date));
}

/**
 * Check if user is logged in (placeholder)
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Validate CSRF token
 */
function validateCSRF() {
    if (!isset($_POST[CSRF_TOKEN_NAME]) || $_POST[CSRF_TOKEN_NAME] !== $_SESSION[CSRF_TOKEN_NAME]) {
        die('CSRF token validation failed');
    }
}

/**
 * Get current page
 */
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

/**
 * Calculate percentage
 */
function calculatePercentage($part, $total) {
    if ($total == 0) return 0;
    return round(($part / $total) * 100, 2);
}
?>