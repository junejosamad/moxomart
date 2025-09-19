<?php
/**
 * Database Connection Test
 */

echo "<h1>Database Connection Test</h1>";

// Load environment
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Get database configuration
$host = $_ENV['DB_HOST'] ?? 'localhost';
$database = $_ENV['DB_DATABASE'] ?? '';
$username = $_ENV['DB_USERNAME'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';

echo "<h2>Database Configuration</h2>";
echo "Host: $host<br>";
echo "Database: $database<br>";
echo "Username: $username<br>";
echo "Password: " . (empty($password) ? '❌ Not set' : '✅ Set') . "<br>";

// Test connection
echo "<h2>Connection Test</h2>";
try {
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful!<br>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "✅ Products table exists with " . $result['count'] . " records<br>";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    
    // Provide specific guidance
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<h3>Access Denied - Common Solutions:</h3>";
        echo "<ul>";
        echo "<li>Check if the database user exists in cPanel</li>";
        echo "<li>Verify the password is correct</li>";
        echo "<li>Make sure the user has access to the database</li>";
        echo "<li>Check if the database name is correct</li>";
        echo "</ul>";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<h3>Unknown Database - Solutions:</h3>";
        echo "<ul>";
        echo "<li>Create the database in cPanel MySQL Databases</li>";
        echo "<li>Check the database name spelling</li>";
        echo "<li>Make sure the database exists</li>";
        echo "</ul>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Fix any database connection issues above</li>";
echo "<li>Import your database schema if connection works</li>";
echo "<li>Test your application again</li>";
echo "</ol>";
?> 