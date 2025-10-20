-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 19, 2025 at 07:22 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel-stylex`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_data`
--

CREATE TABLE `app_data` (
  `id` bigint UNSIGNED NOT NULL,
  `logo_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `heading` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Áo Nam', NULL, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(2, 'Áo Nữ', NULL, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(3, 'Quần Nam', NULL, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(4, 'Quần Nữ', NULL, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(5, 'Giày Dép', NULL, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(6, 'Phụ Kiện', NULL, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(7, 'Áo Sơ Mi', 1, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(8, 'Áo Thun', 1, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(9, 'Áo Khoác', 1, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(10, 'Áo Vest', 1, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(11, 'Áo Dài', 2, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(12, 'Áo Blouse', 2, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(13, 'Áo Thun Nữ', 2, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(14, 'Áo Khoác Nữ', 2, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(15, 'Quần Jean', 3, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(16, 'Quần Kaki', 3, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(17, 'Quần Short', 3, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(18, 'Quần Âu', 3, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(19, 'Quần Jean Nữ', 4, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(20, 'Quần Short Nữ', 4, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(21, 'Quần Legging', 4, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(22, 'Chân Váy', 4, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(23, 'Giày Thể Thao', 5, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(24, 'Giày Tây', 5, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(25, 'Giày Cao Gót', 5, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(26, 'Dép', 5, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(27, 'Túi Xách', 6, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(28, 'Ví', 6, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(29, 'Đồng Hồ', 6, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(30, 'Trang Sức', 6, 1, '2025-10-13 20:21:24', '2025-10-13 20:21:24', NULL),
(31, 'Váy', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(32, 'Đầm', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hex_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: active, 0: inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `name`, `hex_code`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Xanh', NULL, 1, '2025-10-11 22:08:07', '2025-10-11 22:08:07', NULL),
(3, 'Vàng', NULL, 1, '2025-10-11 22:08:18', '2025-10-11 22:08:18', NULL),
(4, 'Đỏ', NULL, 1, '2025-10-11 22:08:23', '2025-10-11 22:08:23', NULL),
(5, 'Bui Tuan Phong', NULL, 1, '2025-10-13 03:16:07', '2025-10-13 03:16:12', '2025-10-13 03:16:12'),
(6, 'Đỏ', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(7, 'Xanh dương', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(8, 'Xanh lá', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(9, 'Vàng', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(10, 'Đen', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(11, 'Trắng', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(12, 'Hồng', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(13, 'Tím', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(14, 'Cam', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(15, 'Xám', NULL, 1, '2025-10-18 22:20:09', '2025-10-18 22:20:09', NULL),
(16, 'Đỏ', NULL, 1, '2025-10-18 22:20:30', '2025-10-18 22:20:30', NULL),
(17, 'Xanh dương', NULL, 1, '2025-10-18 22:20:30', '2025-10-18 22:20:30', NULL),
(18, 'Xanh lá', NULL, 1, '2025-10-18 22:20:30', '2025-10-18 22:20:30', NULL),
(19, 'Đỏ', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 23:21:33', '2025-10-18 23:21:33'),
(20, 'Xanh dương', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(21, 'Xanh lá', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(22, 'Vàng', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(23, 'Đen', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(24, 'Trắng', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(25, 'Hồng', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(26, 'Tím', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(27, 'Cam', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(28, 'Xám', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(29, 'Đỏ', NULL, 1, '2025-10-18 22:51:33', '2025-10-18 22:51:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `variant_id` bigint UNSIGNED NOT NULL,
  `change` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_tiers`
--

CREATE TABLE `loyalty_tiers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_spend_required` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_rate` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT 'Ưu đãi theo %',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loyalty_tiers`
--

INSERT INTO `loyalty_tiers` (`id`, `name`, `min_spend_required`, `discount_rate`, `created_at`, `updated_at`) VALUES
(2, 'Bạc', 70000.00, 7.00, '2025-10-13 02:17:45', '2025-10-13 03:08:14'),
(3, 'Vàng', 100000.00, 10.00, '2025-10-13 02:18:49', '2025-10-13 03:07:57'),
(4, 'Đồng', 50000.00, 5.00, '2025-10-13 02:50:05', '2025-10-13 03:18:08');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_04_003345_create_app_data', 1),
(5, '2025_10_06_055501_create_categories_table', 1),
(6, '2025_10_08_133104_add_phone_number_to_users_table', 2),
(7, '2025_10_08_135108_add_is_admin_to_users_table', 3),
(8, '2025_10_09_000000_add_soft_deletes_to_users_table', 3),
(9, '2025_01_15_000000_add_soft_deletes_to_categories_table', 4),
(10, '2025_10_12_031110_create_colors_table', 5),
(11, '2025_10_12_031126_create_sizes_table', 5),
(12, '2025_10_12_031134_create_textures_table', 5),
(13, '2025_10_09_120724_create_loyalty_tables', 6),
(14, '2025_10_13_000001_create_tax_rates_table', 7),
(15, '2025_10_13_000002_create_shipping_carriers_table', 7),
(16, '2025_10_12_123752_create_brands_table', 8),
(17, '2025_10_12_123809_create_products_table', 8),
(18, '2025_10_12_123826_create_product_variants_table', 8),
(19, '2025_10_12_123843_create_product_images_table', 8),
(20, '2025_10_12_123913_create_inventory_logs_table', 8),
(21, '2025_10_19_050429_add_deleted_at_to_products_table', 9),
(22, '2025_10_11_144804_create_products_table', 8),
(23, '2025_10_16_020338_create_product_variants_table', 8),
(24, '2025_10_19_051219_add_is_featured_to_products_table', 10),
(25, '2025_10_19_060000_add_sample_data_to_tables', 11),
(26, '2025_10_19_052120_add_sample_data_to_tables', 12),
(27, '2025_10_19_071508_add_columns_to_product_images_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(20,0) NOT NULL DEFAULT '0',
  `price_sale` decimal(20,0) NOT NULL DEFAULT '0',
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `default_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cost_price` decimal(12,2) DEFAULT NULL,
  `total_stock` int NOT NULL DEFAULT '0',
  `weight` decimal(8,3) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `visibility` enum('hidden','catalog','search','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `additional` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `slug`, `thumbnail`, `short_description`, `description`, `meta_title`, `price`, `price_sale`, `brand_id`, `category_id`, `default_image`, `base_price`, `cost_price`, `total_stock`, `weight`, `is_active`, `is_featured`, `visibility`, `additional`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, NULL, 'Bui Tuan Phong', 'bui-tuan-phong', 'products/oSjeOdjU1VVBWtEve3HaKwsPyphrlaOl9dtRIsf1.jpg', NULL, '123', 'seo', 12000, 10000, NULL, 1, NULL, 0.00, NULL, 0, NULL, 1, 1, 'both', NULL, '2025-10-19 00:07:32', '2025-10-19 00:19:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variant_id` bigint UNSIGNED DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `variant_id`, `image_url`, `sort_order`, `is_main`, `created_at`, `updated_at`, `image_path`, `alt_text`, `is_primary`) VALUES
(1, 11, NULL, 'uploads/products/bui-tuan-phong/image_1.jpg', 0, 0, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 'uploads/products/bui-tuan-phong/image_1.jpg', 'Hình ảnh 1 của Bui Tuan Phong', 1),
(2, 11, NULL, 'uploads/products/bui-tuan-phong/image_2.jpg', 1, 0, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 'uploads/products/bui-tuan-phong/image_2.jpg', 'Hình ảnh 2 của Bui Tuan Phong', 0),
(3, 11, NULL, 'uploads/products/bui-tuan-phong/image_3.jpg', 2, 0, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 'uploads/products/bui-tuan-phong/image_3.jpg', 'Hình ảnh 3 của Bui Tuan Phong', 0),
(4, 11, NULL, 'uploads/products/bui-tuan-phong/image_4.jpg', 3, 0, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 'uploads/products/bui-tuan-phong/image_4.jpg', 'Hình ảnh 4 của Bui Tuan Phong', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_id` bigint UNSIGNED DEFAULT NULL,
  `size_id` bigint UNSIGNED DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `stock_quantity` int NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `attributes` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `texture_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `sku`, `color_id`, `size_id`, `price`, `quantity`, `stock_quantity`, `is_default`, `image`, `status`, `attributes`, `created_at`, `updated_at`, `texture_id`, `deleted_at`) VALUES
(5, 11, 'AJPBX5FGZV17', 3, 4, 0.00, 1, 0, 0, 'products/1SV30Hbc010gdVdXfoOk79zb5RWHu0vXC6OHf6Ue.jpg', 1, NULL, '2025-10-19 00:07:32', '2025-10-19 00:17:15', 3, NULL),
(6, 11, 'bui-tuan-phong-2-1-1', 2, 1, 93731.00, 73, 0, 0, 'uploads/products/bui-tuan-phong/variant_2_1_1.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 1, NULL),
(7, 11, 'bui-tuan-phong-2-1-2', 2, 1, 41528.00, 89, 0, 0, 'uploads/products/bui-tuan-phong/variant_2_1_2.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 2, NULL),
(8, 11, 'bui-tuan-phong-2-2-1', 2, 2, 94811.00, 31, 0, 0, 'uploads/products/bui-tuan-phong/variant_2_2_1.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 1, NULL),
(9, 11, 'bui-tuan-phong-2-2-2', 2, 2, 81219.00, 97, 0, 0, 'uploads/products/bui-tuan-phong/variant_2_2_2.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 2, NULL),
(10, 11, 'bui-tuan-phong-2-3-1', 2, 3, 14830.00, 61, 0, 0, 'uploads/products/bui-tuan-phong/variant_2_3_1.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 1, NULL),
(11, 11, 'bui-tuan-phong-2-3-2', 2, 3, 92641.00, 12, 0, 0, 'uploads/products/bui-tuan-phong/variant_2_3_2.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 2, NULL),
(12, 11, 'bui-tuan-phong-3-1-1', 3, 1, 102497.00, 14, 0, 0, 'uploads/products/bui-tuan-phong/variant_3_1_1.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 1, NULL),
(13, 11, 'bui-tuan-phong-3-1-2', 3, 1, 23590.00, 30, 0, 0, 'uploads/products/bui-tuan-phong/variant_3_1_2.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 2, NULL),
(14, 11, 'bui-tuan-phong-3-2-1', 3, 2, 52056.00, 42, 0, 0, 'uploads/products/bui-tuan-phong/variant_3_2_1.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 1, NULL),
(15, 11, 'bui-tuan-phong-3-2-2', 3, 2, 76287.00, 51, 0, 0, 'uploads/products/bui-tuan-phong/variant_3_2_2.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 2, NULL),
(16, 11, 'bui-tuan-phong-3-3-1', 3, 3, 110626.00, 68, 0, 0, 'uploads/products/bui-tuan-phong/variant_3_3_1.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 1, NULL),
(17, 11, 'bui-tuan-phong-3-3-2', 3, 3, 82906.00, 93, 0, 0, 'uploads/products/bui-tuan-phong/variant_3_3_2.jpg', 1, NULL, '2025-10-19 00:15:59', '2025-10-19 00:15:59', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('qilbOdYn0ZVDfBBkuMZZNgHHowD3bQ8RIubqRQZS', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieDhaSXZmc1pJQkFyalNIUG8wdklPSXJTUXlxMU9NV1IzbEt3SlEwdSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9kdWN0cyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1760858390);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_carriers`
--

CREATE TABLE `shipping_carriers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_carriers`
--

INSERT INTO `shipping_carriers` (`id`, `name`, `code`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Giao Hàng Nhanh (GHN)', 'GHN', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(2, 'Giao Hàng Tiết Kiệm (GHTK)', 'GHTK', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(3, 'Viettel Post', 'VIETTEL', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(4, 'VNPost', 'VNPOST', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(5, 'J&T Express', 'JT', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(6, 'Ninja Van', 'NINJA', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(7, 'Best Express', 'BEST', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(8, 'DHL', 'DHL', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(9, 'FedEx', 'FEDEX', 1, '2025-10-13 03:41:24', '2025-10-13 03:41:24'),
(10, 'UPS', 'UPS', 1, '2025-10-13 03:41:24', '2025-10-13 04:19:57');

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: active, 0: inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'S', NULL, 1, '2025-10-11 20:14:03', '2025-10-11 20:14:03', NULL),
(2, 'L', NULL, 1, '2025-10-11 20:14:58', '2025-10-11 20:14:58', NULL),
(3, 'M', NULL, 1, '2025-10-11 20:15:03', '2025-10-11 20:15:03', NULL),
(4, 'XL', 'To', 1, '2025-10-11 22:07:46', '2025-10-12 00:18:34', NULL),
(5, 'XS', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(6, 'XXL', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(7, 'XXXL', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(8, 'XS', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(9, 'S', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(10, 'M', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(11, 'L', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(12, 'XL', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(13, 'XXL', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(14, 'XXXL', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tax_rates`
--

CREATE TABLE `tax_rates` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(5,4) NOT NULL DEFAULT '0.0000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_rates`
--

INSERT INTO `tax_rates` (`id`, `name`, `rate`, `created_at`, `updated_at`) VALUES
(1, 'VAT 0%', 0.0000, '2025-10-13 03:40:15', '2025-10-13 03:40:15'),
(2, 'VAT 5%', 0.0500, '2025-10-13 03:40:15', '2025-10-13 03:40:15'),
(3, 'VAT 8%', 0.0800, '2025-10-13 03:40:15', '2025-10-13 03:40:15'),
(4, 'VAT 10%', 0.1000, '2025-10-13 03:40:15', '2025-10-13 03:40:15'),
(5, 'Luxury 35%', 0.3500, '2025-10-13 03:40:15', '2025-10-13 04:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `textures`
--

CREATE TABLE `textures` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1: active, 0: inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `textures`
--

INSERT INTO `textures` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Vải', NULL, 1, '2025-10-11 20:14:19', '2025-10-12 00:19:46', NULL),
(2, 'Cotton', NULL, 1, '2025-10-12 00:19:20', '2025-10-12 00:19:20', NULL),
(3, 'Da', NULL, 1, '2025-10-12 00:19:32', '2025-10-12 00:19:32', NULL),
(4, 'Polyester', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(5, 'Linen', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(6, 'Silk', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(7, 'Denim', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(8, 'Wool', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(9, 'Leather', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(10, 'Synthetic', NULL, 1, '2025-10-18 22:21:11', '2025-10-18 22:21:11', NULL),
(11, 'Cotton', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(12, 'Polyester', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(13, 'Linen', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(14, 'Silk', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(15, 'Denim', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(16, 'Wool', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(17, 'Leather', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL),
(18, 'Synthetic', NULL, 1, '2025-10-18 22:21:47', '2025-10-18 22:21:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` int NOT NULL DEFAULT '0' COMMENT '0 -> User, 1 -> Admin',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` int NOT NULL DEFAULT '0' COMMENT '0 -> Unverified, 1 -> Verified',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_token` text COLLATE utf8mb4_unicode_ci,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','blocked') COLLATE utf8mb4_unicode_ci NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `is_admin`, `phone_number`, `is_verified`, `email_verified_at`, `password`, `verification_token`, `token_expires_at`, `remember_token`, `created_at`, `updated_at`, `avatar`, `status`, `salary`, `hire_date`, `deleted_at`) VALUES
(1, 'ADMIN', 'admin@example.com', 1, '0900000001', 1, '2025-10-09 00:06:44', '$2y$12$9Pf7QSBQ4zu84XQzeDuVeuc9hU8.NfUNWSnaIWfiHIw1IESqUsFDq', 'WF81dE5ZCtmqmIdfuYDSINOw4AVYedYs', '2025-10-16 00:06:44', NULL, '2025-10-09 00:06:44', '2025-10-18 21:25:19', 'users/1760847918_4oqVaDXPUD.jpg', 'active', NULL, NULL, NULL),
(2, 'Trycia Hahn', 'user1@example.com', 0, '0900000002', 1, '2025-10-09 00:06:44', '$2y$12$XcJ4ajWu2YdkRPGGGeiOFubOY0ElqIwjxJVbQ3ZYJ3ZytMNPhyfVy', 'Nev1pHIStUwToRmoJ0fOu8DCNs12VuFE', '2025-10-18 00:06:44', NULL, '2025-10-09 00:06:44', '2025-10-11 18:56:59', NULL, 'active', NULL, NULL, NULL),
(3, 'Jameson Berge', 'user2@example.com', 0, '0900000003', 1, '2025-10-09 00:06:44', '$2y$12$sflYEmOJpNqvpEG4YGIiROpIlCdCGjqJkhbgqtn137VJwgaejm82q', 'OrE8hmJSruCsQvCKHcBWXHIF7Z4YFRUn', '2025-10-15 00:06:44', NULL, '2025-10-09 00:06:44', '2025-10-11 21:38:37', 'users/1760243917_EIEcqOOHGF.jpg', 'active', NULL, NULL, NULL),
(4, 'Dena Muller', 'user3@example.com', 0, '0900000004', 1, '2025-10-09 00:06:44', '$2y$12$ygVV.FdW6cPCoS66pGrVG.46BcqVBm21WVb1WRvUrsfmMkxmB0oRy', 'GYblKLZpcp3bLcm960IUTVNZLMVJy4eS', '2025-10-19 00:06:44', NULL, '2025-10-09 00:06:44', '2025-10-13 04:56:38', 'users/1760356548_d9kkOqB4MI.jpg', 'inactive', NULL, NULL, NULL),
(5, 'Dr. Misty Douglas', 'user4@example.com', 0, '0900000005', 1, NULL, '$2y$12$wO9WgkprHoIN07QZ9h8HquHBw9xXAOymPXzLCGsG6PLfMl.oyuxx6', 'PD1iVG1ocLs7Zz6DsJbIJ8z1GkXOOmSg', '2025-10-19 00:06:45', NULL, '2025-10-09 00:06:45', '2025-10-18 23:21:14', 'users/1760854874_UQHoDybSM0.jpg', 'active', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_loyalty`
--

CREATE TABLE `user_loyalty` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `loyalty_tier_id` bigint UNSIGNED NOT NULL,
  `total_spent` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_data`
--
ALTER TABLE `app_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_slug_unique` (`slug`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_logs_variant_id_foreign` (`variant_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_tiers`
--
ALTER TABLE `loyalty_tiers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loyalty_tiers_name_unique` (`name`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_sku_index` (`sku`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`),
  ADD KEY `product_images_variant_id_foreign` (`variant_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_variants_sku_unique` (`sku`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`),
  ADD KEY `product_variants_color_id_foreign` (`color_id`),
  ADD KEY `product_variants_size_id_foreign` (`size_id`),
  ADD KEY `product_variants_texture_id_foreign` (`texture_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shipping_carriers`
--
ALTER TABLE `shipping_carriers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shipping_carriers_code_unique` (`code`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_rates`
--
ALTER TABLE `tax_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_rates_name_unique` (`name`);

--
-- Indexes for table `textures`
--
ALTER TABLE `textures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_loyalty`
--
ALTER TABLE `user_loyalty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_loyalty_user_id_foreign` (`user_id`),
  ADD KEY `user_loyalty_loyalty_tier_id_foreign` (`loyalty_tier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_data`
--
ALTER TABLE `app_data`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_tiers`
--
ALTER TABLE `loyalty_tiers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shipping_carriers`
--
ALTER TABLE `shipping_carriers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tax_rates`
--
ALTER TABLE `tax_rates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `textures`
--
ALTER TABLE `textures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_loyalty`
--
ALTER TABLE `user_loyalty`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_images_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variants_size_id_foreign` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_variants_texture_id_foreign` FOREIGN KEY (`texture_id`) REFERENCES `textures` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_loyalty`
--
ALTER TABLE `user_loyalty`
  ADD CONSTRAINT `user_loyalty_loyalty_tier_id_foreign` FOREIGN KEY (`loyalty_tier_id`) REFERENCES `loyalty_tiers` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `user_loyalty_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
