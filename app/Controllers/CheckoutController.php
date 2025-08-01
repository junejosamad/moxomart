<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;

class CheckoutController extends Controller
{
    private $cartModel;
    private $orderModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function index()
    {
        $user = getCurrentUser();
        $cartItems = $this->cartModel->getCartItems($user['id']);
        
        if (empty($cartItems)) {
            setFlash('error', 'Your cart is empty.');
            return $this->redirect('/cart');
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['subtotal'];
        }
        
        $shippingCost = $this->calculateShipping($subtotal);
        $taxAmount = $this->calculateTax($subtotal);
        $discountAmount = 0; // No discount for now
        $total = $subtotal + $shippingCost + $taxAmount - $discountAmount;
        $cartCount = count($cartItems);

        // Get user addresses
        $addresses = $this->getUserAddresses($user['id']);
        
        $meta = [
            'title' => 'Checkout - Moxo Mart',
            'description' => 'Complete your order securely'
        ];

        return $this->render('checkout/index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'taxAmount' => $taxAmount,
            'discountAmount' => $discountAmount,
            'total' => $total,
            'cartCount' => $cartCount,
            'addresses' => $addresses,
            'user' => $user,
            'meta' => $meta
        ]);
    }

    public function process()
    {
        $user = getCurrentUser();

        // Validate input
        $errors = validate($_POST, [
            'payment_method' => 'required',
            'billing_address_id' => 'required|numeric',
            'shipping_address_id' => 'required|numeric'
        ]);

        // Validate payment method
        $allowedPaymentMethods = ['credit_card', 'paypal', 'cod', 'bank_transfer'];
        if (!in_array($_POST['payment_method'], $allowedPaymentMethods)) {
            $errors['payment_method'][] = 'Invalid payment method selected.';
        }

        // Validate addresses belong to user
        $billingAddressId = $_POST['billing_address_id'] ?? null;
        $shippingAddressId = $_POST['shipping_address_id'] ?? null;
        
        // Handle new address creation
        if ($billingAddressId === 'new') {
            $billingAddress = $this->createAddressFromForm($_POST, $user['id'], 'billing');
            if (!$billingAddress) {
                $errors['billing_address_id'][] = 'Invalid billing address information.';
            }
        } else {
            $billingAddress = $this->validateUserAddress($billingAddressId, $user['id']);
            if (!$billingAddress) {
                $errors['billing_address_id'][] = 'Invalid billing address.';
            }
        }
        
        if ($shippingAddressId === 'new') {
            $shippingAddress = $this->createAddressFromForm($_POST, $user['id'], 'shipping');
            if (!$shippingAddress) {
                $errors['shipping_address_id'][] = 'Invalid shipping address information.';
            }
        } else {
            $shippingAddress = $this->validateUserAddress($shippingAddressId, $user['id']);
            if (!$shippingAddress) {
                $errors['shipping_address_id'][] = 'Invalid shipping address.';
            }
        }

        // Get cart items and validate
        $cartItems = $this->cartModel->getCartItems($user['id']);
        if (empty($cartItems)) {
            setFlash('error', 'Your cart is empty.');
            return $this->redirect('/cart');
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            if (!$product || $product['status'] !== 'active') {
                $errors['cart'][] = "Product '{$item['name']}' is no longer available.";
            } elseif ($product['track_quantity'] && $product['stock_quantity'] < $item['quantity']) {
                $errors['cart'][] = "Insufficient stock for '{$item['name']}'. Only {$product['stock_quantity']} available.";
            }
        }

        if (!empty($errors)) {
            // Calculate totals for the view
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['subtotal'];
            }
            
            $shippingCost = $this->calculateShipping($subtotal);
            $taxAmount = $this->calculateTax($subtotal);
            $discountAmount = 0;
            $total = $subtotal + $shippingCost + $taxAmount - $discountAmount;
            $cartCount = count($cartItems);
            
            // Get user addresses
            $addresses = $this->getUserAddresses($user['id']);
            
            return $this->render('checkout/index', [
                'errors' => $errors,
                'old' => $_POST,
                'cartItems' => $cartItems,
                'subtotal' => $subtotal,
                'shippingCost' => $shippingCost,
                'taxAmount' => $taxAmount,
                'discountAmount' => $discountAmount,
                'total' => $total,
                'cartCount' => $cartCount,
                'addresses' => $addresses,
                'user' => $user
            ]);
        }

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['subtotal'];
            }
            
            $shippingCost = $this->calculateShipping($subtotal);
            $taxAmount = $this->calculateTax($subtotal);
            $total = $subtotal + $shippingCost + $taxAmount;

            // Prepare order data
            $orderData = [
                'total_amount' => $total,
                'shipping_amount' => $shippingCost,
                'tax_amount' => $taxAmount,
                'payment_method' => $_POST['payment_method'],
                'payment_status' => 'pending',
                'shipping_address' => json_encode($shippingAddress),
                'billing_address' => json_encode($billingAddress),
                'notes' => $_POST['notes'] ?? null
            ];

            // Create order
            $order = $this->orderModel->createFromCart($user['id'], $orderData);
            
            // Update product stock
            $this->updateProductStock($cartItems);

            // Process payment based on method
            $paymentResult = $this->processPayment($order, $_POST['payment_method'], $_POST);

            if ($paymentResult['success']) {
                // Update order status
                $this->orderModel->updateStatus($order['id'], 'processing');
                
                logActivity('order_placed', "Order #{$order['order_number']} placed by user: {$user['email']}");

                // Redirect to success page
                $_SESSION['order_success'] = $order['id'];
                return $this->redirect('/checkout/success');
            } else {
                // Payment failed
                $this->orderModel->updateStatus($order['id'], 'cancelled');
                setFlash('error', 'Payment failed: ' . $paymentResult['message']);
                return $this->redirect('/checkout');
            }

        } catch (\Exception $e) {
            error_log("Checkout error: " . $e->getMessage());
            setFlash('error', 'An error occurred while processing your order. Please try again.');
            return $this->redirect('/checkout');
        }
    }

    public function success()
    {
        if (!isset($_SESSION['order_success'])) {
            return $this->redirect('/');
        }

        $orderId = $_SESSION['order_success'];
        unset($_SESSION['order_success']);

        $user = getCurrentUser();
        $order = $this->orderModel->getOrderWithItems($orderId);

        if (!$order || $order['user_id'] != $user['id']) {
            return $this->redirect('/');
        }

        $meta = [
            'title' => 'Order Confirmation - Moxo Mart',
            'description' => 'Your order has been placed successfully'
        ];

        return $this->render('checkout/success', [
            'order' => $order,
            'meta' => $meta
        ]);
    }

    public function cancel()
    {
        setFlash('info', 'Your order has been cancelled.');
        return $this->redirect('/cart');
    }

    private function calculateShipping($subtotal)
    {
        // Free shipping over $100
        if ($subtotal >= 100) {
            return 0;
        }
        
        // Flat rate shipping
        return 9.99;
    }

    private function calculateTax($subtotal)
    {
        // 8% tax rate
        return $subtotal * 0.08;
    }

    private function getUserAddresses($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    private function validateUserAddress($addressId, $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_addresses WHERE id = ? AND user_id = ?");
        $stmt->execute([$addressId, $userId]);
        return $stmt->fetch();
    }

    private function createAddressFromForm($formData, $userId, $type)
    {
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'postal_code'];
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                return false;
            }
        }

        // Create address data
        $addressData = [
            'user_id' => $userId,
            'first_name' => $formData['first_name'],
            'last_name' => $formData['last_name'],
            'phone' => $formData['phone'],
            'address_line_1' => $formData['address'],
            'address_line_2' => $formData['address2'] ?? null,
            'city' => $formData['city'],
            'state' => $formData['state'],
            'postal_code' => $formData['postal_code'],
            'country' => 'Pakistan',
            'is_default' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert address into database
        $stmt = $this->db->prepare("
            INSERT INTO user_addresses (user_id, first_name, last_name, phone, 
                                      address_line_1, address_line_2, city, state, postal_code, country, 
                                      is_default, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([
            $addressData['user_id'], $addressData['first_name'],
            $addressData['last_name'], $addressData['phone'],
            $addressData['address_line_1'], $addressData['address_line_2'], $addressData['city'],
            $addressData['state'], $addressData['postal_code'], $addressData['country'],
            $addressData['is_default'], $addressData['created_at']
        ])) {
            return $addressData;
        }
        
        return false;
    }

    private function updateProductStock($cartItems)
    {
        foreach ($cartItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            if ($product && $product['track_quantity']) {
                $newStock = $product['stock_quantity'] - $item['quantity'];
                $this->productModel->update($item['product_id'], [
                    'stock_quantity' => max(0, $newStock)
                ]);
            }
        }
    }

    private function processPayment($order, $paymentMethod, $paymentData)
    {
        switch ($paymentMethod) {
            case 'credit_card':
                return $this->processCreditCardPayment($order, $paymentData);
            
            case 'paypal':
                return $this->processPayPalPayment($order, $paymentData);
            
            case 'cod':
                return ['success' => true, 'message' => 'Cash on delivery order placed'];
            
            case 'bank_transfer':
                return ['success' => true, 'message' => 'Bank transfer order placed'];
            
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    private function processCreditCardPayment($order, $paymentData)
    {
        // In a real application, this would integrate with Stripe, Square, etc.
        // For now, simulate payment processing
        
        $errors = validate($paymentData, [
            'card_number' => 'required',
            'card_expiry' => 'required',
            'card_cvv' => 'required',
            'cardholder_name' => 'required'
        ]);

        if (!empty($errors)) {
            return ['success' => false, 'message' => 'Invalid credit card information'];
        }

        // Simulate payment processing
        if ($paymentData['card_number'] === '4111111111111111') {
            return ['success' => true, 'transaction_id' => 'sim_' . uniqid()];
        } else {
            return ['success' => false, 'message' => 'Payment declined'];
        }
    }

    private function processPayPalPayment($order, $paymentData)
    {
        // In a real application, this would integrate with PayPal API
        return ['success' => true, 'message' => 'PayPal payment processed'];
    }
} 