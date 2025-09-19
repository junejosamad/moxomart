<?php
/**
 * Server Test - Upload this to your cPanel to check server configuration
 */

echo "<h1>Server Configuration Test</h1>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Check if mod_rewrite is available
echo "<h2>Apache Modules</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: " . (in_array('mod_rewrite', $modules) ? '✅ Available' : '❌ Not Available') . "<br>";
} else {
    echo "Cannot check Apache modules (function not available)<br>";
}

// Check server software
echo "<h2>Server Information</h2>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";

// Check file permissions
echo "<h2>File Permissions Test</h2>";
$test_file = __FILE__;
echo "Current file permissions: " . substr(sprintf('%o', fileperms($test_file)), -4) . "<br>";

// Check if .htaccess exists and is readable
$htaccess_file = dirname(__FILE__) . '/.htaccess';
if (file_exists($htaccess_file)) {
    echo ".htaccess exists: ✅<br>";
    echo ".htaccess readable: " . (is_readable($htaccess_file) ? '✅' : '❌') . "<br>";
    echo ".htaccess permissions: " . substr(sprintf('%o', fileperms($htaccess_file)), -4) . "<br>";
} else {
    echo ".htaccess exists: ❌<br>";
}

// Check environment variables
echo "<h2>Environment Variables</h2>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "<br>";

// Test URL rewriting manually
echo "<h2>Manual URL Rewrite Test</h2>";
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
echo "Requested URI: " . $request_uri . "<br>";

// Check if this looks like an asset request
$is_asset = (
    strpos($request_uri, '/assets/') === 0 ||
    strpos($request_uri, '.css') !== false ||
    strpos($request_uri, '.js') !== false
);

echo "Is asset request: " . ($is_asset ? 'Yes' : 'No') . "<br>";

// Test file existence
$test_paths = [
    'assets/css/main.min.css',
    'public/assets/css/main.min.css',
    'css/main.min.css'
];

echo "<h2>File Path Tests</h2>";
foreach ($test_paths as $path) {
    $full_path = dirname(__FILE__) . '/' . $path;
    echo "$path: " . (file_exists($full_path) ? '✅ Exists' : '❌ Not found') . "<br>";
}

echo "<h2>Recommendations</h2>";
echo "<ul>";
echo "<li>If mod_rewrite is not available, use the PHP-based solution</li>";
echo "<li>If .htaccess is not readable, check file permissions</li>";
echo "<li>If files are not found, check your upload structure</li>";
echo "</ul>";
?> 