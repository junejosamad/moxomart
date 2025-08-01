<?php
/**
 * CSS Path Fix Tool
 * Creates absolute path CSS links to bypass URL issues
 */

require_once __DIR__ . '/app/Core/helpers.php';

// Check current URL generation
$currentAssetUrl = asset('css/main.min.css');
echo "<h1>CSS Path Fix Tool</h1>";

echo "<h2>Current Asset URL:</h2>";
echo "<p><strong>Generated:</strong> " . $currentAssetUrl . "</p>";

// Check if CSS file exists
$cssPath = __DIR__ . '/public/assets/css/main.min.css';
$cssExists = file_exists($cssPath);
$cssSize = $cssExists ? filesize($cssPath) : 0;

echo "<h2>CSS File Status:</h2>";
echo "<p><strong>File exists:</strong> " . ($cssExists ? 'YES' : 'NO') . "</p>";
echo "<p><strong>File size:</strong> " . number_format($cssSize) . " bytes</p>";
echo "<p><strong>File path:</strong> " . $cssPath . "</p>";

if ($cssExists) {
    // Create different URL variants to test
    $host = $_SERVER['HTTP_HOST'];
    echo "<h2>Test these URLs (right-click → Open in new tab):</h2>";
    echo "<ol>";
    echo "<li><a href='http://{$host}/assets/css/main.min.css' target='_blank'>Method 1: /assets/css/main.min.css</a></li>";
    echo "<li><a href='http://{$host}/public/assets/css/main.min.css' target='_blank'>Method 2: /public/assets/css/main.min.css</a></li>";
    echo "<li><a href='{$currentAssetUrl}' target='_blank'>Method 3: Generated URL</a></li>";
    echo "</ol>";
    
    // Try to output first few lines of CSS
    $cssContent = file_get_contents($cssPath);
    $firstLines = implode("\n", array_slice(explode("\n", $cssContent), 0, 5));
    echo "<h2>CSS File Preview (first 5 lines):</h2>";
    echo "<pre>" . htmlspecialchars($firstLines) . "</pre>";
    
    // Create a working CSS link
    echo "<h2>🔧 Quick Fix - Use This CSS Link:</h2>";
    echo '<p>Replace your current CSS link in header.php with one of these:</p>';
    echo '<h3>Option 1 (Recommended): Direct Path</h3>';
    echo '<code>&lt;link href="/assets/css/main.min.css" rel="stylesheet"&gt;</code><br><br>';
    
    echo '<h3>Option 2: Full URL</h3>';
    echo '<code>&lt;link href="http://' . $host . '/assets/css/main.min.css" rel="stylesheet"&gt;</code><br><br>';
    
    echo '<h3>Option 3: If above don\'t work, try public path</h3>';
    echo '<code>&lt;link href="/public/assets/css/main.min.css" rel="stylesheet"&gt;</code>';
    
} else {
    echo "<p style='color: red;'><strong>ERROR:</strong> CSS file not found! Run: <code>php compile-scss.php</code></p>";
}
?>