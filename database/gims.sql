-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2026 at 05:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gims`
--

-- --------------------------------------------------------

--
-- Table structure for table `accessions`
--

CREATE TABLE `accessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accession_number` varchar(255) NOT NULL,
  `accession_name` varchar(255) DEFAULT NULL,
  `scientific_name` varchar(255) DEFAULT NULL,
  `crop_id` bigint(20) UNSIGNED DEFAULT NULL,
  `variety_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accession_type` enum('Seed','Tissue','Clone','Plant') DEFAULT NULL,
  `collection_number` varchar(100) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `quantity_unit` enum('Nos','Packet','Bag') DEFAULT NULL,
  `capacity` decimal(10,2) DEFAULT NULL,
  `capacity_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `storage_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `storage_time_id` int(11) NOT NULL,
  `storage_condition_id` int(11) NOT NULL,
  `storage_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `germination_percentage` decimal(5,2) DEFAULT NULL,
  `moisture_content` decimal(5,2) DEFAULT NULL,
  `purity_percentage` decimal(5,2) DEFAULT NULL,
  `viability_test_date` date DEFAULT NULL,
  `seed_health_status` enum('Healthy','Infected','Damaged','Under Treatment') DEFAULT NULL,
  `barcode_type` enum('auto','manual','existing','scan','none') NOT NULL DEFAULT 'auto',
  `barcode` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `passport_file_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `entered_by` bigint(20) UNSIGNED DEFAULT NULL,
  `collection_date` date DEFAULT NULL,
  `collector_name` varchar(255) DEFAULT NULL,
  `donor_name` varchar(255) DEFAULT NULL,
  `collection_site` varchar(255) DEFAULT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `state_id` bigint(20) UNSIGNED DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `village` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `altitude` int(11) DEFAULT NULL COMMENT 'in meters',
  `biological_status` enum('Wild','Landrace','Breeding Material','Improved Variety') DEFAULT NULL,
  `sample_type` enum('Seed','Plant','Tissue') DEFAULT NULL,
  `reproductive_type` enum('Self Pollinated','Cross Pollinated') DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `recheck_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accessions`
--

INSERT INTO `accessions` (`id`, `accession_number`, `accession_name`, `scientific_name`, `crop_id`, `variety_id`, `accession_type`, `collection_number`, `quantity`, `quantity_unit`, `capacity`, `capacity_unit_id`, `warehouse_id`, `storage_location_id`, `storage_time_id`, `storage_condition_id`, `storage_type_id`, `germination_percentage`, `moisture_content`, `purity_percentage`, `viability_test_date`, `seed_health_status`, `barcode_type`, `barcode`, `image_path`, `passport_file_path`, `notes`, `entry_date`, `entered_by`, `collection_date`, `collector_name`, `donor_name`, `collection_site`, `country_id`, `state_id`, `district`, `village`, `latitude`, `longitude`, `altitude`, `biological_status`, `sample_type`, `reproductive_type`, `status`, `description`, `created_by`, `created_at`, `updated_at`, `recheck_date`, `expiry_date`) VALUES
(1, 'ACC-2026-0001', 'HD-2967 Punjab Collection', NULL, 1, 1, 'Seed', NULL, 500.00, 'Bag', 500.00, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', 'ACC-2026-0001', NULL, NULL, 'High-yielding wheat variety resistant to rust disease. Collected from Punjab region.', '2026-01-15', NULL, '2026-01-15', 'Dr. Sarah Johnson', 'Research Station Alpha', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL),
(2, 'ACC-2026-0002', 'IR-64 Drought Tolerant', NULL, 2, 2, 'Seed', NULL, 250.00, 'Bag', 250.00, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', 'ACC-2026-0002', NULL, NULL, 'Popular rice variety known for drought tolerance. Requires cold storage.', '2026-02-10', NULL, '2026-02-10', 'Mr. Rajesh Kumar', 'Farmer Field Collection', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL),
(3, 'ACC-2026-0003', 'DKC-9090 QPM', NULL, 3, 3, 'Seed', NULL, 300.00, 'Bag', 300.00, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', 'ACC-2026-0003', NULL, NULL, 'Quality protein maize variety with enhanced nutritional value.', '2026-01-28', NULL, '2026-01-28', 'Dr. Michael Chen', 'Breeding Program', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL),
(4, 'ACC-2026-0004', 'BH-906 Malting Barley', NULL, 4, 4, 'Seed', NULL, 150.00, 'Bag', 150.00, NULL, 3, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', 'ACC-2026-0004', NULL, NULL, 'Two-row barley variety suitable for malting. Stored for long-term preservation.', '2026-02-05', NULL, '2026-02-05', 'Ms. Priya Sharma', 'Regional Collection Center', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL),
(5, 'ACC-2026-0005', 'CSH-16 Sweet Sorghum', NULL, 5, 5, 'Seed', NULL, 200.00, 'Bag', 200.00, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', 'ACC-2026-0005', NULL, NULL, 'Sweet sorghum variety under quarantine for pest screening.', '2026-02-20', NULL, '2026-02-20', 'Dr. Ahmed Hassan', 'Imported Germplasm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL),
(6, 'ACC-2026-0006', 'PBW-725 Research Stock', NULL, 1, 6, 'Seed', NULL, 0.00, 'Bag', 0.00, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'auto', 'ACC-2026-0006', NULL, NULL, 'Previously active accession that has been fully utilized in research.', '2026-01-20', NULL, '2026-01-20', 'Dr. Lisa Wong', 'Research Station Beta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL),
(7, 'ACC-2026-01', 'HD_2561', 'maixey', 3, 5, 'Seed', 'col-20236-05', 10.00, 'Nos', 100.00, 2, NULL, NULL, 0, 0, 2, NULL, NULL, NULL, NULL, NULL, 'auto', NULL, NULL, NULL, NULL, '2026-03-09', 1, '2026-03-02', 'rahul', 'rahul=raipur', 'tbt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Breeding Material', 'Seed', 'Self Pollinated', 1, NULL, 1, '2026-03-09 02:23:42', '2026-03-09 02:23:42', NULL, NULL),
(8, 'ACC-2026-09', 'HD09', 'sw', 1, 1, 'Seed', 'fg', 10.00, 'Nos', 20.00, 9, NULL, NULL, 0, 0, 2, 85.00, 12.00, 66.00, '2026-03-05', 'Healthy', 'manual', 'ACC-2025-22', NULL, NULL, 'test', '2026-03-09', 1, '2026-03-06', 'dfd', 'fdg', 'dfg', NULL, NULL, 'gfd', 'fdg', 28.72540000, 25.53650000, 254, 'Breeding Material', 'Seed', 'Self Pollinated', 1, NULL, 1, '2026-03-09 04:09:55', '2026-03-09 04:09:55', NULL, NULL),
(9, '2026-0229', '2564', NULL, 1, 1, 'Seed', '2026-22', 10.00, 'Bag', 120.00, 2, NULL, 1, 0, 0, 2, 85.00, 15.00, 15.00, '2026-03-30', 'Healthy', 'auto', NULL, NULL, NULL, NULL, '2026-03-13', 1, '2026-03-04', 'ww', 'ww', 'pput', NULL, NULL, 'ww', 'ww', 21.25140000, 81.62960000, 255, 'Breeding Material', 'Seed', 'Cross Pollinated', 1, NULL, 1, '2026-03-13 06:06:24', '2026-03-13 06:06:24', NULL, NULL),
(10, '2026-test01', 'test', NULL, 4, 4, 'Seed', '20256-2', 10.00, 'Nos', 20.00, 2, 1, 1, 1, 0, 1, 85.00, 12.00, 52.00, NULL, 'Healthy', 'auto', NULL, NULL, NULL, NULL, '2026-03-18', 1, NULL, 'ree', 'df', 'sdf', NULL, NULL, 'sdf', 'sdf', 21.25140000, 81.62960000, 254, 'Improved Variety', 'Seed', 'Self Pollinated', 1, NULL, 1, '2026-03-14 04:53:16', '2026-03-18 03:28:01', NULL, NULL),
(12, '18-03-20226', '2563', NULL, 3, 3, 'Seed', '2026', 23.00, 'Packet', 32.00, 2, 3, 1, 3, 0, 3, 34.00, 4.00, 4.00, NULL, 'Healthy', 'auto', NULL, NULL, NULL, NULL, '2026-03-18', 1, NULL, 'sdf', 'd', 'sdf', NULL, NULL, 'sdf', 'sdf', 21.25140000, 25.53650000, 254, 'Landrace', 'Seed', 'Self Pollinated', 1, NULL, 1, '2026-03-18 02:25:45', '2026-03-18 05:36:49', '2026-03-25', '2026-04-18'),
(13, 'ret', 'HD09', NULL, 4, 4, 'Seed', 'ret', 122.00, 'Nos', 12.00, 9, 3, 1, 2, 0, 5, 34.00, 34.00, 34.00, '2026-03-19', 'Damaged', 'auto', NULL, NULL, NULL, NULL, '2026-03-18', 1, '2026-03-11', 'rrer', 'ert', 'ret', NULL, NULL, 'yyyyyyy', 'sdf', 21.25140000, 81.62960000, 25, 'Landrace', 'Plant', 'Cross Pollinated', 1, NULL, 1, '2026-03-18 05:04:21', '2026-03-18 05:04:21', '2026-03-27', '2026-04-18'),
(14, '2026-19', 'DSF', NULL, 4, 4, 'Seed', 'SDF', 12.00, 'Nos', 12.00, 9, 3, 1, 3, 1, 3, 21.00, 21.00, 12.00, '2026-03-26', 'Healthy', 'auto', NULL, NULL, NULL, NULL, '2026-03-19', 1, '2026-03-18', 'SDF', 'SDF', 'SDF', NULL, NULL, 'SDF', 'SD', 21.25140000, 81.62960000, 23, 'Wild', 'Seed', 'Self Pollinated', 1, NULL, 1, '2026-03-19 07:15:37', '2026-03-19 07:15:37', '2026-03-26', '2026-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `record_label` varchar(255) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `record_label`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'auth', 1, 'super_admin', NULL, NULL, '192.168.34.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 04:20:29'),
(2, 1, 'login', 'auth', 1, 'super_admin', NULL, NULL, '192.168.34.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 10:38:50'),
(3, 1, 'login', 'auth', 1, 'super_admin', NULL, NULL, '192.168.34.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 10:40:33'),
(4, 1, 'login', 'auth', 1, 'super_admin', NULL, NULL, '192.168.34.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 12:50:39'),
(5, 1, 'login', 'auth', 1, 'super_admin', NULL, NULL, '192.168.34.92', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 13:02:42'),
(6, 1, 'login', 'auth', 1, 'super_admin', NULL, NULL, '192.168.34.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-18 04:14:23'),
(7, 1, 'login', 'auth', 1, 'super_admin', NULL, NULL, '192.168.34.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 04:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Cereals', 'CER', 'Cereal crops including wheat, rice, maize', 1, '2026-03-09 01:36:17', '2026-03-09 01:36:17'),
(2, 'Pulses', 'PUL', 'Pulse crops including lentils, chickpeas', 1, '2026-03-09 01:36:17', '2026-03-09 01:36:17'),
(3, 'Oilseeds', 'OIL', 'Oilseed crops including sunflower, mustard', 1, '2026-03-09 01:36:17', '2026-03-09 01:36:17'),
(4, 'Vegetables', 'VEG', 'Vegetable crops', 1, '2026-03-09 01:36:17', '2026-03-09 01:36:17'),
(5, 'Fruits', 'FRT', 'Fruit crops', 1, '2026-03-09 01:36:17', '2026-03-09 01:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `state_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `iso2` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crop_name` varchar(255) NOT NULL,
  `crop_code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `scientific_name` varchar(255) DEFAULT NULL,
  `common_name` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crop_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crop_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `season_id` bigint(20) UNSIGNED DEFAULT NULL,
  `family_name` varchar(255) DEFAULT NULL,
  `genus` varchar(255) DEFAULT NULL,
  `species` varchar(255) DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `sowing_time` varchar(255) DEFAULT NULL,
  `harvest_time` varchar(255) DEFAULT NULL,
  `climate_requirement` varchar(255) DEFAULT NULL,
  `soil_type_id` int(11) NOT NULL,
  `isolation_distance` int(11) DEFAULT NULL,
  `expected_yield` decimal(8,2) DEFAULT NULL,
  `crop_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `crop_name`, `crop_code`, `description`, `created_at`, `updated_at`, `scientific_name`, `common_name`, `category_id`, `crop_category_id`, `crop_type_id`, `season_id`, `family_name`, `genus`, `species`, `duration_days`, `sowing_time`, `harvest_time`, `climate_requirement`, `soil_type_id`, `isolation_distance`, `expected_yield`, `crop_status`) VALUES
(1, 'Wheat', 'WHT', 'ew', '2026-03-09 01:35:23', '2026-03-13 00:10:23', 'ewee', 'we', 5, 1, 1, 2, 'maize', 'maize', '3', 3, 'June-july', 'jan', 'warm', 0, 33, 33.00, 0),
(2, 'Rice', 'RIC', NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0),
(3, 'Maize', 'MAZ', NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0),
(4, 'Barley', 'BAR', 'test', '2026-03-09 01:35:23', '2026-03-19 04:18:07', 'sw', 'sdf', 1, 1, 1, 2, 'd', 'dgf', 'df', 12, 'June-july', 'jan', 'warm', 2, 23, 43.00, 1),
(5, 'Sorghum', 'SOR', 'sdf', '2026-03-09 01:35:23', '2026-03-14 00:39:37', 'dvd', 'sdf', 1, 3, 1, 1, 'sdsd', 'sd', 'sd', 23, 'June-july', 'jan', 'warm', 2, 3, 3.00, 1),
(6, 'Maize1', 'MJ01', 'test onlyd', '2026-03-12 23:25:44', '2026-03-13 00:08:17', 'maxxy', 'd', 2, 4, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0),
(8, 'df', 'df', 'df', '2026-03-13 01:23:19', '2026-03-13 01:23:19', 'df', 'df', 1, 2, 2, 2, 'df', 'f', 'f', 11, 'June-july', '2', 'warm', 0, NULL, NULL, 0),
(9, 'rt', 'r', 'ret', '2026-03-13 01:32:58', '2026-03-13 01:32:58', 're', 're', 2, 3, 2, 1, 're', 'r', 'r', 223, '21', 'd', 'd', 0, NULL, NULL, 0),
(10, 'sd', 'dsf', 'dsf', '2026-03-13 01:37:48', '2026-03-14 00:12:03', 'sdf', 'sdf', 3, 2, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1),
(11, 'dsds', 'dsd', 's', '2026-03-14 00:11:35', '2026-03-14 00:11:35', 'ds', 'ds', 1, 1, 2, 2, 'ds', 'd', 'd', 12, 'June-july', 'jan', 'warm', 0, 3, 3.00, 1),
(12, 'sad-01', 'asd', 'sadas', '2026-03-14 00:23:27', '2026-03-14 00:23:27', 'as', 'sd', 2, 2, 2, 1, 'as', 'as', 'as', 123, 'June-july', 'jan', 'warm', 3, 4, 4.00, 1),
(13, 'test01', 'test01', 'test', '2026-03-19 03:13:34', '2026-03-19 03:13:34', 'test', 'tets', 2, 1, 1, 1, 'ddd', 'dd', 'dd', 120, '125', 'jan', 'warm', 2, 3, 3.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `crop_categories`
--

CREATE TABLE `crop_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crop_categories`
--

INSERT INTO `crop_categories` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Food Crops', 'FOOD', 'Crops grown for human consumption', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(2, 'Cash Crops', 'CASH', 'Crops grown for commercial purposes', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(3, 'Fodder Crops', 'FODD', 'Crops grown for animal feed', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(4, 'Fiber Crops', 'FIBR', 'Crops grown for fiber production', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `crop_types`
--

CREATE TABLE `crop_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crop_types`
--

INSERT INTO `crop_types` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Annual', 'ANN', 'Crops that complete lifecycle in one year', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(2, 'Perennial', 'PER', 'Crops that live for multiple years', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(3, 'Biennial', 'BIE', 'Crops that complete lifecycle in two years', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lots`
--

