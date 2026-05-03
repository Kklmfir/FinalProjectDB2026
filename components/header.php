<?php
/**
 * header.php
 * Komponen HTML <head> — digunakan di semua halaman
 *
 * Variabel yang dapat diset sebelum include:
 *   $pageTitle  - Judul halaman (string)
 *   $extraCss   - Path CSS tambahan (string|null)
 */

$pageTitle = $pageTitle ?? 'MDG - My Dompet Gue';

// Tentukan path ke root (relatif dari file yang meng-include)
$rootPath = $rootPath ?? '../';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MDG - My Dompet Gue | Aplikasi Keuangan Pribadi">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | MDG</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $rootPath ?>assets/icons/favicon.png">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Font Awesome 6 (icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $rootPath ?>assets/css/style.css">

    <?php if (!empty($extraCss)): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($extraCss, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
</head>
<body class="mdg-body">
