<?php
/**
 * Check what files are in the current directory
 */

echo "<h1>Directory Contents Check</h1>";

// List all files in current directory
echo "<h2>Files in current directory:</h2>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $full_path = __DIR__ . '/' . $file;
        $type = is_dir($full_path) ? 'Directory' : 'File';
        $size = is_file($full_path) ? filesize($full_path) . ' bytes' : '-';
        echo "$type: $file ($size)<br>";
    }
}

// Check if index.php exists
echo "<h2>Index Files Check:</h2>";
$index_files = ['index.php', 'index.html', 'index.htm'];
foreach ($index_files as $index_file) {
    $full_path = __DIR__ . '/' . $index_file;
    if (file_exists($full_path)) {
        echo "✅ $index_file exists<br>";
        echo "Size: " . filesize($full_path) . " bytes<br>";
        echo "Permissions: " . substr(sprintf('%o', fileperms($full_path)), -4) . "<br>";
    } else {
        echo "❌ $index_file not found<br>";
    }
}

// Check public directory
echo "<h2>Public Directory Check:</h2>";
$public_path = __DIR__ . '/public';
if (is_dir($public_path)) {
    echo "✅ public/ directory exists<br>";
    $public_files = scandir($public_path);
    echo "Files in public/: ";
    foreach ($public_files as $file) {
        if ($file != '.' && $file != '..') {
            echo "$file, ";
        }
    }
    echo "<br>";
} else {
    echo "❌ public/ directory not found<br>";
}

echo "<h2>Recommendations:</h2>";
echo "<ul>";
echo "<li>If index.php is missing, create one in the root directory</li>";
echo "<li>If public/index.php exists, make sure your .htaccess redirects to it</li>";
echo "<li>Check that your .htaccess file is in the root directory</li>";
echo "</ul>";
?> 