-- SadaCart Database Seed Data
-- Insert demo data for development and testing

-- Insert categories
INSERT INTO categories (name, slug, description, is_active, sort_order, created_at, updated_at) VALUES
('Electronics', 'electronics', 'Latest gadgets and electronic devices', 1, 1, NOW(), NOW()),
('Clothing', 'clothing', 'Fashion and apparel for all ages', 1, 2, NOW(), NOW()),
('Home & Garden', 'home-garden', 'Everything for your home and garden', 1, 3, NOW(), NOW()),
('Sports & Outdoors', 'sports-outdoors', 'Sports equipment and outdoor gear', 1, 4, NOW(), NOW()),
('Books', 'books', 'Books, magazines, and educational materials', 1, 5, NOW(), NOW());

-- Insert admin user
INSERT INTO users (email, password, first_name, last_name, role, is_active, email_verified, created_at, updated_at) VALUES
('admin@sadacart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 1, 1, NOW(), NOW());

-- Insert demo products
INSERT INTO products (name, slug, description, short_description, sku, price, compare_price, cost_price, stock_quantity, weight, category_id, brand, status, featured, meta_title, meta_description, created_at, updated_at) VALUES
(
    'Wireless Bluetooth Headphones',
    'wireless-bluetooth-headphones',
    '<p>Experience premium sound quality with these state-of-the-art wireless Bluetooth headphones. Featuring active noise cancellation, 30-hour battery life, and comfortable over-ear design.</p><p><strong>Key Features:</strong></p><ul><li>Active Noise Cancellation (ANC)</li><li>30-hour battery life</li><li>Quick charge: 5 minutes = 3 hours playback</li><li>Premium leather ear cushions</li><li>Built-in microphone for calls</li><li>Compatible with all Bluetooth devices</li></ul><p>Perfect for music lovers, commuters, and professionals who demand the best audio experience.</p>',
    'Premium wireless headphones with active noise cancellation and 30-hour battery life.',
    'WBH-001',
    199.99,
    249.99,
    120.00,
    50,
    1.2,
    1,
    'AudioTech',
    'active',
    1,
    'Wireless Bluetooth Headphones - Premium Sound Quality',
    'Shop premium wireless Bluetooth headphones with active noise cancellation, 30-hour battery, and superior comfort. Free shipping on orders over $50.',
    NOW(),
    NOW()
),
(
    'Organic Cotton T-Shirt',
    'organic-cotton-t-shirt',
    '<p>Soft, comfortable, and environmentally friendly organic cotton t-shirt. Made from 100% certified organic cotton with a relaxed fit that''s perfect for everyday wear.</p><p><strong>Features:</strong></p><ul><li>100% certified organic cotton</li><li>Pre-shrunk for consistent fit</li><li>Reinforced seams for durability</li><li>Available in multiple colors</li><li>Unisex sizing</li><li>Machine washable</li></ul><p>This versatile t-shirt is perfect for casual wear, layering, or as a comfortable base layer. The organic cotton is gentle on your skin and better for the environment.</p>',
    'Comfortable organic cotton t-shirt made from 100% certified organic materials.',
    'OCT-001',
    29.99,
    39.99,
    15.00,
    100,
    0.3,
    2,
    'EcoWear',
    'active',
    1,
    'Organic Cotton T-Shirt - Comfortable & Eco-Friendly',
    'Shop our comfortable organic cotton t-shirt made from 100% certified organic materials. Soft, durable, and environmentally friendly.',
    NOW(),
    NOW()
),
(
    'Smart Home Security Camera',
    'smart-home-security-camera',
    '<p>Keep your home secure with this advanced smart security camera featuring 4K resolution, night vision, and AI-powered motion detection.</p><p><strong>Advanced Features:</strong></p><ul><li>4K Ultra HD video recording</li><li>Color night vision technology</li><li>AI-powered person and vehicle detection</li><li>Two-way audio communication</li><li>Weather-resistant design (IP65)</li><li>Cloud and local storage options</li><li>Mobile app with real-time alerts</li><li>Easy wireless setup</li></ul><p>Monitor your property 24/7 with crystal-clear video quality and intelligent alerts that distinguish between people, vehicles, and other motion.</p>',
    '4K smart security camera with AI detection, night vision, and mobile app control.',
    'SHSC-001',
    149.99,
    199.99,
    85.00,
    25,
    0.8,
    1,
    'SecureHome',
    'active',
    1,
    'Smart Home Security Camera - 4K AI Detection',
    'Advanced 4K smart security camera with AI-powered detection, night vision, and mobile alerts. Keep your home secure 24/7.',
    NOW(),
    NOW()
),
(
    'Ceramic Plant Pot Set',
    'ceramic-plant-pot-set',
    '<p>Beautiful set of three ceramic plant pots perfect for indoor plants and herbs. Each pot features a unique glazed finish and comes with drainage holes and matching saucers.</p><p><strong>Set Includes:</strong></p><ul><li>3 ceramic pots in different sizes (4", 6", 8")</li><li>3 matching drainage saucers</li><li>Drainage holes for healthy plant growth</li><li>Glossy ceramic finish</li><li>Available in multiple color options</li><li>Perfect for succulents, herbs, or small plants</li></ul><p>These elegant pots will complement any home decor style and provide the perfect home for your favorite plants.</p>',
    'Set of 3 beautiful ceramic plant pots with drainage holes and matching saucers.',
    'CPPS-001',
    49.99,
    64.99,
    25.00,
    30,
    2.1,
    3,
    'GardenCraft',
    'active',
    0,
    'Ceramic Plant Pot Set - Beautiful Indoor Planters',
    'Elegant set of 3 ceramic plant pots with drainage holes and saucers. Perfect for indoor plants, herbs, and succulents.',
    NOW(),
    NOW()
),
(
    'Professional Yoga Mat',
    'professional-yoga-mat',
    '<p>Premium non-slip yoga mat designed for serious practitioners. Made from eco-friendly TPE material with superior grip and cushioning for all types of yoga practice.</p><p><strong>Professional Features:</strong></p><ul><li>6mm thick for optimal cushioning</li><li>Non-slip surface on both sides</li><li>Eco-friendly TPE material</li><li>Extra long (72") and wide (24")</li><li>Lightweight and portable</li><li>Easy to clean and maintain</li><li>Comes with carrying strap</li><li>Available in multiple colors</li></ul><p>Whether you''re a beginner or advanced practitioner, this mat provides the stability and comfort you need for your yoga journey.</p>',
    'Premium 6mm yoga mat with non-slip surface and eco-friendly TPE material.',
    'PYM-001',
    79.99,
    99.99,
    35.00,
    40,
    1.8,
    4,
    'ZenFit',
    'active',
    1,
    'Professional Yoga Mat - Non-Slip Premium Quality',
    'Premium 6mm yoga mat with superior grip and cushioning. Made from eco-friendly TPE material. Perfect for all yoga styles.',
    NOW(),
    NOW()
);

-- Insert product images
INSERT INTO product_images (product_id, image_path, alt_text, is_primary, sort_order, created_at) VALUES
(1, 'headphones-main.jpg', 'Wireless Bluetooth Headphones - Main View', 1, 1, NOW()),
(1, 'headphones-side.jpg', 'Wireless Bluetooth Headphones - Side View', 0, 2, NOW()),
(1, 'headphones-case.jpg', 'Wireless Bluetooth Headphones - With Case', 0, 3, NOW()),
(2, 'tshirt-main.jpg', 'Organic Cotton T-Shirt - Front View', 1, 1, NOW()),
(2, 'tshirt-back.jpg', 'Organic Cotton T-Shirt - Back View', 0, 2, NOW()),
(3, 'camera-main.jpg', 'Smart Home Security Camera - Main View', 1, 1, NOW()),
(3, 'camera-app.jpg', 'Smart Home Security Camera - Mobile App', 0, 2, NOW()),
(4, 'pots-main.jpg', 'Ceramic Plant Pot Set - Complete Set', 1, 1, NOW()),
(4, 'pots-individual.jpg', 'Ceramic Plant Pot Set - Individual Pots', 0, 2, NOW()),
(5, 'yoga-mat-main.jpg', 'Professional Yoga Mat - Main View', 1, 1, NOW()),
(5, 'yoga-mat-rolled.jpg', 'Professional Yoga Mat - Rolled with Strap', 0, 2, NOW());

-- Insert product attributes
INSERT INTO product_attributes (product_id, name, value, created_at) VALUES
(1, 'Color', 'Black', NOW()),
(1, 'Color', 'White', NOW()),
(1, 'Color', 'Silver', NOW()),
(1, 'Connectivity', 'Bluetooth 5.0', NOW()),
(1, 'Battery Life', '30 hours', NOW()),
(1, 'Warranty', '2 years', NOW()),
(2, 'Size', 'XS', NOW()),
(2, 'Size', 'S', NOW()),
(2, 'Size', 'M', NOW()),
(2, 'Size', 'L', NOW()),
(2, 'Size', 'XL', NOW()),
(2, 'Size', 'XXL', NOW()),
(2, 'Color', 'White', NOW()),
(2, 'Color', 'Black', NOW()),
(2, 'Color', 'Navy', NOW()),
(2, 'Color', 'Gray', NOW()),
(2, 'Material', '100% Organic Cotton', NOW()),
(3, 'Resolution', '4K Ultra HD', NOW()),
(3, 'Night Vision', 'Color Night Vision', NOW()),
(3, 'Storage', 'Cloud & Local', NOW()),
(3, 'Weather Rating', 'IP65', NOW()),
(3, 'Power', 'Rechargeable Battery', NOW()),
(4, 'Material', 'Ceramic', NOW()),
(4, 'Sizes', '4", 6", 8"', NOW()),
(4, 'Color', 'White', NOW()),
(4, 'Color', 'Terracotta', NOW()),
(4, 'Color', 'Blue', NOW()),
(4, 'Features', 'Drainage Holes', NOW()),
(5, 'Thickness', '6mm', NOW()),
(5, 'Dimensions', '72" x 24"', NOW()),
(5, 'Material', 'Eco-friendly TPE', NOW()),
(5, 'Color', 'Purple', NOW()),
(5, 'Color', 'Blue', NOW()),
(5, 'Color', 'Green', NOW()),
(5, 'Color', 'Pink', NOW()),
(5, 'Features', 'Non-slip both sides', NOW());

-- Insert site settings
INSERT INTO settings (key_name, value, created_at, updated_at) VALUES
('site_name', 'SadaCart', NOW(), NOW()),
('site_tagline', 'Your Trusted Online Shopping Destination', NOW(), NOW()),
('site_description', 'Discover amazing products at great prices with fast shipping and excellent customer service.', NOW(), NOW()),
('admin_email', 'admin@sadacart.com', NOW(), NOW()),
('contact_phone', '1-800-SADACART', NOW(), NOW()),
('currency', 'USD', NOW(), NOW()),
('tax_rate', '8.00', NOW(), NOW()),
('free_shipping_threshold', '50.00', NOW(), NOW()),
('default_shipping_cost', '9.99', NOW(), NOW()),
('inventory_tracking', '1', NOW(), NOW()),
('guest_checkout', '1', NOW(), NOW()),
('order_confirmation_email', '1', NOW(), NOW()),
('shipping_notification_email', '1', NOW(), NOW()),
('low_stock_alerts', '1', NOW(), NOW()),
('meta_title', 'SadaCart - Your Trusted Online Shopping Destination', NOW(), NOW()),
('meta_description', 'Shop the latest products at SadaCart. Fast shipping, great prices, and excellent customer service. Free shipping on orders over $50.', NOW(), NOW()),
('meta_keywords', 'ecommerce, online shopping, electronics, clothing, home garden, sports', NOW(), NOW()),
('sitemap_enabled', '1', NOW(), NOW()),
('robots_txt_enabled', '1', NOW(), NOW());

-- Insert blog posts
INSERT INTO blog_posts (title, slug, content, excerpt, category_id, author_id, status, meta_title, meta_description, published_at, created_at, updated_at) VALUES
(
    'Welcome to SadaCart - Your New Favorite Shopping Destination',
    'welcome-to-sadacart',
    '<p>We''re excited to introduce you to SadaCart, your new go-to destination for online shopping. Our mission is simple: to provide you with an exceptional shopping experience featuring quality products, competitive prices, and outstanding customer service.</p><h2>What Makes SadaCart Special?</h2><p>At SadaCart, we believe shopping should be enjoyable, not stressful. That''s why we''ve carefully curated our product selection to include only the best items from trusted brands and suppliers.</p><h3>Our Commitment to You</h3><ul><li>Quality products at competitive prices</li><li>Fast and reliable shipping</li><li>Excellent customer support</li><li>Secure and easy checkout process</li><li>30-day return policy</li></ul><p>Whether you''re looking for the latest electronics, comfortable clothing, home essentials, or outdoor gear, we''ve got you covered. Our team works tirelessly to ensure every product meets our high standards for quality and value.</p><p>Thank you for choosing SadaCart. We look forward to serving you and making your shopping experience exceptional!</p>',
    'Discover what makes SadaCart your ideal online shopping destination. Learn about our commitment to quality, service, and customer satisfaction.',
    1,
    1,
    'published',
    'Welcome to SadaCart - Your Online Shopping Destination',
    'Welcome to SadaCart! Discover quality products, competitive prices, and exceptional customer service. Your new favorite shopping destination awaits.',
    NOW(),
    NOW(),
    NOW()
),
(
    '5 Tips for Safe Online Shopping',
    '5-tips-safe-online-shopping',
    '<p>Online shopping has become an integral part of our daily lives, but it''s important to stay safe while Browse and buying. Here are five essential tips to help you shop securely online.</p><h2>1. Shop on Secure Websites</h2><p>Always look for the padlock icon in your browser''s address bar and ensure the URL starts with "https://" rather than just "http://". This indicates that the website uses encryption to protect your data.</p><h2>2. Use Strong, Unique Passwords</h2><p>Create strong passwords for your shopping accounts and never reuse the same password across multiple sites. Consider using a password manager to keep track of your credentials securely.</p><h2>3. Be Cautious with Public Wi-Fi</h2><p>Avoid making purchases while connected to public Wi-Fi networks. If you must shop on public Wi-Fi, consider using a VPN to encrypt your connection.</p><h2>4. Check Your Statements Regularly</h2><p>Monitor your credit card and bank statements regularly for any unauthorized charges. Report suspicious activity to your financial institution immediately.</p><h2>5. Research the Retailer</h2><p>Before making a purchase from a new website, research the company. Read reviews, check their return policy, and verify their contact information.</p><p>By following these simple tips, you can enjoy the convenience of online shopping while keeping your personal and financial information secure.</p>',
    'Learn essential tips for safe online shopping. Protect your personal information and financial data while enjoying the convenience of e-commerce.',
    3,
    1,
    'published',
    '5 Essential Tips for Safe Online Shopping | SadaCart Blog',
    'Stay safe while shopping online with these 5 essential security tips. Protect your personal and financial information during e-commerce transactions.',
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    DATE_SUB(NOW(), INTERVAL 7 DAY)
);

-- Insert newsletter subscriptions (sample)
INSERT INTO newsletter_subscriptions (email, status, subscribed_at) VALUES
('subscriber1@example.com', 'active', NOW()),
('subscriber2@example.com', 'active', DATE_SUB(NOW(), INTERVAL 5 DAY)),
('subscriber3@example.com', 'active', DATE_SUB(NOW(), INTERVAL 10 DAY));

-- Insert sample customer (for testing orders)
INSERT INTO users (email, password, first_name, last_name, phone, role, is_active, email_verified, created_at, updated_at) VALUES
('customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '555-123-4567', 'customer', 1, 1, NOW(), NOW());

-- Insert sample order
INSERT INTO orders (
    order_number, user_id, status, payment_status, payment_method, 
    subtotal, tax_amount, shipping_amount, total_amount,
    created_at, updated_at
) VALUES (
    'SC-2024-000001', 2, 'processing', 'paid', 'credit_card',
    229.98, 18.40, 0.00, 248.38,
    DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)
);

-- Insert order items
INSERT INTO order_items (order_id, product_id, product_name, product_sku, quantity, price, total, created_at) VALUES
(1, 1, 'Wireless Bluetooth Headphones', 'WBH-001', 1, 199.99, 199.99, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 2, 'Organic Cotton T-Shirt', 'OCT-001', 1, 29.99, 29.99, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Update product stock quantities after order
UPDATE products SET stock_quantity = stock_quantity - 1 WHERE id IN (1, 2);

-- Insert activity logs
INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) VALUES
(1, 'login', 'Admin user logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW()),
(2, 'register', 'New customer registered', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'order_placed', 'Order SC-2024-000001 placed', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Insert user address
INSERT INTO user_addresses (
    user_id, type, first_name, last_name, address_line_1, city, state, postal_code, country, phone, is_default, created_at
) VALUES (
    2, 'shipping', 'John', 'Doe', '123 Main Street', 'New York', 'NY', '10001', 'United States', '555-123-4567', 1, NOW()
);

-- Insert contact form submissions (sample)
INSERT INTO contact_submissions (
    name, email, subject, message, status, created_at
) VALUES (
    'Jane Smith', 'jane.smith@example.com', 'general', 
    'Hello, I love your website! The products look great and the checkout process was very smooth. Keep up the good work!', 
    'new', DATE_SUB(NOW(), INTERVAL 1 DAY)
);

-- Set auto-increment values to ensure consistent IDs
ALTER TABLE categories AUTO_INCREMENT = 6;
ALTER TABLE products AUTO_INCREMENT = 6;
ALTER TABLE users AUTO_INCREMENT = 3;
ALTER TABLE orders AUTO_INCREMENT = 2;
ALTER TABLE blog_posts AUTO_INCREMENT = 3;