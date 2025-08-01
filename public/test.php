<?php
// Simple test page to check if web server is working
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moxo Mart Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Moxo Mart Test Page</h1>
    
    <h2>Basic Tests:</h2>
    <p class="success">✅ PHP is working</p>
    <p class="success">✅ Web server is working</p>
    
    <h2>Application Tests:</h2>
    <?php
    try {
        // Test autoloader
        require_once '../vendor/autoload.php';
        echo '<p class="success">✅ Autoloader loaded</p>';
        
        // Test environment
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
        echo '<p class="success">✅ Environment loaded</p>';
        echo '<p>APP_ENV: ' . ($_ENV['APP_ENV'] ?? 'Not set') . '</p>';
        
        // Test database
        $config = require '../config/database.php';
        $dsn = "mysql:host={$config['connections']['mysql']['host']};dbname={$config['connections']['mysql']['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['connections']['mysql']['username'], $config['connections']['mysql']['password']);
        echo '<p class="success">✅ Database connected</p>';
        
        // Test products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch();
        echo '<p class="success">✅ Products found: ' . $result['count'] . '</p>';
        
        // Test helpers
        require_once '../app/Core/helpers.php';
        echo '<p class="success">✅ Helpers loaded</p>';
        
        // Test asset function
        $assetUrl = asset('css/main.min.css');
        echo '<p>Asset URL: ' . $assetUrl . '</p>';
        
    } catch (Exception $e) {
        echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
    }
    ?>
    
    <h2>Next Steps:</h2>
    <p>If all tests pass above, try visiting:</p>
    <ul>
        <li><a href="http://localhost/moxo/public/">Main Application</a></li>
        <li><a href="http://localhost/moxo/public/assets/css/main.min.css">CSS File</a></li>
    </ul>
</body>
</html> 