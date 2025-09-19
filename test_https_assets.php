<?php
/**
 * Test HTTPS Asset URLs
 */

// Simulate HTTPS request
$_SERVER['HTTPS'] = 'on';
$_SERVER['HTTP_HOST'] = 'moxomart.store';

// Include the helpers file
require_once __DIR__ . '/app/Core/helpers.php';

echo "<h1>HTTPS Asset URL Test</h1>";

// Test the asset function
echo "<h2>Asset URL Tests</h2>";
echo "<p>Testing asset() function with HTTPS detection:</p>";

$test_paths = [
    'css/main.min.css',
    'js/app.js',
    'images/logo.jpg',
    'assets/css/main.min.css'
];

foreach ($test_paths as $path) {
    $url = asset($path);
    echo "<p><strong>$path</strong>: <code>$url</code></p>";
}

echo "<h2>Expected Results</h2>";
echo "<p>All URLs should start with <code>https://</code> instead of <code>http://</code></p>";

echo "<h2>Test in Browser</h2>";
echo "<p>Visit your website now and check if CSS loads properly!</p>";
echo "<p>Website: <a href='https://moxomart.store' target='_blank'>https://moxomart.store</a></p>";
?> 