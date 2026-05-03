<?php
/**
 * footer.php
 * Komponen footer halaman — digunakan di semua halaman
 *
 * Variabel yang dapat diset sebelum include:
 *   $rootPath  - Path ke root
 */

$rootPath = $rootPath ?? '../';
?>
    <!-- Footer -->
    <footer class="mdg-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <i class="fas fa-wallet me-2"></i>
                <strong>MDG</strong> — My Dompet Gue
            </div>
            <div class="footer-info">
                Final Project Database System 2026 &bull;
                Kelompok MIT-7 &bull;
                President University &bull;
                Faculty of Computer Science
            </div>
            <div class="footer-year">
                &copy; <?= date('Y') ?> MDG App
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= $rootPath ?>assets/js/app.js"></script>

    <?php if (!empty($extraJs)): ?>
    <script src="<?= htmlspecialchars($extraJs, ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endif; ?>

    <?php if (!empty($inlineJs)): ?>
    <script><?= $inlineJs ?></script>
    <?php endif; ?>

</body>
</html>
