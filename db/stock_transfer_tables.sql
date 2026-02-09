-- SQL Script for Stock Transfer Feature
-- Created: February 9, 2026
-- This script creates the necessary tables for the stock transfer feature

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `manager_name` varchar(100) DEFAULT NULL,
  `status` enum('active', 'inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample branches
INSERT INTO `branches` (`name`, `location`, `address`, `phone`, `status`) VALUES
('Colombo Branch', 'Colombo', '123 Main Street, Colombo 01', '+94112345677', 'active'),
('Kandy Branch', 'Kandy', '456 Peradeniya Road, Kandy', '+94812345678', 'active'),
('Galle Branch', 'Galle', '789 Fort Road, Galle', '+94912345679', 'active'),
('Negombo Branch', 'Negombo', '321 Beach Road, Negombo', '+94312345680', 'active'),
('Jaffna Branch', 'Jaffna', '654 Hospital Road, Jaffna', '+94212345681', 'active');

-- Create stock_transfers table
CREATE TABLE IF NOT EXISTS `stock_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `transferred_by` int(11) NOT NULL,
  `transferred_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending', 'completed', 'cancelled') DEFAULT 'completed',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `branch_id` (`branch_id`),
  KEY `transferred_by` (`transferred_by`),
  CONSTRAINT `stock_transfers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_ibfk_3` FOREIGN KEY (`transferred_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add index for better query performance
CREATE INDEX idx_transferred_at ON stock_transfers(transferred_at DESC);
CREATE INDEX idx_status ON stock_transfers(status);
