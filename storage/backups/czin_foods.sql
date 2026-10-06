-- MySQL dump 10.13  Distrib 8.0.44, for macos12.7 (arm64)
--
-- Host: 127.0.0.1    Database: czin_foods
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_user_id_foreign` (`user_id`),
  CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_notifications`
--

DROP TABLE IF EXISTS `admin_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `dismissed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_notifications_reference_key_unique` (`reference_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notifications`
--

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('czin-cache-a75f3f172bfb296f2e10cbfc6dfc1883','i:2;',1785766640),('czin-cache-a75f3f172bfb296f2e10cbfc6dfc1883:timer','i:1785766640;',1785766640);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `special_request` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reserved_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_cart_id_product_variant_id_unique` (`cart_id`,`product_variant_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  KEY `cart_items_product_variant_id_foreign` (`product_variant_id`),
  KEY `cart_items_cart_id_index` (`cart_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (1,16,1,3,1,45.00,NULL,NULL,'2026-07-27 16:10:15','2026-07-27 16:10:15');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_user_id_foreign` (`user_id`),
  KEY `carts_session_id_index` (`session_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,NULL,'VgyVxzYIB7L4HTNSC3R2pKycEuVNm1VKICxD2Das','2026-07-24 10:12:21','2026-07-24 10:12:21'),(2,NULL,'SfK6JCOQ77qIS82cejidBO0yfzyoNzxEynw5qbg0','2026-07-24 10:48:03','2026-07-24 10:48:03'),(3,NULL,'ksGN4EcpJSkzEeiQg2lILL0FhKjbBLklvrJU4And','2026-07-24 10:48:03','2026-07-24 10:48:03'),(4,NULL,'lBtv4VF9Xi3FVseDF0BCCyWqM0NBE1FYbyOrkqOK','2026-07-24 10:48:03','2026-07-24 10:48:03'),(5,NULL,'lQFbqkBluFSeVU3UzYft8jieoR2tBEKIfCiiw6Oy','2026-07-24 13:21:45','2026-07-24 13:21:45'),(6,NULL,'02dMupN54Tk9joUQAYfmn2PetOpPjvRcqSTB71kf','2026-07-24 13:21:52','2026-07-24 13:21:52'),(7,NULL,'tEYBYfB0S8Ri0S9a0iUXkNs0n6cUxxxkuMLUI8RJ','2026-07-24 13:24:22','2026-07-24 13:24:22'),(8,NULL,'6HIP000H5yHlaMOtome9MYAwN9xcFr6gCobUjCtS','2026-07-24 13:26:09','2026-07-24 13:26:09'),(9,NULL,'nZ2PWkPawaD992APaLFaW0YwWzlCL38pCrf9dK55','2026-07-24 13:29:36','2026-07-24 13:29:36'),(10,NULL,'rTHeadBbYEo3F7dP1ld2ZJGv8BbFIufO4b0dwd6f','2026-07-24 13:40:55','2026-07-24 13:40:55'),(11,NULL,'EsxXT85rKF7x5lX8N9s3Vp2iS2NyxlUarCW6f7rT','2026-07-24 13:40:56','2026-07-24 13:40:56'),(12,NULL,'ZONV5yks3uaXg7s53CwaBvrNsPZKXcmpPcFxwDKe','2026-07-24 13:50:09','2026-07-24 13:50:09'),(13,NULL,'hLhnh7irlRHtuSm1Ks2htK6Bf1o7zwaZFg4u0BNc','2026-07-27 08:31:50','2026-07-27 08:31:50'),(14,1,NULL,'2026-07-27 08:44:48','2026-07-27 08:44:48'),(15,NULL,'Y3ZpIVIuGfUPSNrSwrKoSfD0vxWcnlq4C4K2E7Oz','2026-07-27 12:22:28','2026-07-27 12:22:28'),(16,NULL,'VnrQ7ZOthVaEBr6UvSfqorNEurW1KOOEGiOPK5tD','2026-07-27 15:49:42','2026-07-27 15:49:42'),(17,NULL,'tePH6K30HE1zzTnFu6ElaGqtUc63c8Rjy944jjqi','2026-07-28 08:22:04','2026-07-28 08:22:04'),(18,NULL,'device:068f4b57ad15bcc4046942048c64ab86','2026-08-03 12:37:02','2026-08-03 12:37:02'),(19,NULL,'PX3hzNghoY3An2XGgLOUnwxMBBgWqKZoDeWHNwpx','2026-08-03 12:56:06','2026-08-03 12:56:06'),(20,NULL,'device:test-img-check','2026-08-03 13:07:33','2026-08-03 13:07:33');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `show_in_navbar` tinyint(1) NOT NULL DEFAULT '0',
  `navbar_sort_order` int unsigned NOT NULL DEFAULT '0',
  `shop_sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,'Mains','mains','Hearty plates and signature restaurant favourites.',NULL,'active',1,1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(2,NULL,'Sides','sides','Extras and shareable sides to complete your meal.',NULL,'active',1,2,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,NULL,'Drinks','drinks','Cold drinks and local favourites to go with your order.',NULL,'active',1,3,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,NULL,'Desserts','desserts','Sweet finishes after a satisfying meal.',NULL,'active',1,4,4,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `min_order_amount` decimal(12,2) DEFAULT NULL,
  `max_discount` decimal(12,2) DEFAULT NULL,
  `usage_limit` int unsigned DEFAULT NULL,
  `used_count` int unsigned NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'WELCOME10','percent',10.00,100.00,100.00,100,0,NULL,NULL,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(2,'SAVE50','fixed',50.00,200.00,NULL,50,0,NULL,NULL,1,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_tags`
