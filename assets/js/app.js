// Financial Management Dashboard - JavaScript

$(document).ready(function () {
    // Initialize DataTables
    $('.datatable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records..."
        },
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var select = $('<select class="form-select form-select-sm"><option value="">All</option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }
    });

    // Sidebar toggle for mobile
    $('#sidebarToggle').on('click', function () {
        $('.sidebar').toggleClass('show');
    });

    // Close sidebar when clicking outside on mobile
    $(document).on('click', function (e) {
        if ($(window).width() < 769) {
            if (!$(e.target).closest('.sidebar, #sidebarToggle').length) {
                $('.sidebar').removeClass('show');
            }
        }
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form validation
    $('form').on('submit', function (e) {
        var isValid = true;
        $(this).find('input[required], select[required], textarea[required]').each(function () {
            if ($(this).val().trim() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirm delete actions
    $('.delete-btn').on('click', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        if (confirm('Are you sure you want to delete this item?')) {
            window.location.href = href;
        }
    });

    // Format currency inputs
    $('.currency-input').on('input', function () {
        var value = $(this).val().replace(/[^\d]/g, '');
        $(this).val(formatCurrency(value));
    });

    function formatCurrency(value) {
        return 'IDR ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    }
});

function initializeCharts() {
    // Monthly transactions chart
    var ctx = document.getElementById('monthlyChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Income',
                    data: [12000000, 15000000, 18000000, 14000000, 16000000, 19000000, 17000000, 20000000, 18000000, 21000000, 19000000, 22000000],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Expense',
                    data: [8000000, 9000000, 10000000, 8500000, 9500000, 11000000, 10500000, 12000000, 11500000, 13000000, 12500000, 14000000],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Transactions'
                    }
                }
            }
        });
    }

    // Category pie chart
    var pieCtx = document.getElementById('categoryChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Food', 'Transport', 'Entertainment', 'Bills', 'Shopping', 'Others'],
                datasets: [{
                    data: [25, 15, 10, 20, 15, 15],
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#fbbf24',
                        '#ef4444',
                        '#8b5cf6',
                        '#6b7280'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
}

// Utility functions
function showLoading() {
    $('.loading-overlay').show();
}

function hideLoading() {
    $('.loading-overlay').hide();
}

function updateProgressBar(selector, percentage) {
    $(selector).css('width', percentage + '%').attr('aria-valuenow', percentage);
}