<?php
/**
 * env_loader.php
 * Memuat variabel dari file .env ke $_ENV dan getenv()
 * Aman untuk development dan production
 */

function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Lewati baris komentar
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Pisahkan key=value (hanya di tanda = pertama)
        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Hapus tanda kutip jika ada
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Simpan ke $_ENV dan putenv
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

/**
 * Ambil nilai dari environment variable.
 * Kembalikan $default jika tidak ada.
 */
function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === '') {
        return $default;
    }

    // Konversi string boolean
    return match (strtolower((string)$value)) {
        'true'  => true,
        'false' => false,
        'null'  => null,
        default => $value,
    };
}

// Auto-load .env dari root project
$envFile = dirname(__DIR__) . '/.env';
loadEnv($envFile);
