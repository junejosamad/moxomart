<?php
/**
 * Database Setup Script
 * Creates the database and tables for Moxo Mart
 */

echo "<h1>Moxo Mart Database Setup</h1>";

// Database connection settings
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'moxo_mart';

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL successfully<br>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$database' created/verified<br>";
    
    // Select the database
    $pdo->exec("USE `$database`");
    echo "✅ Database selected<br>";
    
    // Read and execute schema
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $schema = file_get_contents($schemaFile);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|#|\/\*)/', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore errors for statements that might already exist
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "⚠️ Statement error (may be expected): " . $e->getMessage() . "<br>";
                    }
                }
            }
        }
        echo "✅ Database schema executed<br>";
    } else {
        echo "❌ Schema file not found: $schemaFile<br>";
    }
    
    // Read and execute seed data
    $seedFile = __DIR__ . '/database/seed.sql';
    if (file_exists($seedFile)) {
        $seed = file_get_contents($seedFile);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $seed)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(--|#|\/\*)/', $statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore errors for statements that might already exist
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "⚠️ Seed statement error (may be expected): " . $e->getMessage() . "<br>";
                    }
                }
            }
        }
        echo "✅ Seed data executed<br>";
    } else {
        echo "❌ Seed file not found: $seedFile<br>";
    }
    
    echo "<h2>✅ Database setup completed!</h2>";
    echo "<p>You can now:</p>";
    echo "<ol>";
    echo "<li>Go to <a href='login'>Login Page</a></li>";
    echo "<li>Use email: <strong>admin@sadacart.com</strong></li>";
    echo "<li>Use password: <strong>password</strong></li>";
    echo "<li>After login, click 'Admin Panel' in the user dropdown</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Default credentials are: username='root', password=''</li>";
    echo "</ul>";
}
?> 