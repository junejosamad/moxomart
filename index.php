<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Root Entry Point for Moxo Mart
 * This allows accessing the application without /public in the URL
 */

// Define the public path for assets
define('PUBLIC_URL_PATH', '/');

// Include the actual application
require_once __DIR__ . '/index_public.php';
?>