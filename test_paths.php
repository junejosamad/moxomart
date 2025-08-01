<?php
// Quick path testing
require_once 'app/Core/helpers.php';

echo "<h3>Path Testing</h3>";
echo "<strong>CSS Path:</strong> " . asset('css/main.min.css') . "<br>";
echo "<strong>Base URL:</strong> " . url() . "<br>";
echo "<strong>Environment APP_URL:</strong> " . ($_ENV['APP_URL'] ?? 'NOT SET') . "<br>";

if (file_exists('public/assets/css/main.min.css')) {
    echo "<strong>CSS File:</strong> EXISTS<br>";
} else {
    echo "<strong>CSS File:</strong> MISSING<br>";
}

echo "<hr>";
echo "<strong>Full CSS URL:</strong> <a href='" . asset('css/main.min.css') . "' target='_blank'>" . asset('css/main.min.css') . "</a>";
?>