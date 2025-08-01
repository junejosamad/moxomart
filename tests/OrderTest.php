<?php

use PHPUnit\Framework\TestCase;
use App\Models\Order;
use App\Core\Database;

class OrderTest extends TestCase
{
    private $db;
    private $orderModel;

    protected function setUp(): void
    {
        // Use an in-memory SQLite database for testing
        $this->db = new Database([
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:'
        ]);
        $this->db->connect();

        // Create necessary tables for testing Order functionality
        $this->db->query("
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                order_number VARCHAR(255) UNIQUE NOT NULL,
                total_amount DECIMAL(10, 2) NOT NULL,
                status VARCHAR(50) DEFAULT 'pending',
                payment_status VARCHAR(50) DEFAULT 'pending',
                payment_method VARCHAR(50) NULL,
                billing_first_name VARCHAR(255) NOT NULL,
                billing_last_name VARCHAR(255) NOT NULL,
                billing_email VARCHAR(255) NOT NULL,
                billing_phone VARCHAR(255) NULL,
                billing_address_line1 VARCHAR(255) NOT NULL,
                billing_address_line2 VARCHAR(255) NULL,
                billing_city VARCHAR(255) NOT NULL,
                billing_state VARCHAR(255) NOT NULL,
                billing_zip_code VARCHAR(20) NOT NULL,
                billing_country VARCHAR(255) NOT NULL,
                shipping_first_name VARCHAR(255) NULL,
                shipping_last_name VARCHAR(255) NULL,
                shipping_address_line1 VARCHAR(255) NULL,
                shipping_address_line2 VARCHAR(255) NULL,
                shipping_city VARCHAR(255) NULL,
                shipping_state VARCHAR(255) NULL,
                shipping_zip_code VARCHAR(20) NULL,
                shipping_country VARCHAR(255) NULL,
                notes TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                product_name VARCHAR(255) NOT NULL,
                quantity INTEGER NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            );
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                stock_quantity INTEGER DEFAULT 0
            );
        ");

        // Seed a product for testing order items
        $this->db->query("INSERT INTO products (name, price, stock_quantity) VALUES ('Ordered Product', 25.00, 50);");

        $this->orderModel = new Order();
        $this->orderModel->setDb($this->db); // Inject the test database connection
    }

    protected function tearDown(): void
    {
        // Clean up the database after each test
        $this->db->query("DROP TABLE IF EXISTS order_items;");
        $this->db->query("DROP TABLE IF EXISTS orders;");
        $this->db->query("DROP TABLE IF EXISTS products;");
        $this->db->close();
    }

    public function testCreateOrder()
    {
        $orderData = [
            'user_id' => 1,
            'total_amount' => 50.00,
            'billing_first_name' => 'Test',
            'billing_last_name' => 'Order',
            'billing_email' => 'testorder@example.com',
            'billing_address_line1' => '123 Test St',
            'billing_city' => 'Testville',
            'billing_state' => 'TS',
            'billing_zip_code' => '12345',
            'billing_country' => 'USA',
            'payment_method' => 'stripe',
        ];

        $items = [
            ['product_id' => 1, 'product_name' => 'Ordered Product', 'quantity' => 2, 'price' => 25.00]
        ];

        $orderId = $this->orderModel->createOrder($orderData, $items);
        $this->assertIsInt($orderId);
        $this->assertGreaterThan(0, $orderId);

        $order = $this->orderModel->find($orderId);
        $this->assertNotNull($order);
        $this->assertEquals(50.00, $order['total_amount']);
        $this->assertStringStartsWith('SADA', $order['order_number']);

        $orderItems = $this->orderModel->getOrderItems($orderId);
        $this->assertCount(1, $orderItems);
        $this->assertEquals('Ordered Product', $orderItems[0]['product_name']);
        $this->assertEquals(2, $orderItems[0]['quantity']);
    }

    public function testUpdateOrderStatus()
    {
        $orderData = [
            'user_id' => 1,
            'total_amount' => 10.00,
            'billing_first_name' => 'Update',
            'billing_last_name' => 'Status',
            'billing_email' => 'updatestatus@example.com',
            'billing_address_line1' => '123 Update St',
            'billing_city' => 'Updateville',
            'billing_state' => 'US',
            'billing_zip_code' => '54321',
            'billing_country' => 'USA',
            'payment_method' => 'paypal',
        ];
        $items = [['product_id' => 1, 'product_name' => 'Ordered Product', 'quantity' => 1, 'price' => 10.00]];
        $orderId = $this->orderModel->createOrder($orderData, $items);

        $updated = $this->orderModel->updateStatus($orderId, 'shipped');
        $this->assertTrue($updated);

        $order = $this->orderModel->find($orderId);
        $this->assertEquals('shipped', $order['status']);
    }

    public function testUpdatePaymentStatus()
    {
        $orderData = [
            'user_id' => 1,
            'total_amount' => 10.00,
            'billing_first_name' => 'Update',
            'billing_last_name' => 'Payment',
            'billing_email' => 'updatepayment@example.com',
            'billing_address_line1' => '123 Payment St',
            'billing_city' => 'Paymentville',
            'billing_state' => 'PM',
            'billing_zip_code' => '67890',
            'billing_country' => 'USA',
            'payment_method' => 'cash',
        ];
        $items = [['product_id' => 1, 'product_name' => 'Ordered Product', 'quantity' => 1, 'price' => 10.00]];
        $orderId = $this->orderModel->createOrder($orderData, $items);

        $updated = $this->orderModel->updatePaymentStatus($orderId, 'paid');
        $this->assertTrue($updated);

        $order = $this->orderModel->find($orderId);
        $this->assertEquals('paid', $order['payment_status']);
    }

    public function testGetOrdersByUserId()
    {
        $userId = 5;
        $orderData1 = [
            'user_id' => $userId, 'total_amount' => 10.00, 'billing_first_name' => 'User', 'billing_last_name' => 'Five', 'billing_email' => 'user5@example.com', 'billing_address_line1' => '1', 'billing_city' => 'C', 'billing_state' => 'S', 'billing_zip_code' => 'Z', 'billing_country' => 'USA', 'payment_method' => 'card'
        ];
        $items1 = [['product_id' => 1, 'product_name' => 'Ordered Product', 'quantity' => 1, 'price' => 10.00]];
        $this->orderModel->createOrder($orderData1, $items1);

        $orderData2 = [
            'user_id' => $userId, 'total_amount' => 20.00, 'billing_first_name' => 'User', 'billing_last_name' => 'Five', 'billing_email' => 'user5@example.com', 'billing_address_line1' => '2', 'billing_city' => 'C', 'billing_state' => 'S', 'billing_zip_code' => 'Z', 'billing_country' => 'USA', 'payment_method' => 'card'
        ];
        $items2 = [['product_id' => 1, 'product_name' => 'Ordered Product', 'quantity' => 2, 'price' => 10.00]];
        $this->orderModel->createOrder($orderData2, $items2);

        $orders = $this->orderModel->getOrdersByUserId($userId);
        $this->assertCount(2, $orders);
        $this->assertEquals(20.00, $orders[0]['total_amount']); // Assuming latest order first
    }

    public function testGenerateOrderNumber()
    {
        $orderNumber = $this->orderModel->generateOrderNumber();
        $this->assertStringStartsWith('SADA', $orderNumber);
        $this->assertEquals(12, strlen($orderNumber)); // SADA + 8 random chars
    }
}
