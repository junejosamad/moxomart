<?php
/**
 * Advanced CSS Debug Tool
 * Deep dive into CSS loading issues
 */

// Load helpers
require_once __DIR__ . '/app/Core/helpers.php';

// Load environment manually
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
<html>
<head>
    <title>Advanced CSS Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-section { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .warning { border-left-color: #ffc107; background: #fff3cd; }
        pre { background: white; padding: 10px; border: 1px solid #ddd; overflow-x: auto; white-space: pre-wrap; }
        .test-link { display: inline-block; margin: 5px; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 3px; }
        .test-link:hover { background: #0056b3; }
    </style>
</head>
<body>

<h1>🔍 Advanced CSS Debug Report</h1>

<div class="debug-section">
    <h2>🌐 Current Request Info</h2>
    <strong>Protocol:</strong> <?= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'HTTPS' : 'HTTP' ?><br>
    <strong>Host:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'Unknown' ?><br>
    <strong>Request URI:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'Unknown' ?><br>
    <strong>User Agent:</strong> <?= substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 100) ?>...<br>
    <strong>Document Root:</strong> <?= $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' ?><br>
    <strong>Script Filename:</strong> <?= $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown' ?><br>
</div>

<div class="debug-section">
    <h2>🔗 URL Generation Analysis</h2>
    <strong>APP_URL from ENV:</strong> <?= $_ENV['APP_URL'] ?? 'NOT SET' ?><br>
    <strong>Generated base URL:</strong> <?= url() ?><br>
    <strong>Generated CSS URL:</strong> <?= asset('css/main.min.css') ?><br>
    <strong>Generated Logo URL:</strong> <?= asset('images/logo.jpg') ?><br>
</div>

<div class="debug-section">
    <h2>📁 File System Deep Check</h2>
    <?php
    $checks = [
        'Root directory' => __DIR__,
        'Public directory' => __DIR__ . '/public',
        'Assets directory' => __DIR__ . '/public/assets',
        'CSS directory' => __DIR__ . '/public/assets/css',
        'Images directory' => __DIR__ . '/public/assets/images',
        'CSS file' => __DIR__ . '/public/assets/css/main.min.css',
        'SCSS file' => __DIR__ . '/public/assets/css/main.scss',
        'Logo file' => __DIR__ . '/public/assets/images/logo.jpg',
        'Assets .htaccess' => __DIR__ . '/public/assets/.htaccess',
    ];
    
    foreach ($checks as $name => $path) {
        if (file_exists($path)) {
            $isDir = is_dir($path);
            $size = $isDir ? 'Directory' : number_format(filesize($path)) . ' bytes';
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            echo "<div class='success'>✅ <strong>{$name}:</strong> EXISTS - {$size} - Permissions: {$perms}</div>";
            
            if ($name === 'CSS file' && !$isDir) {
                $content = file_get_contents($path);
                $firstLine = explode("\n", $content)[0];
                echo "<div style='margin-left: 20px; font-size: 12px;'>First line: " . htmlspecialchars(substr($firstLine, 0, 100)) . "...</div>";
            }
        } else {
            echo "<div class='error'>❌ <strong>{$name}:</strong> NOT FOUND</div>";
        }
    }
    ?>
</div>

<div class="debug-section">
    <h2>🌐 Live URL Tests</h2>
    <p>Click these links to test different URL patterns:</p>
    
    <?php
    $host = $_SERVER['HTTP_HOST'];
    $testUrls = [
        "HTTP Direct CSS" => "http://{$host}/assets/css/main.min.css",
        "HTTP Public CSS" => "http://{$host}/public/assets/css/main.min.css",
        "HTTPS Direct CSS" => "https://{$host}/assets/css/main.min.css",
        "HTTPS Public CSS" => "https://{$host}/public/assets/css/main.min.css",
        "Generated CSS URL" => asset('css/main.min.css'),
        "Homepage" => url(),
    ];
    
    foreach ($testUrls as $name => $url) {
        echo "<a href='{$url}' target='_blank' class='test-link'>{$name}</a>";
    }
    ?>
</div>

<div class="debug-section">
    <h2>🔧 .htaccess Analysis</h2>
    <?php
    $htaccessFiles = [
        'Root .htaccess' => __DIR__ . '/.htaccess',
        'Public .htaccess' => __DIR__ . '/public/.htaccess',
        'Assets .htaccess' => __DIR__ . '/public/assets/.htaccess',
    ];
    
    foreach ($htaccessFiles as $name => $path) {
        echo "<h4>{$name}:</h4>";
        if (file_exists($path)) {
            $content = file_get_contents($path);
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
        } else {
            echo "<div class='error'>File not found</div>";
        }
    }
    ?>
</div>

<div class="debug-section">
    <h2>🧪 JavaScript Fetch Test</h2>
    <div id="fetch-results"></div>
    
    <script>
    async function testUrls() {
        const urls = [
            '<?= asset("css/main.min.css") ?>',
            'http://<?= $host ?>/assets/css/main.min.css',
            'http://<?= $host ?>/public/assets/css/main.min.css'
        ];
        
        const resultsDiv = document.getElementById('fetch-results');
        resultsDiv.innerHTML = '<p>Testing URLs...</p>';
        
        for (const url of urls) {
            try {
                const response = await fetch(url, { method: 'HEAD' });
                const status = response.status;
                const contentType = response.headers.get('content-type') || 'unknown';
                const statusClass = status === 200 ? 'success' : 'error';
                
                resultsDiv.innerHTML += `<div class="${statusClass}">
                    <strong>${url}</strong><br>
                    Status: ${status} | Type: ${contentType}
                </div>`;
            } catch (error) {
                resultsDiv.innerHTML += `<div class="error">
                    <strong>${url}</strong><br>
                    Error: ${error.message}
                </div>`;
            }
        }
    }
    
    // Run tests after page loads
    window.addEventListener('load', testUrls);
    </script>
</div>

<div class="debug-section warning">
    <h2>⚡ Quick Fixes to Try</h2>
    <ol>
        <li><strong>Recompile CSS:</strong> Run <code>php compile-scss.php</code></li>
        <li><strong>Check file permissions:</strong> CSS should be 644, directories 755</li>
        <li><strong>Clear browser cache:</strong> Ctrl+F5 or hard refresh</li>
        <li><strong>Test direct access:</strong> Visit the HTTP Direct CSS link above</li>
        <li><strong>Check cPanel File Manager:</strong> Verify files exist and have correct permissions</li>
    </ol>
</div>

<div class="debug-section error">
    <h2>🗑️ Cleanup</h2>
    <p><strong>IMPORTANT:</strong> Delete this file and other debug files after testing!</p>
    <ul>
        <li>advanced_debug.php</li>
        <li>debug_css.php</li>
        <li>test_access.php</li>
        <li>test_paths.php</li>
    </ul>
</div>

</body>
</html>