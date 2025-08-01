<?php

use PHPUnit\Framework\TestCase;
use App\Models\Cart;
use App\Core\Database;

class CartTest extends TestCase
{
    private $db;
    private $cartModel;

    protected function setUp(): void
    {
        // Use an in-memory SQLite database for testing
        $this->db = new Database([
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:'
        ]);
        $this->db->connect();

        // Create necessary tables for testing Cart functionality
        $this->db->query("
            CREATE TABLE IF NOT EXISTS carts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NULL,
                session_id VARCHAR(255) NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS cart_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cart_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE
            );
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                stock_quantity INTEGER DEFAULT 0
            );
        ");

        // Seed a product for testing cart items
        $this->db->query("INSERT INTO products (name, price, stock_quantity) VALUES ('Test Product', 10.00, 100);");

        $this->cartModel = new Cart();
        $this->cartModel->setDb($this->db); // Inject the test database connection
    }

    protected function tearDown(): void
    {
        // Clean up the database after each test
        $this->db->query("DROP TABLE IF EXISTS cart_items;");
        $this->db->query("DROP TABLE IF EXISTS carts;");
        $this->db->query("DROP TABLE IF EXISTS products;");
        $this->db->close();
    }

    public function testCreateCartForSession()
    {
        $sessionId = 'test_session_123';
        $cartId = $this->cartModel->createCartForSession($sessionId);
        $this->assertIsInt($cartId);
        $this->assertGreaterThan(0, $cartId);

        $cart = $this->db->query("SELECT * FROM carts WHERE session_id = ?", [$sessionId])->fetch();
        $this->assertNotNull($cart);
        $this->assertEquals($cartId, $cart['id']);
    }

    public function testGetCartBySessionId()
    {
        $sessionId = 'get_session_456';
        $this->cartModel->createCartForSession($sessionId);

        $cart = $this->cartModel->getCartBySessionId($sessionId);
        $this->assertNotNull($cart);
        $this->assertEquals($sessionId, $cart['session_id']);
    }

    public function testGetCartByUserId()
    {
        $userId = 1;
        $this->db->query("INSERT INTO carts (user_id) VALUES (?)", [$userId]);

        $cart = $this->cartModel->getCartByUserId($userId);
        $this->assertNotNull($cart);
        $this->assertEquals($userId, $cart['user_id']);
    }

    public function testAddItemToCart()
    {
        $sessionId = 'add_item_session';
        $cartId = $this->cartModel->createCartForSession($sessionId);
        $productId = 1; // Assuming product ID 1 exists from setUp()

        $itemAdded = $this->cartModel->addItem($cartId, $productId, 2, 10.00);
        $this->assertTrue($itemAdded);

        $cartItems = $this->cartModel->getCartItems($cartId);
        $this->assertCount(1, $cartItems);
        $this->assertEquals(2, $cartItems[0]['quantity']);
        $this->assertEquals(10.00, $cartItems[0]['price']);
    }

    public function testUpdateCartItemQuantity()
    {
        $sessionId = 'update_item_session';
        $cartId = $this->cartModel->createCartForSession($sessionId);
        $productId = 1;
        $this->cartModel->addItem($cartId, $productId, 1, 10.00);

        $updated = $this->cartModel->updateItemQuantity($cartId, $productId, 5);
        $this->assertTrue($updated);

        $cartItems = $this->cartModel->getCartItems($cartId);
        $this->assertEquals(5, $cartItems[0]['quantity']);
    }

    public function testRemoveItemFromCart()
    {
        $sessionId = 'remove_item_session';
        $cartId = $this->cartModel->createCartForSession($sessionId);
        $productId = 1;
        $this->cartModel->addItem($cartId, $productId, 1, 10.00);

        $removed = $this->cartModel->removeItem($cartId, $productId);
        $this->assertTrue($removed);

        $cartItems = $this->cartModel->getCartItems($cartId);
        $this->assertEmpty($cartItems);
    }

    public function testClearCart()
    {
        $sessionId = 'clear_cart_session';
        $cartId = $this->cartModel->createCartForSession($sessionId);
        $productId = 1;
        $this->cartModel->addItem($cartId, $productId, 1, 10.00);
        $this->cartModel->addItem($cartId, $productId, 2, 10.00); // Add another item to ensure multiple are cleared

        $cleared = $this->cartModel->clearCart($cartId);
        $this->assertTrue($cleared);

        $cartItems = $this->cartModel->getCartItems($cartId);
        $this->assertEmpty($cartItems);
    }

    public function testGetCartTotals()
    {
        $sessionId = 'totals_session';
        $cartId = $this->cartModel->createCartForSession($sessionId);
        $productId = 1;

        $this->cartModel->addItem($cartId, $productId, 2, 10.00); // 2 * 10 = 20
        $this->db->query("INSERT INTO products (name, price, stock_quantity) VALUES ('Another Product', 5.00, 50);");
        $productId2 = $this->db->lastInsertId();
        $this->cartModel->addItem($cartId, $productId2, 3, 5.00); // 3 * 5 = 15

        $totals = $this->cartModel->getCartTotals($cartId);
        $this->assertEquals(35.00, $totals['subtotal']);
        $this->assertEquals(5, $totals['total_items']);
    }

    public function testMergeCarts()
    {
        $guestSessionId = 'guest_session_merge';
        $guestCartId = $this->cartModel->createCartForSession($guestSessionId);
        $this->cartModel->addItem($guestCartId, 1, 2, 10.00); // Guest has 2 of product 1

        $userId = 10;
        $userCartId = $this->db->query("INSERT INTO carts (user_id) VALUES (?)", [$userId])->lastInsertId();
        $this->cartModel->addItem($userCartId, 1, 1, 10.00); // User has 1 of product 1

        $merged = $this->cartModel->mergeCarts($userId, $guestSessionId);
        $this->assertTrue($merged);

        // Check user's cart
        $userCartItems = $this->cartModel->getCartItems($userCartId);
        $this->assertCount(1, $userCartItems);
        $this->assertEquals(3, $userCartItems[0]['quantity']); // 1 + 2 = 3

        // Ensure guest cart is deleted
        $guestCart = $this->db->query("SELECT * FROM carts WHERE id = ?", [$guestCartId])->fetch();
        $this->assertFalse($guestCart);
    }
}
