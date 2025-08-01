<?php

namespace App\Models;

use App\Core\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $fillable = ['user_id', 'session_id'];

    public function getOrCreateCart($userId, $sessionId = null)
    {
        // Try to find existing cart for user
        $cart = $this->findBy('user_id', $userId);
        
        if (!$cart) {
            // Create new cart
            $cartId = $this->create([
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
            $cart = $this->find($cartId);
        }
        
        return $cart;
    }

    public function getCartItems($userId)
    {
        $sql = "SELECT ci.*, p.name, p.price, p.image, p.slug, p.stock_quantity,
                       (ci.quantity * p.price) as subtotal
                FROM cart_items ci 
                JOIN cart c ON ci.cart_id = c.id 
                JOIN products p ON ci.product_id = p.id 
                WHERE c.user_id = ? 
                ORDER BY ci.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getCartItem($cartId, $productId, $attributes = [])
    {
        $attributesJson = json_encode($attributes);
        
        $sql = "SELECT * FROM cart_items 
                WHERE cart_id = ? AND product_id = ? AND attributes = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cartId, $productId, $attributesJson]);
        return $stmt->fetch();
    }

    public function addCartItem($cartId, $productId, $quantity, $attributes = [])
    {
        $attributesJson = json_encode($attributes);
        
        $sql = "INSERT INTO cart_items (cart_id, product_id, quantity, attributes) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cartId, $productId, $quantity, $attributesJson]);
    }

    public function updateCartItem($cartItemId, $quantity)
    {
        $sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$quantity, $cartItemId]);
    }

    public function removeCartItem($cartItemId)
    {
        $sql = "DELETE FROM cart_items WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cartItemId]);
    }

    public function getItemCount($userId)
    {
        $sql = "SELECT COALESCE(SUM(ci.quantity), 0) as count
                FROM cart_items ci 
                JOIN cart c ON ci.cart_id = c.id 
                WHERE c.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getTotal($userId)
    {
        $sql = "SELECT COALESCE(SUM(ci.quantity * p.price), 0) as total
                FROM cart_items ci 
                JOIN cart c ON ci.cart_id = c.id 
                JOIN products p ON ci.product_id = p.id 
                WHERE c.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function clearCart($userId)
    {
        $sql = "DELETE ci FROM cart_items ci 
                JOIN cart c ON ci.cart_id = c.id 
                WHERE c.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }

    public function mergeSessionCart($userId, $sessionCart)
    {
        $cart = $this->getOrCreateCart($userId);
        
        foreach ($sessionCart as $item) {
            $existingItem = $this->getCartItem($cart['id'], $item['product_id'], $item['attributes']);
            
            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $item['quantity'];
                $this->updateCartItem($existingItem['id'], $newQuantity);
            } else {
                $this->addCartItem($cart['id'], $item['product_id'], $item['quantity'], $item['attributes']);
            }
        }
    }
}

