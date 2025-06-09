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
-- Table structure for table `quotation_trans`
--

CREATE TABLE `quotation_trans` (
  `qt_id` int(11) NOT NULL,
  `qt_qm_uuid` varchar(20) NOT NULL,
  `qt_qm_id` int(11) NOT NULL,
  `qt_trans_type` varchar(20) NOT NULL,
  `qt_sku_id` int(11) NOT NULL,
  `qt_sdt_id` int(11) NOT NULL,
  `qt_rate` double(16,2) NOT NULL,
  `qt_qty` int(11) NOT NULL,
  `qt_amt` double(16,2) NOT NULL,
  `qt_disc_per` double(16,2) NOT NULL,
  `qt_disc_amt` double(16,2) NOT NULL,
  `qt_taxable_amt` double(16,2) NOT NULL,
  `qt_sgst_per` double(16,2) NOT NULL,
  `qt_sgst_amt` double(16,2) NOT NULL,
  `qt_cgst_per` double(16,2) NOT NULL,
  `qt_cgst_amt` double(16,2) NOT NULL,
  `qt_igst_per` double(16,2) NOT NULL,
  `qt_igst_amt` double(16,2) NOT NULL,
  `qt_total_amt` double(16,2) NOT NULL,
  `qt_description` text NOT NULL,
  `qt_delete_status` tinyint(4) NOT NULL,
  `qt_created_by` int(11) NOT NULL,
  `qt_created_at` datetime NOT NULL,
  `qt_updated_by` int(11) NOT NULL,
  `qt_updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quotation_trans`
--
ALTER TABLE `quotation_trans`
  ADD PRIMARY KEY (`qt_id`),
  ADD KEY `qt_om_uuid` (`qt_qm_uuid`),
  ADD KEY `qt_om_id` (`qt_qm_id`),
  ADD KEY `qt_trans_type` (`qt_trans_type`),
  ADD KEY `qt_delete_status` (`qt_delete_status`),
  ADD KEY `qt_created_by` (`qt_created_by`),
  ADD KEY `qt_updated_by` (`qt_updated_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quotation_trans`
--
ALTER TABLE `quotation_trans`
  MODIFY `qt_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
