<?php
/**
 * Moxo Mart E-commerce Platform
 * Entry Point - cPanel Version (public/ as web root)
 */

// Define constants for cPanel setup (public/ as web root)
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH);
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load Composer autoloader
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
} else {
    // Fallback: simple autoloader for core classes
    spl_autoload_register(function ($class) {
        $file = ROOT_PATH . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// Load environment variables
if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
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
if (file_exists(CONFIG_PATH . '/database.php')) {
    require_once CONFIG_PATH . '/database.php';
}
if (file_exists(CONFIG_PATH . '/routes.php')) {
    require_once CONFIG_PATH . '/routes.php';
}

// Load helpers
if (file_exists(APP_PATH . '/Core/helpers.php')) {
    require_once APP_PATH . '/Core/helpers.php';
} else {
    // Fallback asset helper if helpers.php is missing
    if (!function_exists('asset')) {
        function asset($path) {
            $path = ltrim($path, '/');
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $baseUrl = $protocol . '://' . $host;
            
            if (strpos($path, 'assets/') === 0) {
                return rtrim($baseUrl, '/') . '/' . $path;
            } else {
                return rtrim($baseUrl, '/') . '/assets/' . $path;
            }
        }
    }
}

// Initialize router
if (class_exists('App\Core\Router')) {
    $router = new App\Core\Router();
    
    // Load routes
    if (function_exists('loadRoutes')) {
        loadRoutes($router);
    }
    
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
            if (file_exists(APP_PATH . '/Views/errors/500.php')) {
                include APP_PATH . '/Views/errors/500.php';
            } else {
                echo '<h1>500 Internal Server Error</h1>';
                echo '<p>We\'re experiencing some technical difficulties. Please try again later.</p>';
            }
        }
    }
} else {
    // Fallback: simple routing
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $requestUri = parse_url($requestUri, PHP_URL_PATH);
    
    // Serve static files
    if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i', $requestUri)) {
        $filePath = __DIR__ . $requestUri;
        if (file_exists($filePath)) {
            $mimeTypes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf',
                'eot' => 'application/vnd.ms-fontobject'
            ];
            
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (isset($mimeTypes[$extension])) {
                header('Content-Type: ' . $mimeTypes[$extension]);
                readfile($filePath);
                exit;
            }
        }
    }
    
    // Show homepage or 404
    if ($requestUri === '/' || $requestUri === '') {
        echo '<h1>Moxo Mart</h1>';
        echo '<p>Welcome to Moxo Mart E-commerce Platform</p>';
        echo '<p>CSS should be loading now. Check if the page is styled.</p>';
    } else {
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        echo '<p>The requested page could not be found.</p>';
    }
}
?> 