-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for logis_db
CREATE DATABASE IF NOT EXISTS `logis_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `logis_db`;

-- Dumping structure for table logis_db.brands
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.brands: ~10 rows (approximately)
DELETE FROM `brands`;
INSERT INTO `brands` (`id`, `name`, `created_at`) VALUES
	(1, 'Coca-Cola', '2026-01-07 13:24:45'),
	(2, 'Pepsi Co.', '2026-01-07 13:24:45'),
	(3, 'Nestlé', '2026-01-07 13:24:45'),
	(4, 'Unilever', '2026-01-07 13:24:45'),
	(5, 'Mondelez', '2026-01-07 13:24:45'),
	(6, 'Kraft', '2026-01-07 13:24:45'),
	(7, 'Barilla', '2026-01-07 13:24:45'),
	(8, 'Dove', '2026-01-07 13:24:45'),
	(9, 'Nestl├®', '2026-01-07 14:03:47'),
	(17, 'Nestl??', '2026-01-07 14:04:01');

-- Dumping structure for table logis_db.cart
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.cart: ~1 rows (approximately)
DELETE FROM `cart`;
INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`, `updated_at`) VALUES
	(22, 3, 2, 12, '2026-01-14 19:03:59', '2026-01-14 19:03:59');

-- Dumping structure for table logis_db.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.categories: ~8 rows (approximately)
DELETE FROM `categories`;
INSERT INTO `categories` (`id`, `name`, `icon`, `description`, `created_at`) VALUES
	(1, 'Beverages', 'local_drink', 'Soft drinks, juices, and beverages', '2026-01-07 13:24:45'),
	(2, 'Snacks', 'bakery_dining', 'Chips, cookies, and snack items', '2026-01-07 13:24:45'),
	(3, 'Staples', 'grocery', 'Rice, flour, and essential staples', '2026-01-07 13:24:45'),
	(4, 'Home Care', 'clean_hands', 'Cleaning and household products', '2026-01-07 13:24:45'),
	(5, 'Personal Care', 'sentiment_satisfied', 'Personal hygiene products', '2026-01-07 13:24:45'),
	(6, 'Dairy', 'egg', 'Milk, cheese, and dairy products', '2026-01-07 13:24:45'),
	(7, 'Frozen Foods', 'ac_unit', 'Frozen items and ice cream', '2026-01-07 13:24:45'),
	(8, 'Confectionery', 'cake', 'Chocolates and sweets', '2026-01-07 13:24:45'),
	(25, 'protine', NULL, NULL, '2026-01-07 14:41:45');

-- Dumping structure for table logis_db.drivers
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `license_plate` varchar(20) NOT NULL,
  `distribution_centre` varchar(100) NOT NULL,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `start_date` date NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.drivers: ~1 rows (approximately)
DELETE FROM `drivers`;
INSERT INTO `drivers` (`id`, `employee_id`, `full_name`, `phone_number`, `email`, `license_number`, `vehicle_type`, `vehicle_model`, `license_plate`, `distribution_centre`, `status`, `start_date`, `profile_image`, `created_at`, `updated_at`) VALUES
	(2, 'EMP-2026-002', 'ravi', '+945698743', 'ravi@gmail.com', 'DL-42365894', 'truck', 'Toyota  Truck', 'BBK-569853', 'East RDC - Changi', 'active', '2026-01-05', NULL, '2026-01-08 10:25:43', '2026-01-08 10:25:43');

