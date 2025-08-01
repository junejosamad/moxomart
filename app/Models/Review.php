<?php

namespace App\Models;

use App\Core\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $fillable = ['user_id', 'product_id', 'rating', 'title', 'comment', 'verified_purchase', 'helpful_count', 'status'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all reviews for a product
     */
    public function getProductReviews($productId, $limit = null, $offset = 0)
    {
        $sql = "SELECT r.*, u.name as user_name, u.avatar 
                FROM {$this->table} r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? AND r.status = 'approved' 
                ORDER BY r.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->query($sql, [$productId, $limit, $offset])->fetchAll();
        }
        
        return $this->db->query($sql, [$productId])->fetchAll();
    }

    /**
     * Get user's reviews
     */
    public function getUserReviews($userId, $limit = null)
    {
        $sql = "SELECT r.*, p.name as product_name, p.image as product_image 
                FROM {$this->table} r 
                LEFT JOIN products p ON r.product_id = p.id 
                WHERE r.user_id = ? 
                ORDER BY r.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->query($sql, [$userId, $limit])->fetchAll();
        }
        
        return $this->db->query($sql, [$userId])->fetchAll();
    }

    /**
     * Get average rating for a product
     */
    public function getProductRating($productId)
    {
        $sql = "SELECT 
                    AVG(rating) as average_rating,
                    COUNT(*) as total_reviews,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                FROM {$this->table} 
                WHERE product_id = ? AND status = 'approved'";
        
        return $this->db->query($sql, [$productId])->fetch();
    }

    /**
     * Check if user can review product (purchased and not reviewed)
     */
    public function canUserReview($userId, $productId)
    {
        // Check if user purchased the product
        $purchaseCheck = $this->db->query(
            "SELECT COUNT(*) as count FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'",
            [$userId, $productId]
        )->fetch();

        if ($purchaseCheck['count'] == 0) {
            return false;
        }

        // Check if user already reviewed
        $reviewCheck = $this->db->query(
            "SELECT COUNT(*) as count FROM {$this->table} 
             WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        )->fetch();

        return $reviewCheck['count'] == 0;
    }

    /**
     * Create a new review
     */
    public function createReview($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = 'pending'; // Reviews need approval
        
        return $this->create($data);
    }

    /**
     * Update helpful count
     */
    public function updateHelpfulCount($reviewId, $increment = true)
    {
        $operator = $increment ? '+' : '-';
        $sql = "UPDATE {$this->table} SET helpful_count = helpful_count {$operator} 1 WHERE id = ?";
        return $this->db->query($sql, [$reviewId]);
    }

    /**
     * Get reviews for admin management
     */
    public function getAdminReviews($status = null, $limit = 20, $offset = 0)
    {
        $sql = "SELECT r.*, u.name as user_name, p.name as product_name 
                FROM {$this->table} r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN products p ON r.product_id = p.id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE r.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }

    /**
     * Update review status
     */
    public function updateStatus($reviewId, $status)
    {
        return $this->update($reviewId, ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
