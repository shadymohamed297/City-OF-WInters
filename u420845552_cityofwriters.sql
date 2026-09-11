-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 08, 2026 at 08:31 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u420845552_cityofwriters`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `governorate` varchar(60) NOT NULL,
  `city` varchar(80) NOT NULL,
  `street` varchar(200) NOT NULL,
  `building` varchar(60) DEFAULT NULL,
  `apartment` varchar(60) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` char(36) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `name_ar` varchar(120) NOT NULL,
  `name_en` varchar(120) NOT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `nav_order` int(11) NOT NULL DEFAULT 0,
  `parent_id` char(36) DEFAULT NULL,
  `show_in_nav` tinyint(1) NOT NULL DEFAULT 1,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `slug`, `name_ar`, `name_en`, `description_ar`, `description_en`, `image_url`, `display_order`, `nav_order`, `parent_id`, `show_in_nav`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
('7e71bbe9-f790-455c-a078-92e0349968ca', 'item-8owv2k', 'اكثر مبيعا', 'اكثر مبيعا', NULL, NULL, '🏆', 0, 0, NULL, 1, '🏆', 1, '2026-07-30 15:25:33', '2026-07-30 15:25:33'),
('b28dc395-a09d-11f1-b690-00391ce115c6', 'novels', 'روايات', 'Novels', NULL, NULL, NULL, 1, 0, NULL, 1, NULL, 1, '2026-08-25 15:57:39', '2026-08-25 15:57:39'),
('b28dc83a-a09d-11f1-b690-00391ce115c6', 'science', 'علوم', 'Science', NULL, NULL, NULL, 2, 0, NULL, 1, NULL, 1, '2026-08-25 15:57:39', '2026-08-25 15:57:39'),
('b28dd067-a09d-11f1-b690-00391ce115c6', 'history', 'تاريخ', 'History', NULL, NULL, NULL, 3, 0, NULL, 1, NULL, 1, '2026-08-25 15:57:39', '2026-08-25 15:57:39'),
('b28dd0b8-a09d-11f1-b690-00391ce115c6', 'children', 'أطفال', 'Children', NULL, NULL, NULL, 4, 0, NULL, 1, NULL, 1, '2026-08-25 15:57:39', '2026-08-25 15:57:39'),
('b28dd0ed-a09d-11f1-b690-00391ce115c6', 'self-help', 'تطوير ذات', 'Self-Help', NULL, NULL, NULL, 5, 0, NULL, 1, NULL, 1, '2026-08-25 15:57:39', '2026-08-25 15:57:39');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` char(36) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `type`, `value`, `min_subtotal`, `max_discount`, `usage_limit`, `used_count`, `starts_at`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
('b28e2917-a09d-11f1-b690-00391ce115c6', 'WELCOME10', NULL, 'percent', 10.00, 0.00, NULL, NULL, 0, NULL, NULL, 1, '2026-08-25 15:57:39', '2026-08-25 15:57:39'),
('b28e2a47-a09d-11f1-b690-00391ce115c6', 'FLAT50', NULL, 'fixed', 50.00, 100.00, NULL, NULL, 0, NULL, NULL, 1, '2026-08-25 15:57:39', '2026-08-25 15:57:39');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_costs`
--

