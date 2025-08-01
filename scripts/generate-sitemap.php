<?php
/**
 * Script to generate sitemap.xml and robots.txt for SadaCart.
 *
 * Usage: php scripts/generate-sitemap.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/helpers.php'; // For asset() and url() functions
require_once __DIR__ . '/../config/database.php'; // For DB connection config

use App\Core\Database;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Initialize database connection
$dbConfig = [
    'DB_CONNECTION' => $_ENV['DB_CONNECTION'],
    'DB_HOST' => $_ENV['DB_HOST'],
    'DB_PORT' => $_ENV['DB_PORT'],
    'DB_DATABASE' => $_ENV['DB_DATABASE'],
    'DB_USERNAME' => $_ENV['DB_USERNAME'],
    'DB_PASSWORD' => $_ENV['DB_PASSWORD'],
];
$db = new Database($dbConfig);
$db->connect();

$appUrl = rtrim($_ENV['APP_URL'], '/');
$sitemapPath = __DIR__ . '/../public/sitemap.xml';
$robotsPath = __DIR__ . '/../public/robots.txt';

echo "Generating sitemap.xml and robots.txt...\n";

// --- Generate Sitemap XML ---
$sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$sitemapContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Add static pages
$staticPages = [
    '/',
    '/products',
    '/cart',
    '/login',
    '/register',
    '/forgot-password',
    '/blog',
    '/about',
    '/faq',
    '/contact',
];

foreach ($staticPages as $page) {
    $sitemapContent .= "  <url>\n";
    $sitemapContent .= "    <loc>" . $appUrl . $page . "</loc>\n";
    $sitemapContent .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    $sitemapContent .= "    <changefreq>daily</changefreq>\n";
    $sitemapContent .= "    <priority>0.8</priority>\n";
    $sitemapContent .= "  </url>\n";
}

// Add dynamic products
try {
    $products = $db->query("SELECT slug, updated_at FROM products WHERE status = 'active'")->fetchAll();
    foreach ($products as $product) {
        $sitemapContent .= "  <url>\n";
        $sitemapContent .= "    <loc>" . $appUrl . '/products/' . $product['slug'] . "</loc>\n";
        $sitemapContent .= "    <lastmod>" . date('Y-m-d', strtotime($product['updated_at'])) . "</lastmod>\n";
        $sitemapContent .= "    <changefreq>weekly</changefreq>\n";
        $sitemapContent .= "    <priority>0.9</priority>\n";
        $sitemapContent .= "  </url>\n";
    }
} catch (PDOException $e) {
    echo "Warning: Could not fetch products for sitemap. Database error: " . $e->getMessage() . "\n";
}

// Add dynamic blog posts
try {
    $posts = $db->query("SELECT slug, updated_at FROM blog_posts WHERE status = 'published'")->fetchAll();
    foreach ($posts as $post) {
        $sitemapContent .= "  <url>\n";
        $sitemapContent .= "    <loc>" . $appUrl . '/blog/' . $post['slug'] . "</loc>\n";
        $sitemapContent .= "    <lastmod>" . date('Y-m-d', strtotime($post['updated_at'])) . "</lastmod>\n";
        $sitemapContent .= "    <changefreq>weekly</changefreq>\n";
        $sitemapContent .= "    <priority>0.7</priority>\n";
        $sitemapContent .= "  </url>\n";
    }
} catch (PDOException $e) {
    echo "Warning: Could not fetch blog posts for sitemap. Database error: " . $e->getMessage() . "\n";
}


$sitemapContent .= '</urlset>';

file_put_contents($sitemapPath, $sitemapContent);
echo "sitemap.xml generated at: " . $sitemapPath . "\n";

// --- Generate robots.txt ---
$robotsContent = "User-agent: *\n";
$robotsContent .= "Allow: /\n";
$robotsContent .= "Sitemap: " . $appUrl . "/sitemap.xml\n";

file_put_contents($robotsPath, $robotsContent);
echo "robots.txt generated at: " . $robotsPath . "\n";

$db->close();
echo "Sitemap and robots.txt generation complete.\n";
