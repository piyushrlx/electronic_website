-- Migration: 001_baseline_schema.sql
-- Description: Baseline schema definition for TechFusion (electronic_store)
-- Note: This records existing baseline tables. IF NOT EXISTS guarantees idempotent execution.

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','super_admin') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `category` enum('TV','Refrigerator','Mobile') NOT NULL,
  `price` decimal(10,2) unsigned NOT NULL,
  `stock` int(10) unsigned NOT NULL DEFAULT 0,
  `image_url` varchar(500) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category_stock` (`category`,`stock`),
  KEY `idx_brand` (`brand`),
  KEY `idx_price` (`price`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tv_specs` (
  `product_id` int(10) unsigned NOT NULL,
  `screen_size` varchar(50) NOT NULL,
  `resolution` varchar(50) NOT NULL,
  `display_tech` varchar(50) NOT NULL,
  `smart_os` varchar(50) NOT NULL,
  PRIMARY KEY (`product_id`),
  CONSTRAINT `tv_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `fridge_specs` (
  `product_id` int(10) unsigned NOT NULL,
  `capacity_liters` smallint(5) unsigned NOT NULL,
  `door_type` varchar(50) NOT NULL,
  `defrost_type` varchar(50) NOT NULL,
  `energy_rating` tinyint(3) unsigned NOT NULL DEFAULT 3,
  PRIMARY KEY (`product_id`),
  CONSTRAINT `fridge_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `mobile_specs` (
  `product_id` int(10) unsigned NOT NULL,
  `ram_storage` varchar(50) NOT NULL,
  `battery_mah` smallint(5) unsigned NOT NULL,
  `camera_mp` varchar(50) NOT NULL,
  `network_type` varchar(20) NOT NULL DEFAULT '5G',
  PRIMARY KEY (`product_id`),
  CONSTRAINT `mobile_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `shipping_address` text NOT NULL,
  `total_amount` decimal(10,2) unsigned NOT NULL,
  `order_status` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_status` (`order_status`),
  KEY `idx_customer_email` (`customer_email`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` smallint(5) unsigned NOT NULL,
  `unit_price` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_order_id` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `target_entity` varchar(50) NOT NULL,
  `target_id` int(10) unsigned DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
