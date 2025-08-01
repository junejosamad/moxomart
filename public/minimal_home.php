<?php
// Minimal working homepage test

// Load the application
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once ROOT_PATH . '/vendor/autoload.php';

if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
}

session_start();
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/routes.php';
require_once APP_PATH . '/Core/helpers.php';

// Simple homepage
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moxo Mart - Minimal Test</title>
    <link href="<?= asset('css/main.min.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <nav class="navbar">
                <a href="#" class="logo">Moxo Mart</a>
                <ul class="nav-menu">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Products</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="hero">
                <div class="container">
                    <h1>Welcome to Moxo Mart</h1>
                    <p>Your trusted e-commerce partner</p>
                    <a href="#" class="btn">Shop Now</a>
                </div>
            </section>

            <section class="featured-products">
                <div class="container">
                    <h2>Featured Products</h2>
                    <div class="products-grid">
                        <?php
                        try {
                            $productModel = new App\Models\Product();
                            $featuredProducts = $productModel->getFeatured(4);
                            
                            foreach ($featuredProducts as $product) {
                                echo '<div class="product-card">';
                                echo '<div class="product-image">';
                                echo '<img src="' . asset('images/placeholder-product.jpg') . '" alt="' . e($product['name']) . '">';
                                echo '</div>';
                                echo '<div class="product-info">';
                                echo '<h3 class="product-title">' . e($product['name']) . '</h3>';
                                echo '<p class="product-price">$' . number_format($product['price'], 2) . '</p>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } catch (Exception $e) {
                            echo '<p>Error loading products: ' . $e->getMessage() . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="container">
                <p>&copy; 2025 Moxo Mart. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html> 