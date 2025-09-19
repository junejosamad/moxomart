<?php
/**
 * CSS Fix Test - Check if CSS files are accessible
 */

echo "<h1>CSS Loading Test</h1>";

// Check if CSS file exists
$css_path = __DIR__ . '/public/assets/css/main.min.css';
echo "<h2>CSS File Check</h2>";
echo "CSS Path: $css_path<br>";
echo "File exists: " . (file_exists($css_path) ? '✅ Yes' : '❌ No') . "<br>";
if (file_exists($css_path)) {
    echo "File size: " . filesize($css_path) . " bytes<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms($css_path)), -4) . "<br>";
}

// Test different CSS URLs
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
echo "<h2>CSS URL Tests</h2>";
echo "<p>Click these links to test CSS loading:</p>";
echo "<ul>";
echo "<li><a href='http://$host/assets/css/main.min.css' target='_blank'>Method 1: /assets/css/main.min.css</a></li>";
echo "<li><a href='http://$host/public/assets/css/main.min.css' target='_blank'>Method 2: /public/assets/css/main.min.css</a></li>";
echo "</ul>";

// Check current .htaccess
echo "<h2>Current .htaccess Check</h2>";
$htaccess_path = __DIR__ . '/.htaccess';
if (file_exists($htaccess_path)) {
    echo "✅ .htaccess exists<br>";
    $content = file_get_contents($htaccess_path);
    echo "<h3>Current .htaccess content:</h3>";
    echo "<pre>" . htmlspecialchars($content) . "</pre>";
} else {
    echo "❌ .htaccess not found<br>";
}

// Test asset helper function
echo "<h2>Asset Helper Test</h2>";
try {
    require_once __DIR__ . '/app/Core/helpers.php';
    $asset_url = asset('css/main.min.css');
    echo "Asset URL generated: $asset_url<br>";
} catch (Exception $e) {
    echo "Error loading helpers: " . $e->getMessage() . "<br>";
}

echo "<h2>Quick Fix Options</h2>";
echo "<h3>Option 1: Update .htaccess for CSS</h3>";
echo "<pre>";
echo "RewriteEngine On\n";
echo "\n";
echo "# Handle CSS, JS, images directly\n";
echo "RewriteCond %{REQUEST_URI} \\.(css|js|png|jpg|jpeg|gif|svg|ico)$ [NC]\n";
echo "RewriteRule ^assets/(.*)$ public/assets/$1 [L]\n";
echo "\n";
echo "# Handle existing files\n";
echo "RewriteCond %{REQUEST_FILENAME} -f [OR]\n";
echo "RewriteCond %{REQUEST_FILENAME} -d\n";
echo "RewriteRule ^ - [L]\n";
echo "\n";
echo "# All other requests go to index.php\n";
echo "RewriteRule ^(.*)$ index.php [QSA,L]\n";
echo "</pre>";

echo "<h3>Option 2: Direct CSS Link</h3>";
echo "<p>If the above doesn't work, update your header.php to use:</p>";
echo "<code>&lt;link href=\"/public/assets/css/main.min.css\" rel=\"stylesheet\"&gt;</code>";
?> 