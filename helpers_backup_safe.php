<?php
/**
 * Safe Asset Helper Function
 * Use this if the current asset() function is causing 500 errors
 */

function asset($path) {
    try {
        // Clean the path
        $path = ltrim($path, '/');
        
        // Get base URL from environment or auto-detect
        $baseUrl = $_ENV['APP_URL'] ?? null;
        
        // Auto-detect base URL if not set in environment
        if (!$baseUrl) {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            
            // Safer HTTPS detection
            $isHttps = false;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                $isHttps = true;
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $isHttps = true;
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
                $isHttps = true;
            }
            
            $protocol = $isHttps ? 'https' : 'http';
            $baseUrl = $protocol . '://' . $host;
        }
        
        // Ensure we don't have double /assets/ in the path
        if (strpos($path, 'assets/') === 0) {
            return rtrim($baseUrl, '/') . '/' . $path;
        } else {
            return rtrim($baseUrl, '/') . '/assets/' . $path;
        }
    } catch (Exception $e) {
        // Fallback to simple HTTP if anything goes wrong
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return 'http://' . $host . '/assets/' . ltrim($path, '/');
    }
}

// Test the function
if (basename(__FILE__) === 'helpers_backup_safe.php') {
    echo "<h1>Safe Asset Helper Test</h1>";
    
    // Test with HTTPS
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['HTTP_HOST'] = 'moxomart.store';
    
    $test_url = asset('css/main.min.css');
    echo "Asset URL: <code>$test_url</code><br>";
    
    if (strpos($test_url, 'https://') === 0) {
        echo "✅ HTTPS URL generated correctly<br>";
    } else {
        echo "❌ URL is not HTTPS<br>";
    }
}
?> 