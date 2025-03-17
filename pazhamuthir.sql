-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 17, 2025 at 09:17 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pazhamuthir`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `id` int NOT NULL,
  `category_name` varchar(250) NOT NULL,
  `created_by` tinyint NOT NULL,
  `created_dt` datetime NOT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  `del_status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gst`
--

CREATE TABLE `tbl_gst` (
  `id` int NOT NULL,
  `sgst` varchar(10) NOT NULL,
  `cgst` varchar(10) NOT NULL,
  `igst` varchar(10) NOT NULL,
  `descriptions` varchar(500) NOT NULL,
  `created_by` tinyint NOT NULL,
  `created_dt` datetime NOT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  `del_status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_gst`
--

INSERT INTO `tbl_gst` (`id`, `sgst`, `cgst`, `igst`, `descriptions`, `created_by`, `created_dt`, `updated_by`, `updated_dt`, `del_status`) VALUES
(1, '10', '10', '10', 'nothing', 1, '2025-03-16 13:22:21', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `id` int NOT NULL,
  `p_code` varchar(20) NOT NULL,
  `p_name` varchar(250) NOT NULL,
  `uom` tinyint NOT NULL,
  `sales_price` decimal(12,2) NOT NULL,
  `purchase_price` decimal(12,2) NOT NULL,
  `gst` tinyint NOT NULL,
  `p_img` varchar(300) NOT NULL,
  `created_by` tinyint NOT NULL,
  `created_dt` datetime NOT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  `del_status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`id`, `p_code`, `p_name`, `uom`, `sales_price`, `purchase_price`, `gst`, `p_img`, `created_by`, `created_dt`, `updated_by`, `updated_dt`, `del_status`) VALUES
