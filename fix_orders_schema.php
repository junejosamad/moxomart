<?php
/**
 * Fix Orders Table Schema for Guest Checkout
 */

$host = 'localhost';
$dbname = 'moxo_mart';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database successfully<br>";
    
    // Check current orders table structure
    echo "<h3>Current Orders Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE orders");
    while ($row = $stmt->fetch()) {
        echo "{$row['Field']} - {$row['Null']} - {$row['Type']}<br>";
    }
    
    // Drop all foreign key constraints
    echo "<h3>Dropping foreign key constraints...</h3>";
    $stmt = $pdo->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = 'moxo_mart' AND TABLE_NAME = 'orders' AND REFERENCED_TABLE_NAME IS NOT NULL");
    while ($row = $stmt->fetch()) {
        try {
            $pdo->exec("ALTER TABLE orders DROP FOREIGN KEY {$row['CONSTRAINT_NAME']}");
            echo "✅ Dropped constraint: {$row['CONSTRAINT_NAME']}<br>";
        } catch (Exception $e) {
            echo "⚠️ Could not drop constraint {$row['CONSTRAINT_NAME']}: " . $e->getMessage() . "<br>";
        }
    }
    
    // Modify user_id to allow NULL
    echo "<h3>Modifying user_id column...</h3>";
    $pdo->exec("ALTER TABLE orders MODIFY COLUMN user_id INT NULL");
    echo "✅ user_id column updated to allow NULL<br>";
    
    // Re-add foreign key constraint
    echo "<h3>Re-adding foreign key constraint...</h3>";
    $pdo->exec("ALTER TABLE orders ADD CONSTRAINT orders_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT");
    echo "✅ Foreign key constraint re-added<br>";
    
    // Check if guest columns exist
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'guest_email'");
    if (!$stmt->fetch()) {
        echo "<h3>Adding guest columns...</h3>";
        $pdo->exec("ALTER TABLE orders ADD COLUMN guest_email VARCHAR(255) NULL AFTER user_id");
        $pdo->exec("ALTER TABLE orders ADD COLUMN guest_name VARCHAR(255) NULL AFTER guest_email");
        $pdo->exec("ALTER TABLE orders ADD COLUMN guest_phone VARCHAR(20) NULL AFTER guest_name");
        echo "✅ Guest columns added<br>";
    } else {
        echo "✅ Guest columns already exist<br>";
    }
    
    // Show final structure
    echo "<h3>Final Orders Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE orders");
    while ($row = $stmt->fetch()) {
        echo "{$row['Field']} - {$row['Null']} - {$row['Type']}<br>";
    }
    
    // Test inserting a guest order
    echo "<h3>Testing guest order insertion...</h3>";
    $testOrderData = [
        'user_id' => null,
        'guest_email' => 'test@example.com',
        'guest_name' => 'Test User',
        'guest_phone' => '+92-300-1234567',
        'order_number' => 'TEST' . time(),
        'status' => 'pending',
        'total_amount' => 100.00,
        'payment_method' => 'cod',
        'payment_status' => 'pending'
    ];
    
    $columns = implode(', ', array_keys($testOrderData));
    $values = implode(', ', array_fill(0, count($testOrderData), '?'));
    $sql = "INSERT INTO orders ($columns) VALUES ($values)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($testOrderData));
    echo "✅ Test guest order inserted successfully<br>";
    
    // Clean up test data
    $pdo->exec("DELETE FROM orders WHERE order_number = '{$testOrderData['order_number']}'");
    echo "✅ Test data cleaned up<br>";
    
    echo "<h2>✅ Orders table schema fixed for guest checkout!</h2>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?> 