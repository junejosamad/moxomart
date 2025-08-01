<?php
/**
 * CSS Debug Tool for Moxo Mart
 * This helps identify why CSS is not loading
 */

// Load the helpers
require_once __DIR__ . '/app/Core/helpers.php';

// Load environment if available
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Debug - Moxo Mart</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        pre { background: white; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>CSS Loading Debug Report</h1>
    
    <div class="debug-box">
        <h3>🌐 Environment Information</h3>
        <strong>Domain:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'Unknown' ?><br>
        <strong>Request URI:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'Unknown' ?><br>
        <strong>Document Root:</strong> <?= $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' ?><br>
        <strong>Script Path:</strong> <?= __FILE__ ?><br>
        <strong>APP_URL from ENV:</strong> <?= $_ENV['APP_URL'] ?? 'NOT SET' ?><br>
    </div>

    <div class="debug-box">
        <h3>🔗 URL Generation Test</h3>
        <strong>Base URL:</strong> <?= url() ?><br>
        <strong>CSS Asset URL:</strong> <?= asset('css/main.min.css') ?><br>
        <strong>Logo Asset URL:</strong> <?= asset('images/logo.jpg') ?><br>
    </div>

    <div class="debug-box">
        <h3>📁 File System Check</h3>
        <?php
        $files_to_check = [
            'public/assets/css/main.min.css',
            'public/assets/css/main.scss', 
            'public/assets/images/logo.jpg',
            '.env'
        ];
        
        foreach ($files_to_check as $file) {
            $full_path = __DIR__ . '/' . $file;
            if (file_exists($full_path)) {
                $size = filesize($full_path);
                $permissions = substr(sprintf('%o', fileperms($full_path)), -4);
                echo "<div class='success'>✅ {$file} - Size: {$size} bytes - Permissions: {$permissions}</div>";
            } else {
                echo "<div class='error'>❌ {$file} - NOT FOUND</div>";
            }
        }
        ?>
    </div>

    <div class="debug-box">
        <h3>🌐 HTTP Accessibility Test</h3>
        <?php
        $css_url = asset('css/main.min.css');
        ?>
        <strong>Generated CSS URL:</strong> <?= $css_url ?><br>
        <strong>Protocol:</strong> <?= parse_url($css_url, PHP_URL_SCHEME) ?><br>
        <a href="<?= $css_url ?>" target="_blank">🔗 Click to test CSS file directly</a><br><br>
        
        <strong>Manual Test URLs:</strong><br>
        <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/assets/css/main.min.css" target="_blank">📄 HTTP Direct Test</a><br>
        <a href="https://<?= $_SERVER['HTTP_HOST'] ?>/assets/css/main.min.css" target="_blank">📄 HTTPS Direct Test</a><br>
        
        <script>
            // Test if CSS loads via JavaScript
            fetch('<?= $css_url ?>')
                .then(response => {
                    const status = response.status === 200 ? '✅ SUCCESS' : '❌ FAILED';
                    document.getElementById('css-test-result').innerHTML = 
                        status + ' - HTTP ' + response.status + ' - Type: ' + (response.headers.get('content-type') || 'unknown');
                })
                .catch(error => {
                    document.getElementById('css-test-result').innerHTML = '❌ NETWORK ERROR: ' + error.message;
                });
        </script>
        <div id="css-test-result" class="warning">⏳ Testing...</div>
    </div>

    <div class="debug-box">
        <h3>🔧 Suggested Fixes</h3>
        <?php if (!isset($_ENV['APP_URL']) || $_ENV['APP_URL'] === 'NOT SET'): ?>
        <div class="error">
            <strong>❌ Missing .env file or APP_URL</strong><br>
            Create a .env file with: <code>APP_URL=https://moxomart.store</code>
        </div>
        <?php endif; ?>
        
        <?php if (!file_exists(__DIR__ . '/public/assets/css/main.min.css')): ?>
        <div class="error">
            <strong>❌ CSS file missing</strong><br>
            Run: <code>php compile-scss.php</code> to regenerate CSS
        </div>
        <?php endif; ?>
    </div>

    <div class="debug-box">
        <h3>📋 Quick Actions</h3>
        <button onclick="location.reload()">🔄 Refresh Test</button>
        <button onclick="window.open('<?= url() ?>', '_blank')">🏠 Test Homepage</button>
        <button onclick="window.open('<?= asset('css/main.min.css') ?>', '_blank')">📄 Test CSS Direct</button>
    </div>

    <div class="debug-box warning">
        <strong>⚠️ Security Note:</strong> Delete this file (debug_css.php) after testing!
    </div>

</body>
</html>