(1, '001', 'Paint', 5, 100.00, 90.00, 1, '001_product_Paint.jfif', 1, '2025-03-16 13:23:02', NULL, NULL, 0),
(2, '002', 'mobile', 4, 10000.00, 9000.00, 1, '002_product_mobile.jfif', 1, '2025-03-17 12:56:51', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_roles`
--

CREATE TABLE `tbl_roles` (
  `id` tinyint NOT NULL,
  `role_name` varchar(100) DEFAULT NULL,
  `menu_permission` text NOT NULL,
  `status` tinyint NOT NULL,
  `created_by` tinyint DEFAULT NULL,
  `created_dt` datetime DEFAULT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  `del_status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_roles`
--

INSERT INTO `tbl_roles` (`id`, `role_name`, `menu_permission`, `status`, `created_by`, `created_dt`, `updated_by`, `updated_dt`, `del_status`) VALUES
(1, 'Super Admin', 'mnuSales||mnuCustomer||mnuGST||mnuUOM||mnuRolesPermissions||mnuUsers||mnuShop', 1, 1, '2023-01-03 12:57:26', 1, '2024-07-08 14:14:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sales`
--

CREATE TABLE `tbl_sales` (
  `id` int NOT NULL,
  `table_name` varchar(20) NOT NULL,
  `cus_name` varchar(250) NOT NULL,
  `tot_amount` int NOT NULL,
  `order_status` tinyint NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `discount_type` varchar(2) DEFAULT NULL,
  `discount_amount` decimal(12,2) DEFAULT NULL,
  `sgst_amt` decimal(12,2) NOT NULL,
  `cgst_amt` decimal(12,2) NOT NULL,
  `igst_amt` decimal(12,2) NOT NULL,
  `gst_amt` decimal(12,2) NOT NULL,
  `round_amount` decimal(12,2) NOT NULL,
  `grand_total` decimal(12,2) NOT NULL,
  `created_by` tinyint NOT NULL,
  `created_dt` datetime NOT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  `del_status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_sales`
--

INSERT INTO `tbl_sales` (`id`, `table_name`, `cus_name`, `tot_amount`, `order_status`, `order_no`, `discount_type`, `discount_amount`, `sgst_amt`, `cgst_amt`, `igst_amt`, `gst_amt`, `round_amount`, `grand_total`, `created_by`, `created_dt`, `updated_by`, `updated_dt`, `del_status`) VALUES
(1, 'T2', 'werfdf', 2400, 2, 'ORD001', 'P', 13.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2088.00, 1, '2025-03-16 13:27:18', 1, '2025-03-16 13:28:56', 0),
(2, 'T3', 'dcdsfd', 500, 2, 'ORD002', 'F', 223.00, 0.00, 0.00, 0.00, 0.00, 0.00, 277.00, 1, '2025-03-16 13:29:49', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sales_dts`
--

CREATE TABLE `tbl_sales_dts` (
  `sale_id` int NOT NULL,
  `p_id` int NOT NULL,
  `uom_id` int NOT NULL,
  `sale_price` decimal(12,2) NOT NULL,
  `count` int NOT NULL,
  `tot_sale_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_sales_dts`
--

INSERT INTO `tbl_sales_dts` (`sale_id`, `p_id`, `uom_id`, `sale_price`, `count`, `tot_sale_price`) VALUES
(1, 1, 4, 100.00, 24, 2400.00),
(2, 1, 5, 100.00, 5, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sales_dts_temp`
--

CREATE TABLE `tbl_sales_dts_temp` (
  `sale_id` int NOT NULL,
  `p_id` int NOT NULL,
  `uom_id` int NOT NULL,
  `sale_price` decimal(12,2) NOT NULL,
  `count` int NOT NULL,
  `tot_sale_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_shop`
--

CREATE TABLE `tbl_shop` (
  `id` int NOT NULL,
  `shop_name` varchar(250) NOT NULL,
  `shop_gst_no` varchar(100) NOT NULL,
  `owner_name` varchar(250) NOT NULL,
  `mobile_no1` varchar(10) NOT NULL,
  `mobile_no2` varchar(10) NOT NULL,
  `email_id` varchar(250) NOT NULL,
  `door_no` varchar(100) NOT NULL,
  `address_line1` varchar(500) NOT NULL,
  `address_line2` varchar(500) NOT NULL,
  `city` varchar(100) NOT NULL,
  `shop_state` varchar(100) NOT NULL,
  `pincode` varchar(7) NOT NULL,
  `updated_by` tinyint NOT NULL,
  `updated_dt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_shop`
--

INSERT INTO `tbl_shop` (`id`, `shop_name`, `shop_gst_no`, `owner_name`, `mobile_no1`, `mobile_no2`, `email_id`, `door_no`, `address_line1`, `address_line2`, `city`, `shop_state`, `pincode`, `updated_by`, `updated_dt`) VALUES
(1, 'MUNUSAMY', 'fv13j12344', 'Munusamy A', '7019601667', '9626366878', 'munus94SFDDF04@gmail.com', '3/17', 'Kariyampatti,Periyathottam Puthur,sathyanathapuram', 'Pennagaram', 'Dharmapuri', 'Tamilnadu', '636813', 1, '2025-03-17 12:55:46');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_uom`
--

CREATE TABLE `tbl_uom` (
  `id` tinyint NOT NULL,
  `uom_name` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `uom` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_by` tinyint DEFAULT NULL,
  `created_dt` datetime DEFAULT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  `del_status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_uom`
--

INSERT INTO `tbl_uom` (`id`, `uom_name`, `uom`, `created_by`, `created_dt`, `updated_by`, `updated_dt`, `del_status`) VALUES
(1, 'Liters', 'Lts', 1, '2023-04-11 14:35:37', NULL, NULL, 0),
(2, 'Kilograms', 'Kgs', 1, '2023-04-11 14:35:53', NULL, NULL, 0),
(3, 'Numbers', 'Nos', 1, '2023-04-11 14:36:03', NULL, NULL, 0),
(4, 'Boxes', 'Box', 1, '2023-04-11 14:36:14', NULL, NULL, 0),
(5, 'Bags', 'Bags', 1, '2023-04-11 14:37:23', NULL, NULL, 0),
(6, 'Pieces', 'Pcs', 1, '2024-06-29 09:11:41', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` tinyint NOT NULL,
  `uname` varchar(100) DEFAULT NULL,
  `mobile_no` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `roles_id` tinyint DEFAULT NULL,
  `status` tinyint NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `hint_password` varchar(100) NOT NULL,
  `created_by` tinyint DEFAULT NULL,
  `created_dt` datetime DEFAULT NULL,
  `updated_by` tinyint DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  `del_status` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `uname`, `mobile_no`, `email`, `roles_id`, `status`, `username`, `password`, `hint_password`, `created_by`, `created_dt`, `updated_by`, `updated_dt`, `del_status`) VALUES
(1, 'Administrator', '9944537774', 'admin@gmail.com', 1, 1, 'admin', 'e122911e07b7fe7df3cb4eaf9cd03f57', 'admin1234', 1, '2023-01-07 15:48:57', 0, '2023-01-07 16:28:03', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_gst`
--
ALTER TABLE `tbl_gst`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_sales`
--
ALTER TABLE `tbl_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_shop`
--
ALTER TABLE `tbl_shop`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_uom`
--
ALTER TABLE `tbl_uom`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_gst`
--
ALTER TABLE `tbl_gst`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  MODIFY `id` tinyint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_sales`
--
ALTER TABLE `tbl_sales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_shop`
--
ALTER TABLE `tbl_shop`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_uom`
--
ALTER TABLE `tbl_uom`
  MODIFY `id` tinyint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` tinyint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
