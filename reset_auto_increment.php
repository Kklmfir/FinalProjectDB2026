<?php
// reset_auto_increment.php - Versi Diperbaiki
include 'CRUD_Pocket/db.php';   // Menggunakan koneksi dari Pocket

echo "<h2>Reset AUTO_INCREMENT Semua Tabel</h2>";

$tables = [
    'Pocket',
    'Category',
    'Sub_Category',
    'Budget',
    'Goal',
    'Contact',
    'Transfer',
    'Debt_Loan',
    'Transactions'
];

foreach ($tables as $table) {
    $id_column = $table . "_ID";
    
    echo "<h3>Processing: $table</h3>";
    
    // 1. Ubah kolom menjadi AUTO_INCREMENT (tanpa mendefinisikan PRIMARY KEY lagi)
    $sql1 = "ALTER TABLE `$table` 
             MODIFY `$id_column` INT NOT NULL AUTO_INCREMENT";
    
    if (mysqli_query($conn, $sql1)) {
        echo "<p style='color:green;'>✅ $table : Kolom $id_column berhasil diubah menjadi AUTO_INCREMENT</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ $table : " . mysqli_error($conn) . "</p>";
    }

    // 2. Reset AUTO_INCREMENT ke 1
    $sql2 = "ALTER TABLE `$table` AUTO_INCREMENT = 1";
    
    if (mysqli_query($conn, $sql2)) {
        echo "<p style='color:green;'>✅ $table : AUTO_INCREMENT berhasil direset ke 1</p>";
    } else {
        echo "<p style='color:red;'>❌ $table : Gagal reset - " . mysqli_error($conn) . "</p>";
    }

    echo "<hr>";
}

echo "<h3 style='color:blue;'>✅ Proses reset AUTO_INCREMENT selesai untuk semua tabel!</h3>";
echo "<p><strong>Sekarang coba tambah data baru di salah satu tabel.</strong></p>";
echo "<p><a href='menu.php'>← Kembali ke Menu Utama</a></p>";
?>