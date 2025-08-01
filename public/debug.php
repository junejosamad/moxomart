<?php
// Debug the router and request handling

// Load the application
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once ROOT_PATH . '/vendor/autoload.php';

if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

session_start();
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/routes.php';
require_once APP_PATH . '/Core/helpers.php';

// Debug information
echo "<h1>Router Debug</h1>";

echo "<h2>Request Information:</h2>";
echo "<p>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
echo "<p>REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "</p>";
echo "<p>SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "</p>";

// Test router initialization
try {
    $router = new App\Core\Router();
    echo "<p class='success'>✅ Router initialized successfully</p>";
    
    // Load routes
    loadRoutes($router);
    echo "<p class='success'>✅ Routes loaded successfully</p>";
    
    // Test HomeController
    $homeController = new App\Controllers\HomeController();
    echo "<p class='success'>✅ HomeController created successfully</p>";
    
    // Test getting featured products
    $productModel = new App\Models\Product();
    $featuredProducts = $productModel->getFeatured(8);
    echo "<p class='success'>✅ Featured products retrieved: " . count($featuredProducts) . " products</p>";
    
    // Test view rendering
    $view = new App\Core\View();
    echo "<p class='success'>✅ View class created successfully</p>";
    
    // Test rendering home template
    $meta = [
        'title' => 'Moxo Mart - Your Trusted E-commerce Partner',
        'description' => 'Discover quality products at Moxo Mart.'
    ];
    
    $data = [
        'featuredProducts' => $featuredProducts,
        'categories' => [],
        'recentPosts' => [],
        'meta' => $meta
    ];
    
    $content = $view->render('home/index', $data);
    echo "<p class='success'>✅ View rendered successfully</p>";
    echo "<p>Content length: " . strlen($content) . " characters</p>";
    
    // Show first 500 characters of content
    echo "<h3>First 500 characters of rendered content:</h3>";
    echo "<pre>" . htmlspecialchars(substr($content, 0, 500)) . "</pre>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<style>
.success { color: green; }
.error { color: red; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>";
?> 