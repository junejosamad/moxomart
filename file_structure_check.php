<?php
/**
 * File Structure Check - See what's actually on the server
 */

echo "<h1>File Structure Check</h1>";

// Check current directory
echo "<h2>Current Directory</h2>";
echo "Current path: " . __DIR__ . "<br>";

// List all files and directories
echo "<h2>Root Directory Contents</h2>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $full_path = __DIR__ . '/' . $file;
        $type = is_dir($full_path) ? 'Directory' : 'File';
        $size = is_file($full_path) ? filesize($full_path) . ' bytes' : '-';
        echo "$type: $file ($size)<br>";
    }
}

// Check public directory
echo "<h2>Public Directory Contents</h2>";
$public_path = __DIR__ . '/public';
if (is_dir($public_path)) {
    $public_files = scandir($public_path);
    foreach ($public_files as $file) {
        if ($file != '.' && $file != '..') {
            $full_path = $public_path . '/' . $file;
            $type = is_dir($full_path) ? 'Directory' : 'File';
            $size = is_file($full_path) ? filesize($full_path) . ' bytes' : '-';
            echo "$type: $file ($size)<br>";
        }
    }
} else {
    echo "❌ public/ directory not found<br>";
}

// Check assets directory
echo "<h2>Assets Directory Check</h2>";
$assets_path = __DIR__ . '/public/assets';
if (is_dir($assets_path)) {
    echo "✅ public/assets/ exists<br>";
    
    // Check CSS directory
    $css_path = $assets_path . '/css';
    if (is_dir($css_path)) {
        echo "✅ public/assets/css/ exists<br>";
        $css_files = scandir($css_path);
        foreach ($css_files as $file) {
            if ($file != '.' && $file != '..') {
                $full_path = $css_path . '/' . $file;
                $size = is_file($full_path) ? filesize($full_path) . ' bytes' : '-';
                echo "CSS File: $file ($size)<br>";
            }
        }
    } else {
        echo "❌ public/assets/css/ not found<br>";
    }
} else {
    echo "❌ public/assets/ not found<br>";
}

// Check .htaccess files
echo "<h2>.htaccess Files</h2>";
$htaccess_files = [
    'Root .htaccess' => __DIR__ . '/.htaccess',
    'Public .htaccess' => __DIR__ . '/public/.htaccess'
];

foreach ($htaccess_files as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name exists<br>";
    } else {
        echo "❌ $name not found<br>";
    }
}

echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Make sure your .htaccess is in the root directory (not public/)</li>";
echo "<li>Check if CSS files are in the correct location</li>";
echo "<li>Verify the file structure matches the expected layout</li>";
echo "</ol>";
?> 