<?php
/**
 * Working index.php for root directory
 * This will redirect to the public directory
 */

// Check if we're accessing the root directory
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php') {
    // Include the main application
    if (file_exists(__DIR__ . '/public/index.php')) {
        include __DIR__ . '/public/index.php';
    } else {
        echo '<h1>Moxo Mart</h1>';
        echo '<p>Application is loading...</p>';
        echo '<p>If you see this message, the public/index.php file is missing.</p>';
    }
} else {
    // For other requests, try to serve assets or redirect
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Check if this is an asset request
    if (strpos($request_uri, '/assets/') === 0) {
        $asset_path = __DIR__ . '/public' . $request_uri;
        if (file_exists($asset_path)) {
            $extension = pathinfo($asset_path, PATHINFO_EXTENSION);
            $mime_types = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon'
            ];
            
            if (isset($mime_types[$extension])) {
                header('Content-Type: ' . $mime_types[$extension]);
            }
            
            readfile($asset_path);
            exit;
        }
    }
    
    // For all other requests, include the main application
    if (file_exists(__DIR__ . '/public/index.php')) {
        include __DIR__ . '/public/index.php';
    } else {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p>The requested page could not be found.</p>';
    }
}
?> 