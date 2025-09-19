<?php
/**
 * Fix for Asset Helper Function
 * This updates the asset() function to work with public directory as web root
 */

echo "<h1>Asset Helper Fix</h1>";

// Check current asset helper
echo "<h2>Current Asset Helper Test</h2>";
try {
    require_once __DIR__ . '/../app/Core/helpers.php';
    $asset_url = asset('css/main.min.css');
    echo "Current asset URL: $asset_url<br>";
    
    // Test if the URL is correct
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $expected_url = "http://$host/assets/css/main.min.css";
    echo "Expected URL: $expected_url<br>";
    
    if ($asset_url === $expected_url) {
        echo "✅ Asset URL is correct!<br>";
    } else {
        echo "❌ Asset URL needs fixing<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Asset Helper Fix</h2>";
echo "<p>If the asset URL is wrong, you need to update the asset() function in <code>app/Core/helpers.php</code>:</p>";

echo "<h3>Current asset() function (lines 25-40):</h3>";
echo "<pre>";
echo "function asset(\$path) {\n";
echo "    // Clean the path\n";
echo "    \$path = ltrim(\$path, '/');\n";
echo "    \n";
echo "    // Get base URL - force HTTP for now\n";
echo "    \$baseUrl = \$_ENV['APP_URL'] ?? null;\n";
echo "    \n";
echo "    // Auto-detect base URL if not set in environment\n";
echo "    if (!\$baseUrl) {\n";
echo "        // Force HTTP since HTTPS redirects are causing issues\n";
echo "        \$host = \$_SERVER['HTTP_HOST'] ?? 'localhost';\n";
echo "        \$baseUrl = 'http://' . \$host;\n";
echo "    }\n";
echo "    \n";
echo "    // Ensure we don't have double /assets/ in the path\n";
echo "    if (strpos(\$path, 'assets/') === 0) {\n";
echo "        return rtrim(\$baseUrl, '/') . '/' . \$path;\n";
echo "    } else {\n";
echo "        return rtrim(\$baseUrl, '/') . '/assets/' . \$path;\n";
echo "    }\n";
echo "}\n";
echo "</pre>";

echo "<h3>This should work correctly with your setup!</h3>";

// Test CSS file existence
echo "<h2>CSS File Check</h2>";
$css_path = __DIR__ . '/assets/css/main.min.css';
echo "CSS Path: $css_path<br>";
echo "File exists: " . (file_exists($css_path) ? '✅ Yes' : '❌ No') . "<br>";

if (file_exists($css_path)) {
    echo "File size: " . filesize($css_path) . " bytes<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms($css_path)), -4) . "<br>";
}

echo "<h2>Test CSS URLs</h2>";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
echo "<ul>";
echo "<li><a href='http://$host/assets/css/main.min.css' target='_blank'>Test CSS: /assets/css/main.min.css</a></li>";
echo "</ul>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update your .htaccess file with the content from htaccess_public_root.txt</li>";
echo "<li>Test the CSS URL above</li>";
echo "<li>If CSS loads, your website should be fully styled</li>";
echo "</ol>";
?> 