CREATE TABLE `lots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `lot_type_id` int(11) NOT NULL,
  `batch_number` varchar(255) DEFAULT NULL,
  `season` varchar(255) DEFAULT NULL,
  `production_year` year(4) DEFAULT NULL,
  `harvest_date` date DEFAULT NULL,
  `processing_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `germination_percent` decimal(5,2) DEFAULT NULL,
  `moisture_content` decimal(5,2) DEFAULT NULL,
  `purity_percent` decimal(5,2) DEFAULT NULL,
  `seed_class` varchar(255) DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `storage_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accession_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crop_id` bigint(20) UNSIGNED DEFAULT NULL,
  `variety_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lots`
--

INSERT INTO `lots` (`id`, `name`, `code`, `lot_type_id`, `batch_number`, `season`, `production_year`, `harvest_date`, `processing_date`, `expiry_date`, `quantity`, `unit_id`, `germination_percent`, `moisture_content`, `purity_percent`, `seed_class`, `warehouse_id`, `storage_location_id`, `accession_id`, `crop_id`, `variety_id`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Lot-GIMS-01', 'GIMS_G_01', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, '2026-03-10 06:09:43', '2026-03-17 06:47:19'),
(2, 'Lot-GIMS-02', 'LOT02', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, 'test', 1, '2026-03-17 06:49:51', '2026-03-17 06:49:51'),
(3, 'LOT_03', 'LOT03', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, NULL, 'test', 1, '2026-03-17 23:47:01', '2026-03-17 23:47:01');

-- --------------------------------------------------------

--
-- Table structure for table `lot_types`
--

CREATE TABLE `lot_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lot_types`
--

INSERT INTO `lot_types` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Procurement Lot', 'PLOT', 'Seeds received from farmer/vendor\r\nInitial entry into system', 1, '2026-03-17 06:53:44', '2026-03-17 06:53:44'),
(2, 'Production Lot', 'LOT_2', 'Seeds produced internally\r\n\r\nFrom research farm or multiplication', 1, '2026-03-17 06:54:22', '2026-03-17 06:54:22'),
(3, 'Processing Lot', 'lo03', 'After cleaning, grading, treatment\r\n\r\nMay change quantity/quality', 1, '2026-03-17 06:54:46', '2026-03-17 06:54:46'),
(4, 'Storage Lot', 'lo04', 'Final stored lot in warehouse\r\n\r\nReady for distribution', 1, '2026-03-17 06:55:19', '2026-03-17 06:55:19'),
(5, 'Distribution Lot', '02', 'Lot prepared for issue/sale\r\n\r\nUsed in dispatch', 1, '2026-03-17 06:55:41', '2026-03-17 06:55:41'),
(6, 'Testing Lot', 'T-Lot', 'Sample sent for lab testing\r\n\r\nGermination, purity, moisture', 1, '2026-03-17 06:56:05', '2026-03-17 06:56:05'),
(7, 'Rejected Lot', 'Reject', 'Failed quality standards\r\n\r\nNot usable', 1, '2026-03-17 06:56:28', '2026-03-17 06:56:28'),
(8, 'Breeder / Foundation / Certified Lot (Seed Class Based)', 'BR', 'Breeder Seed Lot\r\n\r\nFoundation Seed Lot\r\n\r\nCertified Seed Lot', 1, '2026-03-17 06:57:06', '2026-03-17 06:57:06');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_06_000000_create_crops_table', 1),
(5, '2026_03_06_000001_create_varieties_table', 1),
(6, '2026_03_06_000002_create_categories_table', 1),
(7, '2026_03_06_000003_create_units_table', 1),
(8, '2026_03_06_000004_create_warehouses_table', 1),
(9, '2026_03_06_000005_create_storage_locations_table', 1),
(10, '2026_03_06_085319_create_roles_table', 1),
(11, '2026_03_06_085344_create_permissions_table', 1),
(12, '2026_03_06_085427_create_role_user_table', 1),
(13, '2026_03_06_085653_create_permission_role_table', 1),
(14, '2026_03_06_103818_create_storages_table', 1),
(15, '2026_03_06_104648_create_accessions_table', 1),
(16, '2026_03_06_104700_create_lots_table', 1),
(17, '2026_03_06_112450_create_storage_types_table', 1),
(18, '2026_03_07_000000_create_requests_table', 1),
(19, '2026_03_07_000001_create_crop_categories_table', 1),
(20, '2026_03_07_000002_create_crop_types_table', 1),
(21, '2026_03_07_000003_create_variety_types_table', 1),
(22, '2026_03_07_000004_create_seasons_table', 1),
(23, '2026_03_07_000005_create_seed_classes_table', 1),
(24, '2026_03_07_000006_create_countries_table', 1),
(25, '2026_03_07_000007_create_states_table', 1),
(26, '2026_03_07_000008_create_cities_table', 1),
(27, '2026_03_09_065003_update_accessions_table_add_comprehensive_fields', 1),
(29, '2026_03_10_091536_add_image_to_storages_table', 2),
(30, '2026_03_11_064341_add_user_id_to_requests_table', 3),
(31, '2026_03_11_094143_create_notifications_table', 4),
(32, '2026_03_12_120426_add_details_to_crops_table', 5),
(33, '2026_03_13_053528_add_category_id_to_crops_table', 6),
(34, '2026_03_13_055527_create_soil_types_table', 7),
(35, '2026_03_13_065112_add_soil_type_id_to_crops_table', 8),
(36, '2026_03_13_072414_add_fields_to_varieties_table', 9),
(37, '2026_03_13_080040_add_master_fields_to_varieties_table', 10),
(38, '2026_03_16_000000_create_activity_logs_table', 11),
(39, '2026_03_17_000001_create_storage_times_table', 12),
(40, '2026_03_17_000002_create_storage_conditions_table', 13),
(41, '2026_03_17_000003_create_lot_types_table', 14),
(42, '2026_03_18_102326_add_dates_to_accessions_table', 15),
(43, '2026_03_18_102841_add_dates_to_accessions_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'New Seed Request', 'User submitted a new seed request.', 0, '2026-03-11 04:21:51', '2026-03-11 04:21:51'),
(2, 2, 'New Seed Request', 'User submitted a new seed request.', 0, '2026-03-11 04:21:51', '2026-03-11 04:21:51'),
(3, 4, 'Request Approved', 'Your seed request REQ-00007 has been approved. Dispatch will happen soon.', 0, '2026-03-11 04:35:14', '2026-03-11 04:35:14'),
(4, 1, 'New Seed Request', 'super_admin submitted a new seed request.', 0, '2026-03-11 04:55:45', '2026-03-11 04:55:45'),
(5, 2, 'New Seed Request', 'super_admin submitted a new seed request.', 0, '2026-03-11 04:55:45', '2026-03-11 04:55:45'),
(6, 4, 'Request Approved', 'Your seed request REQ-00005 has been approved. Dispatch will happen soon.', 0, '2026-03-12 04:58:40', '2026-03-12 04:58:40'),
(7, 1, 'New Seed Request', 'super_admin submitted a new seed request.', 0, '2026-03-16 05:11:57', '2026-03-16 05:11:57'),
(8, 2, 'New Seed Request', 'super_admin submitted a new seed request.', 0, '2026-03-16 05:11:57', '2026-03-16 05:11:57');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'View Users', 'view-users', 'Can view user list', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(2, 'Create Users', 'create-users', 'Can create new users', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(3, 'Edit Users', 'edit-users', 'Can edit user information', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(4, 'Delete Users', 'delete-users', 'Can delete users', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(5, 'View Roles', 'view-roles', 'Can view roles list', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(6, 'Create Roles', 'create-roles', 'Can create new roles', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(7, 'Edit Roles', 'edit-roles', 'Can edit role information', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(8, 'Delete Roles', 'delete-roles', 'Can delete roles', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(9, 'View Accessions', 'view-accessions', 'Can view accession list', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(10, 'Create Accessions', 'create-accessions', 'Can create new accessions', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(11, 'Edit Accessions', 'edit-accessions', 'Can edit accession information', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(12, 'Delete Accessions', 'delete-accessions', 'Can delete accessions', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(13, 'View Masters', 'view-masters', 'Can view master data', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(14, 'Create Masters', 'create-masters', 'Can create master data', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(15, 'Edit Masters', 'edit-masters', 'Can edit master data', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(16, 'Delete Masters', 'delete-masters', 'Can delete master data', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(17, 'View Reports', 'view-reports', 'Can view reports', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(18, 'Export Reports', 'export-reports', 'Can export reports', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(19, 'View Settings', 'view-settings', 'Can view system settings', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(20, 'Edit Settings', 'edit-settings', 'Can edit system settings', '2026-03-09 01:33:56', '2026-03-09 01:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `request_through` varchar(11) DEFAULT NULL,
  `crop_id` bigint(20) UNSIGNED NOT NULL,
  `variety_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `requester_name` varchar(255) NOT NULL,
  `requester_email` varchar(255) DEFAULT NULL,
  `requester_phone` varchar(255) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `request_date` date NOT NULL,
  `required_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `remarks` varchar(111) DEFAULT NULL,
  `purpose_details` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `request_number`, `request_through`, `crop_id`, `variety_id`, `quantity`, `unit_id`, `requester_name`, `requester_email`, `requester_phone`, `purpose`, `status`, `request_date`, `required_date`, `notes`, `approved_by`, `approved_at`, `remarks`, `purpose_details`, `created_at`, `updated_at`) VALUES
(1, 0, 'REQ-00001', '1', 4, 4, 10.00, 2, 'atul', 'atul@gmail.com', '9856784526', 'for research', 'approved', '2026-03-09', '2026-03-12', 'test and research', 1, '2026-03-11 09:10:26', '', '', '2026-03-09 04:44:52', '2026-03-09 06:06:57'),
(3, 0, 'REQ-00003', '2', 1, 3, 4.00, 1, 'dev', 'narayankumar.vspl@gmail.com', '9856784526', 'e', 'pending', '2026-03-09', '2026-03-19', 'ee', NULL, '2026-03-11 09:10:33', NULL, '', '2026-03-09 06:35:11', '2026-03-09 06:35:11'),
(4, 7, 'REQ-00004', '2', 1, 1, 5.00, 2, 'Narayan', 'narayan@gims.com', '9856784526', 'test', 'pending', '2026-03-10', '2026-03-16', 'test', NULL, '2026-03-11 09:10:37', NULL, '', '2026-03-11 02:08:23', '2026-03-11 02:08:23'),
(5, 4, 'REQ-00005', 'mail', 1, 1, 3.00, 1, 'User', 'user@gims.com', '9856784526', 'For Research', 'approved', '2026-03-11', '2026-03-12', '3', 1, '2026-03-12 04:58:40', 'ok', '', '2026-03-11 03:25:09', '2026-03-12 04:58:40'),
(6, 4, 'REQ-00006', 'portal', 1, 1, 9.00, 2, 'User', 'user@gims.com', '9856784526', 'For Breeding Program', 'pending', '2026-03-11', '2026-03-13', '34', NULL, '2026-03-11 09:00:38', NULL, '', '2026-03-11 03:30:38', '2026-03-11 03:30:38'),
(7, 4, 'REQ-00007', 'portal', 3, 5, 3.00, 2, 'User', 'user@gims.com', '9856784526', 'For Breeding Program', 'approved', '2026-03-11', '2026-03-19', 'r', 1, '2026-03-11 04:35:14', 'test', '', '2026-03-11 03:32:10', '2026-03-11 04:35:14'),
(8, 4, 'REQ-00008', '3', 1, 1, 2.00, 1, 'User', 'user@gims.com', '9856784526', 'For Research', 'approved', '2026-03-11', '2026-03-12', 'r', 1, '2026-03-11 04:23:25', '', '', '2026-03-11 04:21:51', '2026-03-11 04:23:25'),
(9, 6, 'REQ-00009', '1', 4, 2, 21.00, 3, 'pooja', 'pooja@gims.com', '9856784526', 'For Breeding Program', 'pending', '2026-03-11', '2026-03-19', '2', NULL, '2026-03-11 10:25:45', NULL, '', '2026-03-11 04:55:45', '2026-03-11 04:55:45'),
(10, 4, 'REQ-00010', '1', 1, 1, 10.00, 2, 'User', 'user@gims.com', '9856784526', 'For Research', 'pending', '2026-03-16', '2026-03-17', 'teast', NULL, '2026-03-16 10:41:57', NULL, '', '2026-03-16 05:11:57', '2026-03-16 05:11:57');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Full system access with all permissions', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(2, 'Admin', 'admin', 'Administrative access to manage users and system settings', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(3, 'Manager', 'manager', 'Manage inventory, accessions, and reports', '2026-03-09 01:33:56', '2026-03-09 01:33:56'),
(4, 'User', 'user', 'Basic user with limited access to view data', '2026-03-09 01:33:56', '2026-03-09 01:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL),
(3, 3, 3, NULL, NULL),
(4, 4, 4, NULL, NULL),
(5, 6, 4, NULL, NULL),
(6, 7, 2, NULL, NULL),
(7, 7, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seasons`
--

INSERT INTO `seasons` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Kharif', 'KHR', 'Monsoon season (June-October)', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(2, 'Rabi', 'RAB', 'Winter season (November-March)', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(3, 'Zaid', 'ZAI', 'Summer season (March-June)', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(4, 'Year Round', 'YR', 'Can be grown throughout the year', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `seed_classes`
--

CREATE TABLE `seed_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seed_classes`
--

INSERT INTO `seed_classes` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Breeder Seed', 'BS', 'Highest quality seed produced by breeder', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(2, 'Foundation Seed', 'FS', 'Progeny of breeder seed', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(3, 'Certified Seed', 'CS', 'Progeny of foundation seed', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(4, 'Truthfully Labeled', 'TL', 'Seed meeting minimum standards', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--
-- Error reading structure for table gims.sessions: #1932 - Table &#039;gims.sessions&#039; doesn&#039;t exist in engine
-- Error reading data for table gims.sessions: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `gims`.`sessions`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `soil_types`
--

CREATE TABLE `soil_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `soil_types`
--

INSERT INTO `soil_types` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Sandy Soil', 'SANDY', 'Large particles, gritty texture,\r\nDrains quickly,\r\nLow nutrient and water retention,\r\nWarms up quickly in spring', 'active', '2026-03-13 00:45:31', '2026-03-13 00:50:42'),
(2, 'Clay Soil', 'CLAY', 'Very small particles,\r\nHolds water well (poor drainage),\r\nNutrient-rich but can become compacted,\r\nSticky when wet, hard when dry', 'active', '2026-03-13 00:51:31', '2026-03-13 00:51:31'),
(3, 'Silty Soil', 'SILTY', 'Fine, smooth particles,\r\nRetains moisture better than sandy soil,\r\nFertile and good for plant growth,\r\nCan compact easily', 'active', '2026-03-13 00:52:13', '2026-03-13 00:52:13'),
(4, 'Loamy Soil', 'LOAMY', 'Mix of sand, silt, and clay,\r\nIdeal balance of drainage and nutrient retention,\r\nBest for most crops and gardening', 'active', '2026-03-13 00:52:55', '2026-03-13 00:52:55'),
(5, 'Peaty Soil', 'PEATY', 'High organic matter,\r\nRetains moisture,\r\nOften acidic,\r\nDark in color', 'active', '2026-03-13 00:53:39', '2026-03-13 00:53:39'),
(6, 'Chalky Soil', 'CHALKY', 'Contains calcium carbonate, \r\nAlkaline (high pH), \r\nCan be stony and free-draining', 'active', '2026-03-13 00:54:10', '2026-03-13 00:54:10');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storages`
--

CREATE TABLE `storages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `storage_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `storage_time_id` int(11) NOT NULL,
  `storage_condition_id` int(11) NOT NULL,
  `storage_type_id` int(11) NOT NULL,
  `storage_location_id` int(11) DEFAULT NULL,
  `capacity` decimal(10,2) DEFAULT NULL,
  `unit_id` int(255) NOT NULL,
  `current_usage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `temperature` varchar(255) DEFAULT NULL,
  `humidity` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(111) NOT NULL,
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `managed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storages`
--

INSERT INTO `storages` (`id`, `storage_id`, `name`, `storage_time_id`, `storage_condition_id`, `storage_type_id`, `storage_location_id`, `capacity`, `unit_id`, `current_usage`, `temperature`, `humidity`, `description`, `image`, `status`, `managed_by`, `created_at`, `updated_at`) VALUES
(1, 'STG-2024-0001', 'Main Warehouse A', 0, 0, 0, 0, 5000.00, 0, 1250.50, 'Room Temperature', '40-60%', 'Primary storage warehouse for bulk germplasm materials', '', 'active', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(2, 'STG-2024-0002', 'Cold Storage Unit 1', 0, 0, 0, 0, 2000.00, 0, 850.25, '-20°C', 'Controlled', 'Temperature-controlled storage for sensitive germplasm', '', 'active', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(3, 'STG-2024-0003', 'Seed Cabinet Alpha', 0, 0, 0, 0, 100.00, 0, 45.75, '22°C', '45%', 'Small-scale storage cabinet for active research samples', '', 'active', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(4, 'STG-2024-0004', 'Outdoor Storage Shed', 0, 0, 0, 0, 3000.00, 0, 1200.00, 'Ambient', 'Variable', 'Weather-protected outdoor storage for non-sensitive materials', '', 'active', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(5, 'STG-2024-0005', 'Maintenance Bay', 0, 0, 0, 0, NULL, 0, 0.00, 'Room Temperature', 'Standard', 'Storage area currently under maintenance', '', 'maintenance', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(6, 'STG-2026-2063', 'Room 2', 0, 0, 0, 0, 2500.00, 0, 0.00, '-18', '25', 'test', 'storages/iUcNjIr4dANl21cHzqGlnQPo1Ya2F5OnKPmt3B8x.png', 'active', 3, '2026-03-10 05:19:16', '2026-03-10 05:19:16'),
(7, 'STG-2026-6386', 'Storage Room-01', 1, 4, 4, 0, 23.00, 3, 0.00, '32', '33', '333', 'storages/DfBxfMCKNWzE7IUBbps9WI91IkUuswBy60QDmIwb.png', 'active', 2, '2026-03-17 05:41:47', '2026-03-17 05:48:20'),
(8, 'STG-2026-8851', 'storage -4', 2, 1, 1, 0, 10.00, 2, 0.00, '30', '40', 'test', 'storages/LCrG9jxZiUQXJ5OLLpHEudJyoFcOg6i7zTx33t9z.png', 'active', 2, '2026-03-17 05:49:37', '2026-03-17 05:49:37'),
(9, 'STG-2026-5064', 'location test', 1, 1, 1, 1, 200.00, 1, 0.00, '25', '10-20', 'test', 'storages/fD2T1w3vmhJ8L8EQzOG3ZoBSReqwvSFcE0tzDtPx.png', 'active', 2, '2026-03-19 05:53:59', '2026-03-19 05:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `storage_conditions`
--

CREATE TABLE `storage_conditions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `temp_min` decimal(6,2) DEFAULT NULL,
  `temp_max` decimal(6,2) DEFAULT NULL,
  `humidity_min` decimal(5,2) DEFAULT NULL,
  `humidity_max` decimal(5,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storage_conditions`
--

INSERT INTO `storage_conditions` (`id`, `name`, `code`, `temp_min`, `temp_max`, `humidity_min`, `humidity_max`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ambient Storage', 'AMS', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-17 02:06:19', '2026-03-17 02:06:19'),
(2, 'Cold Storage', 'CS', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-17 02:06:37', '2026-03-17 02:06:37'),
(3, 'Dry Storage', 'DS', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-17 02:06:56', '2026-03-17 02:06:56'),
(4, 'Refrigerated', 'REF', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-17 02:07:27', '2026-03-17 02:07:27');

-- --------------------------------------------------------

--
-- Table structure for table `storage_locations`
--

CREATE TABLE `storage_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storage_locations`
--

INSERT INTO `storage_locations` (`id`, `name`, `code`, `warehouse_id`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Gomachi', 'G-Raipur', NULL, NULL, 1, '2026-03-10 23:09:31', '2026-03-10 23:09:31');

-- --------------------------------------------------------

--
-- Table structure for table `storage_times`
--

CREATE TABLE `storage_times` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `duration_value` int(11) DEFAULT NULL,
  `duration_unit` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storage_times`
--

INSERT INTO `storage_times` (`id`, `name`, `code`, `duration_value`, `duration_unit`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Short-Term Storage (STS)', 'STS', 6, 'months', 'Short-Term Storage (STS)', 1, '2026-03-17 01:40:39', '2026-03-17 01:40:39'),
(2, 'Medium-Term Storage(MTS)', 'MTS', 6, 'years', 'Medium-Term Storage(MTS)', 1, '2026-03-17 01:41:10', '2026-03-17 01:41:10'),
(3, 'Long-Term Storage(LTS)', 'LTS', 20, 'years', 'Long-Term Storage', 1, '2026-03-17 01:41:51', '2026-03-17 01:41:51');

-- --------------------------------------------------------

--
-- Table structure for table `storage_types`
--

CREATE TABLE `storage_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storage_types`
--

INSERT INTO `storage_types` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Shelf Storage', NULL, '1', '2026-03-09 01:37:21', '2026-03-17 02:10:13'),
(2, 'Rack Storage', NULL, '1', '2026-03-09 01:37:21', '2026-03-17 02:09:27'),
(3, 'Bin Storage', NULL, '1', '2026-03-09 01:37:21', '2026-03-17 02:10:42'),
(4, 'Pallet Storage', NULL, '1', '2026-03-09 01:37:21', '2026-03-17 02:11:05'),
(5, 'Bulk Storage', NULL, '1', '2026-03-17 02:27:10', '2026-03-17 02:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Kilogram', 'kg', 'Weight measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(2, 'Gram', 'g', 'Weight measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(3, 'Ton', 't', 'Weight measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(4, 'Quintal', 'q', 'Weight measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(5, 'Liter', 'l', 'Volume measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(6, 'Milliliter', 'ml', 'Volume measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(7, 'Piece', 'pcs', 'Count measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(8, 'Packet', 'pkt', 'Count measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(9, 'Bag', 'bag', 'Count measurement unit', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'super_admin', 'superadmin@gims.com', NULL, '$2y$12$pIFVUglXEmSArFpDJUzBBONSO0jIo7a9tVSIP/nEfgB95H6VfkKhG', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(2, 2, 'admin', 'admin@gims.com', NULL, '$2y$12$w0dtR8R3XOmCyoAZXJHWeectfL.qWLZcOFnZkA5Hhp9SaiQvJ0qxW', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(3, 3, 'Manager', 'manager@gims.com', NULL, '$2y$12$gzifOgJavMZUbvjLkqTXBOsW6cQMf9LfdPfjPN2ekLdkhmidrFWX.', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(4, 4, 'User', 'user@gims.com', NULL, '$2y$12$RZIxCuh1jbjUoE23WH3Ah.PDmv/CoFdta54wKL52Ll.4YzmzQip/2', NULL, '2026-03-09 01:33:57', '2026-03-09 01:33:57'),
(6, 4, 'pooja', 'pooja@gims.com', NULL, '$2y$12$ed3GgOvcS4NQEqCkY60syeKKiWhsZWuGCt7/BIAGI4ETSg6bpuSFK', NULL, '2026-03-09 04:26:09', '2026-03-09 04:26:09'),
(7, 4, 'Narayan', 'narayan@gims.com', NULL, '$2y$12$kYh.4sN4Dvqb3a2LxLGAz.VnwOhTKvi6Yo9Gc1YlZXcL3cxqldphi', NULL, '2026-03-09 04:36:34', '2026-03-09 04:36:34');

-- --------------------------------------------------------

--
-- Table structure for table `varieties`
--

CREATE TABLE `varieties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `variety_name` varchar(255) NOT NULL,
  `variety_code` varchar(255) DEFAULT NULL,
  `crop_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `variety_type` varchar(255) DEFAULT NULL,
  `breeder_name` varchar(255) DEFAULT NULL,
  `release_year` year(4) DEFAULT NULL,
  `release_authority` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `state_id` int(255) DEFAULT NULL,
  `maturity_days` int(11) DEFAULT NULL,
  `plant_height` varchar(255) DEFAULT NULL,
  `maturity_duration` varchar(111) DEFAULT NULL,
  `grain_type` varchar(255) DEFAULT NULL,
  `seed_color` varchar(255) DEFAULT NULL,
  `yield_potential` decimal(8,2) DEFAULT NULL,
  `germination_percent` decimal(5,2) DEFAULT NULL,
  `purity_percent` decimal(5,2) DEFAULT NULL,
  `moisture_percent` decimal(5,2) DEFAULT NULL,
  `test_weight` decimal(6,2) DEFAULT NULL,
  `disease_resistance` varchar(255) DEFAULT NULL,
  `pest_resistance` varchar(255) DEFAULT NULL,
  `drought_tolerance` varchar(255) DEFAULT NULL,
  `flood_tolerance` varchar(255) DEFAULT NULL,
  `salinity_tolerance` varchar(255) DEFAULT NULL,
  `isolation_distance` varchar(255) DEFAULT NULL,
  `seed_class` varchar(255) DEFAULT NULL,
  `production_region` varchar(255) DEFAULT NULL,
  `storage_life` int(11) DEFAULT NULL,
  `variety_status` int(11) NOT NULL,
  `variety_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seed_class_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `varieties`
--

INSERT INTO `varieties` (`id`, `variety_name`, `variety_code`, `crop_id`, `description`, `created_at`, `updated_at`, `variety_type`, `breeder_name`, `release_year`, `release_authority`, `source`, `country_id`, `state_id`, `maturity_days`, `plant_height`, `maturity_duration`, `grain_type`, `seed_color`, `yield_potential`, `germination_percent`, `purity_percent`, `moisture_percent`, `test_weight`, `disease_resistance`, `pest_resistance`, `drought_tolerance`, `flood_tolerance`, `salinity_tolerance`, `isolation_distance`, `seed_class`, `production_region`, `storage_life`, `variety_status`, `variety_type_id`, `seed_class_id`) VALUES
(1, 'HD-2967', NULL, 1, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(2, 'IR-64', NULL, 2, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(3, 'DKC-9090', NULL, 3, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(4, 'BH-906', NULL, 4, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(5, 'CSH-16', NULL, 5, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(6, 'PBW-725', NULL, 1, NULL, '2026-03-09 01:35:23', '2026-03-09 01:35:23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL),
(7, 'ds', 'sdf', 1, NULL, '2026-03-13 03:52:50', '2026-03-13 03:52:50', NULL, 'ds', '2025', 'd', 'SAU', 0, 0, NULL, '95', '123', 'fdg', 'red', 3.00, 3.00, 3.00, 3.00, 3.00, 'd', 'd', 'd', 'd', 'd', 'd', NULL, 'd', 3, 1, 1, 2),
(8, 'tes', NULL, 3, NULL, '2026-03-13 03:54:51', '2026-03-13 03:54:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 3, 3),
(9, 'dfd', 'dsf', 1, NULL, '2026-03-14 02:05:14', '2026-03-14 02:05:14', NULL, 'dsf', '2025', 'sdf', 'ICAR', 0, 0, NULL, '3', '1232', '13', 'dd', 3.00, 435.00, 534.00, 354.00, 345.00, '34', '34', 'sdf', 'sdf', 'sdf', 'sdf', NULL, 'fd', 32, 1, 3, 2),
(10, 'rice-01', 'v-rice01', 2, NULL, '2026-03-19 02:04:29', '2026-03-19 02:04:29', NULL, 'tr', '2024', 'rty', 'ICAR', NULL, NULL, NULL, '52', '120', 'bold', 'red', 55.00, 85.00, 95.00, 555.00, 12.00, 'ewr', 'ewr', 'wer', 'ew', 'ew', '20', NULL, 'india', 12, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `variety_types`
--

CREATE TABLE `variety_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variety_types`
--

INSERT INTO `variety_types` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hybrid', 'HYB', 'Hybrid varieties', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(2, 'Open Pollinated', 'OP', 'Open pollinated varieties', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(3, 'Landrace', 'LR', 'Traditional landrace varieties', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52'),
(4, 'Improved', 'IMP', 'Improved varieties', 1, '2026-03-09 01:36:52', '2026-03-09 01:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `code`, `location`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Main Storage Facility', 'MSF', 'Raipur', NULL, 1, '2026-03-09 01:35:23', '2026-03-17 01:44:04'),
(3, 'Central Seed Warehouse', 'WH3', 'Gomachi', NULL, 1, '2026-03-09 01:35:23', '2026-03-17 01:43:00'),
(4, 'Inventory Warehouse', 'IW', 'Gomachi', 'Inventory Warehouse', 1, '2026-03-10 23:19:42', '2026-03-17 01:44:57'),
(5, 'Primary Seed Warehouse', 'PSW', 'Gomachi', 'Primary Seed Warehouse', 1, '2026-03-10 23:20:19', '2026-03-17 01:44:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessions`
--
ALTER TABLE `accessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accessions_accession_id_unique` (`accession_number`),
  ADD UNIQUE KEY `accessions_barcode_unique` (`barcode`),
  ADD KEY `accessions_created_by_foreign` (`created_by`),
  ADD KEY `accessions_status_index` (`status`),
  ADD KEY `accessions_collection_date_index` (`collection_date`),
  ADD KEY `accessions_crop_id_foreign` (`crop_id`),
  ADD KEY `accessions_variety_id_foreign` (`variety_id`),
  ADD KEY `accessions_country_id_foreign` (`country_id`),
  ADD KEY `accessions_state_id_foreign` (`state_id`),
  ADD KEY `accessions_capacity_unit_id_foreign` (`capacity_unit_id`),
  ADD KEY `accessions_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `accessions_storage_location_id_foreign` (`storage_location_id`),
  ADD KEY `accessions_storage_type_id_foreign` (`storage_type_id`),
  ADD KEY `accessions_entered_by_foreign` (`entered_by`),
  ADD KEY `accessions_accession_number_index` (`accession_number`),
  ADD KEY `accessions_barcode_index` (`barcode`),
  ADD KEY `accessions_entry_date_index` (`entry_date`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_code_unique` (`code`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_state_id_name_index` (`state_id`,`name`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `countries_name_index` (`name`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crops_name_unique` (`crop_name`),
  ADD UNIQUE KEY `crops_code_unique` (`crop_code`),
  ADD KEY `crops_crop_category_id_foreign` (`crop_category_id`),
  ADD KEY `crops_crop_type_id_foreign` (`crop_type_id`),
  ADD KEY `crops_season_id_foreign` (`season_id`);

--
-- Indexes for table `crop_categories`
--
ALTER TABLE `crop_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crop_categories_name_unique` (`name`),
  ADD UNIQUE KEY `crop_categories_code_unique` (`code`);

--
-- Indexes for table `crop_types`
--
ALTER TABLE `crop_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crop_types_name_unique` (`name`),
  ADD UNIQUE KEY `crop_types_code_unique` (`code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `lots`
--
ALTER TABLE `lots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lots_name_unique` (`name`),
  ADD UNIQUE KEY `lots_code_unique` (`code`),
  ADD KEY `lots_accession_id_foreign` (`accession_id`),
  ADD KEY `lots_crop_id_foreign` (`crop_id`),
  ADD KEY `lots_variety_id_foreign` (`variety_id`),
  ADD KEY `lots_unit_id_foreign` (`unit_id`),
  ADD KEY `lots_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `lots_storage_location_id_foreign` (`storage_location_id`);

--
-- Indexes for table `lot_types`
--
ALTER TABLE `lot_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lot_types_code_unique` (`code`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requests_request_number_unique` (`request_number`),
  ADD KEY `requests_crop_id_foreign` (`crop_id`),
  ADD KEY `requests_variety_id_foreign` (`variety_id`),
  ADD KEY `requests_unit_id_foreign` (`unit_id`),
  ADD KEY `requests_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seasons_name_unique` (`name`),
  ADD UNIQUE KEY `seasons_code_unique` (`code`);

--
-- Indexes for table `seed_classes`
--
ALTER TABLE `seed_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seed_classes_name_unique` (`name`),
  ADD UNIQUE KEY `seed_classes_code_unique` (`code`);

--
-- Indexes for table `soil_types`
--
ALTER TABLE `soil_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `states_country_id_name_index` (`country_id`,`name`);

--
-- Indexes for table `storages`
--
ALTER TABLE `storages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `storages_storage_id_unique` (`storage_id`),
  ADD KEY `storages_managed_by_foreign` (`managed_by`);

--
-- Indexes for table `storage_conditions`
--
ALTER TABLE `storage_conditions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `storage_conditions_code_unique` (`code`);

--
-- Indexes for table `storage_locations`
--
ALTER TABLE `storage_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `storage_locations_name_unique` (`name`),
  ADD UNIQUE KEY `storage_locations_code_unique` (`code`),
  ADD KEY `storage_locations_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `storage_times`
--
ALTER TABLE `storage_times`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `storage_times_code_unique` (`code`);

--
-- Indexes for table `storage_types`
--
ALTER TABLE `storage_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `storage_types_name_unique` (`name`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_name_unique` (`name`),
  ADD UNIQUE KEY `units_code_unique` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `varieties`
--
ALTER TABLE `varieties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `varieties_name_unique` (`variety_name`),
  ADD UNIQUE KEY `varieties_code_unique` (`variety_code`),
  ADD KEY `varieties_crop_id_foreign` (`crop_id`),
  ADD KEY `varieties_variety_type_id_foreign` (`variety_type_id`),
  ADD KEY `varieties_seed_class_id_foreign` (`seed_class_id`);

--
-- Indexes for table `variety_types`
--
ALTER TABLE `variety_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variety_types_name_unique` (`name`),
  ADD UNIQUE KEY `variety_types_code_unique` (`code`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouses_name_unique` (`name`),
  ADD UNIQUE KEY `warehouses_code_unique` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accessions`
--
ALTER TABLE `accessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `crop_categories`
--
ALTER TABLE `crop_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `crop_types`
--
ALTER TABLE `crop_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lots`
--
ALTER TABLE `lots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lot_types`
--
ALTER TABLE `lot_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `permission_role`
--
ALTER TABLE `permission_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seed_classes`
--
ALTER TABLE `seed_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `soil_types`
--
ALTER TABLE `soil_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storages`
--
ALTER TABLE `storages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `storage_conditions`
--
ALTER TABLE `storage_conditions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `storage_locations`
--
ALTER TABLE `storage_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `storage_times`
--
ALTER TABLE `storage_times`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `storage_types`
--
ALTER TABLE `storage_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `varieties`
--
ALTER TABLE `varieties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `variety_types`
--
ALTER TABLE `variety_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accessions`
--
ALTER TABLE `accessions`
  ADD CONSTRAINT `accessions_capacity_unit_id_foreign` FOREIGN KEY (`capacity_unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_entered_by_foreign` FOREIGN KEY (`entered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_storage_location_id_foreign` FOREIGN KEY (`storage_location_id`) REFERENCES `storage_locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_storage_type_id_foreign` FOREIGN KEY (`storage_type_id`) REFERENCES `storage_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_variety_id_foreign` FOREIGN KEY (`variety_id`) REFERENCES `varieties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `accessions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crops`
--
ALTER TABLE `crops`
  ADD CONSTRAINT `crops_crop_category_id_foreign` FOREIGN KEY (`crop_category_id`) REFERENCES `crop_categories` (`id`),
  ADD CONSTRAINT `crops_crop_type_id_foreign` FOREIGN KEY (`crop_type_id`) REFERENCES `crop_types` (`id`),
  ADD CONSTRAINT `crops_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`);

--
-- Constraints for table `lots`
--
ALTER TABLE `lots`
  ADD CONSTRAINT `lots_accession_id_foreign` FOREIGN KEY (`accession_id`) REFERENCES `accessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lots_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lots_storage_location_id_foreign` FOREIGN KEY (`storage_location_id`) REFERENCES `storage_locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lots_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lots_variety_id_foreign` FOREIGN KEY (`variety_id`) REFERENCES `varieties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lots_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_variety_id_foreign` FOREIGN KEY (`variety_id`) REFERENCES `varieties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `storages`
--
ALTER TABLE `storages`
  ADD CONSTRAINT `storages_managed_by_foreign` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `storage_locations`
--
ALTER TABLE `storage_locations`
  ADD CONSTRAINT `storage_locations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `varieties`
--
ALTER TABLE `varieties`
  ADD CONSTRAINT `varieties_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `varieties_seed_class_id_foreign` FOREIGN KEY (`seed_class_id`) REFERENCES `seed_classes` (`id`),
  ADD CONSTRAINT `varieties_variety_type_id_foreign` FOREIGN KEY (`variety_type_id`) REFERENCES `variety_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
