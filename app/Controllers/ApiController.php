<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ApiController extends Controller
{
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }

    public function subscribeNewsletter()
    {
        $errors = validate($_POST, [
            'email' => 'required|email|max:255'
        ]);

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors], 400);
        }

        try {
            // Check if already subscribed
            $stmt = $this->db->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            $existing = $stmt->fetch();

            if ($existing) {
                return $this->json(['success' => false, 'message' => 'Email already subscribed'], 400);
            }

            // Add subscription
            $stmt = $this->db->prepare("INSERT INTO newsletter_subscriptions (email, status, subscribed_at) VALUES (?, 'active', NOW())");
            $stmt->execute([$_POST['email']]);

            logActivity('newsletter_subscription', "Newsletter subscription: {$_POST['email']}", null);

            return $this->json([
                'success' => true,
                'message' => 'Successfully subscribed to newsletter'
            ]);

        } catch (\Exception $e) {
            error_log("Newsletter subscription error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Subscription failed'], 500);
        }
    }

    public function searchProducts()
    {
        $query = $_GET['q'] ?? '';
        $limit = min((int)($_GET['limit'] ?? 10), 50); // Max 50 results
        
        if (strlen($query) < 2) {
            return $this->json(['products' => []]);
        }

        try {
            $sql = "SELECT id, name, slug, price, image, stock_quantity 
                    FROM products 
                    WHERE status = 'active' 
                    AND (name LIKE ? OR description LIKE ? OR sku LIKE ?) 
                    ORDER BY 
                        CASE WHEN name LIKE ? THEN 1 ELSE 2 END,
                        name ASC 
                    LIMIT ?";

            $searchTerm = "%{$query}%";
            $exactMatch = "{$query}%";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $exactMatch, $limit]);
            $products = $stmt->fetchAll();

            // Format results
            $results = array_map(function($product) {
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'price' => formatPrice($product['price']),
                    'image' => $product['image'] ? asset($product['image']) : null,
                    'in_stock' => $product['stock_quantity'] > 0,
                    'url' => url("/products/{$product['slug']}")
                ];
            }, $products);

            return $this->json(['products' => $results]);

        } catch (\Exception $e) {
            error_log("Product search error: " . $e->getMessage());
            return $this->json(['products' => []], 500);
        }
    }

    public function submitReview()
    {
        $user = getCurrentUser();

        $errors = validate($_POST, [
            'product_id' => 'required|numeric',
            'rating' => 'required|numeric',
            'title' => 'max:255',
            'review' => 'required|max:1000'
        ]);

        // Validate rating range
        $rating = (int)($_POST['rating'] ?? 0);
        if ($rating < 1 || $rating > 5) {
            $errors['rating'][] = 'Rating must be between 1 and 5 stars.';
        }

        // Check if product exists
        $product = $this->productModel->find($_POST['product_id']);
        if (!$product || $product['status'] !== 'active') {
            $errors['product_id'][] = 'Invalid product.';
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors], 400);
        }

        try {
            // Check if user already reviewed this product
            $stmt = $this->db->prepare("SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ?");
            $stmt->execute([$_POST['product_id'], $user['id']]);
            $existing = $stmt->fetch();

            if ($existing) {
                return $this->json(['success' => false, 'message' => 'You have already reviewed this product'], 400);
            }

            // Add review
            $stmt = $this->db->prepare("INSERT INTO product_reviews (product_id, user_id, rating, title, review, is_approved, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
            $stmt->execute([
                $_POST['product_id'],
                $user['id'],
                $rating,
                $_POST['title'] ?? null,
                $_POST['review']
            ]);

            logActivity('product_review_submitted', "Review submitted for product ID: {$_POST['product_id']}");

            return $this->json([
                'success' => true,
                'message' => 'Review submitted successfully. It will be published after moderation.'
            ]);

        } catch (\Exception $e) {
            error_log("Review submission error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to submit review'], 500);
        }
    }
} 