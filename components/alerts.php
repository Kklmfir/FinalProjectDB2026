<?php
/**
 * alerts.php
 * Komponen notifikasi — Success, Error, Warning, Info
 *
 * Cara pakai:
 *   // Dari session flash
 *   include 'components/alerts.php';
 *
 *   // Atau dengan variabel langsung:
 *   $alertSuccess = 'Data berhasil disimpan!';
 *   $alertError   = 'Terjadi kesalahan.';
 *   include 'components/alerts.php';
 */

// Muat flash messages dari session jika ada
if (session_status() === PHP_SESSION_NONE) session_start();

$flashTypes = ['success', 'error', 'warning', 'info'];

foreach ($flashTypes as $type) {
    $flashKey = "flash_{$type}";
    if (isset($_SESSION[$flashKey])) {
        ${'alert' . ucfirst($type)} = $_SESSION[$flashKey];
        unset($_SESSION[$flashKey]);
    }
}
?>

<?php if (!empty($alertSuccess)): ?>
<div class="alert mdg-alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?= htmlspecialchars($alertSuccess, ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (!empty($alertError)): ?>
<div class="alert mdg-alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    <?= htmlspecialchars($alertError, ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (!empty($alertWarning)): ?>
<div class="alert mdg-alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <?= htmlspecialchars($alertWarning, ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (!empty($alertInfo)): ?>
<div class="alert mdg-alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <?= htmlspecialchars($alertInfo, ENT_QUOTES, 'UTF-8') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php
// Tampilkan error validasi (array)
if (!empty($validationErrors) && is_array($validationErrors)):
?>
<div class="alert mdg-alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    <strong>Validasi gagal:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach ($validationErrors as $err): ?>
        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
