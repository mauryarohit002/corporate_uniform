-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 09, 2025 at 08:13 AM
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
-- Database: `corporate_uniform`
--

-- --------------------------------------------------------

--
-- Table structure for table `quotation_master`
--

CREATE TABLE `quotation_master` (
  `qm_id` int(11) NOT NULL,
  `qm_uuid` varchar(20) NOT NULL,
  `qm_entry_no` int(11) NOT NULL,
  `qm_entry_date` date NOT NULL,
  `qm_status` tinyint(4) NOT NULL COMMENT '0-quotation,1-quotation',
  `qm_gst_type` tinyint(4) NOT NULL,
  `qm_bill_type` tinyint(4) NOT NULL,
  `qm_billing_id` int(11) NOT NULL,
  `qm_customer_id` int(11) NOT NULL,
  `qm_customer_name` varchar(50) NOT NULL,
  `qm_customer_mobile` varchar(20) NOT NULL,
  `qm_notes` text NOT NULL,
  `qm_total_qty` int(11) NOT NULL,
  `qm_sub_amt` double(16,2) NOT NULL,
  `qm_disc_amt` double(16,2) NOT NULL,
  `qm_taxable_amt` double(16,2) NOT NULL,
  `qm_sgst_amt` double(16,2) NOT NULL,
  `qm_cgst_amt` double(16,2) NOT NULL,
  `qm_igst_amt` double(16,2) NOT NULL,
  `qm_bill_disc_per` double(16,2) NOT NULL,
  `qm_bill_disc_amt` double(16,2) NOT NULL,
  `qm_round_off` double(16,2) NOT NULL,
  `qm_total_amt` double(16,2) NOT NULL,
  `qm_allocated_amt` double(16,2) NOT NULL,
  `qm_delete_status` tinyint(4) NOT NULL,
  `qm_fin_year` varchar(10) NOT NULL,
  `qm_branch_id` int(11) NOT NULL,
  `qm_created_by` int(11) NOT NULL,
  `qm_created_at` datetime NOT NULL,
  `qm_updated_by` int(11) NOT NULL,
  `qm_updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quotation_master`
--
ALTER TABLE `quotation_master`
  ADD PRIMARY KEY (`qm_id`),
  ADD KEY `qm_uuid` (`qm_uuid`),
  ADD KEY `qm_customer_id` (`qm_customer_id`),
  ADD KEY `qm_delete_status` (`qm_delete_status`),
  ADD KEY `qm_fin_year` (`qm_fin_year`),
  ADD KEY `qm_branch_id` (`qm_branch_id`),
  ADD KEY `qm_created_by` (`qm_created_by`),
  ADD KEY `qm_updated_by` (`qm_updated_by`),
  ADD KEY `qm_gst_type` (`qm_gst_type`),
  ADD KEY `qm_bill_type` (`qm_bill_type`),
  ADD KEY `qm_billing_id` (`qm_billing_id`),
  ADD KEY `qm_entry_no` (`qm_entry_no`,`qm_fin_year`,`qm_branch_id`,`qm_delete_status`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quotation_master`
--
ALTER TABLE `quotation_master`
  MODIFY `qm_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
