<?php
/**
 * validation.php
 * Fungsi validasi input untuk aplikasi MDG
 */

/**
 * Validasi bahwa field tidak kosong.
 */
function validateRequired(mixed $value, string $fieldName = 'Field'): ?string
{
    if ($value === null || trim((string)$value) === '') {
        return "{$fieldName} tidak boleh kosong.";
    }
    return null;
}

/**
 * Validasi angka positif.
 */
function validatePositiveNumber(mixed $value, string $fieldName = 'Angka'): ?string
{
    if (!is_numeric($value)) {
        return "{$fieldName} harus berupa angka.";
    }
    if ((float)$value < 0) {
        return "{$fieldName} tidak boleh negatif.";
    }
    return null;
}

/**
 * Validasi format tanggal YYYY-MM-DD.
 */
function validateDate(string $value, string $fieldName = 'Tanggal'): ?string
{
    if (empty($value)) {
        return "{$fieldName} tidak boleh kosong.";
    }
    $d = DateTime::createFromFormat('Y-m-d', $value);
    if (!$d || $d->format('Y-m-d') !== $value) {
        return "{$fieldName} format tidak valid (YYYY-MM-DD).";
    }
    return null;
}

/**
 * Validasi bahwa tanggal akhir >= tanggal mulai.
 */
function validateDateRange(string $startDate, string $endDate): ?string
{
    if (strtotime($endDate) < strtotime($startDate)) {
        return "Tanggal selesai tidak boleh sebelum tanggal mulai.";
    }
    return null;
}

/**
 * Validasi panjang string.
 */
function validateLength(string $value, int $min, int $max, string $fieldName = 'Field'): ?string
{
    $len = mb_strlen(trim($value));
    if ($len < $min) {
        return "{$fieldName} minimal {$min} karakter.";
    }
    if ($len > $max) {
        return "{$fieldName} maksimal {$max} karakter.";
    }
    return null;
}

/**
 * Kumpulkan semua error validasi.
 * Kembalikan array error; array kosong = valid.
 *
 * Contoh:
 *   $errors = validateAll([
 *     validateRequired($_POST['name'], 'Nama'),
 *     validatePositiveNumber($_POST['balance'], 'Saldo'),
 *   ]);
 */
function validateAll(array $results): array
{
    return array_filter($results);
}

/**
 * Cek apakah ada error validasi.
 */
function hasErrors(array $errors): bool
{
    return count($errors) > 0;
}
