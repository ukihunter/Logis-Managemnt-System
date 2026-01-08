-- Cart and Orders Schema
-- Add to database.sql

-- Shopping Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Customer Information
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    business_name VARCHAR(255),
    
    -- Shipping Address
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(100),
    shipping_province VARCHAR(100),
    shipping_postal_code VARCHAR(20),
    
    -- Order Details
    subtotal DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    shipping_fee DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    
    -- Payment Information
    payment_method ENUM('stripe', 'cash', 'bank_transfer') DEFAULT 'stripe',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    stripe_payment_intent_id VARCHAR(255),
    stripe_charge_id VARCHAR(255),
    paid_at TIMESTAMP NULL,
    
    -- Order Status
    order_status ENUM('pending', 'processing', 'packed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    
    -- Driver Assignment
    driver_id INT NULL,
    
    -- Notes
    customer_notes TEXT,
    admin_notes TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_user_id (user_id),
    INDEX idx_order_status (order_status),
    INDEX idx_payment_status (payment_status)
);

-- Order Items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NULL,  -- Changed to NULL to allow ON DELETE SET NULL
    
    -- Product snapshot at time of order
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(50) NOT NULL,
    product_image VARCHAR(255),
    
    -- Pricing
    unit_price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    subtotal DECIMAL(10, 2) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id)
);

-- Order Status History table
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status ENUM('pending', 'processing', 'packed', 'shipped', 'delivered', 'cancelled') NOT NULL,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id)
);

-- Sample Orders Data
INSERT INTO orders (user_id, order_number, customer_name, customer_email, customer_phone, business_name, shipping_address, shipping_city, shipping_province, shipping_postal_code, subtotal, tax_amount, shipping_fee, total_amount, payment_method, payment_status, order_status, customer_notes, driver_id, created_at) VALUES
(2, 'ORD-2023-849', 'John Doe', 'john@islandgrocers.com', '+1 555-0199', 'Island Grocers Ltd.', '142 Market Street, Logistics Hub A', 'Northeast City', 'Province A', '12345', 3300.00, 495.00, 0.00, 3795.00, 'stripe', 'paid', 'pending', 'Gate code 1234. Please call 10 mins before arrival.', NULL, '2024-01-15 09:42:00'),
(3, 'ORD-2023-848', 'Sarah Johnson', 'sarah@freshmart.com', '+1 555-0234', 'FreshMart Express', '89 Commerce Ave, Zone B', 'South City', 'Province B', '23456', 890.50, 133.58, 0.00, 1024.08, 'stripe', 'paid', 'processing', NULL, 1, '2024-01-15 08:15:00'),
(4, 'ORD-2023-847', 'Mike Williams', 'mike@cornerstore.com', '+1 555-0387', 'Corner Store Co.', '34 Main Road, District C', 'West City', 'Province C', '34567', 2150.00, 322.50, 50.00, 2522.50, 'bank_transfer', 'pending', 'pending', NULL, NULL, '2024-01-14 16:30:00'),
(5, 'ORD-2023-846', 'Emily Davis', 'emily@quickshop.com', '+1 555-0445', 'QuickShop Retail', '78 Industrial Park, Sector D', 'East City', 'Province D', '45678', 5670.00, 850.50, 0.00, 6520.50, 'stripe', 'paid', 'shipped', 'Fragile items - handle with care', 2, '2024-01-14 11:20:00'),
(2, 'ORD-2023-845', 'David Brown', 'david@megastore.com', '+1 555-0523', 'MegaStore Chain', '156 Highway 7, Location E', 'Central City', 'Province E', '56789', 12340.00, 1851.00, 100.00, 14291.00, 'stripe', 'paid', 'delivered', NULL, 3, '2024-01-13 10:05:00'),
(3, 'ORD-2023-844', 'Lisa Anderson', 'lisa@dailyneeds.com', '+1 555-0601', 'Daily Needs Market', '92 Street View, Hub F', 'North City', 'Province F', '67890', 780.25, 117.04, 0.00, 897.29, 'cash', 'pending', 'pending', NULL, NULL, '2024-01-13 14:45:00'),
(4, 'ORD-2023-843', 'Robert Taylor', 'robert@supplyco.com', '+1 555-0729', 'Supply Co. Ltd.', '45 Warehouse Blvd, Area G', 'South City', 'Province G', '78901', 4560.00, 684.00, 75.00, 5319.00, 'stripe', 'paid', 'processing', 'Delivery between 9 AM - 12 PM only', 4, '2024-01-12 09:30:00'),
(5, 'ORD-2023-842', 'Jennifer Wilson', 'jennifer@valueshop.com', '+1 555-0812', 'Value Shop Inc.', '23 Trade Center, District H', 'West City', 'Province H', '89012', 3200.00, 480.00, 0.00, 3680.00, 'stripe', 'paid', 'cancelled', NULL, NULL, '2024-01-11 15:55:00');

