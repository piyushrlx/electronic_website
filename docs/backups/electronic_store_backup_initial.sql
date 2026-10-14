-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: electronic_store
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'manager','manager@techfusion.com','$2y$12$0BycnGMBlqug5bUGrGKU0OZ/yFo0zBWbrrox52ppeL7zwVn8jn6Q2','admin',1,NULL,'2026-10-12 09:53:29'),(2,'dev_lead','dev@techfusion.com','$2y$12$X29POdszfMPCQHHmgYBI2uqJoB0kjoCD5PTUDaTD8.iiHA0/pQ11q','super_admin',1,NULL,'2026-10-12 09:53:29');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fridge_specs`
--

DROP TABLE IF EXISTS `fridge_specs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fridge_specs` (
  `product_id` int(10) unsigned NOT NULL,
  `capacity_liters` smallint(5) unsigned NOT NULL,
  `door_type` varchar(50) NOT NULL,
  `defrost_type` varchar(50) NOT NULL,
  `energy_rating` tinyint(3) unsigned NOT NULL DEFAULT 3,
  PRIMARY KEY (`product_id`),
  CONSTRAINT `fridge_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fridge_specs`
--

LOCK TABLES `fridge_specs` WRITE;
/*!40000 ALTER TABLE `fridge_specs` DISABLE KEYS */;
INSERT INTO `fridge_specs` VALUES (3,345,'Double Door','Frost Free',4),(4,400,'Triple Door','Frost Free',5);
/*!40000 ALTER TABLE `fridge_specs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobile_specs`
--

DROP TABLE IF EXISTS `mobile_specs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile_specs` (
  `product_id` int(10) unsigned NOT NULL,
  `ram_storage` varchar(50) NOT NULL,
  `battery_mah` smallint(5) unsigned NOT NULL,
  `camera_mp` varchar(50) NOT NULL,
  `network_type` varchar(20) NOT NULL DEFAULT '5G',
  PRIMARY KEY (`product_id`),
  CONSTRAINT `mobile_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobile_specs`
--

LOCK TABLES `mobile_specs` WRITE;
/*!40000 ALTER TABLE `mobile_specs` DISABLE KEYS */;
INSERT INTO `mobile_specs` VALUES (5,'8GB / 256GB',4422,'48MP + 12MP + 12MP','5G'),(6,'12GB / 512GB',5000,'200MP + 50MP + 12MP + 10MP','5G');
/*!40000 ALTER TABLE `mobile_specs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Sony Bravia XR OLED 55\" 4K','Sony','TV',1299.99,15,'https://images.unsplash.com/photo-1593784991095-a205069470b6?auto=format&fit=crop&w=600&q=80','Cognitive Processor XR delivers realistic depth and deep blacks with OLED clarity.','2026-10-12 09:53:29','2026-10-12 09:53:29'),(2,'LG C3 Series 65\" OLED evo 4K','LG','TV',1799.00,8,'https://images.unsplash.com/photo-1577979749830-f1d742b96791?auto=format&fit=crop&w=600&q=80','Self-lit pixels with ??9 AI Processor Gen6 and Dolby Vision HDR.','2026-10-12 09:53:29','2026-10-12 09:53:29'),(3,'Samsung Double Door Frost-Free Refrigerator 345L','Samsung','Refrigerator',649.00,10,'https://images.unsplash.com/photo-1584568694244-14fbdf83bd30?auto=format&fit=crop&w=600&q=80','Twin Cooling Plus technology with 5 convertible modes and digital inverter.','2026-10-12 09:53:29','2026-10-12 09:53:29'),(4,'Whirlpool Multi-Door Inverter Refrigerator 400L','Whirlpool','Refrigerator',899.50,6,'https://images.unsplash.com/photo-1571175443880-49e1d25b2bc5?auto=format&fit=crop&w=600&q=80','Active Fresh Blue Light technology for extended freshness retention.','2026-10-12 09:53:29','2026-10-12 09:53:29'),(5,'Apple iPhone 15 Pro Max 256GB','Apple','Mobile',1199.00,25,'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=600&q=80','Titanium design with A17 Pro Chip, 48MP triple camera array, and USB-C.','2026-10-12 09:53:29','2026-10-12 09:53:29'),(6,'Samsung Galaxy S24 Ultra 512GB','Samsung','Mobile',1299.99,18,'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=600&q=80','Built-in S-Pen, Snapdragon 8 Gen 3 for Galaxy, and AI zoom photo processing.','2026-10-12 09:53:29','2026-10-12 09:53:29');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tv_specs`
--

DROP TABLE IF EXISTS `tv_specs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tv_specs` (
  `product_id` int(10) unsigned NOT NULL,
  `screen_size` varchar(50) NOT NULL,
  `resolution` varchar(50) NOT NULL,
  `display_tech` varchar(50) NOT NULL,
  `smart_os` varchar(50) NOT NULL,
  PRIMARY KEY (`product_id`),
  CONSTRAINT `tv_specs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tv_specs`
--

LOCK TABLES `tv_specs` WRITE;
/*!40000 ALTER TABLE `tv_specs` DISABLE KEYS */;
INSERT INTO `tv_specs` VALUES (1,'55 Inch','4K UHD (3840x2160)','OLED','Google TV'),(2,'65 Inch','4K UHD (3840x2160)','OLED evo','webOS 23');
/*!40000 ALTER TABLE `tv_specs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-14 11:51:10
