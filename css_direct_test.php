<?php
/**
 * CSS Direct Access Test
 */

echo "<h1>CSS Direct Access Test</h1>";

// Test 1: Check if CSS file exists and is readable
echo "<h2>Test 1: File Existence</h2>";
$css_path = __DIR__ . '/assets/css/main.min.css';
echo "CSS Path: $css_path<br>";
echo "File exists: " . (file_exists($css_path) ? '✅ Yes' : '❌ No') . "<br>";
echo "File readable: " . (is_readable($css_path) ? '✅ Yes' : '❌ No') . "<br>";

if (file_exists($css_path)) {
    echo "File size: " . filesize($css_path) . " bytes<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms($css_path)), -4) . "<br>";
}

// Test 2: Try to read and output CSS content
echo "<h2>Test 2: CSS Content Check</h2>";
if (file_exists($css_path) && is_readable($css_path)) {
    $css_content = file_get_contents($css_path);
    $first_100_chars = substr($css_content, 0, 100);
    echo "First 100 characters of CSS:<br>";
    echo "<pre>" . htmlspecialchars($first_100_chars) . "</pre>";
    
    if (strpos($css_content, 'bootstrap') !== false) {
        echo "✅ CSS contains Bootstrap styles<br>";
    } else {
        echo "❌ CSS doesn't contain Bootstrap (might be empty or wrong file)<br>";
    }
} else {
    echo "❌ Cannot read CSS file<br>";
}

// Test 3: Check current .htaccess
echo "<h2>Test 3: Current .htaccess</h2>";
$htaccess_path = __DIR__ . '/.htaccess';
if (file_exists($htaccess_path)) {
    echo "✅ .htaccess exists<br>";
    $htaccess_content = file_get_contents($htaccess_path);
    echo "First 10 lines of .htaccess:<br>";
    echo "<pre>" . htmlspecialchars(substr($htaccess_content, 0, 500)) . "</pre>";
} else {
    echo "❌ .htaccess not found<br>";
}

// Test 4: Test different CSS URLs
echo "<h2>Test 4: CSS URL Tests</h2>";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
echo "<p>Click these links to test CSS access:</p>";
echo "<ul>";
echo "<li><a href='http://$host/assets/css/main.min.css' target='_blank'>Method 1: /assets/css/main.min.css</a></li>";
echo "<li><a href='http://$host/assets/css/main.min.css' target='_blank'>Method 2: Direct CSS (should show CSS content)</a></li>";
echo "</ul>";

// Test 5: Check if .htaccess is being processed
echo "<h2>Test 5: .htaccess Processing</h2>";
echo "<p>If you see this page, .htaccess is working for PHP files.</p>";
echo "<p>If CSS URLs show '404 Not Found', the .htaccess rewrite rules aren't working.</p>";

// Test 6: Alternative CSS serving method
echo "<h2>Test 6: Alternative CSS Serving</h2>";
echo "<p>If the above doesn't work, try this alternative .htaccess:</p>";
echo "<pre>";
echo "RewriteEngine On\n";
echo "\n";
echo "# Serve CSS files directly\n";
echo "RewriteCond %{REQUEST_URI} \\.css$ [NC]\n";
echo "RewriteCond %{REQUEST_FILENAME} -f\n";
echo "RewriteRule ^(.*)$ $1 [L]\n";
echo "\n";
echo "# All other requests go to index.php\n";
echo "RewriteCond %{REQUEST_FILENAME} !-f\n";
echo "RewriteCond %{REQUEST_FILENAME} !-d\n";
echo "RewriteRule ^(.*)$ index.php [QSA,L]\n";
echo "</pre>";

echo "<h2>Debugging Steps</h2>";
echo "<ol>";
echo "<li>Check if CSS file exists and is readable</li>";
echo "<li>Test direct CSS URL access</li>";
echo "<li>Verify .htaccess content</li>";
echo "<li>Check server error logs</li>";
echo "<li>Try alternative .htaccess rules</li>";
echo "</ol>";
?> 