-- Dumping structure for table logis_db.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_province` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(20) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('stripe','cash','bank_transfer') DEFAULT 'stripe',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `stripe_payment_intent_id` varchar(255) DEFAULT NULL,
  `stripe_charge_id` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `order_status` enum('pending','processing','packed','shipped','delivered','cancelled') DEFAULT 'pending',
  `driver_id` int(11) DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `driver_id` (`driver_id`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_order_status` (`order_status`),
  KEY `idx_payment_status` (`payment_status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.orders: ~8 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`, `business_name`, `shipping_address`, `shipping_city`, `shipping_province`, `shipping_postal_code`, `subtotal`, `tax_amount`, `shipping_fee`, `discount_amount`, `total_amount`, `payment_method`, `payment_status`, `stripe_payment_intent_id`, `stripe_charge_id`, `paid_at`, `order_status`, `driver_id`, `customer_notes`, `admin_notes`, `created_at`, `updated_at`) VALUES
	(1, 3, 'ORD-20260108-2760F6', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 2534.40, 0.00, 0.00, 0.00, 2534.40, 'stripe', 'paid', 'pi_3SnHJ47xTxyRTXVz1eUvrdBK', 'cs_test_a1CkZugZpnJr41s36ojoc93QYRczCxY9J3D4WoLRWlCRAqj3CgD7538foE', '2026-01-08 11:23:14', 'shipped', 2, NULL, NULL, '2026-01-08 11:23:14', '2026-01-08 12:15:27'),
	(2, 3, 'ORD-20260108-DA189F', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 25728.00, 0.00, 0.00, 0.00, 25728.00, 'stripe', 'paid', 'pi_3SnHTq7xTxyRTXVz0IDTdRi5', 'cs_test_a17YHATNTx12R34czZ9sDbwgiv5wTDinJHk1Lgq1Y1Yk2JUxv9C4iauVSa', '2026-01-08 11:34:21', 'delivered', 2, NULL, NULL, '2026-01-08 11:34:21', '2026-01-08 14:24:35'),
	(3, 3, 'ORD-20260108-2A7705', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 1267.20, 0.00, 0.00, 0.00, 1267.20, 'stripe', 'paid', 'pi_3SnIFf7xTxyRTXVz0iRNsZbi', 'cs_test_a15hIlRgiJv59h8ANZSAdkwXEQLWuOmyiZOtJJAJ4ZmKn1AQppsq1blEdv', '2026-01-08 12:23:46', 'delivered', 2, NULL, NULL, '2026-01-08 12:23:46', '2026-01-08 12:25:19'),
	(4, 3, 'ORD-20260108-5BA865', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 2534.40, 0.00, 0.00, 0.00, 2534.40, 'stripe', 'paid', 'pi_3SnOUo7xTxyRTXVz008HD4v7', 'cs_test_a1yfcfMmye9aY8o9mgAeyUnSGuL01LHhPJ3tgQb6acXRD4NixaQwpXxqew', '2026-01-08 19:03:49', 'pending', NULL, NULL, NULL, '2026-01-08 19:03:49', '2026-01-08 19:03:49'),
	(5, 3, 'ORD-20260108-3E22B1', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 12864.00, 0.00, 0.00, 0.00, 12864.00, 'stripe', 'paid', 'pi_3SnOVZ7xTxyRTXVz1XaGdrz2', 'cs_test_a1z9dmRpylLU5dvxzjyUXxB7NmxwDqKSslxIoEag6l2iByVI4wgf305uuv', '2026-01-08 19:04:35', 'pending', NULL, NULL, NULL, '2026-01-08 19:04:35', '2026-01-08 19:04:35'),
	(6, 3, 'ORD-20260108-38EA07', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 1267.20, 0.00, 0.00, 0.00, 1267.20, 'stripe', 'paid', 'pi_3SnOW47xTxyRTXVz0ga9DMKp', 'cs_test_a18RLqaIID9YgF4roIzXGd7THhnwWNfY7F9RB82R2AuYJo2r85Uq0yi0KB', '2026-01-08 19:05:07', 'pending', NULL, NULL, NULL, '2026-01-08 19:05:07', '2026-01-08 19:05:07'),
	(7, 3, 'ORD-20260108-0E4EA4', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 1267.20, 0.00, 0.00, 0.00, 1267.20, 'stripe', 'paid', 'pi_3SnOe17xTxyRTXVz1AllxkK1', 'cs_test_a1Zo9Z3rNRlr7BLRlKQBEWDp6bEs2HUhoVVw4KrMEqT5e2gfAJnGWKzCz1', '2026-01-08 19:13:20', 'pending', NULL, NULL, NULL, '2026-01-08 19:13:20', '2026-01-08 19:13:20'),
	(8, 3, 'ORD-20260108-EB665E', 'uki hunter', 'uki@gmail.com', '+945685236', 'uki cafe', '12/2 Kandy Road Mawathagama', '', 'North Western', '', 26995.20, 0.00, 0.00, 0.00, 26995.20, 'stripe', 'paid', 'pi_3SnOrP7xTxyRTXVz02HHoVIW', 'cs_test_b1xaNfmeCQWR3WrM51NDfmxobhRoPCtp1b8sKPyc5XCpBY7X17g3yOkYCH', '2026-01-08 19:27:10', 'pending', NULL, NULL, NULL, '2026-01-08 19:27:10', '2026-01-08 19:27:10');

