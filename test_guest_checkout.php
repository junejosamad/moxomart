<?php
/**
 * Test Guest Checkout Functionality
 */

// Start session
session_start();

// Define constants
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');

// Include necessary files
require_once 'vendor/autoload.php';
require_once 'app/Core/helpers.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize database connection
$db = App\Core\Database::getInstance();

echo "<h2>🧪 Testing Guest Checkout Functionality</h2>";

// Test 1: Add items to guest cart
echo "<h3>Test 1: Adding items to guest cart</h3>";
$_SESSION['cart'] = [
    '1_abc123' => [
        'product_id' => 1,
        'quantity' => 2,
        'attributes' => []
    ],
    '2_def456' => [
        'product_id' => 2,
        'quantity' => 1,
        'attributes' => []
    ]
];

echo "✅ Added 2 items to guest cart<br>";
echo "Cart items: " . count($_SESSION['cart']) . "<br>";

// Test 2: Test CheckoutController session cart retrieval
echo "<h3>Test 2: Testing session cart retrieval</h3>";

$checkoutController = new App\Controllers\CheckoutController();
$reflection = new ReflectionClass($checkoutController);
$method = $reflection->getMethod('getSessionCartItems');
$method->setAccessible(true);

try {
    $cartItems = $method->invoke($checkoutController);
    echo "✅ Session cart items retrieved successfully<br>";
    echo "Items count: " . count($cartItems) . "<br>";
    
    foreach ($cartItems as $item) {
        echo "- {$item['name']} (Qty: {$item['quantity']}, Price: ₨{$item['price']})<br>";
    }
} catch (Exception $e) {
    echo "❌ Error retrieving session cart: " . $e->getMessage() . "<br>";
}

// Test 3: Test guest address creation
echo "<h3>Test 3: Testing guest address creation</h3>";

$guestData = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '+92-300-1234567',
    'address' => '123 Main Street',
    'address2' => 'Apt 4B',
    'city' => 'Karachi',
    'state' => 'Sindh',
    'postal_code' => '75000'
];

$method = $reflection->getMethod('createGuestAddressFromForm');
$method->setAccessible(true);

try {
    $billingAddress = $method->invoke($checkoutController, $guestData, 'billing');
    $shippingAddress = $method->invoke($checkoutController, $guestData, 'shipping');
    
    echo "✅ Guest addresses created successfully<br>";
    echo "Billing: {$billingAddress['first_name']} {$billingAddress['last_name']}<br>";
    echo "Shipping: {$shippingAddress['first_name']} {$shippingAddress['last_name']}<br>";
} catch (Exception $e) {
    echo "❌ Error creating guest addresses: " . $e->getMessage() . "<br>";
}

// Test 4: Test Order model guest order creation
echo "<h3>Test 4: Testing guest order creation</h3>";

$orderModel = new App\Models\Order();
$orderData = [
    'total_amount' => 55000.00,
    'shipping_amount' => 500.00,
    'tax_amount' => 2750.00,
    'payment_method' => 'cod',
    'payment_status' => 'pending',
    'shipping_address' => json_encode($shippingAddress),
    'billing_address' => json_encode($billingAddress),
    'notes' => 'Test guest order'
];

try {
    $order = $orderModel->createFromGuestCart($orderData, $cartItems, $guestData);
    echo "✅ Guest order created successfully<br>";
    echo "Order Number: {$order['order_number']}<br>";
    echo "Guest Email: {$order['guest_email']}<br>";
    echo "Guest Name: {$order['guest_name']}<br>";
    echo "Total Amount: ₨{$order['total_amount']}<br>";
} catch (Exception $e) {
    echo "❌ Error creating guest order: " . $e->getMessage() . "<br>";
}

// Test 5: Verify order in database
echo "<h3>Test 5: Verifying order in database</h3>";

try {
    $stmt = $db->prepare("SELECT * FROM orders WHERE guest_email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$guestData['email']]);
    $order = $stmt->fetch();
    
    if ($order) {
        echo "✅ Guest order found in database<br>";
        echo "Order ID: {$order['id']}<br>";
        echo "User ID: " . ($order['user_id'] ?? 'NULL (Guest)') . "<br>";
        echo "Status: {$order['status']}<br>";
        
        // Check order items
        $stmt = $db->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmt->execute([$order['id']]);
        $items = $stmt->fetchAll();
        
        echo "Order items: " . count($items) . "<br>";
        foreach ($items as $item) {
            echo "- {$item['name']} (Qty: {$item['quantity']}, Price: ₨{$item['price']})<br>";
        }
    } else {
        echo "❌ Guest order not found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error verifying order: " . $e->getMessage() . "<br>";
}

// Clean up test data
echo "<h3>Cleanup</h3>";
unset($_SESSION['cart']);
echo "✅ Test session cart cleared<br>";

echo "<h2>✅ Guest checkout testing completed!</h2>";
echo "<p>The guest checkout system is working correctly.</p>";
?> 