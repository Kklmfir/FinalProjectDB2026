<?php
/**
 * functions.php
 * Utility functions untuk aplikasi MDG (My Dompet Gue)
 */

/**
 * Format angka sebagai mata uang Rupiah.
 * Contoh: formatRupiah(1500000) → "Rp 1.500.000"
 */
function formatRupiah(float $amount, bool $showSymbol = true): string
{
    $formatted = number_format($amount, 0, ',', '.');
    return $showSymbol ? "Rp {$formatted}" : $formatted;
}

/**
 * Format tanggal ke format Indonesia.
 * Contoh: formatDate('2026-01-15') → "15 Januari 2026"
 */
function formatDate(string $date, string $format = 'd F Y'): string
{
    if (empty($date)) return '-';
    
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $timestamp = strtotime($date);
    if ($timestamp === false) return $date;

    $day   = date('d', $timestamp);
    $month = (int) date('n', $timestamp);
    $year  = date('Y', $timestamp);

    return "{$day} {$bulan[$month]} {$year}";
}

/**
 * Format tanggal singkat.
 * Contoh: formatDateShort('2026-01-15') → "15 Jan 2026"
 */
function formatDateShort(string $date): string
{
    if (empty($date)) return '-';
    $bulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
        4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep',
        10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];
    $timestamp = strtotime($date);
    if ($timestamp === false) return $date;
    $day   = date('d', $timestamp);
    $month = (int) date('n', $timestamp);
    $year  = date('Y', $timestamp);
    return "{$day} {$bulan[$month]} {$year}";
}

/**
 * Hitung persentase dengan batas maksimal 100.
 */
function calcPercent(float $current, float $target): int
{
    if ($target <= 0) return 0;
    $pct = (int) round(($current / $target) * 100);
    return min($pct, 100);
}

/**
 * Kembalikan warna badge Bootstrap berdasarkan persentase.
 */
function percentColor(int $percent): string
{
    if ($percent >= 100) return 'success';
    if ($percent >= 75)  return 'info';
    if ($percent >= 50)  return 'warning';
    return 'danger';
}

/**
 * Kembalikan warna Bootstrap untuk jenis transaksi.
 */
function transactionColor(string $type): string
{
    return match (strtolower($type)) {
        'income'   => 'success',
        'expense'  => 'danger',
        'transfer' => 'info',
        default    => 'secondary',
    };
}

/**
 * Kembalikan ikon Font Awesome untuk jenis transaksi.
 */
function transactionIcon(string $type): string
{
    return match (strtolower($type)) {
        'income'   => 'fa-arrow-down',
        'expense'  => 'fa-arrow-up',
        'transfer' => 'fa-exchange-alt',
        default    => 'fa-circle',
    };
}

/**
 * Potong teks panjang.
 */
function truncate(string $text, int $length = 50, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Kembalikan URL base aplikasi.
 */
function baseUrl(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Cari root path aplikasi secara otomatis
    $script   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    
    // Naik ke root project jika berada di subfolder
    $base = rtrim("{$protocol}://{$host}{$script}", '/');
    
    return $base . '/' . ltrim($path, '/');
}

/**
 * Redirect ke URL lain.
 */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * Kembalikan pesan flash dari session, lalu hapus.
 */
function getFlash(string $key): ?array
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

/**
 * Simpan pesan flash ke session.
 */
function setFlash(string $key, string $message, string $type = 'success'): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
}