-- Dumping structure for table logis_db.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(50) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.order_items: ~9 rows (approximately)
DELETE FROM `order_items`;
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `product_image`, `unit_price`, `quantity`, `discount_percentage`, `subtotal`, `created_at`) VALUES
	(4, 1, 2, 'cola', 'SKU 43234', 'assest/product/SKU 43234_1767795227.jpg', 120.00, 24, 12.00, 2534.40, '2026-01-08 11:23:14'),
	(5, 2, 1, 'coffe ', '43234', 'assest/product/43234_1767794876.png', 120.00, 268, 20.00, 25728.00, '2026-01-08 11:34:21'),
	(6, 3, 2, 'cola', 'SKU 43234', 'assest/product/SKU 43234_1767795227.jpg', 120.00, 12, 12.00, 1267.20, '2026-01-08 12:23:46'),
	(7, 4, 2, 'cola', 'SKU 43234', 'assest/product/SKU 43234_1767795227.jpg', 120.00, 24, 12.00, 2534.40, '2026-01-08 19:03:49'),
	(8, 5, 1, 'coffe ', '43234', 'assest/product/43234_1767794876.png', 120.00, 134, 20.00, 12864.00, '2026-01-08 19:04:35'),
	(9, 6, 2, 'cola', 'SKU 43234', 'assest/product/SKU 43234_1767795227.jpg', 120.00, 12, 12.00, 1267.20, '2026-01-08 19:05:07'),
	(10, 7, 2, 'cola', 'SKU 43234', 'assest/product/SKU 43234_1767795227.jpg', 120.00, 12, 12.00, 1267.20, '2026-01-08 19:13:20'),
	(11, 8, 2, 'cola', 'SKU 43234', 'assest/product/SKU 43234_1767795227.jpg', 120.00, 12, 12.00, 1267.20, '2026-01-08 19:27:10'),
	(12, 8, 1, 'coffe ', '43234', 'assest/product/43234_1767794876.png', 120.00, 268, 20.00, 25728.00, '2026-01-08 19:27:10');

-- Dumping structure for table logis_db.order_status_history
CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` enum('pending','processing','packed','shipped','delivered','cancelled') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_order_id` (`order_id`),
  CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.order_status_history: ~18 rows (approximately)
DELETE FROM `order_status_history`;
INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `notes`, `created_by`, `created_at`) VALUES
	(3, 1, 'pending', 'Order placed successfully', NULL, '2026-01-08 11:23:14'),
	(4, 1, 'processing', 'Order approved and being processed', 2, '2026-01-08 11:31:37'),
	(5, 2, 'pending', 'Order placed successfully', NULL, '2026-01-08 11:34:21'),
	(6, 1, 'packed', 'Order has been packed and ready for shipment', 2, '2026-01-08 12:15:21'),
	(7, 1, 'shipped', 'Order dispatched for delivery', 2, '2026-01-08 12:15:27'),
	(8, 3, 'pending', 'Order placed successfully', NULL, '2026-01-08 12:23:46'),
	(9, 3, 'processing', 'Order approved and being processed', 2, '2026-01-08 12:24:54'),
	(10, 3, 'packed', 'Order has been packed and ready for shipment', 2, '2026-01-08 12:25:03'),
	(11, 3, 'shipped', 'Order dispatched for delivery', 2, '2026-01-08 12:25:13'),
	(12, 3, 'delivered', 'Order successfully delivered to customer', 2, '2026-01-08 12:25:19'),
	(13, 2, 'processing', 'Order approved and being processed', 2, '2026-01-08 14:11:46'),
	(14, 2, 'packed', 'Order has been packed and ready for shipment', 2, '2026-01-08 14:12:07'),
	(15, 2, 'shipped', 'Order dispatched for delivery', 2, '2026-01-08 14:12:32'),
	(16, 2, 'delivered', 'Order successfully delivered to customer', 2, '2026-01-08 14:24:35'),
	(17, 4, 'pending', 'Order placed successfully', NULL, '2026-01-08 19:03:49'),
	(18, 5, 'pending', 'Order placed successfully', NULL, '2026-01-08 19:04:35'),
	(19, 6, 'pending', 'Order placed successfully', NULL, '2026-01-08 19:05:07'),
	(20, 7, 'pending', 'Order placed successfully', NULL, '2026-01-08 19:13:20'),
	(21, 8, 'pending', 'Order placed successfully', NULL, '2026-01-08 19:27:10');

