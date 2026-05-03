<?php
/**
 * Security Functions
 */

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate secure token
 */
function generateToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Sanitize output to prevent XSS
 */
function sanitizeOutput($data) {
    if (is_array($data)) {
        return array_map('sanitizeOutput', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Rate limiting (simple implementation)
 */
function checkRateLimit($key, $limit = 10, $timeWindow = 60) {
    $sessionKey = 'rate_limit_' . $key;
    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = ['count' => 1, 'time' => time()];
        return true;
    }

    $data = $_SESSION[$sessionKey];
    if (time() - $data['time'] > $timeWindow) {
        $_SESSION[$sessionKey] = ['count' => 1, 'time' => time()];
        return true;
    }

    if ($data['count'] >= $limit) {
        return false;
    }

    $_SESSION[$sessionKey]['count']++;
    return true;
}

/**
 * Log security events
 */
function logSecurityEvent($event, $details = '') {
    $logFile = ROOT_PATH . 'logs/security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $message = "[$timestamp] [$ip] $event: $details" . PHP_EOL;
    file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
}
?>