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
    
    -- Notes
    customer_notes TEXT,
    admin_notes TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
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