-- Dumping structure for table logis_db.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `carton_quantity` int(11) DEFAULT 1,
  `carton_price` decimal(10,2) DEFAULT NULL,
  `stock_level` int(11) DEFAULT 0,
  `max_level` int(11) DEFAULT 0,
  `allocated` int(11) DEFAULT 0,
  `min_order_quantity` int(11) DEFAULT 1,
  `status` enum('active','inactive','out_of_stock') DEFAULT 'active',
  `offer_label` varchar(50) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.products: ~2 rows (approximately)
DELETE FROM `products`;
INSERT INTO `products` (`id`, `sku`, `name`, `description`, `category`, `brand`, `image_path`, `unit_price`, `carton_quantity`, `carton_price`, `stock_level`, `max_level`, `allocated`, `min_order_quantity`, `status`, `offer_label`, `discount_percentage`, `is_featured`, `created_at`, `updated_at`) VALUES
	(1, '43234', 'coffe ', 'god ', 'Beverages', 'Barilla', 'assest/product/43234_1767794876.png', 120.00, 134, 16080.00, 1255, 1900, 2193, 1, 'active', 'Best Seller', 20.00, 1, '2026-01-07 14:07:56', '2026-01-08 19:27:10'),
	(2, 'SKU 43234', 'cola', 'good ones again', 'Beverages', 'Coca-Cola', 'assest/product/SKU 43234_1767795227.jpg', 120.00, 12, 1440.00, 12200, 120000, 1608, 1, 'active', 'New Arrival', 12.00, 1, '2026-01-07 14:13:47', '2026-01-08 19:27:10');

-- Dumping structure for table logis_db.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `province` varchar(100) NOT NULL,
  `user_type` enum('customer','admin','staff') DEFAULT 'customer',
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table logis_db.users: ~7 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `business_name`, `full_name`, `email`, `username`, `password`, `phone_number`, `address`, `province`, `user_type`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Admin', 'System Administrator', 'admin@logis.com', 'admin', '$2y$10$utVgawrmv.qr0jYBmA8BWeoQw/YCmBo5dw9vR0HM38CXvxFeyFbb6', '0000000000', 'System', 'System', 'admin', 'active', '2026-01-06 13:29:50', '2026-01-08 20:19:10'),
	(2, 'staff', 'Tera hunter', 'Tera@gmail.com', 'Tera', '$2y$10$utVgawrmv.qr0jYBmA8BWeoQw/YCmBo5dw9vR0HM38CXvxFeyFbb6', '1234567890', '123 Kandy, City', 'Western', 'staff', 'active', '2026-01-06 13:29:50', '2026-01-06 22:54:33'),
	(3, 'uki cafe', 'uki hunter', 'uki@gmail.com', 'uki', '$2y$10$utVgawrmv.qr0jYBmA8BWeoQw/YCmBo5dw9vR0HM38CXvxFeyFbb6', '+945685236', '12/2 Kandy Road Mawathagama', 'North Western', 'customer', 'active', '2026-01-06 13:30:08', '2026-01-06 13:30:08'),
	(4, 'shan shop', 'shan', 'shan@gmail.com', 'shan', '$2y$10$pGxtdEOAS9UX36ppPuByFeVwxVvDol/kauKkv1XrTpgXc8HQxRASG', '+945685236', 'kandy', 'Western', 'customer', 'active', '2026-01-06 14:50:14', '2026-01-06 14:50:14'),
	(5, 'amila shop', 'amila', 'amila@gmail.com', 'amila', '$2y$10$rzkVu5HFsi2lHtPWHJ23NOTeqbcxYtRGtNWB4EJV5Cqt5DVdtIma6', '+945685236', 'Kandy', 'Northern', 'customer', 'active', '2026-01-06 15:10:18', '2026-01-06 15:10:18'),
	(6, 'samn caffe', 'saman kumara', 'samn@gmail.com', 'saman', '$2y$10$p2I5IaaEC83bQRGecRfCw.hT9k7TOFH26nTfuun1Qzm3xxmXmulzG', '+945867423', '123 kandy', 'Northern', 'customer', 'active', '2026-01-07 12:57:25', '2026-01-07 13:02:17'),
	(7, '', 'akila', 'akila@gmail.com', 'akila', '$2y$10$0K32OSyJePq4uTDaKjIYS.kVNjxR7j2UkYusegRbmJltkebNVjUi2', '+945867423', '', 'Uva', 'staff', 'active', '2026-01-07 13:03:33', '2026-01-07 13:03:33');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
