<?php
/**
 * Database Configuration
 * Supports dual-mode: MySQL (primary) and Supabase PostgreSQL (secondary)
 */

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception('.env file not found');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/../.env');

// Get database connection type
$dbConnection = getenv('DB_CONNECTION') ?: 'mysql';

if ($dbConnection === 'mysql') {
    // MySQL Connection
    $host = getenv('MYSQL_HOST') ?: 'localhost';
    $port = getenv('MYSQL_PORT') ?: 3306;
    $database = getenv('MYSQL_DATABASE') ?: 'final-project-db2026';
    $username = getenv('MYSQL_USERNAME') ?: 'root';
    $password = getenv('MYSQL_PASSWORD') ?: '';

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("MySQL Connection failed: " . $e->getMessage());
    }
} elseif ($dbConnection === 'supabase') {
    // Supabase PostgreSQL Connection
    $url = getenv('SUPABASE_URL');
    $key = getenv('SUPABASE_KEY');
    $database = getenv('SUPABASE_DB');

    if (!$url || !$key || !$database) {
        die("Supabase configuration incomplete. Please check .env file.");
    }

    try {
        $pdo = new PDO("pgsql:host=$url;dbname=$database", $key, '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Supabase Connection failed: " . $e->getMessage());
    }
} else {
    die("Invalid DB_CONNECTION. Supported: mysql, supabase");
}

// Return PDO instance
return $pdo;
?>