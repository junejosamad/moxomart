<?php
/**
 * SCSS Compiler Script
 * Compiles main.scss to main.min.css
 */

require_once 'vendor/autoload.php';

use ScssPhp\ScssPhp\Compiler;

try {
    // Create SCSS compiler
    $scss = new Compiler();
    
    // Set import paths
    $scss->setImportPaths([
        'public/assets/css/',
        'vendor/twbs/bootstrap/scss/' // Bootstrap installed via Composer
    ]);
    
    // Read the SCSS file
    $scssFile = 'public/assets/css/main.scss';
    $cssFile = 'public/assets/css/main.min.css';
    
    if (!file_exists($scssFile)) {
        die("Error: SCSS file not found: $scssFile\n");
    }
    
    $scssContent = file_get_contents($scssFile);
    
    // Compile SCSS to CSS
    echo "Compiling SCSS to CSS...\n";
    $result = $scss->compileString($scssContent);
    $css = $result->getCss();
    
    // Write the compiled CSS
    file_put_contents($cssFile, $css);
    
    echo "✅ Successfully compiled $scssFile to $cssFile\n";
    echo "File size: " . number_format(strlen($css)) . " bytes\n";
    
} catch (Exception $e) {
    echo "❌ Error compiling SCSS: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} 