--

DROP TABLE IF EXISTS `customer_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_tags_user_id_tag_unique` (`user_id`,`tag`),
  CONSTRAINT `customer_tags_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_tags`
--

LOCK TABLES `customer_tags` WRITE;
/*!40000 ALTER TABLE `customer_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_assignments`
--

DROP TABLE IF EXISTS `delivery_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `driver_user_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_assignments_order_id_unique` (`order_id`),
  KEY `delivery_assignments_driver_user_id_foreign` (`driver_user_id`),
  CONSTRAINT `delivery_assignments_driver_user_id_foreign` FOREIGN KEY (`driver_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_assignments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_assignments`
--

LOCK TABLES `delivery_assignments` WRITE;
/*!40000 ALTER TABLE `delivery_assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dining_tables`
--

DROP TABLE IF EXISTS `dining_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dining_tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` smallint unsigned NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'small',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dining_tables_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dining_tables`
--

LOCK TABLES `dining_tables` WRITE;
/*!40000 ALTER TABLE `dining_tables` DISABLE KEYS */;
INSERT INTO `dining_tables` VALUES (1,'T-01','Table 1','Main Hall',4,'small',126.00,'available',1,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(2,'T-02','Table 2','Main Hall',6,'medium',126.00,'occupied',2,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(3,'T-03','Table 3','Main Hall',8,'large',126.00,'reserved',3,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(4,'T-04','Table 4','Main Hall',4,'small',126.00,'available',4,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(5,'T-05','Table 5','Main Hall',6,'medium',126.00,'occupied',5,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(6,'T-06','Table 6','Main Hall',8,'large',126.00,'available',6,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(7,'T-07','Table 7','Patio',4,'small',98.00,'occupied',7,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(8,'T-08','Table 8','Patio',6,'medium',110.00,'reserved',8,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(9,'T-09','Table 9','Patio',4,'small',98.00,'available',9,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(10,'T-10','Table 10','Patio',6,'medium',110.00,'available',10,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(11,'T-11','Table 11','VIP Lounge',8,'large',180.00,'occupied',11,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(12,'T-12','Table 12','VIP Lounge',8,'large',180.00,'available',12,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(13,'T-13','Table 13','VIP Lounge',6,'medium',150.00,'available',13,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(14,'T-14','Table 14','Bar Area',4,'small',85.00,'occupied',14,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(15,'T-15','Table 15','Bar Area',4,'small',85.00,'available',15,'2026-07-27 13:48:14','2026-07-27 13:48:14'),(16,'T-16','Table 16','Bar Area',6,'medium',95.00,'available',16,'2026-07-27 13:48:14','2026-07-27 13:48:14');
/*!40000 ALTER TABLE `dining_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_dispatches`
--

DROP TABLE IF EXISTS `email_dispatches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_dispatches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email_template_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `is_test` tinyint(1) NOT NULL DEFAULT '0',
  `sent_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_dispatches_order_id_foreign` (`order_id`),
  KEY `email_dispatches_email_template_slug_sent_at_index` (`email_template_slug`,`sent_at`),
  CONSTRAINT `email_dispatches_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_dispatches`
--

LOCK TABLES `email_dispatches` WRITE;
/*!40000 ALTER TABLE `email_dispatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_dispatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `placeholders` json DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES (1,'welcome','Welcome email','Sent when a customer creates an account.','Welcome to {{store_name}}','# Welcome to {{store_name}}\n\nHi {{first_name}},\n\nThanks for creating your account. You can now shop our latest footwear, track orders, and manage your profile anytime.\n\nWe are glad to have you with us — step into style with every order.','[\"first_name\", \"customer_name\", \"store_name\"]',1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(2,'order_created','Order received','Sent when a new order is placed and awaiting payment.','Order {{order_number}} received','# Order Received\n\nHi {{customer_name}},\n\nThank you for shopping with {{store_name}}. We received your order and it is waiting for payment.\n\nComplete payment by **{{payment_due_at}}** to confirm your order. If payment is not received within {{payment_timeout_hours}} hours, the order will be cancelled and items returned to stock.\n\nIf you have questions, contact us at {{contact_email}} or {{contact_phone}}.','[\"customer_name\", \"store_name\", \"order_number\", \"payment_due_at\", \"payment_timeout_hours\", \"contact_email\", \"contact_phone\"]',2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,'payment_received','Payment confirmed','Sent when payment is successful. Includes the PDF invoice attachment.','Good things are heading your way! — {{store_name}}','# Good things are heading your way!\n\nHi {{customer_name}},\n\nWe have finished processing your order. Here\'s a reminder of what you\'ve ordered.\n\nYour invoice is attached to this email as a PDF — you can open it directly from your inbox without clicking any links.\n\nIf you have questions, contact us at {{contact_email}} or {{contact_phone}}.','[\"customer_name\", \"store_name\", \"order_number\", \"contact_email\", \"contact_phone\"]',3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,'order_status','Delivery update','Sent when an order moves to processing, ready for delivery, shipped, or delivered.','Delivery update for order {{order_number}}','# Delivery Update\n\nHi {{customer_name}},\n\nYour order **{{order_number}}** has moved to the next stage:\n\n**{{order_status_label}}**\n\n{{status_message}}','[\"customer_name\", \"store_name\", \"order_number\", \"order_status_label\", \"status_message\"]',4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(5,'order_cancelled','Order cancelled','Sent when an unpaid order is automatically cancelled.','Order {{order_number}} cancelled','# Order Cancelled\n\nHi {{customer_name}},\n\nYour order **{{order_number}}** was cancelled because payment was not received within {{payment_timeout_hours}} hours.\n\nThe items have been returned to stock. You can place a new order anytime from our shop.\n\nIf you believe this is a mistake or you already paid, contact us at {{contact_email}} or {{contact_phone}}.','[\"customer_name\", \"store_name\", \"order_number\", \"payment_timeout_hours\", \"contact_email\", \"contact_phone\"]',5,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `favorites_product_id_foreign` (`product_id`),
  CONSTRAINT `favorites_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `home_sections`
--

DROP TABLE IF EXISTS `home_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `home_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eyebrow` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_highlight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `primary_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `home_sections_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `home_sections`
--

LOCK TABLES `home_sections` WRITE;
/*!40000 ALTER TABLE `home_sections` DISABLE KEYS */;
INSERT INTO `home_sections` VALUES (1,'hero','Hero section','Order · Pickup · Delivery','Fresh meals,','made to order.','Homestyle Ghanaian favourites and everyday comfort food — cooked fresh and delivered across Accra.','View Menu','/shop','Today’s Specials','/shop','images/brand/food-hero-1.jpg',1,1,'2026-07-24 09:20:08','2026-07-24 13:21:45'),(2,'free_delivery_banner','Free delivery banner',NULL,NULL,NULL,'Free delivery on orders over {currency_symbol} {threshold} — Hot meals delivered across Accra',NULL,NULL,NULL,NULL,NULL,1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,'shop_by_category','Shop by category','Our Menu','Browse by Category',NULL,'Mains, sides, drinks, and sweets — pick what you are craving','Full Menu','/shop',NULL,NULL,NULL,1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,'cta','Call to action','Hungry?','Skip the wait. Order from CZIN today.',NULL,'Fresh kitchen favourites, clear portions, and reliable delivery across Accra.','Order Now','/shop','See Specials','/shop',NULL,1,4,'2026-07-24 09:20:08','2026-07-27 15:29:54'),(5,'new_arrivals','New arrivals','Kitchen Fresh','Popular Dishes',NULL,'Guest favourites from the CZIN kitchen.','Browse Menu','/shop',NULL,NULL,NULL,1,5,'2026-07-24 09:20:08','2026-07-27 15:29:54'),(6,'testimonials_header','Testimonials header','Reviews','What our guests say',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,6,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(7,'delivery_notice','Delivery notice',NULL,'Delivery Information',NULL,'Delivery fee is paid directly to the dispatch rider upon arrival. Fee varies by location across Accra and surrounding areas.',NULL,NULL,NULL,NULL,NULL,1,7,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `home_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_accounts`
--

DROP TABLE IF EXISTS `loyalty_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loyalty_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `points_balance` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loyalty_accounts_user_id_unique` (`user_id`),
  CONSTRAINT `loyalty_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_accounts`
--

LOCK TABLES `loyalty_accounts` WRITE;
/*!40000 ALTER TABLE `loyalty_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `loyalty_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_transactions`
--

DROP TABLE IF EXISTS `loyalty_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loyalty_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `loyalty_account_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `points` int NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loyalty_transactions_loyalty_account_id_foreign` (`loyalty_account_id`),
  KEY `loyalty_transactions_order_id_foreign` (`order_id`),
  CONSTRAINT `loyalty_transactions_loyalty_account_id_foreign` FOREIGN KEY (`loyalty_account_id`) REFERENCES `loyalty_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loyalty_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_transactions`
--

LOCK TABLES `loyalty_transactions` WRITE;
/*!40000 ALTER TABLE `loyalty_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `loyalty_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_15_000001_add_profile_fields_to_users_table',1),(5,'2026_06_15_000002_create_categories_table',1),(6,'2026_06_15_000003_create_products_table',1),(7,'2026_06_15_000004_create_product_images_table',1),(8,'2026_06_15_000005_create_addresses_table',1),(9,'2026_06_15_000006_create_orders_table',1),(10,'2026_06_15_000007_create_order_items_table',1),(11,'2026_06_15_000008_create_payments_table',1),(12,'2026_06_15_000009_create_carts_table',1),(13,'2026_06_16_000001_create_home_sections_and_testimonials_tables',1),(14,'2026_06_16_100000_create_admin_notifications_table',1),(15,'2026_06_16_200000_add_admin_permissions_and_create_pages_table',1),(16,'2026_06_16_300000_make_order_and_payment_user_id_nullable',1),(17,'2026_06_16_400000_create_product_variants_table',1),(18,'2026_06_16_400001_add_product_variant_to_cart_and_order_items',1),(19,'2026_06_16_500000_make_product_variant_heel_length_nullable',1),(20,'2026_06_17_000000_create_shipping_regions_and_options_tables',1),(21,'2026_06_17_000001_add_shipping_fields_to_orders_table',1),(22,'2026_06_17_100000_add_stock_reservation_support',1),(23,'2026_06_17_110000_add_payment_due_at_to_orders_table',1),(24,'2026_06_17_120000_add_navbar_fields_to_categories_table',1),(25,'2026_06_17_120000_create_favorites_table',1),(26,'2026_06_17_130000_add_parent_id_to_categories_table',1),(27,'2026_06_17_130000_renumber_orders_to_incremental_format',1),(28,'2026_06_17_140000_add_provider_transaction_id_to_payments_table',1),(29,'2026_06_18_000000_add_shop_sort_order_to_categories_table',1),(30,'2026_06_18_000000_create_store_settings_table',1),(31,'2026_06_18_000001_add_customer_comment_to_orders_table',1),(32,'2026_06_18_000002_create_coupons_table',1),(33,'2026_06_18_100000_add_social_links_to_store_settings_table',1),(34,'2026_06_19_120000_add_delivery_info_to_store_settings_table',1),(35,'2026_06_19_130000_remove_delivery_info_regional_from_store_settings_table',1),(36,'2026_06_22_150000_add_footer_copy_to_store_settings_table',1),(37,'2026_06_23_100000_add_published_at_to_products_table',1),(38,'2026_06_23_120000_add_maintenance_mode_to_store_settings_table',1),(39,'2026_06_23_140000_add_contact_page_fields_to_store_settings_table',1),(40,'2026_06_24_120000_create_email_templates_table',1),(41,'2026_06_24_130000_create_email_dispatches_table',1),(42,'2026_06_25_170000_refresh_payment_received_email_template',1),(43,'2026_07_27_140000_create_dining_tables_table',2),(44,'2026_07_27_150000_add_pos_fields_to_orders_table',3),(45,'2026_07_27_160000_add_kitchen_alert_settings',4),(46,'2026_07_27_170000_add_special_request_to_cart_items_table',5),(47,'2026_07_28_100000_add_restaurant_operations_tables',6),(48,'2026_07_28_120000_create_personal_access_tokens_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modifier_groups`
--

DROP TABLE IF EXISTS `modifier_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modifier_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_selections` int unsigned NOT NULL DEFAULT '0',
  `max_selections` int unsigned NOT NULL DEFAULT '1',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modifier_groups`
--

LOCK TABLES `modifier_groups` WRITE;
/*!40000 ALTER TABLE `modifier_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `modifier_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modifiers`
--

DROP TABLE IF EXISTS `modifiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modifiers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `modifier_group_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modifiers_modifier_group_id_foreign` (`modifier_group_id`),
  CONSTRAINT `modifiers_modifier_group_id_foreign` FOREIGN KEY (`modifier_group_id`) REFERENCES `modifier_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modifiers`
--

LOCK TABLES `modifiers` WRITE;
/*!40000 ALTER TABLE `modifiers` DISABLE KEYS */;
/*!40000 ALTER TABLE `modifiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variant_sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_options` json DEFAULT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `dining_table_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `delivery_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment',
  `order_source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `fulfillment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `billing_full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_region_id` bigint unsigned DEFAULT NULL,
  `shipping_option_id` bigint unsigned DEFAULT NULL,
  `shipping_region_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_option_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_comment` text COLLATE utf8mb4_unicode_ci,
  `coupon_id` bigint unsigned DEFAULT NULL,
  `coupon_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `shipping_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_at` timestamp NULL DEFAULT NULL,
  `kitchen_alert_sent_at` timestamp NULL DEFAULT NULL,
  `payment_due_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_shipping_region_id_foreign` (`shipping_region_id`),
  KEY `orders_shipping_option_id_foreign` (`shipping_option_id`),
  KEY `orders_coupon_id_foreign` (`coupon_id`),
  KEY `orders_dining_table_id_foreign` (`dining_table_id`),
  KEY `orders_created_by_foreign` (`created_by`),
  CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_dining_table_id_foreign` FOREIGN KEY (`dining_table_id`) REFERENCES `dining_tables` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_shipping_option_id_foreign` FOREIGN KEY (`shipping_option_id`) REFERENCES `shipping_options` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_shipping_region_id_foreign` FOREIGN KEY (`shipping_region_id`) REFERENCES `shipping_regions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci,
  `footer_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'about-us','About Us','CZIN started with a simple idea: serve honest, flavourful meals that feel like home — ready when you are.\n\nWhat began as a love for Ghanaian kitchen classics has grown into a full menu of mains, sides, drinks, and desserts, prepared fresh for dine-in, pickup, and delivery.\n\n## Our mission\n\nGreat food should arrive hot, taste memorable, and feel worth every bite. That is why we cook with care, keep our menu clear, and focus on a smooth ordering experience from browse to delivery.\n\n## What we stand for\n\n- **Fresh first** — meals prepared to order, not sitting under a lamp\n- **Guest care** — responsive support before and after your order\n- **Honest portions** — clear sizes and fair pricing\n- **Local focus** — built for Accra and surrounding communities\n\nWhether you need a quick weekday lunch or a family dinner delivered, CZIN is here to feed you well.',NULL,0,1,'2026-07-24 09:20:08','2026-07-27 15:29:54'),(2,'delivery-info','Delivery Info','We deliver hot meals across Accra and nearby areas through trusted dispatch riders.\n\nStandard Accra delivery: typically 45–90 minutes depending on kitchen volume and location.\nOuter areas: timing may vary — we will confirm at checkout where possible.\n\nYou will receive updates once your order is out for delivery. Please ensure your phone number and delivery address are correct at checkout.\n\nFor large or timed orders, contact our team before placing your order.','customer_care',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,'returns-policy','Returns Policy','Food orders are prepared fresh and generally cannot be returned once delivered in good condition.\n\nIf something is wrong — missing items, incorrect order, or a quality issue — contact us within 2 hours of delivery with your order number and photos where helpful.\n\nWhere we confirm an error on our side, we will offer a replacement, credit, or refund as appropriate.\n\nRefunds, when approved, are processed to the original payment method within 5–10 business days.',NULL,2,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,'contact-us','Contact Us','We are here to help with orders, menu questions, delivery updates, and catering enquiries.\n\nSend us a message and our team will respond as soon as possible — typically within one business day.','customer_care',3,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(5,'privacy-policy','Privacy Policy','At **CZIN**, we respect your privacy and are committed to protecting your personal information. This Privacy Policy explains how we collect, use, and safeguard the information you provide when ordering with us.\n\n## Information We Collect\n\nWhen you place an order or contact us, we may collect the following information:\n\n- **Full name**\n- **Phone number**\n- **Delivery address**\n- **Email address** (if provided)\n- **Payment information** necessary to process your order\n\n## How We Use Your Information\n\nWe use your personal information to:\n\n- **Process and confirm** your orders.\n- **Arrange and complete** deliveries.\n- **Contact you** regarding your order, delivery, or customer support requests.\n- **Improve** our menu and services.\n- **Send promotional offers or updates**, only if you have agreed to receive them.\n\n## Sharing Your Information\n\n**We value your trust and do not sell, rent, or trade your personal information.**\n\nYour information may only be shared with:\n\n- **Delivery partners** for the purpose of completing your order.\n- **Payment service providers** to securely process payments.\n- **Authorities** where required by law.\n\n## Data Security\n\nWe take reasonable administrative and technical measures to protect your personal information against **unauthorized access, loss, misuse, or disclosure**.\n\nWhile we strive to keep your information secure, no method of electronic storage or transmission over the internet is completely secure.\n\n## Data Retention\n\nWe keep your personal information only for as long as necessary to:\n\n- Process your orders\n- Provide customer support\n- Comply with legal obligations\n- Resolve disputes\n\n## Your Rights\n\nYou have the right to:\n\n- **Request access** to the personal information we hold about you.\n- **Request correction** of inaccurate or incomplete information.\n- **Request deletion** of your personal information where applicable by law.\n\nTo make any of these requests, please contact us using the details below.\n\n## Cookies and Online Services\n\nIf you visit our website or use our online services, we may use **cookies or similar technologies** to improve your browsing experience and understand how our services are used.\n\n## Changes to This Privacy Policy\n\n**CZIN** may update this Privacy Policy from time to time. Any changes will be posted on our platforms with the updated effective date.\n\n## Contact Us\n\nIf you have any questions about this Privacy Policy or how your personal information is handled, please contact us:\n\n**CZIN**\n\n- **Phone:** +233 530 668 945\n- **Email:** support@CZIN.com\n- **Social media:** See the links in our website footer for our current Instagram, Facebook, and other profiles.','legal',1,1,'2026-07-24 09:20:08','2026-07-27 15:29:54'),(6,'terms-and-conditions','Terms & Conditions','Welcome to **CZIN**. By ordering with us, you agree to the following **Terms & Conditions**. Please read them carefully before placing your order.\n\n## General\n\n**CZIN** is a restaurant offering **prepared meals, sides, drinks, and desserts** for pickup and delivery.\n\nWe reserve the right to update **prices, menu availability, and policies** without prior notice.\n\n## Orders & Payment\n\n- Orders are confirmed only after **successful payment** or agreement with our designated payment method.\n- We reserve the right to **cancel any order** due to stock unavailability, kitchen capacity, or payment issues.\n\n## Shipping & Delivery\n\n- **Accra deliveries:** typically 45–90 minutes.\n- **Nearby areas:** timing may vary with distance and demand.\n- Delivery times may vary during **peak hours, public holidays**, or due to unforeseen circumstances.\n- Customers are required to provide **accurate delivery details**. CZIN will not be liable for failed deliveries caused by incorrect information.\n\n## Food Quality & Issues\n\n- Please inspect your order on arrival.\n- Report missing items, wrong dishes, or quality concerns **promptly** (ideally within 2 hours) with your order number.\n- Because meals are prepared fresh, **returns of consumed or correctly delivered food are not accepted**.\n\n## Refunds\n\n- Refunds are **not guaranteed** and will only be considered where we confirm an error on our side or cannot fulfill a suitable replacement.\n- **Delivery fees are non-refundable** once a rider has been dispatched, except where we cancel the order.\n\n## Liability\n\n**CZIN** will not be held responsible for issues arising after a correctly delivered order has been accepted, including improper storage or reheating by the customer.','legal',2,1,'2026-07-24 09:20:08','2026-07-27 15:29:54');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paystack',
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GHS',
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `metadata` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_reference_unique` (`reference`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_user_id_foreign` (`user_id`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'test-tablet','55601cb40e49342e44d2eb6720c67bf9bedd5907e58f8dc31ebf35861d8a2a91','[\"*\"]',NULL,NULL,'2026-07-28 12:01:50','2026-07-28 12:01:50'),(2,'App\\Models\\User',1,'mobile','8cc23da6744dbde81863254251572654bc124d4b86d516e078170466dbcfe728','[\"*\"]','2026-07-28 12:01:57',NULL,'2026-07-28 12:01:57','2026-07-28 12:01:57'),(3,'App\\Models\\User',1,'flutter-app','72dcd1daba38a0dfa162ded93285d61f6508002ffc1a6e6d0a41cbab2c48d916','[\"*\"]','2026-08-03 12:31:26',NULL,'2026-08-03 12:31:12','2026-08-03 12:31:26'),(4,'App\\Models\\User',1,'test','172b06254758e5cd36b30aa838c5c043a2cc5bdf721690f451ed54ab8e5c774d','[\"*\"]','2026-08-03 12:32:50',NULL,'2026-08-03 12:32:50','2026-08-03 12:32:50');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,1,'images/products/jollof-rice.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(2,2,'images/products/grilled-chicken.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(3,3,'images/products/waakye.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(4,4,'images/products/fried-plantain.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(5,5,'images/products/coleslaw.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(6,6,'images/products/banku.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(7,7,'images/products/sobolo.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(8,8,'images/products/fresh-juice.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(9,9,'images/products/bottled-water.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(10,10,'images/products/chocolate-cake.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(11,11,'images/products/ice-cream.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25'),(12,12,'images/products/puff-puff.jpg',1,0,'2026-07-27 08:52:25','2026-07-27 08:52:25');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_modifier_group`
--

DROP TABLE IF EXISTS `product_modifier_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_modifier_group` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `modifier_group_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_modifier_group_product_id_modifier_group_id_unique` (`product_id`,`modifier_group_id`),
  KEY `product_modifier_group_modifier_group_id_foreign` (`modifier_group_id`),
  CONSTRAINT `product_modifier_group_modifier_group_id_foreign` FOREIGN KEY (`modifier_group_id`) REFERENCES `modifier_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_modifier_group_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_modifier_group`
--

LOCK TABLES `product_modifier_group` WRITE;
/*!40000 ALTER TABLE `product_modifier_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_modifier_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `heel_length` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '0',
  `reserved_quantity` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  UNIQUE KEY `product_variants_product_id_size_color_heel_length_unique` (`product_id`,`size`,`color`,`heel_length`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,1,'MAINS-001-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(2,1,'MAINS-001-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,1,'MAINS-001-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,1,'MAINS-001-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(5,1,'MAINS-001-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(6,1,'MAINS-001-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(7,2,'MAINS-002-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(8,2,'MAINS-002-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(9,2,'MAINS-002-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(10,2,'MAINS-002-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(11,2,'MAINS-002-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(12,2,'MAINS-002-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(13,3,'MAINS-003-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(14,3,'MAINS-003-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(15,3,'MAINS-003-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(16,3,'MAINS-003-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(17,3,'MAINS-003-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(18,3,'MAINS-003-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(19,4,'SIDES-001-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(20,4,'SIDES-001-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(21,4,'SIDES-001-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(22,4,'SIDES-001-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(23,4,'SIDES-001-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(24,4,'SIDES-001-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(25,5,'SIDES-002-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(26,5,'SIDES-002-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(27,5,'SIDES-002-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(28,5,'SIDES-002-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(29,5,'SIDES-002-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(30,5,'SIDES-002-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(31,6,'SIDES-003-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(32,6,'SIDES-003-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(33,6,'SIDES-003-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(34,6,'SIDES-003-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(35,6,'SIDES-003-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(36,6,'SIDES-003-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(37,7,'DRINKS-001-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(38,7,'DRINKS-001-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(39,7,'DRINKS-001-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(40,7,'DRINKS-001-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(41,7,'DRINKS-001-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(42,7,'DRINKS-001-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(43,8,'DRINKS-002-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(44,8,'DRINKS-002-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(45,8,'DRINKS-002-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(46,8,'DRINKS-002-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(47,8,'DRINKS-002-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(48,8,'DRINKS-002-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(49,9,'DRINKS-003-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(50,9,'DRINKS-003-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(51,9,'DRINKS-003-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(52,9,'DRINKS-003-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(53,9,'DRINKS-003-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(54,9,'DRINKS-003-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(55,10,'DESSERTS-001-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(56,10,'DESSERTS-001-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(57,10,'DESSERTS-001-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(58,10,'DESSERTS-001-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(59,10,'DESSERTS-001-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(60,10,'DESSERTS-001-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(61,11,'DESSERTS-002-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(62,11,'DESSERTS-002-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(63,11,'DESSERTS-002-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(64,11,'DESSERTS-002-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(65,11,'DESSERTS-002-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(66,11,'DESSERTS-002-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(67,12,'DESSERTS-003-REGULAR-STANDARD','Regular','Standard',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(68,12,'DESSERTS-003-REGULAR-MILD','Regular','Mild',NULL,10,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(69,12,'DESSERTS-003-REGULAR-SPICY','Regular','Spicy',NULL,11,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(70,12,'DESSERTS-003-LARGE-STANDARD','Large','Standard',NULL,12,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(71,12,'DESSERTS-003-LARGE-MILD','Large','Mild',NULL,8,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(72,12,'DESSERTS-003-LARGE-SPICY','Large','Spicy',NULL,9,0,1,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `discount_price` decimal(12,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_86ed` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Jollof Rice Special','jollof-rice-special','MAINS-001',55.00,45.00,'Smoky party jollof served with your choice of protein and salad.',59,'active',0,'2026-07-24 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(2,1,'Grilled Chicken Plate','grilled-chicken-plate','MAINS-002',70.00,NULL,'Charcoal-grilled chicken with banku or fries and pepper sauce.',59,'active',0,'2026-07-23 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,1,'Waakye Combo','waakye-combo','MAINS-003',50.00,NULL,'Classic waakye with gari, spaghetti, egg, and shito.',59,'active',0,'2026-07-22 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,2,'Fried Plantain','fried-plantain','SIDES-001',20.00,NULL,'Crispy golden plantain, lightly seasoned.',59,'active',0,'2026-07-24 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(5,2,'Coleslaw','coleslaw','SIDES-002',15.00,NULL,'Fresh creamy slaw — a cool contrast to spicy mains.',59,'active',0,'2026-07-23 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(6,2,'Extra Banku','extra-banku','SIDES-003',12.00,NULL,'Soft banku portion to round out your plate.',59,'active',0,'2026-07-22 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(7,3,'Sobolo','sobolo','DRINKS-001',15.00,NULL,'Chilled hibiscus drink with a hint of ginger.',59,'active',0,'2026-07-24 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(8,3,'Fresh Juice','fresh-juice','DRINKS-002',18.00,NULL,'Seasonal fruit blend, made to order.',59,'active',0,'2026-07-23 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(9,3,'Bottled Water','bottled-water','DRINKS-003',5.00,NULL,'500ml still water.',59,'active',0,'2026-07-22 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(10,4,'Chocolate Cake Slice','chocolate-cake-slice','DESSERTS-001',25.00,20.00,'Rich chocolate sponge with cream frosting.',59,'active',0,'2026-07-24 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(11,4,'Ice Cream Cup','ice-cream-cup','DESSERTS-002',18.00,NULL,'Two scoops of rotating seasonal flavours.',59,'active',0,'2026-07-23 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08'),(12,4,'Puff Puff (6pcs)','puff-puff-6pcs','DESSERTS-003',15.00,NULL,'Warm golden doughnuts dusted with sugar.',59,'active',0,'2026-07-22 09:20:08','2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `days_of_week` json DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotions_category_id_foreign` (`category_id`),
  KEY `promotions_product_id_foreign` (`product_id`),
  CONSTRAINT `promotions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `promotions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipe_items`
--

DROP TABLE IF EXISTS `recipe_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipe_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `ingredient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_per_unit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_items_product_id_foreign` (`product_id`),
  CONSTRAINT `recipe_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipe_items`
--

LOCK TABLES `recipe_items` WRITE;
/*!40000 ALTER TABLE `recipe_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `recipe_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dining_table_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `guest_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guest_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_size` smallint unsigned NOT NULL,
  `reserved_at` datetime NOT NULL,
  `duration_minutes` smallint unsigned NOT NULL DEFAULT '90',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_dining_table_id_foreign` (`dining_table_id`),
  KEY `reservations_user_id_foreign` (`user_id`),
  CONSTRAINT `reservations_dining_table_id_foreign` FOREIGN KEY (`dining_table_id`) REFERENCES `dining_tables` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('6rCcRwEICSL1KuU3B8ookOe5f0HWfqV0N7xbvWwD',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:153.0) Gecko/20100101 Firefox/153.0','eyJfdG9rZW4iOiJIQU1kUGJ3MVhHbDBVQXRRNnVlUkxXcU15NllxR1RqMU5YVnhWbFBFIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9',1785589920),('8opR59KPrAxsiJyGKf7N9AyIBUdu1oiWECPfU2SB',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:153.0) Gecko/20100101 Firefox/153.0','eyJfdG9rZW4iOiJEQ00zUm1JRDloV1RqNGdRNll4SkVFZUs2bUFvbXVUMExHdldjVktTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvc2hpcHBpbmctcmVnaW9ucyIsInJvdXRlIjoiYWRtaW4uc2hpcHBpbmctcmVnaW9ucy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sInVybCI6W10sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==',1785164261),('AKFRQKuBRhBTJssHL4pEkxO1Fjt3f9hYynZ9zkaA',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:153.0) Gecko/20100101 Firefox/153.0','eyJfdG9rZW4iOiJIeVB5cUpYS2VOY09vcVBTYXpKNUdvMXFJaFVIOWxud1pSTXpxSUtDIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pbiIsInJvdXRlIjoiYWRtaW4uZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjpbXSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9',1785238268),('VnrQ7ZOthVaEBr6UvSfqorNEurW1KOOEGiOPK5tD',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:153.0) Gecko/20100101 Firefox/153.0','eyJfdG9rZW4iOiJzcVpFa1FaUThyVnJUU1d4YkNsM2RwcFNUeGVtSExTb1JIMUpIV3VpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1785168882),('XUozBksK8hC97iqhcC1v2mUtSs5RvrgoVfszblJr',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:153.0) Gecko/20100101 Firefox/153.0','eyJfdG9rZW4iOiJLMng1b2tiZnQ0R0RRUTUwOWRWVmVyWlFJQ0xBaVRCUnlxZjNNV013IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9zaG9wXC93YWFreWUtY29tYm8iLCJyb3V0ZSI6InNob3Auc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sInVybCI6W10sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==',1785765871),('YIpuNnwuempx6IOLbZJOYghb76UVV6IMSbF5pNGL',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:153.0) Gecko/20100101 Firefox/153.0','eyJfdG9rZW4iOiJCM29Ta0tiV0pjc2FOQXR1OVQwdnpjZGlOM3AxWkNCY2g2M1FUbzlBIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hYm91dCIsInJvdXRlIjoiYWJvdXQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=',1785489493);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_options`
--

DROP TABLE IF EXISTS `shipping_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `shipping_region_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_options_shipping_region_id_name_unique` (`shipping_region_id`,`name`),
  CONSTRAINT `shipping_options_shipping_region_id_foreign` FOREIGN KEY (`shipping_region_id`) REFERENCES `shipping_regions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_options`
--

LOCK TABLES `shipping_options` WRITE;
/*!40000 ALTER TABLE `shipping_options` DISABLE KEYS */;
INSERT INTO `shipping_options` VALUES (1,2,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(2,2,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,2,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,2,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(5,2,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(6,2,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(7,3,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(8,3,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(9,3,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(10,3,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(11,3,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(12,3,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(13,4,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(14,4,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(15,4,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(16,4,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(17,4,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(18,4,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(19,5,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(20,5,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(21,5,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(22,5,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(23,5,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(24,5,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(25,6,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(26,6,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(27,6,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(28,6,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(29,6,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(30,6,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(31,7,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(32,7,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(33,7,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(34,7,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(35,7,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(36,7,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(37,8,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(38,8,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(39,8,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(40,8,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(41,8,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(42,8,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(43,9,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(44,9,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(45,9,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(46,9,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(47,9,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(48,9,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(49,10,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(50,10,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(51,10,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(52,10,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(53,10,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(54,10,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(55,11,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(56,11,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(57,11,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(58,11,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(59,11,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(60,11,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(61,12,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(62,12,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(63,12,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(64,12,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(65,12,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(66,12,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(67,13,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(68,13,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(69,13,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(70,13,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(71,13,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(72,13,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(73,14,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(74,14,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(75,14,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(76,14,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(77,14,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(78,14,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(79,15,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(80,15,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(81,15,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(82,15,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(83,15,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(84,15,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(85,16,'OA',42.00,'Parcel delivery via OA Travel & Tour.',1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(86,16,'STC',42.00,'Delivery via State Transport Corporation.',1,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(87,16,'VIP Parcel Office',42.00,'Delivery to VIP parcel office.',1,2,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(88,16,'KEK',42.00,'Fast regional parcel delivery.',1,3,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(89,16,'Station Cars (Delivery only)',10.00,'Station car delivery only.',1,4,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(90,16,'FedEx',70.00,'FedEx parcel delivery.',1,5,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `shipping_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_regions`
--

DROP TABLE IF EXISTS `shipping_regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_accra` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_regions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_regions`
--

LOCK TABLES `shipping_regions` WRITE;
/*!40000 ALTER TABLE `shipping_regions` DISABLE KEYS */;
INSERT INTO `shipping_regions` VALUES (1,'Accra',1,1,0,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(2,'Ashanti',0,1,10,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,'Western',0,1,20,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(4,'Eastern',0,1,30,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(5,'Central',0,1,40,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(6,'Northern',0,1,50,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(7,'Upper East',0,1,60,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(8,'Upper West',0,1,70,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(9,'Volta',0,1,80,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(10,'Bono',0,1,90,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(11,'Bono East',0,1,100,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(12,'Ahafo',0,1,110,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(13,'Western North',0,1,120,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(14,'Savannah',0,1,130,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(15,'North East',0,1,140,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(16,'Oti',0,1,150,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `shipping_regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_shifts`
--

DROP TABLE IF EXISTS `staff_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `location_id` bigint unsigned DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `role_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_shifts_user_id_foreign` (`user_id`),
  KEY `staff_shifts_location_id_foreign` (`location_id`),
  CONSTRAINT `staff_shifts_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_shifts`
--

LOCK TABLES `staff_shifts` WRITE;
/*!40000 ALTER TABLE `staff_shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `product_variant_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_change` int NOT NULL,
  `quantity_after` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_id_foreign` (`product_id`),
  KEY `stock_movements_product_variant_id_foreign` (`product_variant_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_settings`
--

DROP TABLE IF EXISTS `store_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone_alt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_address` text COLLATE utf8mb4_unicode_ci,
  `contact_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_page_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_page_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_page_phone_alt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_page_address` text COLLATE utf8mb4_unicode_ci,
  `contact_page_hours_days` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_page_hours_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_page_hours_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_hero_description` text COLLATE utf8mb4_unicode_ci,
  `footer_tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_subline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_shipping_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_info_accra` text COLLATE utf8mb4_unicode_ci,
  `social_facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_tiktok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_x` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_youtube` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kitchen_sms_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `kitchen_sms_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kitchen_whatsapp_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `kitchen_whatsapp_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `low_stock_threshold` smallint unsigned NOT NULL DEFAULT '10',
  `upsell_category_slugs` json DEFAULT NULL,
  `business_hours` json DEFAULT NULL,
  `online_ordering_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT '0',
  `maintenance_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_settings`
--

LOCK TABLES `store_settings` WRITE;
/*!40000 ALTER TABLE `store_settings` DISABLE KEYS */;
INSERT INTO `store_settings` VALUES (1,'CZIN','hello@czin.com','+233530668945',NULL,'Dansoman, Dansoman, Greater Accra, Ghana.','http://127.0.0.1:8000','support@czin.com',NULL,NULL,NULL,'Monday – Saturday','9:00 AM – 6:00 PM','Closed on Sundays and public holidays.','about/K0fPe0s5CvJpKepizZfLyhmLaTITFTELveikTzhJ.jpg',NULL,'Fresh meals made to order.','Hot food delivered across Accra.','Delivery fee calculated at checkout.','Hot meals delivered across Accra, typically within 45–90 minutes. Delivery fee is paid to the rider on arrival where applicable.',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,0,NULL,10,'[\"sides\", \"drinks\", \"desserts\"]',NULL,1,0,NULL,'2026-07-24 10:12:21','2026-07-31 09:17:16');
/*!40000 ALTER TABLE `store_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quote` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '5',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'The jollof tastes like home. Portions are generous and delivery was still hot. Ordering from CZIN is my new weekday habit.','Ama K.',5,1,1,'2026-07-24 09:20:08','2026-07-27 15:29:54'),(2,'Great flavours, clear menu options, and friendly service. The grilled chicken with banku was excellent.','Kwame B.',5,2,1,'2026-07-24 09:20:08','2026-07-24 09:20:08'),(3,'Best takeaway experience I have had in Accra lately. Food arrived on time and everything was packed carefully.','Efua S.',5,3,1,'2026-07-24 09:20:08','2026-07-24 09:20:08');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `admin_permissions` json DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'CZIN Admin','CZIN','Admin','admin@czin.com','0200000000','2026-07-24 09:20:08','$2y$12$uWk297lbi3K1yJw9Rce7b.fdMehPQKVBxvuVc3bTzMOMKxy9L7156','admin',1,NULL,NULL,NULL,'2026-07-24 09:20:08','2026-07-27 15:30:03');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'czin_foods'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-03 16:10:25
