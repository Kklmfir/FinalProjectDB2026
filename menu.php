<?php
/**
 * menu.php — Redirect ke main.php (dashboard baru)
 * File ini dipertahankan untuk kompatibilitas backward.
 */
header('Location: main.php');
exit;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDG - My Dompet Gue | Menu Utama</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #1e3a8a;
        }
        h2 {
            color: #1e40af;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 8px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s;
        }
        .card:hover {
            border-color: #3b82f6;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin: 0 0 15px 0;
            color: #1e3a8a;
        }
        .btn {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
            font-weight: bold;
        }
        .btn:hover {
            background: #2563eb;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #64748b;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>MDG - My Dompet Gue</h1>
    <p style="text-align: center; font-size: 1.1em; color: #475569;">
        Aplikasi Pencatatan Keuangan Pribadi - Final Project Database System 2026
    </p>

    <h2>Menu Utama CRUD</h2>

    <div class="grid">

        <div class="card">
            <h3>🏦 Pocket (Kantong)</h3>
            <p>Kelola semua kantong keuangan dan saldo</p>
            <a href="http://localhost/MDG/CRUD_Pocket/index.php" class="btn">→ Buka Pocket</a>
        </div>

        <div class="card">
            <h3>📂 Category</h3>
            <p>Kelola kategori penghasilan & pengeluaran</p>
            <a href="http://localhost/MDG/CRUD_Category/index.php" class="btn">→ Buka Category</a>
        </div>

        <div class="card">
            <h3>📌 Sub Category</h3>
            <p>Detail sub kategori transaksi</p>
            <a href="http://localhost/MDG/CRUD_Sub_Category/index.php" class="btn">→ Buka Sub Category</a>
        </div>

        <div class="card">
            <h3>📅 Budget</h3>
            <p>Kelola batas budget bulanan per kategori</p>
            <a href="http://localhost/MDG/CRUD_Budget/index.php" class="btn">→ Buka Budget</a>
        </div>

        <div class="card">
            <h3>🎯 Goal (Target)</h3>
            <p>Kelola target tabungan dan goals</p>
            <a href="http://localhost/MDG/CRUD_Goal/index.php" class="btn">→ Buka Goal</a>
        </div>

        <div class="card">
            <h3>👥 Contact</h3>
            <p>Daftar kontak untuk hutang/piutang</p>
            <a href="http://localhost/MDG/CRUD_Contact/index.php" class="btn">→ Buka Contact</a>
        </div>

        <div class="card">
            <h3>🔄 Transfer</h3>
            <p>Catat transfer antar kantong</p>
            <a href="http://localhost/MDG/CRUD_Transfer/index.php" class="btn">→ Buka Transfer</a>
        </div>

        <div class="card">
            <h3>💰 Debt / Loan</h3>
            <p>Kelola hutang dan piutang</p>
            <a href="http://localhost/MDG/CRUD_Debt_Loan/index.php" class="btn">→ Buka Debt/Loan</a>
        </div>

        <div class="card">
            <h3>📋 Transactions</h3>
            <p>Semua catatan transaksi keuangan</p>
            <a href="http://localhost/MDG/CRUD_Transactions/index.php" class="btn">→ Buka Transactions</a>
        </div>

    </div>

    <div class="footer">
        <p><strong>Final Project Database System 2026</strong><br>
        Kelompok MIT-7 | Keefi Almer Firdaus & Alfairaz Putra Anantar</p>
        <p>President University - Faculty of Computer Science</p>
    </div>
</div>

</body>
</html>