CREATE TABLE `marketing_costs` (
  `id` char(36) NOT NULL,
  `cost_date` date NOT NULL DEFAULT curdate(),
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `channel` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` char(36) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_phone` varchar(30) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` enum('cod','paymob_card','paymob_wallet') NOT NULL DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`shipping_address`)),
  `notes` text DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `coupon_id` char(36) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `product_title_ar` varchar(200) NOT NULL,
  `product_title_en` varchar(200) NOT NULL,
  `product_cover` text DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL,
  `line_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` char(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` char(36) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `title_ar` varchar(200) NOT NULL,
  `title_en` varchar(200) NOT NULL,
  `author_ar` varchar(120) NOT NULL,
  `author_en` varchar(120) NOT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `category_id` char(36) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `compare_at_price` decimal(10,2) DEFAULT NULL,
  `cover_url` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`images`)),
  `stock` int(11) NOT NULL DEFAULT 0,
  `isbn` varchar(40) DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `publisher_ar` varchar(120) DEFAULT NULL,
  `publisher_en` varchar(120) DEFAULT NULL,
  `language` varchar(20) NOT NULL DEFAULT 'ar',
  `publication_year` int(11) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_bestseller` tinyint(1) NOT NULL DEFAULT 0,
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) NOT NULL DEFAULT 0,
  `cost_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `marketing_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `misc_expenses` decimal(12,2) NOT NULL DEFAULT 0.00,
  `profit_margin` decimal(12,2) GENERATED ALWAYS AS (`price` - coalesce(`cost_price`,0) - coalesce(`marketing_cost`,0) - coalesce(`misc_expenses`,0)) STORED,
  `unlimited_stock` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `slug`, `title_ar`, `title_en`, `author_ar`, `author_en`, `description_ar`, `description_en`, `category_id`, `price`, `compare_at_price`, `cover_url`, `images`, `stock`, `isbn`, `pages`, `publisher_ar`, `publisher_en`, `language`, `publication_year`, `is_featured`, `is_bestseller`, `is_new_arrival`, `is_active`, `rating`, `reviews_count`, `cost_price`, `marketing_cost`, `misc_expenses`, `unlimited_stock`, `display_order`, `created_at`, `updated_at`) VALUES
('00865bee-4dc7-4cec-825d-abd72a0e14b0', 'غارثا', 'غارثا', 'غارثا', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdpbuqa1duo01hnekxm9w17__D8_BA_D8_A7_D8_B1_D8_AB_D8_A7.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('065958c6-9439-4ff4-8cb1-95f6063febd0', '⁨‫معزوفة-الحب-والوجع-أربع-كتب-معا⁩', 'معزوفة الحب والوجع \"أربع كتب معًا\"', 'معزوفة الحب والوجع \"أربع كتب معًا\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 600.00, 1200.00, 'https://assets.wuiltstore.com/cmkal69bp00nw01fu35cab02u__D9_85_D8_B9_D8_B2_D9_88_D9_81_D8_A9__D8_A7_D9_84_D8_AD_D8_A8__D9_88_D8_A7_D9_84_D9_88_D8_AC_D8_B9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('07b417e7-6959-4d80-973d-95800b0f0972', 'اختطاف-غير-متوقع', 'اختطاف غير متوقع', 'اختطاف غير متوقع', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 200.00, NULL, 'https://assets.wuiltstore.com/cmk7qqfb30cw901fu94s3huw8__D8_A7_D8_AE_D8_AA_D8_B7_D8_A7_D9_81__D8_BA_D9_8A_D8_B1__D9_85_D8_AA_D9_88_D9_82_D8_B9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('09c2d032-78c0-440e-99db-cea3d20a2d90', 'عذاب-سيزيف', 'عذاب سيزيف', 'عذاب سيزيف', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmgdre59h1e0y01hn0xjy3vko__D8_B9_D8_B0_D8_A7_D8_A8__D8_B3_D9_8A_D8_B2_D9_8A_D9_81.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('0c8bda4c-7385-4e37-90c4-acfe3be36ce5', 'خمر-ونساء-ولب-أبيض', 'خمر ونساء ولب أبيض', 'خمر ونساء ولب أبيض', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7ooxg50cu001fueylr34l2__D8_AE_D9_85_D8_B1__D9_88_D9_86_D8_B3_D8_A7_D8_A6.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('0cf1aa72-e588-4eef-94eb-7edef5686572', 'جراح-لا-تلتئم', 'جراح لا تلتئم', 'جراح لا تلتئم', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 180.00, NULL, 'https://assets.wuiltstore.com/cmgdqknq61dxl01hnh0dzbtza__D8_AC_D8_B1_D8_A7_D8_AD__D9_84_D8_A7__D8_AA_D9_84_D8_AA_D8_A6_D9_85.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('0dbede2f-b977-43bd-83c4-fe5291cda829', '⁨‫الروم-أصل-الدولة-ومنشأها⁩', 'الروم (أصل الدولة ومنشأها)', 'الروم (أصل الدولة ومنشأها)', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 150.00, 'https://assets.wuiltstore.com/cmk936dlm01ru01fu9t2ug1ms__D8_A7_D9_84_D8_B1_D9_88_D9_85_-__D8_A3_D8_B5_D9_84__D8_A7_D9_84_D8_AF_D9_88_D9_84_D8_A9__D9_88_D9_85_D9_86_D8_B4_D8_A3_D9_87_D8_A7.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('0f2580e7-372f-4f2a-8f4b-f5de4dd50ffe', 'ورطة-في-جزيرة-الحب', 'ورطة في جزيرة الحب', 'ورطة في جزيرة الحب', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 140.00, 'https://assets.wuiltstore.com/cmkam02or00oq01fud6zk33hx__D9_88_D8_B1_D8_B7_D8_A9__D9_81_D9_8A__D8_AC_D8_B2_D9_8A_D8_B1_D8_A9__D8_A7_D9_84_D8_AD_D8_A8_copy.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('1051480a-6e19-4f2d-b029-72d94c725188', 'قناع-هابيل', 'قناع هابيل', 'قناع هابيل', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 180.00, 'https://assets.wuiltstore.com/cmkalfjit00od01fu2a5hazzx__D9_82_D9_86_D8_A7_D8_B9__D9_87_D8_A7_D8_A8_D9_8A_D9_84.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('11a00033-e907-446c-972a-1d952facb25f', 'ملحمة-ماريسا', 'ملحمة ماريسا', 'ملحمة ماريسا', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmgdt1cw41e5p01hnc7adc85w__D9_85_D9_84_D8_AD_D9_85_D8_A9__D9_85_D8_A7_D8_B1_D9_8A_D8_B3_D8_A7.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:52', '2026-07-30 16:15:52'),
('12a7a892-4255-41d9-9f51-e3a7c616ca5c', '⁨‫الروم-الأسرة-المقدونية⁩', 'الروم (الأسرة المقدونية)', 'الروم (الأسرة المقدونية)', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 150.00, 'https://assets.wuiltstore.com/cmk934cjh01rq01fu347kape7__D8_A7_D9_84_D8_B1_D9_88_D9_85_-__D8_A7_D9_84_D8_A3_D8_B3_D8_B1_D8_A9__D8_A7_D9_84_D9_85_D9_82_D8_AF_D9_88_D9_86_D9_8A_D8_A9__D9_88_D8_A7_D9_84_D8_B8_D9_81_D8_B1__D9_88_D8_A7_D9_84_D8_B9_D8_B8_D9_85_D8_A9__D9_88_D8_A7_D9_84_D9_85_D8_AC_D8_AF.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('1bd67efa-17e5-4648-8b16-419ba92bdddc', 'فن-البيع-وإدارة-المبيعات', 'فن البيع وإدارة المبيعات', 'فن البيع وإدارة المبيعات', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 200.00, 240.00, 'https://assets.wuiltstore.com/cmk93800l01s001fufpvk2gpu__D9_81_D9_86__D8_A7_D9_84_D8_A8_D9_8A_D8_B9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('216c9d1e-e9c2-4b4e-a7c0-440cb6d9efb5', 'تمام-بس-مش-تمام', 'تمام.. بس مش تمام', 'تمام.. بس مش تمام', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 140.00, 'https://assets.wuiltstore.com/cmkalwdbg00oo01fugepfbcij__D8_AA_D9_85_D8_A7_D9_85...__D8_A8_D8_B3__D9_85_D8_B4__D8_AA_D9_85_D8_A7_D9_85.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('23a7ea1a-0a16-401f-91ec-c90bd2196c0e', '⁨‫مافيا-الحي-الشعبي-كتابين-معا⁩', 'مافيا الحي الشعبي \"كتابين معًا\"', 'مافيا الحي الشعبي \"كتابين معًا\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 400.00, NULL, 'https://assets.wuiltstore.com/cmgdro3c21e2601hn74y1a1ow__D9_85_D8_A7_D9_81_D9_8A_D8_A7__D8_A7_D9_84_D8_AD_D9_8A__D8_A7_D9_84_D8_B4_D8_B9_D8_A8_D9_8A__D8_A7_D9_84_D9_83_D8_AA_D8_A7_D8_A8__D8_A7_D9_84_D8_A3_D9_88_D9_84.jpg https://assets.wuiltstore.com/cmgdro17d1e2501hne6jb18fa__D9_85_D8_A7_D9_81_D9_8A_D8_A7__D8_A7_D9_84_D8_AD_D9_8A__D8_A7_D9_84_D8_B4_D8_B9_D8_A8_D9_8A__D8_A7_D9_84_D9_83_D8_AA_D8_A7_D8_A8__D8_A7_D9_84_D8_AB_D8_A7_D9_86_D9_8A.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('23d931e1-8a74-47e2-99a8-aa57d1e3ac4d', 'لم-تقتلني-العقارب-قتلتني-فراشة', 'لم تقتلني العقارب، قتلتني فراشة', 'لم تقتلني العقارب، قتلتني فراشة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 130.00, 'https://assets.wuiltstore.com/cmk93nl0q01t001fuczvl4dcw__D9_84_D9_85__D8_AA_D9_82_D8_AA_D9_84_D9_86_D9_8A__D8_A7_D9_84_D8_B9_D9_82_D8_A7_D8_B1_D8_A8...__D9_82_D8_AA_D9_84_D8_AA_D9_86_D9_8A__D9_81_D8_B1_D8_A7_D8_B4_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('266ff87a-e1f5-48ab-bb83-9fdebbff0e4c', 'ذاكرة-مشروخة', 'ذاكرة مشروخة', 'ذاكرة مشروخة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmkalvgxx00on01fu4i8eaxcp__D8_B0_D8_A7_D9_83_D8_B1_D8_A9__D9_85_D8_B4_D8_B1_D9_88_D8_AE_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('281f9a42-4e0f-437b-a4ef-65d9ccfc460a', 'ذاكرة-الرماد', 'ذاكرة الرماد', 'ذاكرة الرماد', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 250.00, 300.00, 'https://assets.wuiltstore.com/cmkam9z0k00ox01fue2rafdqn__D8_B0_D8_A7_D9_83_D8_B1_D8_A9__D8_A7_D9_84_D8_B1_D9_85_D8_A7_D8_AF_copy.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('2f8251c4-79b4-4526-9ff2-9b64d8d1ad43', '⁨‫مهمشون-الجزء-الأول⁩', 'مهمشون \"الجزء الأول\"', 'مُهمشون \"الجزء الأول\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmgdt2fk11e5q01hn3gla79qi__D9_85_D9_87_D9_85_D8_B4_D9_88_D9_86__D8_A7_D9_84_D8_AC_D8_B2_D8_A1__D8_A7_D9_84_D8_A3_D9_88_D9_84.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('33a26693-3bd0-4add-9638-30cbb88adca8', 'أنثى-انتقم-لها-الجن', 'أنثى انتقم لها الجن', 'أنثى انتقم لها الجن', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 180.00, 'https://assets.wuiltstore.com/cmkalcqm100ob01fu3avzgixe__D8_A7_D9_86_D8_AB_D9_89__D8_A7_D9_86_D8_AA_D9_82_D9_85__D9_84_D9_87_D8_A7__D8_A7_D9_84_D8_AC_D9_86.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('34340f95-92fb-47d4-b9f8-73efffb6c96c', '⁨‫عزيزي-ثيو-الجزء-الثالث⁩', 'عزيزي ثيو \"الجزء الثالث\"', 'عزيزي ثيو \"الجزء الثالث\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmk93bi5n01s801fu66dfdffp__D8_B9_D8_B2_D9_8A_D8_B2_D9_8A__D8_AB_D9_8A_D9_88_-__D8_A7_D9_84_D8_AC_D8_B2_D8_A1__D8_A7_D9_84_D8_AB_D8_A7_D9_84_D8_AB.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('397f5e98-3802-4394-9a23-d724f5ff23a4', 'دفشو-مدينة-منسية-في-غرب-الدلتا-في-ضوء-الاكتشافات-الأقرية-الحديثة', 'دفشو - مدينة منسية في غرب الدلتا في ضوء الاكتشافات الأقرية الحديثة', 'دفشو - مدينة منسية في غرب الدلتا في ضوء الاكتشافات الأقرية ا ...', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 200.00, 250.00, 'https://assets.wuiltstore.com/cmk93hdy901st01fu30ly87u1__D8_AF_D9_81_D8_B4_D9_88.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('3d329aa2-5145-4fa0-9453-4a934a653182', 'لأجلك-عزيزي-البائس', 'لأجلك عزيزي البائس', 'لأجلك عزيزي البائس', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 50.00, NULL, 'https://assets.wuiltstore.com/cmk7pk15e0cv101fuf6un3az5__D9_84_D8_A7_D8_AC_D9_84_D9_83__D8_B9_D8_B2_D9_8A_D8_B2_D9_8A__D8_A7_D9_84_D8_A8_D8_A7_D8_A6_D8_B3.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('42b37fa9-0d9f-41c0-9cd8-84e11e35fc4f', '⁨‫ما-خبأته-السماء-تردي-في-العشق-قتيلا⁩', 'ما خبأته السماء \"تردي في العشق قتيلًا\"', 'ما خبأته السماء \"تردي في العشق قتيلًا\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 220.00, 250.00, 'https://assets.wuiltstore.com/cmkam7le300ow01fu4bay8x8u__D9_85_D8_A7__D8_AE_D8_A8_D8_A3_D8_AA_D9_87__D8_A7_D9_84_D8_B3_D9_85_D8_A7_D8_A1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('453f68f7-5468-4dbe-8c5d-a09aeb473b04', '⁨‫في-قبضة-الأقدار-كتابين⁩', 'في قبضة الأقدار \"كتابين\"', 'في قبضة الأقدار \"كتابين\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 250.00, 500.00, 'https://assets.wuiltstore.com/cmk7peqbf0cuw01fu1u4repig__D9_81_D9_8A__D9_82_D8_A8_D8_B6_D8_A9__D8_A7_D9_84_D8_A3_D9_82_D8_AF_D8_A7_D8_B1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('4616a91b-6282-4a1a-9f17-5a4120fdf315', 'طريق-بلا-عودة', 'طريق بلا عودة', 'طريق بلا عودة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 180.00, 'https://assets.wuiltstore.com/cmkam32fn00ou01fu3z5j9kwn__D8_B7_D8_B1_D9_8A_D9_82__D8_A8_D9_84_D8_A7__D8_B9_D9_88_D8_AF_D8_A9_2_copy.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('4a7dfb78-0da4-493b-981a-0cc675ff6225', 'على-طريقة-السيدة-لواحظ', 'على طريقة السيدة لواحظ', 'على طريقة السيدة لواحظ', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdrgpjq1e1b01hn7j0s6fvm__D8_B9_D9_84_D9_89__D8_B7_D8_B1_D9_8A_D9_82_D8_A9__D8_A7_D9_84_D8_B3_D9_8A_D8_AF_D8_A9__D9_84_D9_88_D8_A7_D8_AD_D8_B8.jpeg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('51d2f48c-fe1a-44bc-8edf-1d9f5e9380f9', 'لها-بقلبك-شيء', 'لها بقلبك شيء', 'لها بقلبك شيء', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmgdsz2ul1e5h01hnek7h2j47__D9_84_D9_87_D8_A7__D8_A8_D9_82_D9_84_D8_A8_D9_83__D8_B4_D9_8A_D8_A1.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:52', '2026-07-30 16:15:52'),
('57380845-8327-4299-b0d1-9436a434d3f2', 'نقوش-ملعونة', 'نقوش ملعونة', 'نقوش ملعونة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 90.00, NULL, 'https://assets.wuiltstore.com/cmgdt9yny1e6c01hnay8w9m3u__D9_86_D9_82_D9_88_D8_B4__D9_85_D9_84_D8_B9_D9_88_D9_86_D8_A9.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:52', '2026-07-30 16:15:52'),
('59f00e5d-e556-4323-9789-5c85e084c2a9', 'حكايات-العميد', 'حكايات العميد', 'حكايات العميد', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7q6v9p0cvt01fu4pmf1ru4__D8_AD_D9_83_D8_A7_D9_8A_D8_A7_D8_AA__D8_A7_D9_84_D8_B9_D9_85_D9_8A_D8_AF.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('5a004fd4-0eaa-49a6-94b2-0bbb64cb9159', 'الإعلان-الناجح-كيف-يمكن-تحقيقه', 'الإعلان الناجح - كيف يمكن تحقيقه؟', 'الإعلان الناجح - كيف يمكن تحقيقه؟', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 220.00, 250.00, 'https://assets.wuiltstore.com/cmk938ul501s101fu2bwv26gi__D8_A7_D9_84_D8_A7_D8_B9_D9_84_D8_A7_D9_86__D8_A7_D9_84_D9_86_D8_A7_D8_AC_D8_AD.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('5a6996cf-4432-488a-9b61-ba7dbce58809', 'تل-حوين', 'تل حوين', 'تل حوين', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7q3t1x0cvh01fu54dp7gtj__D8_AA_D9_84-_D8_AD_D9_88_D9_8A_D9_86_optimized.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('5d398460-75c8-408d-b66a-522a17405097', 'قرية الشيخ', 'قرية الشيخ', 'قرية الشيخ', '—', '—', 'فريق العو - المهمة الثانية', 'فريق العو - المهمة الثانية', '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmflbnup901ty01hn5hzz2t6o__D9_82_D8_B1_D9_8A_D8_A9__D8_A7_D9_84_D8_B4_D9_8A_D8_AE.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('60046abe-e259-4474-a919-2808e63d0021', 'الأوركيد-الأسود', 'الأوركيد الأسود', 'الأوركيد الأسود', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 180.00, NULL, 'https://assets.wuiltstore.com/cmk7oql500cu101fu4e5ydahr__D8_A7_D9_84_D8_A3_D9_88_D8_B1_D9_83_D9_8A_D8_AF__D8_A7_D9_84_D8_A3_D8_B3_D9_88_D8_AF.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('60ccaddc-b3fe-4aff-b8c7-de74066f4c1b', 'كتالوج-المشاعر', 'كتالوج المشاعر', 'كتالوج المشاعر', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmk7plhpp0cv401fu78ldh8j3__D9_83_D8_AA_D8_A7_D9_84_D9_88_D8_AC__D8_A7_D9_84_D9_85_D8_B4_D8_A7_D8_B9_D8_B1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('62c5dce6-1862-4b21-92d6-d19bd48378a0', 'مزرعة-بني-يعقوب', 'مزرعة بني يعقوب', 'مزرعة بني يعقوب', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmgdp7c9a1du001hn9au4c2hc__D9_85_D8_B2_D8_B1_D8_B9_D8_A9__D8_A8_D9_86_D9_8A__D9_8A_D8_B9_D9_82_D9_88_D8_A8.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('669a7b49-29a7-4184-851f-4a673994511a', 'حارة الجزار', 'حارة الجزار', 'حارة الجزار', '—', '—', 'فريق العو - المهمة الثالثة', 'فريق العو - المهمة الثالثة', '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmfxz6krb0pgh01hn2stt2flc__D8_AD_D8_A7_D8_B1_D8_A9__D8_A7_D9_84_D8_AC_D8_B2_D8_A7_D8_B1.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('6b46402c-c2e1-4fba-a0f3-07bcb759d347', 'أبناء-الخضري', 'أبناء الخضري', 'أبناء الخضري', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 250.00, 300.00, 'https://assets.wuiltstore.com/cmkamch9k00p001fu0p3bdfk8__D8_A3_D8_A8_D9_86_D8_A7_D8_A1__D8_A7_D9_84_D8_AE_D9_8F_D8_B6_D8_B1_D9_8A.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('6d684315-4963-49a4-a166-5511277f1922', 'أحلام-العصر', 'أحلام العصر', 'أحلام العصر', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmgdrvram1e2w01hnbf3o5bta__D8_A3_D8_AD_D9_84_D8_A7_D9_85__D8_A7_D9_84_D8_B9_D8_B5_D8_B1.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('6e9bbb75-1039-4a51-849d-f2471e2f6ffc', 'كفوف-النور', 'كفوف النور', 'كفوف النور', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7q87ds0cvu01fu24238iva__D9_83_D9_81_D9_88_D9_81__D8_A7_D9_84_D9_86_D9_88_D8_B1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('6ef9d824-3f7e-4908-827a-e0a241fb46e4', 'قصر-الخواجة', 'قصر الخواجة', 'قصر الخواجة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 180.00, 220.00, 'https://assets.wuiltstore.com/cmkamd9q700p101fu890x81yo__D9_88_D8_B4.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('70235448-267e-45da-8fdc-cffb0ecc2b8e', 'فن-أن-تكون-دائما-على-صواب', 'فن أن تكون دائمًا على صواب', 'فن أن تكون دائمًا على صواب', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 80.00, NULL, 'https://assets.wuiltstore.com/cmk939tkp01s301fu6ocb0h7g__D9_81_D9_86__D8_A7_D9_86__D8_AA_D9_83_D9_88_D9_86__D8_B9_D9_84_D9_89__D8_B5_D9_88_D8_A7_D8_A8...webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('70707811-681a-439e-913e-aa0a8b4dd880', 'معزوفة-خاصة', 'معزوفة خاصة', 'معزوفة خاصة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdrprz61e2701hnbunxgpgh__D9_85_D8_B9_D8_B2_D9_88_D9_81_D8_A9__D8_AE_D8_A7_D8_B5_D8_A9.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('70e2bfbc-75af-43e3-83f8-03b4b0901787', 'في-بيتنا-طبيب-نفسي', 'في بيتنا طبيب نفسي', 'في بيتنا طبيب نفسي', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmk7pkwo90cv201fuaqied2by__D9_81_D9_8A__D8_A8_D9_8A_D8_AA_D9_86_D8_A7__D8_B7_D8_A8_D9_8A_D8_A8__D9_86_D9_81_D8_B3_D9_8A.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('72aa4b79-5b07-4fb0-90f8-e7221ff07bb1', 'أوكازيون-البهجة', 'أوكازيون البهجة', 'أوكازيون البهجة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdslmbh1e4e01hn18abag24__D8_A7_D9_88_D9_83_D8_A7_D8_B2_D9_8A_D9_88_D9_86_C2_A0_D8_A7_D9_84_D8_A8_D9_87_D8_AC_D8_A9.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('79e24f15-2d0d-4f93-a3ee-0fb8cd759ae0', 'أقفال-موصدة', 'أقفال موصدة', 'أقفال موصدة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdqg4nn1dx301hndlgufdmb__D8_A3_D9_82_D9_81_D8_A7_D9_84__D9_85_D9_88_D8_B5_D8_AF_D8_A9.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('7a5a8794-7c96-4f90-a789-f107271a8403', 'الحب-في-عصر-السوشيال-ميديا', 'الحب في عصر السوشيال ميديا', 'الحب في عصر السوشيال ميديا', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 170.00, 200.00, 'https://assets.wuiltstore.com/cmkalb7dh00o901fua99qgm09__D8_A7_D9_84_D8_AD_D8_A8__D9_81_D9_8A__D8_A7_D9_84_D8_B3_D9_88_D8_B4_D9_8A_D8_A7_D9_84_3.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('7bd7fb40-ebe2-45fc-ab04-41f92f242323', 'ما-لا-يراه-البعض', 'ما لا يراه البعض', 'ما لا يراه البعض', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmk7pa6qh0cum01fuajbf1cb0_Untitled-1__1_.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('7f86222d-21c9-4058-ae31-260fec55674c', '⁨‫الروم-بين-الرحلة-الأولى-والتفكك-والانهيار⁩', 'الروم (بين الرحلة الأولى والتفكك والانهيار)', 'الروم (بين الرحلة الأولى والتفكك والانهيار)', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 150.00, 'https://assets.wuiltstore.com/cmk934t9k01rr01fuabkbh1o1__D8_A7_D9_84_D8_B1_D9_88_D9_85_-__D8_A8_D9_8A_D9_86__D8_A7_D9_84_D8_B1_D8_AD_D9_84_D8_A9__D8_A7_D9_84_D8_A3_D9_88_D9_84_D9_89__D9_88_D8_A7_D9_84_D8_AA_D9_81_D9_83_D9_83__D9_88_D8_A7_D9_84_D8_A7_D9_86_D9_87_D9_8A_D8_A7_D8_B1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('8172bf58-8691-4cc6-baff-4a3e7f0a83cd', 'جناة-لكن-أبرياء', 'جناة لكن أبرياء', 'جناة لكن أبرياء', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7q5bce0cvs01fu4w7q0ut9__D8_AC_D9_86_D8_A7_D8_A9__D9_84_D9_83_D9_86__D8_A3_D8_A8_D8_B1_D9_8A_D8_A7_D8_A1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('81b9f9d9-82c4-4810-8576-f2e0538e845f', 'الدلالات-السياقية-لبعض-العلامات-الهيروغليفية', 'الدلالات السياقية لبعض العلامات الهيروغليفية', 'الدلالات السياقية لبعض العلامات الهيروغليفية', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 170.00, 200.00, 'https://assets.wuiltstore.com/cmk93kul501sw01fuek55bbzb__D8_A7_D9_84_D8_AF_D9_84_D8_A7_D9_84_D8_A7_D8_AA__D8_A7_D9_84_D8_B3_D9_8A_D8_A7_D9_82_D9_8A_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('835d1cb0-da26-43cc-9dbc-2e1b7b6e00be', 'إليك-صغيرتي-نورس', 'إليك صغيرتي نورس', 'إليك صغيرتي نورس', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 180.00, 'https://assets.wuiltstore.com/cmkala5ma00o801fu3hlwbulw__D8_A5_D9_84_D9_8A_D9_83__D8_B5_D8_BA_D9_8A_D8_B1_D8_AA_D9_8A__D9_86_D9_88_D8_B1_D8_B3.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('84ab3f63-b39d-414e-b070-8a6b87e5e2a1', '⁨‫قلوب-حائرة-ثلاثة-كتب⁩', 'قلوب حائرة \"ثلاث كتب\"', 'قلوب حائرة \"ثلاثة كتب\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 450.00, 900.00, 'https://assets.wuiltstore.com/cmk7pcw8c0cut01fuckk90ru6__D9_82_D9_84_D9_88_D8_A8__D8_AD_D8_A7_D8_A6_D8_B1_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('8af2a07f-d1f3-4ac3-accf-e2a3be895e52', 'الأخذ-بالثأر-والإنتقام-في-مصر-القديمة', 'الأخذ بالثأر والإنتقام في مصر القديمة', 'الأخذ بالثأر والإنتقام في مصر القديمة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 220.00, 275.00, 'https://assets.wuiltstore.com/cmk93loru01sx01fu469u47ls__D8_A7_D9_84_D8_A3_D8_AE_D8_B0__D8_A8_D8_A7_D9_84_D8_AB_D8_A3_D8_B1_copy.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('8b755925-6ed7-466f-bbda-3ffe30c98502', 'رجال-صدقوا', 'رجال صدقوا', 'رجال صدقوا', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, 120.00, 'https://assets.wuiltstore.com/cmk7q1css0cvf01fu6tpq4act_Untitled-2.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('8d92cb1b-e392-4bfc-af04-ac281cf37421', '⁨‫دليلك-إلى-‫15-خطة-في-منتهى-الغباء⁩', 'دليلك إلى 15 خطة في منتهى الغباء', 'دليلك إلى 15 خطة في منتهى الغباء', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmk7pw5170cvb01fuep65eogr__D8_AF_D9_84_D9_8A_D9_84_D9_83__D8_A5_D9_84_D9_89_15__D8_AE_D8_B7_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('992161c3-a284-4166-88d3-0df437165ad6', '⁨‫عزيزي-ثيو-الجزء-الأول⁩', 'عزيزي ثيو \"الجزء الأول\"', 'عزيزي ثيو \"الجزء الأول\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 125.00, 150.00, 'https://assets.wuiltstore.com/cmk93colb01sk01fu5w7t9qpl__D8_B9_D8_B2_D9_8A_D8_B2_D9_8A__D8_AB_D9_8A_D9_88_-__D8_A7_D9_84_D8_AC_D8_B2_D8_A1__D8_A7_D9_84_D8_A3_D9_88_D9_84.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('9c1f8d4c-89e2-4db5-92a7-ae9950974570', '⁨‫براءة-ظلمه-الجزء-الثاني⁩', 'براءة ظلمه \"الجزء الثاني\"', 'براءة ظلمه \"الجزء الثاني\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdp07xt1dth01hnfojp5d5r__D8_A8_D8_B1_D8_A7_D8_A1_D8_A9__D8_A7_D9_84_D8_AC_D8_B2_D8_A1__D8_A7_D9_84_D8_AB_D8_A7_D9_86_D9_8A.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('9e92f599-9f5e-4aea-b6f9-b33d657832d3', 'دماء-هجينة', 'دماء هجينة', 'دماء هجينة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmgdsunzk1e5301hngjcf2mhj__D8_AF_D9_85_D8_A7_D8_A1__D9_87_D8_AC_D9_8A_D9_86_D8_A9.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:52', '2026-07-30 16:15:52'),
('9ed645b9-08f7-429a-9a2a-6d63526dc6d2', 'من-بولاق-إلى-ارتكيدوس', 'من بولاق إلى ارتكيدوس', 'من بولاق إلى ارتكيدوس', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 140.00, 'https://assets.wuiltstore.com/cmkalxy7w00op01fueny4gxyc__D9_85_D9_86__D8_A8_D9_88_D9_84_D8_A7_D9_82__D8_A5_D9_84_D9_89__D8_A7_D8_B1_D8_AA_D9_83_D9_8A_D8_AF_D9_88_D8_B3.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('a40ec653-ea8a-44e7-b292-c542998c0ca8', 'ميثاق-الحب-والياقوت', 'ميثاق الحب والياقوت', 'ميثاق الحب والياقوت', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmk7p90fh0cuj01fudcwogcsn__D9_85_D9_8A_D8_AB_D8_A7_D9_82__D8_A7_D9_84_D8_AD_D8_A8__D9_88_D8_A7_D9_84_D9_8A_D8_A7_D9_82_D9_88_D8_AA.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('a5fb1cab-f4a4-4099-ae5f-87e873cc94e2', 'لعنة-الفراعنة', 'لعنة الفراعنة', 'لعنة الفراعنة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 200.00, 250.00, 'https://assets.wuiltstore.com/cmk7pjfe70cv001fugf8b5qgx__D9_84_D8_B9_D9_86_D8_A9__D8_A7_D9_84_D9_81_D8_B1_D8_A7_D8_B9_D9_86_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('a7d82325-7f02-4f80-bcd8-565ce1462c84', 'بك-بقيت', 'بك بقيت', 'بك بقيت', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7q26f90cvg01fu2bh48zfw__D8_A8_D9_83__D8_A8_D9_82_D9_8A_D8_AA.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('a889f76e-762b-4870-b864-d68c10872893', 'مدرسة ميلر', 'مدرسة ميلر', 'مدرسة ميلر', '—', '—', 'فريق العو - المهمة الأولى', 'فريق العو - المهمة الأولى', '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmfff7jpx048j01lw9ipab4uv__D9_85_D8_AF_D8_B1_D8_B3_D8_A9__D9_85_D9_8A_D9_84_D8_B1.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('aa2987b0-0e78-46b7-ac3b-af9488e87405', '⁨‫الروم-اليقظة-الأخيرة-واخفاقها-والنهاية⁩', 'الروم (اليقظة الأخيرة واخفاقها والنهاية)', 'الروم (اليقظة الأخيرة واخفاقها والنهاية)', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 150.00, 'https://assets.wuiltstore.com/cmk933c3y01re01fu9oc37bbk__D8_A7_D9_84_D8_B1_D9_88_D9_85_-__D8_A7_D9_84_D9_8A_D9_82_D8_B8_D8_A9__D8_A7_D9_84_D8_A3_D8_AE_D9_8A_D8_B1_D8_A9__D9_88_D8_A7_D8_AE_D9_81_D8_A7_D9_82_D9_87_D8_A7__D9_88_D8_A7_D9_84_D9_86_D9_87_D8_A7_D9_8A_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('ac80c0fb-1ee0-450b-b1ea-f68a923dc686', 'النسر', 'النسر', 'النسر', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 170.00, 200.00, 'https://assets.wuiltstore.com/cmkal1i7600nr01fu4plwcxd0__D8_A7_D9_84_D9_86_D8_B3_D8_B1-2.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('b247fa6a-27e8-4cf3-b12b-1807cbc8d1e1', 'مابل-والبوابة-النجمية', 'مابل والبوابة النجمية', 'مابل والبوابة النجمية', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmk7pouck0cv701fueywi3jj0__D9_85_D8_A7_D8_A8_D9_84__D9_88_D8_A7_D9_84_D8_A8_D9_88_D8_A7_D8_A8_D8_A9__D8_A7_D9_84_D9_86_D8_AC_D9_85_D9_8A_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('b5ec1874-2a3c-42ed-98ad-2a8290e344dd', '⁨‫ما-بعد-السقوط-موسم-النجاح⁩', 'ما بعد السقوط \"موسم النجاح\"', 'ما بعد السقوط \"موسم النجاح\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmkale54c00oc01fu00d6827m__D9_85_D8_A7__D8_A8_D8_B9_D8_AF__D8_A7_D9_84_D8_B3_D9_82_D9_88_D8_B7.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('b9ef3700-671c-48bb-8875-9fdc5d8630ea', 'kronos', 'خرونس', 'خرونس', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdo0iyc1dre01hn6cvvfbbh__D8_AE_D8_B1_D9_88_D9_86_D8_B3.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('bd2bb34f-33a6-468a-9ad4-2ea0468f5d03', 'لم-تكتب-بعد', 'لم تكتب بعد', 'لم تكتب بعد', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmkal3fps00nu01fu5a2ea5v5__D9_84_D9_85__D8_AA_D9_83_D8_AA_D8_A8__D8_A8_D8_B9_D8_AF.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('be6e47d5-9d64-46f8-a39c-089b4c3d67d3', '⁨‫حضارة-الروم-بين-تطور-النظم-والانتعاش-والتوطيد-والاستقرار⁩', 'حضارة الروم (بين تطور النظم والانتعاش والتوطيد والاستقرار)', 'حضارة الروم (بين تطور النظم والانتعاش والتوطيد والاستقرار)', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 150.00, 'https://assets.wuiltstore.com/cmk935k1w01rs01fu8ifwf57l__D8_AD_D8_B6_D8_A7_D8_B1_D8_A9__D8_A7_D9_84_D8_B1_D9_88_D9_85.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('c24b7a0e-6223-42f3-9f8d-f1434a738323', 'سر-فلوريت', 'سر فلوريت', 'سر فلوريت', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmk7q93zc0cvw01fuf39x1s5v__D8_B3_D8_B1-_D9_81_D9_84_D9_88_D8_B1_D9_8A_D8_AA-_1__optimized.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('c4147058-7a49-48b1-9264-89cdf622536d', 'يوسف-يا-صديق', 'يوسف يا صديق', 'يوسف يا صديق', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 250.00, NULL, 'https://assets.wuiltstore.com/cmk7qdi6t0cw201fu4yf43uap__D9_8A_D9_88_D8_B3_D9_81__D9_8A_D8_A7__D8_B5_D8_AF_D9_8A_D9_82.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('c4e825cd-92b7-4635-af54-9bc9f69c8a5f', 'أربعة-وعشرون-ساعة-من-حياة-امرأة', 'أربعة وعشرون ساعة من حياة امرأة', 'أربعة وعشرون ساعة من حياة امرأة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 80.00, 100.00, 'https://assets.wuiltstore.com/cmk9374vq01ry01fue41p9fb4_24__D8_B3_D8_A7_D8_B9_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('c50e956f-fe77-4d0e-b88f-fb1d7545952e', 'صرخات-أنثى-أربع-كتب-الأجزاء-الأخيرة', 'صرخات أنثى \"أربع كتب\" الأجزاء الأخيرة', 'صرخات أنثى \"أربع كتب\" الأجزاء الأخيرة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 600.00, 1200.00, 'https://assets.wuiltstore.com/cmkal8eht00nz01fub1u7312p__D8_A8_D9_8A_D9_86__D8_BA_D9_8A_D8_A7_D9_87_D8_A8__D8_A7_D9_84_D8_AC_D9_8F_D8_A8.webp https://assets.wuiltstore.com/cmkal8g2600o001fu2vmb4dgt__D8_B2_D9_82_D8_A7_D9_82__D8_A8_D9_8A_D9_86__D8_A7_D9_84_D8_A3_D8_AD_D9_8A_D8_A7_D8_A1.webp https://assets.wuiltstore.com/cmkal8ivk00o201fu79dvbyng__D8_B3_D9_86_D8_B4_D8_AF__D8_B9_D8_B6_D8_AF_D9_83__D8_A8_D8_A3_D8_AE_D9_8A_D9_83.webp https://assets.wuiltstore.com/cmkal8hf000o101fud1n23kv7__D8_B9_D9_88_D8_AF_D8_A9__D8_A7_D9_84_D8_B7_D8_A7_D9_88_D9_88_D8_B3.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('c649f80a-114f-4b55-973c-8ecf30161ec0', '⁨‫قلبي-بنارها-مغرم-أربع-كتب⁩', 'قلبي بنارها مغرم \"أربع كتب\"', 'قلبي بنارها مغرم \"أربع كتب\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 720.00, NULL, 'https://assets.wuiltstore.com/cmk7pdtj60cuu01fu02a937x1__D9_82_D9_84_D8_A8_D9_8A__D8_A8_D9_86_D8_A7_D8_B1_D9_87_D8_A7__D9_85_D8_BA_D8_B1_D9_85.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('c7e65194-79bf-4d6c-baa8-f8f10ff15319', 'والقلب-في-الهوى-عليل', 'والقلب في الهوى عليل', 'والقلب في الهوى عليل', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmk7pxzi00cvc01fu6qwoh77o__D9_88_D8_A7_D9_84_D9_82_D9_84_D8_A8__D9_81_D9_8A__D8_A7_D9_84_D9_87_D9_88_D9_89__D8_B9_D9_84_D9_8A_D9_84.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('c88c5020-603f-404a-9494-f4558a543305', 'طريقة-طبخ-جثة-بالمنزل', 'طريقة طبخ جثة بالمنزل', 'طريقة طبخ جثة بالمنزل', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 180.00, 'https://assets.wuiltstore.com/cmkam2e4c00ot01fuhghe1d2s__D8_B7_D8_B1_D9_8A_D9_82_D8_A9__D8_B7_D8_A8_D8_AE__D8_AC_D8_AB_D8_A9__D8_A8_D8_A7_D9_84_D9_85_D9_86_D8_B2_D9_84.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('cdc622b9-8b67-4350-ae84-334729bbe7c0', 'أودعتك-نبضي', 'أودعتك نبضي', 'أودعتك نبضي', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 200.00, 240.00, 'https://assets.wuiltstore.com/cmkamavqe00oy01fu9vwk51t2__D8_A3_D9_88_D8_AF_D8_B9_D8_AA_D9_83__D9_86_D8_A8_D8_B6_D9_8A.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('cf3db81b-c9e3-4df4-88ec-86875ee3f2ca', 'الملكة-الأسيرة', 'الملكة الأسيرة', 'الملكة الأسيرة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 200.00, 220.00, 'https://assets.wuiltstore.com/cmkalbzl800oa01fu6nb06j2y__D8_A7_D9_84_D9_85_D9_84_D9_83_D8_A9__D8_A7_D9_84_D8_A3_D8_B3_D9_8A_D8_B1_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('d504e4df-f689-4988-a338-d53338a89e1f', 'خلود-الاسم-وأهميته-في-الحضارة-المصرية-القديمة', 'خلود الاسم وأهميته في الحضارة المصرية القديمة', 'خلود الاسم وأهميته في الحضارة المصرية القديمة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 200.00, 250.00, 'https://assets.wuiltstore.com/cmk93gh0y01sq01fu1qerbqpt__D8_AE_D9_84_D9_88_D8_AF__D8_A7_D9_84_D8_A7_D8_B3_D9_85.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('d51a7bfd-122f-461f-b24a-d6b9b5955df4', '⁨‫براءة-ظلمه-الجزء-الأول⁩', 'براءة ظلمه \"الجزء الأول\"', 'براءة ظلمه \"الجزء الأول\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmgdobfkq1drr01hn52pdaots__D8_A8_D8_B1_D8_A7_D8_A1_D8_A9__D8_A7_D9_84_D8_AC_D8_B2_D8_A1__D8_A7_D9_84_D8_A3_D9_88_D9_84.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('d963f77a-8e9d-4f51-998f-96bf05981c35', 'الضوضاء-في-مصر-القديمة', 'الضوضاء في مصر القديمة', 'الضوضاء في مصر القديمة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 140.00, 170.00, 'https://assets.wuiltstore.com/cmk93k6xo01sv01fud57cf19z__D8_A7_D9_84_D8_B6_D9_88_D8_B6_D8_A7_D8_A1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('d981b181-53d6-4241-98ab-12dd55f45031', 'جريمة-عائلية', 'جريمة عائلية', 'جريمة عائلية', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmk7puz0r0cva01fu6mtv7404__D8_AC_D8_B1_D9_8A_D9_85_D8_A9-_D8_B9_D8_A7_D8_A6_D9_84_D9_8A_D8_A9_optimized.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('dc4f5c9d-3fc9-4683-b50f-38db4604407f', '⁨‫طالوس-غارثا-الجزء-الثاني⁩', 'طالوس \"غارثا الجزء الثاني\"', 'طالوس \"غارثا الجزء الثاني\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 170.00, 'https://assets.wuiltstore.com/cmkambqfr00oz01fueo17gii7__D8_B7_D8_A7_D9_84_D9_88_D8_B3.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('dc9b6ce6-8429-49b2-aa06-9cd370cd8d31', 'السر-الثاني-عشر', 'السر الثاني عشر', 'السر الثاني عشر', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7ot8zj0cu501fufn544doy__D8_A7_D9_84_D8_B3_D8_B1__D8_A7_D9_84_D8_AB_D8_A7_D9_86_D9_8A__D8_B9_D8_B4_D8_B1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('e0dcef35-3ff0-4c77-83f5-baa52d8a474c', 'ملك-سليمان', 'ملك سليمان', 'ملك سليمان', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, NULL, 'https://assets.wuiltstore.com/cmk7orvpm0cu401fu9ufocl09__D9_85_D9_84_D9_83__D8_B3_D9_84_D9_8A_D9_85_D8_A7_D9_86__1_.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('e0ec8ff7-f9b4-41c7-8a1f-144aebbbc9f0', 'ندبات-القلب-قد-لا-تشفى', 'ندبات القلب قد لا تشفى', 'ندبات القلب قد لا تشفى', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7pqpun0cv901fuh1kw2k7k__D9_86_D8_AF_D8_A8_D8_A7_D8_AA__D8_A7_D9_84_D9_82_D9_84_D8_A8__D9_82_D8_AF__D9_84_D8_A7__D8_AA_D8_B4_D9_81_D9_89.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('e236ee58-a37b-44b2-8959-fcded6fc90be', 'أهداني-ضحايا', 'أهداني ضحايا', 'أهداني ضحايا', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 180.00, 'https://assets.wuiltstore.com/cmkam1jw800os01fucioq3cdp__D8_A3_D9_87_D8_AF_D8_A7_D9_86_D9_8A__D8_B6_D8_AD_D8_A7_D9_8A_D8_A7.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('e62c9c8e-9e87-4c49-b50c-ac943e1626fa', 'الإله-العظيم-بان', 'الإله العظيم بان', 'الإله العظيم بان', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 150.00, 'https://assets.wuiltstore.com/cmk93alcs01s601fu2o299q9m__D8_A7_D9_84_D8_A7_D9_84_D9_87__D8_A7_D9_84_D8_B9_D8_B8_D9_8A_D9_85__D8_A8_D8_A7_D9_86.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('e63fdb35-7e19-4340-8e0b-2e739b6e5577', '⁨‫عزيزي-ثيو-الجزء-الثاني⁩', 'عزيزي ثيو \"الجزء الثاني\"', 'عزيزي ثيو \"الجزء الثاني\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 125.00, 150.00, 'https://assets.wuiltstore.com/cmk93c79x01sf01fu4izfhn4t__D8_B9_D8_B2_D9_8A_D8_B2_D9_8A__D8_AB_D9_8A_D9_88_-__D8_A7_D9_84_D8_AC_D8_B2_D8_A1__D8_A7_D9_84_D8_AB_D8_A7_D9_86_D9_8A.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('e7cd20b8-3ebd-4ade-98f2-7c4803d2c29f', 'كتاب-العلاقات', 'كتاب العلاقات', 'كتاب العلاقات', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 90.00, NULL, 'https://assets.wuiltstore.com/cmk7p5tkr0cuc01fuh46h4zrl__D8_A7_D9_84_D8_B9_D9_84_D8_A7_D9_82_D8_A7_D8_AA__3_.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('e8deaa54-3c2b-48d7-9c9e-53974a8e27bf', 'ما-وراء-الكوابيس', 'ما وراء الكوابيس', 'ما وراء الكوابيس', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmkal464q00nv01fu828z9w1y__D9_85_D8_A7__D9_88_D8_B1_D8_A7_D8_A1__D8_A7_D9_84_D9_83_D9_88_D8_A7_D8_A8_D9_8A_D8_B3.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('ebbe5d7a-73bf-47f9-9ac1-17484a212231', 'صرخات-أنثى-ثلاث-كتب-الأجزاء-الأولى', 'صرخات أنثى \"ثلاث كتب\" الأجزاء الأولى', 'صرخات أنثى \"ثلاث كتب\" الأجزاء الأولى', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 450.00, 900.00, 'https://assets.wuiltstore.com/cmk7pbk9n0cun01fu4yja3xfn__D8_A7_D9_84_D8_B7_D8_A7_D9_88_D9_88_D8_B3__D8_A7_D9_84_D9_88_D9_82_D8_AD.webp https://assets.wuiltstore.com/cmk7pbne90cup01fu3nu00sid__D8_A7_D9_84_D8_B7_D8_A8_D9_82_D8_A9__D8_A7_D9_84_D8_A7_D8_B1_D8_B3_D8_AA_D9_82_D8_B1_D8_A7_D8_B7_D9_8A_D8_A9.webp https://assets.wuiltstore.com/cmk7pblwy0cuo01fu0z0k8w6w__D8_AD_D8_A8_D9_8A_D8_A8_D8_AA_D9_8A__D8_A7_D9_84_D8_B9_D8_A8_D8_B1_D9_8A_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('ebf3206f-b753-48e3-936a-42bad2b27b57', 'بين-أروقة-المورستان', 'بين أروقة المورستان', 'بين أروقة المورستان', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 170.00, 200.00, 'https://assets.wuiltstore.com/cmkam0scu00or01fu435h4zpc__D8_A8_D9_8A_D9_86__D8_A3_D8_B1_D9_88_D9_82_D8_A9__D8_A7_D9_84_D9_85_D9_88_D8_B1_D8_B3_D8_AA_D8_A7_D9_86.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('ec40870e-b088-4c1e-9276-cf92c85f0fe9', '⁨‫مهمشون-الجزء-الثاني⁩', 'مهمشون \"الجزء الثاني\"', 'مهمشون \"الجزء الثاني\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 100.00, NULL, 'https://assets.wuiltstore.com/cmk7p80ps0cui01fu0c1r78ib__D9_85_D9_87_D9_85_D8_B4_D9_88_D9_86__D8_A7_D9_84_D8_AC_D8_B2_D8_A1__D8_A7_D9_84_D8_AB_D8_A7_D9_86_D9_8A.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('f0276333-c6f5-49d2-a43c-6dc0a53595f6', '⁨‫مملكة-سفيد-خمس-كتب⁩', 'مملكة سفيد \"خمس كتب\"', 'مملكة سفيد \"خمس كتب\"', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 750.00, 1500.00, 'https://assets.wuiltstore.com/cmk7pi99n0cuz01fuhuzddob9__D9_85_D9_85_D9_84_D9_83_D8_A9__D8_B3_D9_81_D9_8A_D8_AF.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('f0cef7f8-d700-4b5b-88ee-20a15996de39', 'وسيلة-عبر-السديم', 'وسيلة عبر السديم', 'وسيلة عبر السديم', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 140.00, 'https://assets.wuiltstore.com/cmkalgfoi00of01fu4mc312rl__D9_88_D8_B3_D9_8A_D9_84_D8_A9__D8_B9_D8_A8_D8_B1__D8_A7_D9_84_D8_B3_D8_AF_D9_8A_D9_85.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('f581a59b-9083-4971-87f4-14650d0faef2', 'سر-الألة-الكاتبة', 'سر الألة الكاتبة', 'سر الألة الكاتبة', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmgdqdjvw1dwy01hn9ywe3a10__D8_B3_D8_B1__D8_A7_D9_84_D8_A3_D9_84_D8_A9__D8_A7_D9_84_D9_83_D8_A7_D8_AA_D8_A8_D8_A9.jpg', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('f5f7d7c8-ce42-497f-bfa7-081a0ae1498f', 'الهاتف', 'الهاتف', 'الهاتف', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 150.00, 180.00, 'https://assets.wuiltstore.com/cmkam3pnn00ov01fu5ktlerjw__D8_A7_D9_84_D9_87_D8_A7_D8_AA_D9_81.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('f776d82a-8f98-4649-b364-caca2d0e62c2', 'سفر-البداية', 'سفر البداية', 'سفر البداية', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 130.00, 150.00, 'https://assets.wuiltstore.com/cmk93mitk01sy01fu392z2ua4__D8_B3_D9_90_D9_81_D8_B1__D8_A7_D9_84_D8_A8_D8_AF_D8_A7_D9_8A_D8_A9.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('f98e7487-b3a1-44ab-80b8-226846ebc406', 'كيد-ساحر', 'كيد ساحر', 'كيد ساحر', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, NULL, 'https://assets.wuiltstore.com/cmk7qcq8l0cw101fu5l4faug9__D9_83_D9_8A_D8_AF__D8_B3_D8_A7_D8_AD_D8_B1.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:51', '2026-07-30 16:15:51'),
('fc1cf067-5f5e-4897-a1a7-6d9d9d2ab9c8', 'حين-ينكسر-الضوء', 'حين ينكسر الضوء', 'حين ينكسر الضوء', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 120.00, 140.00, 'https://assets.wuiltstore.com/cmkal2rz500nt01fu6twt5yji__D8_AD_D9_8A_D9_86__D9_8A_D9_86_D9_83_D8_B3_D8_B1_3_copy.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50'),
('fe72cb0c-2a0e-46a8-adcc-c38c998e5778', 'على-قارعة-الزمان', 'على قارعة الزمان', 'على قارعة الزمان', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 180.00, 220.00, 'https://assets.wuiltstore.com/cmkalpzil00ol01fu9pt09pyu__D8_B9_D9_84_D9_89__D9_82_D8_A7_D8_B1_D8_B9_D8_A9__D8_A7_D9_84_D8_B2_D9_85_D8_A7_D9_86__2_.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50');
INSERT INTO `products` (`id`, `slug`, `title_ar`, `title_en`, `author_ar`, `author_en`, `description_ar`, `description_en`, `category_id`, `price`, `compare_at_price`, `cover_url`, `images`, `stock`, `isbn`, `pages`, `publisher_ar`, `publisher_en`, `language`, `publication_year`, `is_featured`, `is_bestseller`, `is_new_arrival`, `is_active`, `rating`, `reviews_count`, `cost_price`, `marketing_cost`, `misc_expenses`, `unlimited_stock`, `display_order`, `created_at`, `updated_at`) VALUES
('ff7b6142-d513-498f-a33d-5a036bc96ed0', 'عطر-على-صراط-الألم', 'عطر على صراط الألم', 'عطر على صراط الألم', '—', '—', NULL, NULL, '7e71bbe9-f790-455c-a078-92e0349968ca', 170.00, 200.00, 'https://assets.wuiltstore.com/cmkal9hnw00o301fuftq4c5js__D8_B9_D8_B7_D8_B1__D8_B9_D9_84_D9_89__D8_B5_D8_B1_D8_A7_D8_B7__D8_A7_D9_84_D8_A3_D9_84_D9_85.webp', '[]', 100, NULL, NULL, NULL, NULL, 'ar', NULL, 0, 0, 0, 1, 0.00, 0, 0.00, 0.00, 0.00, 0, 0, '2026-07-30 16:15:50', '2026-07-30 16:15:50');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` char(36) NOT NULL,
  `full_name` text DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avatar_url` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `full_name`, `phone`, `avatar_url`, `created_at`, `updated_at`) VALUES
('05fbb2c4-29b8-483a-821f-b662d486556a', 'Admin', NULL, NULL, '2026-07-30 14:24:26', '2026-07-30 14:24:26'),
('138fb1ce-773f-437b-97dc-c791546aabee', 'ملك خالد', '01157392871', NULL, '2026-08-28 09:21:48', '2026-08-28 09:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `title` varchar(120) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_rates`
--

