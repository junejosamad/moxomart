<?php
/**
 * Check Environment Configuration
 */

echo "<h1>Environment Configuration Check</h1>";

// Check if .env file exists
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    echo "<h2>.env File Status</h2>";
    echo "✅ .env file exists<br>";
    echo "Size: " . filesize($env_file) . " bytes<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($env_file)), -4) . "<br>";
    
    // Read and display .env content (without sensitive data)
    $env_content = file_get_contents($env_file);
    $lines = explode("\n", $env_content);
    
    echo "<h3>.env File Contents:</h3>";
    echo "<pre>";
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && !str_starts_with($line, '#')) {
            // Hide sensitive data
            if (strpos($line, 'DB_PASSWORD') !== false) {
                echo "DB_PASSWORD=***HIDDEN***\n";
            } else {
                echo htmlspecialchars($line) . "\n";
            }
        }
    }
    echo "</pre>";
} else {
    echo "❌ .env file not found<br>";
}

// Check database configuration
echo "<h2>Database Configuration</h2>";
try {
    // Load environment variables
    if (file_exists($env_file)) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
    
    echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Not set') . "<br>";
    echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'Not set') . "<br>";
    echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'Not set') . "<br>";
    echo "DB_PASSWORD: " . (isset($_ENV['DB_PASSWORD']) ? '***SET***' : 'Not set') . "<br>";
    
} catch (Exception $e) {
    echo "Error loading environment: " . $e->getMessage() . "<br>";
}

// Check cPanel database information
echo "<h2>cPanel Database Information</h2>";
echo "<p>You need to check your cPanel for the correct database credentials:</p>";
echo "<ol>";
echo "<li>Log into your cPanel</li>";
echo "<li>Go to 'MySQL Databases'</li>";
echo "<li>Check what databases exist</li>";
echo "<li>Check what database users exist</li>";
echo "<li>Note the database name, username, and password</li>";
echo "</ol>";

echo "<h2>Common cPanel Database Patterns</h2>";
echo "<ul>";
echo "<li>Database name: usually your_cpanel_username_database_name</li>";
echo "<li>Username: usually your_cpanel_username_username</li>";
echo "<li>Host: usually 'localhost'</li>";
echo "<li>Password: the one you set when creating the database</li>";
echo "</ul>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Check your cPanel MySQL Databases section</li>";
echo "<li>Create a database if it doesn't exist</li>";
echo "<li>Create a database user if it doesn't exist</li>";
echo "<li>Update your .env file with the correct credentials</li>";
echo "<li>Import your database schema</li>";
echo "</ol>";
?> 