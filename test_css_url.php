<?php
/**
 * Test CSS URL Access
 */

echo "<h1>CSS URL Test</h1>";

// Test 1: Direct file access
echo "<h2>Test 1: Direct File Access</h2>";
$css_path = __DIR__ . '/assets/css/main.min.css';
if (file_exists($css_path)) {
    echo "✅ CSS file exists at: $css_path<br>";
    echo "File size: " . filesize($css_path) . " bytes<br>";
} else {
    echo "❌ CSS file not found at: $css_path<br>";
}

// Test 2: HTTP access
echo "<h2>Test 2: HTTP Access</h2>";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$css_url = "http://$host/assets/css/main.min.css";
echo "Testing URL: <a href='$css_url' target='_blank'>$css_url</a><br>";

// Try to get the CSS content via HTTP
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]
]);

$css_content = @file_get_contents($css_url, false, $context);

if ($css_content !== false) {
    echo "✅ CSS file is accessible via HTTP<br>";
    echo "Content length: " . strlen($css_content) . " bytes<br>";
    echo "First 200 characters:<br>";
    echo "<pre>" . htmlspecialchars(substr($css_content, 0, 200)) . "</pre>";
} else {
    echo "❌ CSS file is NOT accessible via HTTP<br>";
    
    // Check HTTP response headers
    $headers = @get_headers($css_url);
    if ($headers) {
        echo "HTTP Response: " . $headers[0] . "<br>";
    } else {
        echo "Could not get HTTP response headers<br>";
    }
}

// Test 3: Check if .htaccess is working
echo "<h2>Test 3: .htaccess Test</h2>";
echo "<p>If the CSS URL returns a 404, your .htaccess isn't working for static files.</p>";
echo "<p>Try this simple .htaccess:</p>";
echo "<pre>";
echo "RewriteEngine On\n";
echo "\n";
echo "# Serve existing files directly\n";
echo "RewriteCond %{REQUEST_FILENAME} -f\n";
echo "RewriteRule ^ - [L]\n";
echo "\n";
echo "# All other requests go to index.php\n";
echo "RewriteCond %{REQUEST_FILENAME} !-f\n";
echo "RewriteCond %{REQUEST_FILENAME} !-d\n";
echo "RewriteRule ^(.*)$ index.php [QSA,L]\n";
echo "</pre>";

echo "<h2>Quick Fix</h2>";
echo "<p>If the CSS URL shows 404, replace your .htaccess with the simple version above.</p>";
echo "<p>This should make CSS files accessible at: <code>$css_url</code></p>";
?> 