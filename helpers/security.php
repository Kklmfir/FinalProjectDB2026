<?php
/**
 * security.php
 * Fungsi keamanan untuk aplikasi MDG
 */

/**
 * Bersihkan output untuk mencegah XSS.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate CSRF token dan simpan di session.
 */
function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Kembalikan hidden input dengan CSRF token.
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="_token" value="' . e($token) . '">';
}

/**
 * Validasi CSRF token dari request.
 * Hentikan eksekusi jika token tidak valid.
 */
function verifyCsrf(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (empty($token) || !hash_equals($sessionToken, $token)) {
        http_response_code(403);
        die('<div style="color:red;padding:20px;font-family:sans-serif;">
            <strong>Security Error:</strong> CSRF token tidak valid. Silakan muat ulang halaman.
        </div>');
    }
}

/**
 * Sanitasi input string dari POST/GET.
 * Menghapus whitespace berlebih dan tag HTML.
 */
function sanitizeInput(mixed $value): string
{
    return trim(strip_tags((string)($value ?? '')));
}

/**
 * Sanitasi integer dari input.
 */
function sanitizeInt(mixed $value, int $default = 0): int
{
    $filtered = filter_var($value, FILTER_VALIDATE_INT);
    return ($filtered !== false) ? (int)$filtered : $default;
}

/**
 * Sanitasi float dari input.
 */
function sanitizeFloat(mixed $value, float $default = 0.0): float
{
    $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
    return ($filtered !== false) ? (float)$filtered : $default;
}
