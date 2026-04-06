-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2026 at 06:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u741539493_exchange_manag`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `CLIENT_ID` int(11) NOT NULL,
  `CLIENT_NAME` varchar(100) NOT NULL,
  `PHONE` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `DEPT_NO` int(11) DEFAULT NULL,
  `USER_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debt`
--

CREATE TABLE `debt` (
  `DEBT_ID` int(11) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  `AMMOUNT` decimal(20,8) NOT NULL,
  `CURRENCY` varchar(20) NOT NULL,
  `FOR_OR_ON` varchar(10) NOT NULL,
  `DEBT_DATE` datetime NOT NULL,
  `NOTE` text DEFAULT NULL,
  `sum_ammount_new` decimal(20,8) NOT NULL,
  `sum_ammount_old` decimal(20,8) NOT NULL,
  `sum_ammount_sa` decimal(20,8) NOT NULL,
  `CLIENT_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DEPT_NO` int(11) NOT NULL,
  `DEPT_NAME` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `idempotency_keys`
--

CREATE TABLE `idempotency_keys` (
  `request_key` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_hash` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `INCM_ID` int(11) NOT NULL,
  `SOURCE` varchar(200) NOT NULL,
  `AMMOUNT` decimal(20,8) NOT NULL,
  `CURRENCY` varchar(20) NOT NULL,
  `INCM_DATE` datetime NOT NULL,
  `NOTE` text DEFAULT NULL,
  `sum_ammount_new` decimal(20,8) NOT NULL,
  `sum_ammount_old` decimal(20,8) NOT NULL,
  `sum_ammount_sa` decimal(20,8) NOT NULL,
  `FOR_OR_ON` varchar(10) NOT NULL,
  `USER_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `TRA_ID` int(11) NOT NULL,
  `TYPE` varchar(50) NOT NULL,
  `SENDER_NAME` varchar(100) NOT NULL,
  `FOR_OR_ON` varchar(10) NOT NULL,
  `TRANSFER_NO` varchar(100) DEFAULT NULL,
  `TRA_DATE` datetime NOT NULL,
  `ATM` varchar(100) DEFAULT NULL,
  `TRA_FEES` decimal(20,8) DEFAULT NULL,
  `AMMOUNT` decimal(20,8) NOT NULL,
  `CURRENCY` varchar(20) NOT NULL,
  `NOTE` text DEFAULT NULL,
  `sum_ammount_new` decimal(20,8) NOT NULL,
  `sum_ammount_old` decimal(20,8) NOT NULL,
  `sum_ammount_sa` decimal(20,8) NOT NULL,
  `STATUS` varchar(15) DEFAULT '',
  `RECEIVER_NAME` varchar(100) DEFAULT '',
  `FROM_CURRENCY` varchar(15) DEFAULT '',
  `TO_CURRENCY` varchar(15) DEFAULT '',
  `PRICE` decimal(20,8) DEFAULT 0.00000000,
  `TRANSFERED_AMMOUNT` decimal(20,8) DEFAULT 0.00000000,
  `CLIENT_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `USER_ID` int(11) NOT NULL,
  `USER_NAME` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`CLIENT_ID`),
  ADD KEY `DEPT_NO` (`DEPT_NO`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `debt`
--
ALTER TABLE `debt`
  ADD PRIMARY KEY (`DEBT_ID`),
  ADD KEY `CLIENT_ID` (`CLIENT_ID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DEPT_NO`);

--
-- Indexes for table `idempotency_keys`
--
ALTER TABLE `idempotency_keys`
  ADD PRIMARY KEY (`request_key`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`INCM_ID`),
  ADD KEY `USER_ID` (`USER_ID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`TRA_ID`),
  ADD KEY `fk_client_exchange` (`CLIENT_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`),
  ADD UNIQUE KEY `unique_user_name` (`USER_NAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `CLIENT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debt`
--
ALTER TABLE `debt`
  MODIFY `DEBT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DEPT_NO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `INCM_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `TRA_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `idempotency_keys`
--
ALTER TABLE `idempotency_keys`
  ADD CONSTRAINT `idempotency_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `idempotency_keys_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client` (`CLIENT_ID`) ON DELETE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `fk_client_exchange` FOREIGN KEY (`CLIENT_ID`) REFERENCES `client` (`CLIENT_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
