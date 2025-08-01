<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = 12;
        $sortBy = $_GET['sort'] ?? 'name';
        $categoryId = $_GET['category'] ?? null;
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;

        // Build conditions
        $conditions = ['status' => 'active'];
        if ($categoryId) {
            $conditions['category_id'] = $categoryId;
        }

        // Get products with pagination
        $result = $this->productModel->paginate($page, $perPage, $conditions, $sortBy . ' ASC');
        
        // Get categories for filter
        $categories = $this->categoryModel->getWithProductCount();
        
        // Get price range
        $priceRange = $this->getPriceRange();
        
        $meta = [
            'title' => 'Products - Moxo Mart',
            'description' => 'Browse our wide selection of quality products at competitive prices.',
        ];

        return $this->render('products/index', [
            'products' => $result,
            'categories' => $categories,
            'priceRange' => $priceRange,
            'currentCategory' => $categoryId,
            'currentSort' => $sortBy,
            'meta' => $meta,
            'selectedCategory' => null // Always pass this for the view
        ]);
    }

    public function show($slug)
    {
        $product = $this->productModel->getBySlug($slug);
        
        if (!$product || $product['status'] !== 'active') {
            http_response_code(404);
            return $this->render('errors/404');
        }

        // Get category name for the product
        $category = $this->categoryModel->find($product['category_id']);
        $product['category_name'] = $category ? $category['name'] : null;

        // Get product images
        $images = $this->productModel->getImages($product['id']);
        $product['images'] = $images; // Add images to product array
        
        // Get product reviews
        $reviews = $this->productModel->getReviews($product['id']);
        $rating = $this->productModel->getAverageRating($product['id']);
        
        // Get related products
        $relatedProducts = $this->productModel->getRelated($product['id'], $product['category_id'], 4);
        
        // Get category breadcrumb
        $breadcrumb = $this->categoryModel->getBreadcrumb($product['category_id']);
        
        // Generate structured data
        $structuredData = $this->generateProductStructuredData($product, $rating);
        
        $meta = [
            'title' => $product['meta_title'] ?: $product['name'] . ' - Moxo Mart',
            'description' => $product['meta_description'] ?: $product['short_description'],
            'og_title' => $product['name'],
            'og_description' => $product['short_description'],
            'og_image' => $images[0]['image_path'] ?? asset('images/placeholder-product.jpg')
        ];

        return $this->render('products/show', [
            'product' => $product,
            'images' => $images,
            'reviews' => $reviews,
            'rating' => $rating,
            'relatedProducts' => $relatedProducts,
            'breadcrumb' => $breadcrumb,
            'structuredData' => $structuredData,
            'meta' => $meta
        ]);
    }

    public function category($slug)
    {
        $category = $this->categoryModel->getBySlug($slug);
        
        if (!$category || !$category['is_active']) {
            http_response_code(404);
            return $this->render('errors/404');
        }

        $page = $_GET['page'] ?? 1;
        $perPage = 12;
        $sortBy = $_GET['sort'] ?? 'name';

        // Get products in category
        $conditions = ['category_id' => $category['id'], 'status' => 'active'];
        $result = $this->productModel->paginate($page, $perPage, $conditions, $sortBy . ' ASC');
        
        // Get subcategories
        $subcategories = $this->categoryModel->getSubCategories($category['id']);
        
        // Get breadcrumb
        $breadcrumb = $this->categoryModel->getBreadcrumb($category['id']);
        
        $meta = [
            'title' => $category['name'] . ' - Moxo Mart',
            'description' => $category['description'] ?: 'Shop ' . $category['name'] . ' products at Moxo Mart',
        ];

        return $this->render('products/category', [
            'category' => $category,
            'products' => $result,
            'subcategories' => $subcategories,
            'breadcrumb' => $breadcrumb,
            'currentSort' => $sortBy,
            'meta' => $meta
        ]);
    }

    public function search()
    {
        $query = $_GET['q'] ?? '';
        $categoryId = $_GET['category'] ?? null;
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $page = $_GET['page'] ?? 1;
        $perPage = 12;

        if (empty($query)) {
            return $this->redirect('products');
        }

        // Search products
        $result = $this->productModel->search($query, $categoryId, $minPrice, $maxPrice, $page, $perPage);
        
        // Get categories for filter
        $categories = $this->categoryModel->getWithProductCount();
        
        $meta = [
            'title' => 'Search Results for "' . e($query) . '" - Moxo Mart',
            'description' => 'Search results for ' . e($query) . ' at Moxo Mart',
        ];

        return $this->render('products/search', [
            'products' => $result,
            'categories' => $categories,
            'query' => $query,
            'currentCategory' => $categoryId,
            'meta' => $meta
        ]);
    }

    private function getPriceRange()
    {
        $stmt = $this->db->prepare("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetch();
    }

    private function generateProductStructuredData($product, $rating)
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => $product['short_description'],
            'sku' => $product['sku'],
            'brand' => [
                '@type' => 'Brand',
                'name' => $product['brand'] ?: 'Moxo Mart'
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $product['price'],
                'priceCurrency' => 'PKR',
                'availability' => $product['stock_quantity'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'Moxo Mart'
                ]
            ]
        ];

        if ($rating['count'] > 0) {
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $rating['average'],
                'reviewCount' => $rating['count']
            ];
        }

        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }
}
