/**
 * app.js — MDG App JavaScript
 * Sidebar toggle, DB switcher, DataTables, Chart.js, UX enhancements
 */

'use strict';

/* ─── DOM Ready ───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDataTables();
    initAlertAutoDismiss();
    initTooltips();
});

/* ─── Sidebar Toggle ──────────────────────────────────────── */
function initSidebar() {
    const sidebar  = document.getElementById('mdgSidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const openBtn  = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');

    if (!sidebar) return;

    function openSidebar() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (openBtn)  openBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay)  overlay.addEventListener('click', closeSidebar);

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });
}

/* ─── Database Switcher ───────────────────────────────────── */
/**
 * Kirim request ke server untuk mengganti mode database,
 * lalu reload halaman untuk menerapkan perubahan.
 *
 * @param {string} mode - 'local' atau 'supabase'
 */
function switchDatabase(mode) {
    const validModes = ['local', 'supabase'];
    if (!validModes.includes(mode)) return;

    // Update UI button state sementara
    document.querySelectorAll('.db-switch-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.mode === mode);
    });

    // Kirim ke server via fetch
    const rootPath = getRootPath();
    fetch(`${rootPath}api/switch_db.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mode }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(
                `Database beralih ke ${mode === 'supabase' ? 'Supabase (Cloud)' : 'MySQL Local'}`,
                'success'
            );
            // Reload setelah 800ms untuk menampilkan notifikasi
            setTimeout(() => window.location.reload(), 800);
        } else {
            showNotification(data.message || 'Gagal mengganti database.', 'danger');
        }
    })
    .catch(() => {
        // Fallback: gunakan form POST redirect jika fetch gagal
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${rootPath}api/switch_db.php`;
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'mode';
        input.value = mode;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    });
}

/* ─── DataTables Init ─────────────────────────────────────── */
function initDataTables() {
    if (typeof $.fn === 'undefined' || typeof $.fn.DataTable === 'undefined') return;

    const tables = document.querySelectorAll('.mdg-datatable');
    tables.forEach(table => {
        // Cegah double-init
        if ($.fn.DataTable.isDataTable(table)) return;

        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                search:           'Cari:',
                lengthMenu:       'Tampilkan _MENU_ data',
                info:             'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty:        'Tidak ada data',
                zeroRecords:      'Data tidak ditemukan',
                paginate: {
                    first:    '«',
                    last:     '»',
                    next:     '›',
                    previous: '‹',
                },
            },
            dom: '<"mdg-dt-top"lf>rt<"mdg-dt-bottom"ip>',
        });
    });
}

/* ─── Chart Helpers ───────────────────────────────────────── */
/**
 * Buat Line Chart untuk tren transaksi bulanan.
 *
 * @param {string} canvasId  - ID elemen <canvas>
 * @param {string[]} labels  - Label bulan
 * @param {number[]} income  - Data pemasukan
 * @param {number[]} expense - Data pengeluaran
 */
function createTrendChart(canvasId, labels, income, expense) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: income,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4,
                },
                {
                    label: 'Pengeluaran',
                    data: expense,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,.08)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ef4444',
                    pointRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 16 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` Rp ${ctx.parsed.y.toLocaleString('id-ID')}`,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp ' + v.toLocaleString('id-ID'),
                    },
                    grid: { color: 'rgba(0,0,0,.05)' },
                },
                x: { grid: { display: false } },
            },
        },
    });
}

/**
 * Buat Doughnut Chart untuk distribusi kategori.
 *
 * @param {string}   canvasId
 * @param {string[]} labels
 * @param {number[]} data
 */
function createCategoryChart(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;

    const palette = [
        '#10b981','#3b82f6','#f59e0b','#ef4444',
        '#8b5cf6','#06b6d4','#ec4899','#14b8a6',
    ];

    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: palette.slice(0, data.length),
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` Rp ${ctx.parsed.toLocaleString('id-ID')}`,
                    },
                },
            },
        },
    });
}

/* ─── Notification Toast ──────────────────────────────────── */
/**
 * Tampilkan notifikasi toast sementara.
 *
 * @param {string} message
 * @param {'success'|'danger'|'warning'|'info'} type
 * @param {number} duration - ms sebelum hilang (default 3500)
 */
function showNotification(message, type = 'success', duration = 3500) {
    // Buat container jika belum ada
    let container = document.getElementById('mdgToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'mdgToastContainer';
        container.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }

    const iconMap = {
        success: 'fa-check-circle',
        danger:  'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info:    'fa-info-circle',
    };

    const toast = document.createElement('div');
    toast.style.cssText = `
        background: white;
        border-left: 4px solid var(--color-${type === 'danger' ? 'danger' : type === 'success' ? 'primary' : type === 'warning' ? 'gold' : 'accent'});
        border-radius: 0.75rem;
        box-shadow: 0 4px 24px rgba(0,0,0,.12);
        padding: 0.875rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        min-width: 260px;
        max-width: 380px;
        pointer-events: all;
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.25s ease;
    `;

    toast.innerHTML = `
        <i class="fas ${iconMap[type] || 'fa-bell'}" style="color: var(--color-${type === 'danger' ? 'danger' : type === 'success' ? 'primary' : type === 'warning' ? 'gold' : 'accent'});"></i>
        <span style="flex:1">${message}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;opacity:.5;font-size:1rem;">&times;</button>
    `;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });

    // Auto-remove
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/* ─── Alert Auto-Dismiss ──────────────────────────────────── */
function initAlertAutoDismiss() {
    const alerts = document.querySelectorAll('.mdg-alert.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.style.transition = 'opacity 0.4s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 400);
            }
        }, 4000);
    });
}

/* ─── Bootstrap Tooltips ──────────────────────────────────── */
function initTooltips() {
    if (typeof bootstrap === 'undefined') return;
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });
}

/* ─── Confirm Delete ──────────────────────────────────────── */
/**
 * Tampilkan konfirmasi sebelum delete.
 * Digunakan via: onclick="return confirmDelete('Pocket ini')"
 */
function confirmDelete(itemName = 'item ini') {
    return window.confirm(`Yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`);
}

/* ─── Utility: Root Path ──────────────────────────────────── */
function getRootPath() {
    const base = document.querySelector('base');
    if (base) return base.href;

    // Hitung dari URL saat ini
    const path = window.location.pathname;
    const depth = (path.match(/\//g) || []).length - 1;
    if (depth <= 1) return '/';

    // Cari path root dari script atau fallback
    const scripts = document.querySelectorAll('script[src*="assets/js/app.js"]');
    if (scripts.length > 0) {
        const src = scripts[0].src;
        return src.replace('assets/js/app.js', '');
    }

    return '../'.repeat(Math.max(0, depth - 1));
}

/* ─── Number Formatter ────────────────────────────────────── */
function formatRupiah(amount) {
    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
}

/* ─── Progress Animation ──────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.progress-fill[data-width]').forEach(bar => {
        const width = bar.dataset.width;
        // Animasi dari 0 → target width
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = width + '%'; }, 100);
    });
});
