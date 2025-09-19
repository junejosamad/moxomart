<?php

namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'user_id', 'guest_email', 'guest_name', 'guest_phone', 'order_number', 'status', 'total_amount', 'shipping_amount',
        'tax_amount', 'discount_amount', 'payment_method', 'payment_status',
        'shipping_address', 'billing_address', 'notes'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    public function generateOrderNumber()
    {
        $prefix = 'MX';
        $timestamp = time();
        $random = rand(100, 999);
        return $prefix . $timestamp . $random;
    }

    public function getOrderWithItems($orderId)
    {
        // Get order details
        $order = $this->find($orderId);
        if (!$order) {
            return null;
        }

        // Decode address JSON fields
        $order['shipping_address'] = json_decode($order['shipping_address'], true) ?? [];
        $order['billing_address'] = json_decode($order['billing_address'], true) ?? [];

        // Get order items
        $sql = "SELECT oi.*, p.name, p.image, p.slug 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll();

        return $order;
    }

    public function getTotalSpent($userId)
    {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) as total_spent 
                FROM orders 
                WHERE user_id = ? AND status NOT IN ('cancelled', 'refunded')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['total_spent'] ?? 0;
    }

    public function getRecentOrders($userId, $limit = 5)
    {
        return $this->where(['user_id' => $userId], 'created_at DESC', $limit);
    }

    public function getUserOrders($userId, $page = 1, $perPage = 10)
    {
        $conditions = ['user_id' => $userId];
        return $this->paginate($page, $perPage, $conditions, 'created_at DESC');
    }

    public function createFromCart($userId, $orderData)
    {
        try {
            $this->db->beginTransaction();

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $orderData['order_number'] = $orderNumber;
            $orderData['user_id'] = $userId;
            $orderData['status'] = self::STATUS_PENDING;
            
            $orderId = $this->create($orderData);

            // Get cart items
            $cartModel = new Cart();
            $cartItems = $cartModel->getCartItems($userId);

            if (empty($cartItems)) {
                throw new \Exception('Cart is empty');
            }

            // Create order items
            $this->createOrderItems($orderId, $cartItems);

            // Clear cart
            $cartModel->clearCart($userId);

            $this->db->commit();
            return $this->find($orderId);

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function createFromGuestCart($orderData, $cartItems, $guestData)
    {
        try {
            $this->db->beginTransaction();

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order for guest (user_id = null)
            $orderData['order_number'] = $orderNumber;
            $orderData['user_id'] = null; // Guest order
            $orderData['status'] = self::STATUS_PENDING;
            
            // Add guest information to order data
            $orderData['guest_email'] = $guestData['email'];
            $orderData['guest_name'] = $guestData['first_name'] . ' ' . $guestData['last_name'];
            $orderData['guest_phone'] = $guestData['phone'];
            
            $orderId = $this->create($orderData);

            if (empty($cartItems)) {
                throw new \Exception('Cart is empty');
            }

            // Create order items
            $this->createOrderItems($orderId, $cartItems);

            $this->db->commit();
            return $this->find($orderId);

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function createOrderItems($orderId, $cartItems)
    {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, total) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);

        foreach ($cartItems as $item) {
            $price = $item['price'];
            $quantity = $item['quantity'];
            $total = $price * $quantity;

            $stmt->execute([
                $orderId,
                $item['product_id'],
                $quantity,
                $price,
                $total
            ]);
        }
    }

    public function updateStatus($orderId, $status, $notes = null)
    {
        $updateData = ['status' => $status];
        
        if ($notes) {
            $updateData['notes'] = $notes;
        }

        return $this->update($orderId, $updateData);
    }

    public function getStatusLabel($status)
    {
        $labels = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded'
        ];

        return $labels[$status] ?? 'Unknown';
    }

    public function getStatusColor($status)
    {
        $colors = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_SHIPPED => 'primary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_REFUNDED => 'secondary'
        ];

        return $colors[$status] ?? 'secondary';
    }

    public function getOrdersByStatus($status, $page = 1, $perPage = 20)
    {
        $conditions = ['status' => $status];
        return $this->paginate($page, $perPage, $conditions, 'created_at DESC');
    }

    public function getOrdersByDateRange($startDate, $endDate, $page = 1, $perPage = 20)
    {
        $sql = "SELECT * FROM orders 
                WHERE created_at BETWEEN ? AND ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate, $perPage, $offset]);
        
        return $stmt->fetchAll();
    }

    public function getDailySales($days = 30)
    {
        $sql = "SELECT DATE(created_at) as date, 
                       COUNT(*) as order_count,
                       SUM(total_amount) as total_sales
                FROM orders 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  AND status NOT IN ('cancelled', 'refunded')
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}