-- Sample Order Items Data
INSERT INTO order_items (order_id, product_id, product_name, product_sku, product_image, unit_price, quantity, discount_percentage, subtotal) VALUES
-- Order 1 (ORD-2023-849) - Island Grocers
(1, 1, 'Premium Jasmine Rice (20kg)', 'RICE-PREM-20KG', NULL, 50.00, 50, 0.00, 2500.00),
(1, 2, 'Sunflower Cooking Oil (5L)', 'OIL-SUN-5L', NULL, 40.00, 20, 0.00, 800.00),

-- Order 2 (ORD-2023-848) - FreshMart Express
(2, 3, 'All-Purpose Flour (10kg)', 'FLOUR-AP-10KG', NULL, 25.50, 35, 0.00, 892.50),

-- Order 3 (ORD-2023-847) - Corner Store
(3, 4, 'White Sugar (50kg)', 'SUGAR-WHT-50KG', NULL, 43.00, 50, 0.00, 2150.00),

-- Order 4 (ORD-2023-846) - QuickShop Retail
(4, 1, 'Premium Jasmine Rice (20kg)', 'RICE-PREM-20KG', NULL, 50.00, 100, 0.00, 5000.00),
(4, 5, 'Pasta Penne (500g)', 'PASTA-PEN-500G', NULL, 6.70, 100, 0.00, 670.00),

-- Order 5 (ORD-2023-845) - MegaStore Chain
(5, 1, 'Premium Jasmine Rice (20kg)', 'RICE-PREM-20KG', NULL, 50.00, 200, 0.00, 10000.00),
(5, 2, 'Sunflower Cooking Oil (5L)', 'OIL-SUN-5L', NULL, 39.00, 60, 0.00, 2340.00),

-- Order 6 (ORD-2023-844) - Daily Needs
(6, 6, 'Canned Tomatoes (400g)', 'CAN-TOM-400G', NULL, 3.25, 240, 0.00, 780.00),

-- Order 7 (ORD-2023-843) - Supply Co
(7, 3, 'All-Purpose Flour (10kg)', 'FLOUR-AP-10KG', NULL, 25.50, 120, 0.00, 3060.00),
(7, 4, 'White Sugar (50kg)', 'SUGAR-WHT-50KG', NULL, 43.00, 30, 0.00, 1290.00),
(7, 7, 'Black Pepper Ground (100g)', 'SPICE-PEP-100G', NULL, 7.00, 30, 0.00, 210.00),

-- Order 8 (ORD-2023-842) - Value Shop (Cancelled)
(8, 1, 'Premium Jasmine Rice (20kg)', 'RICE-PREM-20KG', NULL, 50.00, 40, 0.00, 2000.00),
(8, 2, 'Sunflower Cooking Oil (5L)', 'OIL-SUN-5L', NULL, 40.00, 30, 0.00, 1200.00);

-- Sample Order Status History
INSERT INTO order_status_history (order_id, status, notes, created_by) VALUES
(1, 'pending', 'Order received from customer', NULL),
(2, 'pending', 'Order received from customer', NULL),
(2, 'processing', 'Order approved and being prepared', 1),
(3, 'pending', 'Order received - awaiting payment confirmation', NULL),
(4, 'pending', 'Order received from customer', NULL),
(4, 'processing', 'Payment confirmed, preparing shipment', 1),
(4, 'shipped', 'Dispatched with driver EMP-2026-002', 1),
(5, 'pending', 'Order received from customer', NULL),
(5, 'processing', 'Order approved and packed', 1),
(5, 'shipped', 'Out for delivery', 1),
(5, 'delivered', 'Successfully delivered to customer', 1),
(6, 'pending', 'Order received - cash payment pending', NULL),
(7, 'pending', 'Order received from customer', NULL),
(7, 'processing', 'Being prepared for delivery', 1),
(8, 'pending', 'Order received from customer', NULL),
(8, 'cancelled', 'Customer requested cancellation', 1);
