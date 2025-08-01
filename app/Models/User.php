<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 
        'phone', 'role', 'email_verified_at'
    ];

    public function getFullName()
    {
        $user = $this->find($this->id ?? null);
        if ($user) {
            return $user['first_name'] . ' ' . $user['last_name'];
        }
        return '';
    }

    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    public function createUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = hashPassword($data['password']);
        }
        
        return $this->create($data);
    }

    public function updatePassword($userId, $newPassword)
    {
        return $this->update($userId, [
            'password' => hashPassword($newPassword)
        ]);
    }

    public function verifyEmail($userId)
    {
        return $this->update($userId, [
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getOrders($userId, $limit = null)
    {
        $sql = "SELECT o.*, COUNT(oi.id) as item_count 
                FROM orders o 
                LEFT JOIN order_items oi ON o.id = oi.order_id 
                WHERE o.user_id = ? 
                GROUP BY o.id 
                ORDER BY o.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getWishlist($userId)
    {
        $sql = "SELECT p.*, w.created_at as added_to_wishlist 
                FROM wishlist w 
                JOIN products p ON w.product_id = p.id 
                WHERE w.user_id = ? 
                ORDER BY w.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getRecentCustomers($limit = 10)
    {
        return $this->where(['role' => 'customer'], 'created_at DESC', $limit);
    }

    public function searchUsers($query, $role = null)
    {
        $sql = "SELECT * FROM users WHERE 
                (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];
        
        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
