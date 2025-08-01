<?php
/**
 * Test file to verify URL access is working correctly
 */

echo "<h2>Moxo Mart Access Test</h2>";

echo "<h3>Path Information:</h3>";
echo "<strong>Current Directory:</strong> " . getcwd() . "<br>";
echo "<strong>Script Directory:</strong> " . __DIR__ . "<br>";
echo "<strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";

// Test if we can load the helper functions
if (file_exists(__DIR__ . '/app/Core/helpers.php')) {
    require_once __DIR__ . '/app/Core/helpers.php';
    
    echo "<h3>Helper Functions Test:</h3>";
    echo "<strong>Base URL:</strong> " . url() . "<br>";
    echo "<strong>CSS Asset URL:</strong> " . asset('css/main.min.css') . "<br>";
    
    // Test if CSS file exists
    $cssPath = __DIR__ . '/public/assets/css/main.min.css';
    echo "<strong>CSS File Status:</strong> " . (file_exists($cssPath) ? 'EXISTS' : 'MISSING') . "<br>";
    
} else {
    echo "<strong>Error:</strong> Could not load helper functions.<br>";
}

echo "<hr>";
echo "<h3>Access Methods:</h3>";
echo "✅ Root Access: <a href='/'>/</a><br>";
echo "✅ Direct Public: <a href='/public/'>/public/</a> (should redirect)<br>";

echo "<hr>";
echo "<small><em>Delete this file after testing for security.</em></small>";
?>