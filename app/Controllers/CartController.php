<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    private $cartModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }

    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $this->calculateTotal($cartItems);
        
        // Prepare cart data structure expected by the view
        $cartData = [
            'items' => $cartItems,
            'item_count' => count($cartItems),
            'subtotal' => $total,
            'shipping' => 0, // Free shipping for now
            'tax' => 0, // No tax for now
            'total' => $total
        ];
        
        $meta = [
            'title' => 'Shopping Cart - Moxo Mart',
            'description' => 'Review your cart items before checkout'
        ];

        return $this->render('cart/index', [
            'cartData' => $cartData,
            'meta' => $meta
        ]);
    }

    public function add()
    {
        $productId = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        $attributes = $_POST['attributes'] ?? [];

        if (!$productId || $quantity <= 0) {
            return $this->json(['success' => false, 'message' => 'Invalid product or quantity'], 400);
        }

        // Check if product exists and is active
        $product = $this->productModel->find($productId);
        if (!$product || $product['status'] !== 'active') {
            return $this->json(['success' => false, 'message' => 'Product not available'], 404);
        }

        // Check stock
        if ($product['track_quantity'] && $product['stock_quantity'] < $quantity) {
            return $this->json(['success' => false, 'message' => 'Insufficient stock'], 400);
        }

        try {
            if (isLoggedIn()) {
                $this->addToUserCart($productId, $quantity, $attributes);
            } else {
                $this->addToSessionCart($productId, $quantity, $attributes);
            }

            $cartCount = $this->getCartCount();
            
            return $this->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cartCount' => $cartCount
            ]);
        } catch (\Exception $e) {
            error_log("Cart add error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to add item to cart'], 500);
        }
    }

    public function update()
    {
        $productId = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!$productId || $quantity < 0) {
            return $this->json(['success' => false, 'message' => 'Invalid item or quantity'], 400);
        }

        try {
            if ($quantity === 0) {
                return $this->remove();
            }

            if (isLoggedIn()) {
                // For logged in users, we need to find the cart item by product_id
                $user = getCurrentUser();
                $cart = $this->cartModel->getOrCreateCart($user['id']);
                $cartItem = $this->cartModel->getCartItem($cart['id'], $productId, []);
                if ($cartItem) {
                    $this->updateUserCartItem($cartItem['id'], $quantity);
                }
            } else {
                // For session cart, update by product_id
                $this->updateSessionCartItem($productId, $quantity);
            }

            $cartCount = $this->getCartCount();
            $total = $this->getCartTotal();
            $cartItems = $this->getCartItems();
            
            // Find the updated item to get its new total
            $updatedItem = null;
            foreach ($cartItems as $item) {
                if ($item['product_id'] == $productId) {
                    $updatedItem = $item;
                    break;
                }
            }

            return $this->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cartCount' => $cartCount,
                'total' => formatPrice($total),
                'item_total' => $updatedItem ? formatPrice($updatedItem['subtotal']) : formatPrice(0),
                'cart_totals' => [
                    'subtotal_formatted' => formatPrice($total),
                    'shipping_formatted' => formatPrice(0),
                    'tax_formatted' => formatPrice(0),
                    'total_formatted' => formatPrice($total),
                    'item_count' => count($cartItems)
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Cart update error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to update cart'], 500);
        }
    }

    public function remove()
    {
        $productId = $_POST['product_id'] ?? null;

        if (!$productId) {
            return $this->json(['success' => false, 'message' => 'Invalid item'], 400);
        }

        try {
            if (isLoggedIn()) {
                // For logged in users, we need to find the cart item by product_id
                $user = getCurrentUser();
                $cart = $this->cartModel->getOrCreateCart($user['id']);
                $cartItem = $this->cartModel->getCartItem($cart['id'], $productId, []);
                if ($cartItem) {
                    $this->removeFromUserCart($cartItem['id']);
                }
            } else {
                // For session cart, remove by product_id
                $this->removeFromSessionCart($productId);
            }

            $cartCount = $this->getCartCount();
            $total = $this->getCartTotal();
            $cartItems = $this->getCartItems();

            return $this->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartCount' => $cartCount,
                'total' => formatPrice($total),
                'cart_totals' => [
                    'subtotal_formatted' => formatPrice($total),
                    'shipping_formatted' => formatPrice(0),
                    'tax_formatted' => formatPrice(0),
                    'total_formatted' => formatPrice($total),
                    'item_count' => count($cartItems)
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Cart remove error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to remove item'], 500);
        }
    }

    public function mini()
    {
        $cartItems = $this->getCartItems();
        $total = $this->calculateTotal($cartItems);
        $count = count($cartItems);

        return $this->json([
            'items' => $cartItems,
            'total' => formatPrice($total),
            'count' => $count
        ]);
    }

    public function count()
    {
        $count = $this->getCartCount();
        return $this->json(['count' => $count]);
    }

    public function clear()
    {
        try {
            if (isLoggedIn()) {
                $user = getCurrentUser();
                $cart = $this->cartModel->getOrCreateCart($user['id']);
                $this->cartModel->clearCart($cart['id']);
            } else {
                $_SESSION['cart'] = [];
            }

            return $this->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            error_log("Cart clear error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to clear cart'], 500);
        }
    }

    public function applyCoupon()
    {
        $couponCode = $_POST['coupon_code'] ?? '';
        
        // For now, just return a placeholder response
        // In a real application, you would validate the coupon code
        return $this->json([
            'success' => false,
            'message' => 'Coupon functionality not implemented yet'
        ]);
    }

    private function addToUserCart($productId, $quantity, $attributes = [])
    {
        $user = getCurrentUser();
        $cart = $this->cartModel->getOrCreateCart($user['id']);
        
        // Check if item already exists in cart
        $existingItem = $this->cartModel->getCartItem($cart['id'], $productId, $attributes);
        
        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            $this->cartModel->updateCartItem($existingItem['id'], $newQuantity);
        } else {
            $this->cartModel->addCartItem($cart['id'], $productId, $quantity, $attributes);
        }
    }

    private function addToSessionCart($productId, $quantity, $attributes = [])
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $itemKey = $this->generateCartItemKey($productId, $attributes);
        
        if (isset($_SESSION['cart'][$itemKey])) {
            $_SESSION['cart'][$itemKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$itemKey] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'attributes' => $attributes,
                'added_at' => time()
            ];
        }
    }

    private function updateUserCartItem($cartItemId, $quantity)
    {
        $this->cartModel->updateCartItem($cartItemId, $quantity);
    }

    private function updateSessionCartItem($cartItemId, $quantity)
    {
        if (isset($_SESSION['cart'][$cartItemId])) {
            $_SESSION['cart'][$cartItemId]['quantity'] = $quantity;
        }
    }

    private function removeFromUserCart($cartItemId)
    {
        $this->cartModel->removeCartItem($cartItemId);
    }

    private function removeFromSessionCart($cartItemId)
    {
        if (isset($_SESSION['cart'][$cartItemId])) {
            unset($_SESSION['cart'][$cartItemId]);
        }
    }

    private function getCartItems()
    {
        if (isLoggedIn()) {
            $user = getCurrentUser();
            return $this->cartModel->getCartItems($user['id']);
        } else {
            return $this->getSessionCartItems();
        }
    }

    private function getSessionCartItems()
    {
        $items = [];
        $sessionCart = $_SESSION['cart'] ?? [];
        
        foreach ($sessionCart as $key => $item) {
            $product = $this->productModel->find($item['product_id']);
            if ($product) {
                $items[] = [
                    'id' => $key,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'attributes' => $item['attributes'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'stock_quantity' => $product['stock_quantity'],
                    'sku' => $product['sku'],
                    'slug' => $product['slug'],
                    'image_path' => $product['image'] ?? null,
                    'subtotal' => $product['price'] * $item['quantity']
                ];
            }
        }
        
        return $items;
    }

    private function calculateTotal($cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['subtotal'] ?? ($item['price'] * $item['quantity']);
        }
        return $total;
    }

    private function getCartCount()
    {
        if (isLoggedIn()) {
            $user = getCurrentUser();
            return $this->cartModel->getItemCount($user['id']);
        } else {
            $sessionCart = $_SESSION['cart'] ?? [];
            return array_sum(array_column($sessionCart, 'quantity'));
        }
    }

    private function getCartTotal()
    {
        $cartItems = $this->getCartItems();
        return $this->calculateTotal($cartItems);
    }

    private function generateCartItemKey($productId, $attributes = [])
    {
        return $productId . '_' . md5(serialize($attributes));
    }
} 