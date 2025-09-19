<?php
/**
 * CSS Test - Upload this to your cPanel root to test CSS access
 */

echo "<h1>CSS Access Test</h1>";

// Test different paths
$paths = [
    'public/assets/css/main.min.css',
    'assets/css/main.min.css',
    'css/main.min.css'
];

foreach ($paths as $path) {
    $full_path = __DIR__ . '/' . $path;
    echo "<h3>Testing: $path</h3>";
    echo "Full path: $full_path<br>";
    echo "Exists: " . (file_exists($full_path) ? '✅ Yes' : '❌ No') . "<br>";
    echo "Readable: " . (is_readable($full_path) ? '✅ Yes' : '❌ No') . "<br>";
    if (file_exists($full_path)) {
        echo "Size: " . filesize($full_path) . " bytes<br>";
        echo "Permissions: " . substr(sprintf('%o', fileperms($full_path)), -4) . "<br>";
    }
    echo "<hr>";
}

// Test URL generation
echo "<h2>URL Tests</h2>";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
echo "Host: $host<br>";
echo "Direct CSS URL: <a href='http://$host/public/assets/css/main.min.css' target='_blank'>http://$host/public/assets/css/main.min.css</a><br>";
echo "Asset CSS URL: <a href='http://$host/assets/css/main.min.css' target='_blank'>http://$host/assets/css/main.min.css</a><br>";
?> 