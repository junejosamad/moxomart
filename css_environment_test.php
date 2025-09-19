<?php
/**
 * CSS Environment Test - Compare Local vs cPanel
 */

echo "<h1>CSS Environment Test</h1>";

// Test 1: Server Information
echo "<h2>Test 1: Server Information</h2>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</p>";
echo "<p><strong>HTTPS:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Yes' : 'No') . "</p>";

// Test 2: File System Check
echo "<h2>Test 2: File System Check</h2>";
$css_path = __DIR__ . '/assets/css/main.min.css';
echo "<p><strong>CSS Path:</strong> $css_path</p>";
echo "<p><strong>File Exists:</strong> " . (file_exists($css_path) ? '✅ Yes' : '❌ No') . "</p>";
if (file_exists($css_path)) {
    echo "<p><strong>File Size:</strong> " . filesize($css_path) . " bytes</p>";
    echo "<p><strong>File Permissions:</strong> " . substr(sprintf('%o', fileperms($css_path)), -4) . "</p>";
    echo "<p><strong>File Readable:</strong> " . (is_readable($css_path) ? '✅ Yes' : '❌ No') . "</p>";
}

// Test 3: Asset Helper Test
echo "<h2>Test 3: Asset Helper Test</h2>";
if (file_exists(__DIR__ . '/app/Core/helpers.php')) {
    require_once __DIR__ . '/app/Core/helpers.php';
    if (function_exists('asset')) {
        $asset_url = asset('css/main.min.css');
        echo "<p><strong>Asset URL:</strong> <code>$asset_url</code></p>";
        
        // Test if URL is accessible
        $headers = @get_headers($asset_url);
        if ($headers) {
            echo "<p><strong>HTTP Response:</strong> " . $headers[0] . "</p>";
        } else {
            echo "<p><strong>HTTP Response:</strong> ❌ Could not get headers</p>";
        }
    } else {
        echo "<p>❌ Asset function not found</p>";
    }
} else {
    echo "<p>❌ Helpers file not found</p>";
}

// Test 4: Directory Structure
echo "<h2>Test 4: Directory Structure</h2>";
$directories = [
    'assets' => __DIR__ . '/assets',
    'assets/css' => __DIR__ . '/assets/css',
    'app/Core' => __DIR__ . '/app/Core'
];

foreach ($directories as $name => $path) {
    echo "<p><strong>$name:</strong> " . (is_dir($path) ? '✅ Exists' : '❌ Missing') . "</p>";
    if (is_dir($path)) {
        echo "<p><strong>$name Permissions:</strong> " . substr(sprintf('%o', fileperms($path)), -4) . "</p>";
    }
}

// Test 5: .htaccess Check
echo "<h2>Test 5: .htaccess Check</h2>";
$htaccess_path = __DIR__ . '/.htaccess';
if (file_exists($htaccess_path)) {
    echo "<p>✅ .htaccess exists</p>";
    echo "<p><strong>Permissions:</strong> " . substr(sprintf('%o', fileperms($htaccess_path)), -4) . "</p>";
    echo "<p><strong>Size:</strong> " . filesize($htaccess_path) . " bytes</p>";
    
    // Show first few lines
    $content = file_get_contents($htaccess_path);
    echo "<p><strong>First 200 characters:</strong></p>";
    echo "<pre>" . htmlspecialchars(substr($content, 0, 200)) . "</pre>";
} else {
    echo "<p>❌ .htaccess not found</p>";
}

// Test 6: Direct CSS Access Test
echo "<h2>Test 6: Direct CSS Access Test</h2>";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$direct_css_url = "$protocol://$host/assets/css/main.min.css";

echo "<p><strong>Direct CSS URL:</strong> <a href='$direct_css_url' target='_blank'>$direct_css_url</a></p>";

// Test 7: Environment Variables
echo "<h2>Test 7: Environment Variables</h2>";
echo "<p><strong>APP_URL:</strong> " . ($_ENV['APP_URL'] ?? 'Not set') . "</p>";
echo "<p><strong>APP_ENV:</strong> " . ($_ENV['APP_ENV'] ?? 'Not set') . "</p>";

// Test 8: Browser Test Instructions
echo "<h2>Test 8: Manual Browser Tests</h2>";
echo "<p><strong>Test these URLs in your browser:</strong></p>";
echo "<ul>";
echo "<li><a href='$direct_css_url' target='_blank'>Direct CSS: $direct_css_url</a></li>";
echo "<li><a href='$protocol://$host/' target='_blank'>Main Website: $protocol://$host/</a></li>";
echo "</ul>";

echo "<h2>Expected Results</h2>";
echo "<p>✅ <strong>Local:</strong> CSS should load and website should be styled</p>";
echo "<p>❌ <strong>cPanel:</strong> If CSS doesn't load, check the HTTP response above</p>";

echo "<h2>Common Issues</h2>";
echo "<ul>";
echo "<li><strong>404 Error:</strong> CSS file not found - check file path</li>";
echo "<li><strong>403 Error:</strong> Permission denied - check file permissions</li>";
echo "<li><strong>500 Error:</strong> Server error - check .htaccess</li>";
echo "<li><strong>Mixed Content:</strong> HTTPS/HTTP mismatch - check asset helper</li>";
echo "</ul>";
?> 