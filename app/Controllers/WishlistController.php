<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class WishlistController extends Controller
{
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->productModel = new Product();
    }

    public function index()
    {
        $user = getCurrentUser();
        $wishlistItems = $this->getWishlistItems($user['id']);

        $meta = [
            'title' => 'My Wishlist - Moxo Mart',
            'description' => 'Your saved products'
        ];

        return $this->render('wishlist/index', [
            'wishlistItems' => $wishlistItems,
            'meta' => $meta
        ]);
    }

    public function add()
    {
        $productId = $_POST['product_id'] ?? null;

        if (!$productId || !is_numeric($productId)) {
            return $this->json(['success' => false, 'message' => 'Invalid product ID'], 400);
        }

        // Check if product exists and is active
        $product = $this->productModel->find($productId);
        if (!$product || $product['status'] !== 'active') {
            return $this->json(['success' => false, 'message' => 'Product not available'], 404);
        }

        $user = getCurrentUser();

        try {
            // Check if already in wishlist
            if ($this->isInWishlist($user['id'], $productId)) {
                return $this->json(['success' => false, 'message' => 'Product already in wishlist'], 400);
            }

            // Add to wishlist
            $this->addToWishlist($user['id'], $productId);

            logActivity('wishlist_item_added', "Product '{$product['name']}' added to wishlist");

            return $this->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully'
            ]);

        } catch (\Exception $e) {
            error_log("Wishlist add error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to add item to wishlist'], 500);
        }
    }

    public function remove()
    {
        $productId = $_POST['product_id'] ?? null;

        if (!$productId || !is_numeric($productId)) {
            return $this->json(['success' => false, 'message' => 'Invalid product ID'], 400);
        }

        $user = getCurrentUser();

        try {
            // Check if in wishlist
            if (!$this->isInWishlist($user['id'], $productId)) {
                return $this->json(['success' => false, 'message' => 'Product not in wishlist'], 400);
            }

            // Remove from wishlist
            $this->removeFromWishlist($user['id'], $productId);

            $product = $this->productModel->find($productId);
            $productName = $product ? $product['name'] : 'Product';

            logActivity('wishlist_item_removed', "Product '{$productName}' removed from wishlist");

            return $this->json([
                'success' => true,
                'message' => 'Product removed from wishlist successfully'
            ]);

        } catch (\Exception $e) {
            error_log("Wishlist remove error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to remove item from wishlist'], 500);
        }
    }

    private function getWishlistItems($userId)
    {
        $sql = "SELECT p.*, w.created_at as added_to_wishlist 
                FROM wishlist w 
                JOIN products p ON w.product_id = p.id 
                WHERE w.user_id = ? AND p.status = 'active'
                ORDER BY w.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    private function isInWishlist($userId, $productId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    }

    private function addToWishlist($userId, $productId)
    {
        $stmt = $this->db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $productId]);
    }

    private function removeFromWishlist($userId, $productId)
    {
        $stmt = $this->db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }
} 