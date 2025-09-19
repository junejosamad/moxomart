<?php
/**
 * Check HTML Source - See what CSS links are being generated
 */

echo "<h1>HTML Source Check</h1>";

// Load the application and get the rendered HTML
try {
    // Start output buffering to capture the HTML
    ob_start();
    
    // Include the main application
    include __DIR__ . '/index.php';
    
    // Get the captured HTML
    $html = ob_get_clean();
    
    // Extract CSS links
    preg_match_all('/<link[^>]*href=["\']([^"\']*\.css[^"\']*)["\'][^>]*>/i', $html, $matches);
    
    echo "<h2>CSS Links Found in HTML:</h2>";
    if (!empty($matches[1])) {
        echo "<ul>";
        foreach ($matches[1] as $css_link) {
            echo "<li><code>$css_link</code></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No CSS links found in HTML</p>";
    }
    
    // Extract the head section
    if (preg_match('/<head[^>]*>(.*?)<\/head>/is', $html, $head_match)) {
        echo "<h2>Head Section:</h2>";
        echo "<pre>" . htmlspecialchars($head_match[1]) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>Error loading application: " . $e->getMessage() . "</p>";
}

// Test asset helper function
echo "<h2>Asset Helper Test</h2>";
try {
    require_once __DIR__ . '/../app/Core/helpers.php';
    $asset_url = asset('css/main.min.css');
    echo "Asset URL generated: <code>$asset_url</code><br>";
    
    // Test if the URL is accessible
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $test_url = "http://$host/assets/css/main.min.css";
    echo "Test URL: <code>$test_url</code><br>";
    
    // Try to access the CSS file
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $css_content = @file_get_contents($test_url, false, $context);
    
    if ($css_content !== false) {
        echo "✅ CSS file is accessible via HTTP<br>";
        echo "CSS content length: " . strlen($css_content) . " bytes<br>";
    } else {
        echo "❌ CSS file is not accessible via HTTP<br>";
        echo "This means the .htaccess rewrite rules aren't working for CSS files<br>";
    }
    
} catch (Exception $e) {
    echo "Error testing asset helper: " . $e->getMessage() . "<br>";
}

echo "<h2>Quick Fix Options</h2>";
echo "<h3>Option 1: Try the alternative .htaccess</h3>";
echo "<p>Use this simpler .htaccess:</p>";
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

echo "<h3>Option 2: Direct CSS link</h3>";
echo "<p>If .htaccess isn't working, update your header.php to use:</p>";
echo "<code>&lt;link href=\"/assets/css/main.min.css\" rel=\"stylesheet\"&gt;</code>";
?> 