CREATE TABLE `shipping_rates` (
  `id` int(11) NOT NULL,
  `governorate_ar` varchar(80) NOT NULL,
  `governorate_en` varchar(80) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_rates`
--

INSERT INTO `shipping_rates` (`id`, `governorate_ar`, `governorate_en`, `price`, `enabled`, `updated_at`) VALUES
(1, 'القاهرة', 'Cairo', 50.00, 1, '2026-08-25 15:57:39'),
(2, 'الجيزة', 'Giza', 50.00, 1, '2026-08-25 15:57:39'),
(3, 'الإسكندرية', 'Alexandria', 50.00, 1, '2026-08-25 15:57:39'),
(4, 'القليوبية', 'Qalyubia', 50.00, 1, '2026-08-25 15:57:39'),
(5, 'الشرقية', 'Sharqia', 50.00, 1, '2026-08-25 15:57:39'),
(6, 'الدقهلية', 'Dakahlia', 50.00, 1, '2026-08-25 15:57:39'),
(7, 'الغربية', 'Gharbia', 50.00, 1, '2026-08-25 15:57:39'),
(8, 'المنوفية', 'Monufia', 50.00, 1, '2026-08-25 15:57:39'),
(9, 'كفر الشيخ', 'Kafr El Sheikh', 50.00, 1, '2026-08-25 15:57:39'),
(10, 'البحيرة', 'Beheira', 50.00, 1, '2026-08-25 15:57:39'),
(11, 'دمياط', 'Damietta', 50.00, 1, '2026-08-25 15:57:39'),
(12, 'بورسعيد', 'Port Said', 50.00, 1, '2026-08-25 15:57:39'),
(13, 'الإسماعيلية', 'Ismailia', 50.00, 1, '2026-08-25 15:57:39'),
(14, 'السويس', 'Suez', 50.00, 1, '2026-08-25 15:57:39'),
(15, 'شمال سيناء', 'North Sinai', 50.00, 1, '2026-08-25 15:57:39'),
(16, 'جنوب سيناء', 'South Sinai', 50.00, 1, '2026-08-25 15:57:39'),
(17, 'الفيوم', 'Fayoum', 50.00, 1, '2026-08-25 15:57:39'),
(18, 'بني سويف', 'Beni Suef', 50.00, 1, '2026-08-25 15:57:39'),
(19, 'المنيا', 'Minya', 50.00, 1, '2026-08-25 15:57:39'),
(20, 'أسيوط', 'Asyut', 50.00, 1, '2026-08-25 15:57:39'),
(21, 'سوهاج', 'Sohag', 50.00, 1, '2026-08-25 15:57:39'),
(22, 'قنا', 'Qena', 50.00, 1, '2026-08-25 15:57:39'),
(23, 'الأقصر', 'Luxor', 50.00, 1, '2026-08-25 15:57:39'),
(24, 'أسوان', 'Aswan', 50.00, 1, '2026-08-25 15:57:39'),
(25, 'البحر الأحمر', 'Red Sea', 50.00, 1, '2026-08-25 15:57:39'),
(26, 'الوادي الجديد', 'New Valley', 50.00, 1, '2026-08-25 15:57:39'),
(27, 'مطروح', 'Matrouh', 50.00, 1, '2026-08-25 15:57:39');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `logo_url` text DEFAULT NULL,
  `favicon_url` text DEFAULT NULL,
  `site_name_ar` varchar(120) NOT NULL DEFAULT 'مدينة الأدباء',
  `site_name_en` varchar(120) NOT NULL DEFAULT 'Madinat Al-Odabaa',
  `tagline_ar` varchar(200) NOT NULL DEFAULT 'مكتبتك العربية المفضلة',
  `tagline_en` varchar(200) NOT NULL DEFAULT 'Your favorite Arabic bookstore',
  `meta_description_ar` text DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `hero_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`hero_images`)),
  `hero_title_ar` varchar(200) DEFAULT NULL,
  `hero_title_en` varchar(200) DEFAULT NULL,
  `hero_subtitle_ar` varchar(300) DEFAULT NULL,
  `hero_subtitle_en` varchar(300) DEFAULT NULL,
  `social_facebook` varchar(500) DEFAULT NULL,
  `social_instagram` varchar(500) DEFAULT NULL,
  `social_twitter` varchar(500) DEFAULT NULL,
  `social_tiktok` varchar(500) DEFAULT NULL,
  `social_youtube` varchar(500) DEFAULT NULL,
  `social_whatsapp` varchar(50) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_address_ar` varchar(500) DEFAULT NULL,
  `contact_address_en` varchar(500) DEFAULT NULL,
  `footer_about_ar` text DEFAULT NULL,
  `footer_about_en` text DEFAULT NULL,
  `privacy_policy_ar` text DEFAULT NULL,
  `privacy_policy_en` text DEFAULT NULL,
  `terms_ar` text DEFAULT NULL,
  `terms_en` text DEFAULT NULL,
  `refund_policy_ar` text DEFAULT NULL,
  `refund_policy_en` text DEFAULT NULL,
  `shipping_policy_ar` text DEFAULT NULL,
  `shipping_policy_en` text DEFAULT NULL,
  `about_ar` text DEFAULT NULL,
  `about_en` text DEFAULT NULL,
  `custom_strings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' CHECK (json_valid(`custom_strings`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `logo_url`, `favicon_url`, `site_name_ar`, `site_name_en`, `tagline_ar`, `tagline_en`, `meta_description_ar`, `meta_description_en`, `hero_images`, `hero_title_ar`, `hero_title_en`, `hero_subtitle_ar`, `hero_subtitle_en`, `social_facebook`, `social_instagram`, `social_twitter`, `social_tiktok`, `social_youtube`, `social_whatsapp`, `contact_phone`, `contact_email`, `contact_address_ar`, `contact_address_en`, `footer_about_ar`, `footer_about_en`, `privacy_policy_ar`, `privacy_policy_en`, `terms_ar`, `terms_en`, `refund_policy_ar`, `refund_policy_en`, `shipping_policy_ar`, `shipping_policy_en`, `about_ar`, `about_en`, `custom_strings`, `created_at`, `updated_at`) VALUES
