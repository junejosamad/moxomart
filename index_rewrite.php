<?php
/**
 * PHP-based URL Rewriting (Alternative to .htaccess)
 * Place this in your public_html root directory
 */

// Get the requested URI
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string
$request_uri = strtok($request_uri, '?');

// Define routes that should be handled by the main application
$app_routes = [
    '/',
    '/products',
    '/cart',
    '/checkout',
    '/login',
    '/register',
    '/admin',
    '/blog',
    '/contact',
    '/about'
];

// Check if this is an asset request
$is_asset = (
    strpos($request_uri, '/assets/') === 0 ||
    strpos($request_uri, '/css/') === 0 ||
    strpos($request_uri, '/js/') === 0 ||
    strpos($request_uri, '/images/') === 0 ||
    strpos($request_uri, '.css') !== false ||
    strpos($request_uri, '.js') !== false ||
    strpos($request_uri, '.png') !== false ||
    strpos($request_uri, '.jpg') !== false ||
    strpos($request_uri, '.jpeg') !== false ||
    strpos($request_uri, '.gif') !== false ||
    strpos($request_uri, '.svg') !== false ||
    strpos($request_uri, '.ico') !== false
);

// If it's an asset, serve it directly
if ($is_asset) {
    $file_path = __DIR__ . '/public' . $request_uri;
    if (file_exists($file_path)) {
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
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
        
        readfile($file_path);
        exit;
    }
}

// For all other requests, route to the main application
include __DIR__ . '/public/index.php'; 