<?php
/**
 * Moxo Mart E-commerce Platform
 * Entry Point
 */
// temporarily at the very top of public/index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define constants - handle both direct access and root access
if (basename(getcwd()) === 'public') {
    // Called directly from public folder
    define('ROOT_PATH', dirname(__DIR__));
    define('APP_PATH', ROOT_PATH . '/app');
    define('PUBLIC_PATH', ROOT_PATH . '/public');
    define('CONFIG_PATH', ROOT_PATH . '/config');
} else {
    // Called from root via index.php
    define('ROOT_PATH', __DIR__);
    define('APP_PATH', ROOT_PATH . '/app');
    define('PUBLIC_PATH', ROOT_PATH . '/public');
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

// Load Composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment variables
$envPath = (basename(getcwd()) === 'public') ? ROOT_PATH : dirname(ROOT_PATH);
if (file_exists($envPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($envPath);
    $dotenv->load();
}

// Error reporting
if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session
session_start();

// Load configuration
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/routes.php';

// Load helpers
require_once APP_PATH . '/Core/helpers.php';

// Initialize router
$router = new App\Core\Router();

// Load routes
loadRoutes($router);

// Handle request
try {
    $router->handleRequest();
} catch (Exception $e) {
    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
        echo '<h1>Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        // Log error
        error_log($e->getMessage());
        
        // Show generic error page
        http_response_code(500);
        include APP_PATH . '/Views/errors/500.php';
    }
}
