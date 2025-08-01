<?php
/**
 * Root Entry Point for Moxo Mart
 * This allows accessing the application without /public in the URL
 */

// Define the public path for assets
define('PUBLIC_URL_PATH', '/');

// Change working directory to public for proper asset handling
chdir(__DIR__ . '/public');

// Include the actual application
require_once __DIR__ . '/public/index.php';
?>