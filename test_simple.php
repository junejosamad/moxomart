<?php
// Simple test to see if the application is working
echo "=== Moxo Mart Test ===\n";
echo "PHP is working\n";

// Test if we can load the autoloader
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    echo "Autoloader loaded successfully\n";
} else {
    echo "ERROR: Autoloader not found\n";
    exit;
}

// Test environment loading
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "Environment loaded successfully\n";
    echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'Not set') . "\n";
} catch (Exception $e) {
    echo "ERROR loading environment: " . $e->getMessage() . "\n";
}

// Test database connection
try {
    $config = require 'config/database.php';
    $dsn = "mysql:host={$config['connections']['mysql']['host']};dbname={$config['connections']['mysql']['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['connections']['mysql']['username'], $config['connections']['mysql']['password']);
    echo "Database connection successful\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "Products in database: " . $result['count'] . "\n";
} catch (Exception $e) {
    echo "ERROR connecting to database: " . $e->getMessage() . "\n";
}

echo "=== Test Complete ===\n"; 