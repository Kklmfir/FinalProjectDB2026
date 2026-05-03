<?php
/**
 * Financial Management Dashboard - Setup & Run Script
 *
 * This script helps with initial setup and testing
 */

// Include configuration
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'helpers/functions.php';

echo "<h1>Financial Management Dashboard - Setup</h1>";

// Test database connection
try {
    $pdo = require 'config/database.php';
    echo "<p style='color: green;'>✓ Database connection successful</p>";

    // Test tables exist
    $tables = ['budget', 'category', 'contact', 'debt_loan', 'goal', 'pocket', 'sub_category', 'transactions', 'transfer'];
    $missingTables = [];

    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if (!$stmt->fetch()) {
            $missingTables[] = $table;
        }
    }

    if (empty($missingTables)) {
        echo "<p style='color: green;'>✓ All database tables exist</p>";
    } else {
        echo "<p style='color: red;'>✗ Missing tables: " . implode(', ', $missingTables) . "</p>";
        echo "<p>Please run the SQL files in phpMyAdmin:</p>";
        echo "<ul>";
        echo "<li><code>final-project-db2026.sql</code> - Create database structure</li>";
        echo "<li><code>insert-data.sql</code> - Insert sample data</li>";
        echo "</ul>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your .env configuration and database setup.</p>";
}

// Check file permissions
$writableDirs = ['assets/img', 'assets/icons'];
$permissionIssues = [];

foreach ($writableDirs as $dir) {
    if (!is_writable($dir)) {
        $permissionIssues[] = $dir;
    }
}

if (empty($permissionIssues)) {
    echo "<p style='color: green;'>✓ File permissions OK</p>";
} else {
    echo "<p style='color: orange;'>⚠ Permission issues with: " . implode(', ', $permissionIssues) . "</p>";
}

// Environment check
echo "<h2>Environment Information</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li>Database: " . (getenv('DB_CONNECTION') ?: 'Not set') . "</li>";
echo "<li>Environment: " . (getenv('APP_ENV') ?: 'Not set') . "</li>";
echo "<li>Debug Mode: " . (getenv('APP_DEBUG') === 'true' ? 'Enabled' : 'Disabled') . "</li>";
echo "</ul>";

echo "<h2>Quick Start</h2>";
echo "<ol>";
echo "<li>Import <code>final-project-db2026.sql</code> into phpMyAdmin</li>";
echo "<li>Run <code>insert-data.sql</code> for sample data</li>";
echo "<li>Open <a href='main.php'>main.php</a> in your browser</li>";
echo "<li>Start managing your finances!</li>";
echo "</ol>";

echo "<p><a href='main.php' class='btn btn-primary'>Go to Dashboard</a></p>";
?>