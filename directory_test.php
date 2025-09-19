<?php
/**
 * Directory Test - Check what's different about this directory
 */

echo "<h1>Directory Structure Test</h1>";

// Check current directory
echo "<h2>Current Directory</h2>";
echo "Current path: " . __DIR__ . "<br>";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Relative to doc root: " . str_replace($_SERVER['DOCUMENT_ROOT'] ?? '', '', __DIR__) . "<br>";

// Check directory permissions
echo "<h2>Directory Permissions</h2>";
$current_dir = __DIR__;
echo "Current directory permissions: " . substr(sprintf('%o', fileperms($current_dir)), -4) . "<br>";
echo "Current directory readable: " . (is_readable($current_dir) ? '✅ Yes' : '❌ No') . "<br>";
echo "Current directory writable: " . (is_writable($current_dir) ? '✅ Yes' : '❌ No') . "<br>";

// Check parent directory
$parent_dir = dirname($current_dir);
echo "Parent directory: $parent_dir<br>";
echo "Parent permissions: " . substr(sprintf('%o', fileperms($parent_dir)), -4) . "<br>";

// Check if .htaccess exists and its permissions
echo "<h2>.htaccess File</h2>";
$htaccess_file = $current_dir . '/.htaccess';
if (file_exists($htaccess_file)) {
    echo ".htaccess exists: ✅<br>";
    echo ".htaccess permissions: " . substr(sprintf('%o', fileperms($htaccess_file)), -4) . "<br>";
    echo ".htaccess readable: " . (is_readable($htaccess_file) ? '✅ Yes' : '❌ No') . "<br>";
    echo ".htaccess size: " . filesize($htaccess_file) . " bytes<br>";
    
    // Show first few lines
    $content = file_get_contents($htaccess_file);
    $lines = explode("\n", $content);
    echo "<h3>First 5 lines of .htaccess:</h3>";
    echo "<pre>";
    for ($i = 0; $i < min(5, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
} else {
    echo ".htaccess exists: ❌<br>";
}

// Check for other .htaccess files
echo "<h2>Other .htaccess Files</h2>";
$directories = ['public', 'app', 'config', 'vendor'];
foreach ($directories as $dir) {
    $htaccess_path = $current_dir . '/' . $dir . '/.htaccess';
    if (file_exists($htaccess_path)) {
        echo "$dir/.htaccess: ✅ Exists<br>";
    } else {
        echo "$dir/.htaccess: ❌ Not found<br>";
    }
}

// Check server configuration
echo "<h2>Server Configuration</h2>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Check if this is a subdirectory with restrictions
$path_parts = explode('/', trim(str_replace($_SERVER['DOCUMENT_ROOT'] ?? '', '', __DIR__), '/'));
echo "Directory depth: " . count($path_parts) . "<br>";
echo "Path parts: " . implode(' → ', $path_parts) . "<br>";

echo "<h2>Recommendations</h2>";
echo "<ul>";
echo "<li>If this is a subdirectory, try moving files to the root public_html</li>";
echo "<li>Check if your hosting provider has restrictions on subdirectories</li>";
echo "<li>Try creating a simple .htaccess with just 'RewriteEngine On'</li>";
echo "<li>Contact your hosting provider about .htaccess restrictions</li>";
echo "</ul>";
?> 