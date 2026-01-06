-- Logis Distribution System Database
-- Created: January 6, 2026

CREATE DATABASE IF NOT EXISTS logis_db;
USE logis_db;

-- Users table for customer accounts
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    province VARCHAR(100) NOT NULL,
    user_type ENUM('customer', 'admin', 'staff') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert a default admin user (password: admin123)
INSERT INTO users (business_name, full_name, email, username, password, phone_number, address, province, user_type) 
VALUES ('Admin', 'System Administrator', 'admin@logis.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0000000000', 'System', 'System', 'admin');

-- Insert a sample customer user (password: customer123)
INSERT INTO users (business_name, full_name, email, username, password, phone_number, address, province, user_type) 
VALUES ('Island Retail Store', 'John Doe', 'john@example.com', 'customer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', '123 Main Street, City', 'Western', 'customer');
