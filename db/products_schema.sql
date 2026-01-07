-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL,
    brand VARCHAR(100),
    image_path VARCHAR(255),
    unit_price DECIMAL(10, 2) NOT NULL,
    carton_quantity INT DEFAULT 1,
    carton_price DECIMAL(10, 2),
    stock_level INT DEFAULT 0,
    max_level INT DEFAULT 0,
    allocated INT DEFAULT 0,
    min_order_quantity INT DEFAULT 1,
    status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active',
    offer_label VARCHAR(50),
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, icon, description) VALUES
('Beverages', 'local_drink', 'Soft drinks, juices, and beverages'),
('Snacks', 'bakery_dining', 'Chips, cookies, and snack items'),
('Staples', 'grocery', 'Rice, flour, and essential staples'),
('Home Care', 'clean_hands', 'Cleaning and household products'),
('Personal Care', 'sentiment_satisfied', 'Personal hygiene products'),
('Dairy', 'egg', 'Milk, cheese, and dairy products'),
('Frozen Foods', 'ac_unit', 'Frozen items and ice cream'),
('Confectionery', 'cake', 'Chocolates and sweets')
ON DUPLICATE KEY UPDATE name=name;

-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default brands
INSERT INTO brands (name) VALUES
('Coca-Cola'),
('Pepsi Co.'),
('Nestlé'),
('Unilever'),
('Mondelez'),
('Kraft'),
('Barilla'),
('Dove')
ON DUPLICATE KEY UPDATE name=name;
