<?php
/**
 * Safe Asset Helper Test
 */

echo "<h1>Safe Asset Helper Test</h1>";

// Test 1: Check if helpers file exists
echo "<h2>Test 1: File Existence</h2>";
$helpers_path = __DIR__ . '/app/Core/helpers.php';
if (file_exists($helpers_path)) {
    echo "✅ helpers.php exists<br>";
} else {
    echo "❌ helpers.php not found<br>";
    exit;
}

// Test 2: Check if we can include the file
echo "<h2>Test 2: File Inclusion</h2>";
try {
    require_once $helpers_path;
    echo "✅ helpers.php included successfully<br>";
} catch (Exception $e) {
    echo "❌ Error including helpers.php: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Check if asset function exists
echo "<h2>Test 3: Function Existence</h2>";
if (function_exists('asset')) {
    echo "✅ asset() function exists<br>";
} else {
    echo "❌ asset() function not found<br>";
    exit;
}

// Test 4: Test asset function with safe values
echo "<h2>Test 4: Asset Function Test</h2>";
try {
    // Set safe server variables
    $_SERVER['HTTP_HOST'] = 'moxomart.store';
    $_SERVER['HTTPS'] = 'on';
    
    $test_url = asset('css/main.min.css');
    echo "✅ Asset URL generated: <code>$test_url</code><br>";
    
    if (strpos($test_url, 'https://') === 0) {
        echo "✅ URL uses HTTPS protocol<br>";
    } else {
        echo "❌ URL does not use HTTPS protocol<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error calling asset(): " . $e->getMessage() . "<br>";
}

echo "<h2>Next Steps</h2>";
echo "<p>If all tests pass, the issue might be elsewhere. Check your server error logs.</p>";
echo "<p>If any test fails, we need to fix the helpers.php file.</p>";
?> 