(1, 'https://supabase-cityofwriters.creativessquare.store/storage/v1/object/public/site-assets/branding/1785428252301_485159259_1045618527593483_630436883469979457_n.jpg', 'https://supabase-cityofwriters.creativessquare.store/storage/v1/object/public/site-assets/branding/1785428308080_485159259_1045618527593483_630436883469979457_n_copy.png', 'مدينة الأدباء', 'Madinat Al-Odabaa', 'مكتبتك العربية المفضلة', 'Your favorite Arabic bookstore', NULL, NULL, '[]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', '2026-07-30 13:34:53', '2026-07-30 16:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_sign_in_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `email_verified`, `is_active`, `last_sign_in_at`, `created_at`, `updated_at`) VALUES
('05fbb2c4-29b8-483a-821f-b662d486556a', 'cityofwriters24@gmail.com', '', 1, 1, '2026-08-13 12:07:23', '2026-07-30 14:24:26', '2026-08-25 22:00:44'),
('138fb1ce-773f-437b-97dc-c791546aabee', 'm44278622@gmail.com', '', 1, 1, '2026-08-28 09:21:49', '2026-08-28 09:21:48', '2026-09-04 22:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role`, `created_at`) VALUES
('4feb3b7f-7e73-41c8-bc1e-e24c96f36bf0', '05fbb2c4-29b8-483a-821f-b662d486556a', 'customer', '2026-07-30 14:24:26'),
('906577f4-6380-4bae-ac09-14021795b1fa', '05fbb2c4-29b8-483a-821f-b662d486556a', 'admin', '2026-07-30 14:24:26'),
('de0cc4b4-d4e6-4a49-8bee-3df95144690f', '138fb1ce-773f-437b-97dc-c791546aabee', 'customer', '2026-08-28 09:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_addresses_user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_categories_slug` (`slug`),
  ADD KEY `idx_categories_active` (`is_active`),
  ADD KEY `idx_categories_display_order` (`display_order`),
  ADD KEY `idx_categories_parent_id` (`parent_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `idx_coupons_code` (`code`);

--
-- Indexes for table `marketing_costs`
--
ALTER TABLE `marketing_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_marketing_costs_date` (`cost_date`),
  ADD KEY `idx_marketing_costs_created_by` (`created_by`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `idx_orders_user_id` (`user_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created_at` (`created_at` DESC),
  ADD KEY `idx_orders_payment_status` (`payment_status`),
  ADD KEY `idx_orders_order_number` (`order_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order_id` (`order_id`),
  ADD KEY `idx_order_items_product_id` (`product_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_products_slug` (`slug`),
  ADD KEY `idx_products_category_id` (`category_id`),
  ADD KEY `idx_products_active` (`is_active`),
  ADD KEY `idx_products_display_order` (`display_order`,`created_at` DESC),
  ADD KEY `idx_products_featured` (`is_featured`),
  ADD KEY `idx_products_bestseller` (`is_bestseller`),
  ADD KEY `idx_products_new_arrival` (`is_new_arrival`),
  ADD KEY `idx_products_created_at` (`created_at` DESC);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_profiles_phone` (`phone`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_review_user_product` (`product_id`,`user_id`),
  ADD KEY `idx_reviews_product_id` (`product_id`),
  ADD KEY `idx_reviews_user_id` (`user_id`);

--
-- Indexes for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_created_at` (`created_at`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_role` (`user_id`,`role`),
  ADD KEY `idx_user_roles_user_id` (`user_id`),
  ADD KEY `idx_user_roles_role` (`role`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wishlist_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_wishlist_user_id` (`user_id`),
  ADD KEY `idx_wishlist_product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `marketing_costs`
--
ALTER TABLE `marketing_costs`
  ADD CONSTRAINT `marketing_costs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
