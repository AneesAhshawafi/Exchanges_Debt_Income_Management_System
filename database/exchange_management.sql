-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2025 at 04:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exchange_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `CLIENT_ID` int(11) NOT NULL,
  `CLIENT_NAME` varchar(100) NOT NULL,
  `DEPT_NO` int(11) DEFAULT NULL,
  `USER_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`CLIENT_ID`, `CLIENT_NAME`, `DEPT_NO`, `USER_ID`) VALUES
(1, 'سمير الرداعي', 1, 1),
(2, 'عميل 1', 1, 1),
(3, 'عميل 2', 1, 1),
(4, 'عميل 3', 1, 1),
(5, 'عميل 4', 1, 1),
(6, 'عميل 5', 1, 1),
(7, 'عميل 6', 1, 1),
(8, 'عميل 7', 1, 1),
(9, 'عميل 8', 1, 1),
(10, 'عميل 9', 1, 1),
(11, 'عميل 10', 1, 1),
(12, 'عميل 11', 1, 1),
(13, 'عميل 12', 1, 1),
(14, 'عميل 13', 1, 1),
(15, 'عميل 14', 1, 1),
(16, 'عميل 15', 1, 1),
(17, 'عميل 16', 1, 1),
(18, 'عميل 17', 1, 1),
(19, 'عميل 18', 1, 1),
(20, 'عميل 19', 1, 1),
(21, 'عميل 20', 1, 1),
(22, 'عميل 21', 1, 1),
(23, 'عميل 22', 1, 1),
(24, 'عميل 23', 1, 1),
(25, 'عميل 24', 1, 1),
(26, 'عميل 25', 1, 1),
(27, 'عميل 26', 1, 1),
(28, 'عميل 27', 1, 1),
(29, 'عميل 28', 1, 1),
(30, 'عميل 29', 1, 1),
(31, 'عميل 30', 1, 1),
(32, 'عميل 31', 1, 1),
(33, 'عميل 32', 1, 1),
(34, 'عميل 33', 1, 1),
(35, 'عميل 34', 1, 1),
(36, 'عميل 35', 1, 1),
(37, 'عميل 36', 1, 1),
(38, 'عميل 37', 1, 1),
(39, 'عميل 38', 1, 1),
(40, 'عميل 39', 1, 1),
(41, 'عميل 40', 1, 1),
(42, 'عميل 41', 1, 1),
(43, 'عميل 42', 1, 1),
(44, 'عميل 43', 1, 1),
(45, 'عميل 44', 1, 1),
(46, 'عميل 45', 1, 1),
(47, 'عميل 46', 1, 1),
(48, 'عميل 47', 1, 1),
(49, 'عميل 48', 1, 1),
(50, 'عميل 49', 1, 1),
(51, 'عميل 50', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `debt`
--

CREATE TABLE `debt` (
  `DEBT_ID` int(11) NOT NULL,
  `DESCRIPTION` text NOT NULL,
  `AMMOUNT` decimal(15,2) NOT NULL,
  `CURRENCY` varchar(20) NOT NULL,
  `FOR_OR_ON` varchar(100) NOT NULL,
  `DEBT_DATE` datetime NOT NULL,
  `NOTE` text DEFAULT NULL,
  `sum_ammount_new` decimal(15,2) NOT NULL,
  `sum_ammount_old` decimal(15,2) NOT NULL,
  `sum_ammount_sa` decimal(15,2) NOT NULL,
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

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DEPT_NO`, `DEPT_NAME`) VALUES
(1, 'exchange'),
(2, 'debt');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `INCM_ID` int(11) NOT NULL,
  `SOURCE` varchar(100) NOT NULL,
  `AMMOUNT` decimal(15,2) NOT NULL,
  `CURRENCY` varchar(20) NOT NULL,
  `INCM_DATE` datetime NOT NULL,
  `NOTE` text DEFAULT NULL,
  `sum_ammount_new` decimal(15,2) NOT NULL,
  `sum_ammount_old` decimal(15,2) NOT NULL,
  `sum_ammount_sa` decimal(15,2) NOT NULL,
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
  `FOR_OR_ON` varchar(100) NOT NULL,
  `TRANSFER_NO` varchar(100) DEFAULT NULL,
  `TRA_DATE` datetime NOT NULL,
  `ATM` varchar(100) DEFAULT NULL,
  `TRA_FEES` decimal(15,2) DEFAULT NULL,
  `AMMOUNT` decimal(15,2) NOT NULL,
  `CURRENCY` varchar(20) NOT NULL,
  `NOTE` text DEFAULT NULL,
  `sum_ammount_new` decimal(15,2) NOT NULL,
  `sum_ammount_old` decimal(15,2) NOT NULL,
  `sum_ammount_sa` decimal(15,2) NOT NULL,
  `CLIENT_ID` int(11) DEFAULT NULL,
  `RECEIVER_NAME` varchar(100) DEFAULT NULL,
  `STATUS` varchar(15) DEFAULT 'تم الإيداع'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`TRA_ID`, `TYPE`, `SENDER_NAME`, `FOR_OR_ON`, `TRANSFER_NO`, `TRA_DATE`, `ATM`, `TRA_FEES`, `AMMOUNT`, `CURRENCY`, `NOTE`, `sum_ammount_new`, `sum_ammount_old`, `sum_ammount_sa`, `CLIENT_ID`, `RECEIVER_NAME`, `STATUS`) VALUES
(4, 'حوالة', 'سمحون النعينع', 'له', '2344344354643', '2025-07-30 00:00:00', 'النجم', 200.00, 345345.00, 'new', '', 345345.00, 0.00, 0.00, 51, 'نحناح', 'تم الإيداع'),
(8, 'إيداع', 'سمحون النعينع', 'له', '', '2025-07-30 00:00:00', 'النجم', 2000.00, 345345.00, 'new', '', 690690.00, 0.00, 0.00, 51, 'نحناح', 'تم الإيداع'),
(9, 'حوالة', 'انيس', 'له', '2344354643', '2025-07-30 00:00:00', 'النجم', 200.00, 20000.00, 'new', '', 710690.00, 0.00, 0.00, 51, 'نحناح', 'تم الإيداع'),
(10, 'إيداع', 'انيس', 'له', '', '2025-07-30 00:00:00', 'النجم', 200.00, 25000.00, 'old', '', 710690.00, 25000.00, 0.00, 51, 'نحناح', 'تم الإيداع'),
(11, 'حوالة', 'سمحون النعينع', 'له', '2344344354643', '2025-07-30 00:00:00', 'النجم', 2.00, 345345.00, 'sa', '', 710690.00, 25000.00, 345345.00, 51, 'نحناح', 'تم الإيداع'),
(12, 'إيداع', 'سمحون', 'عليه', '', '2025-07-30 00:00:00', 'النجم', 200.00, 25000.00, 'old', '', 710690.00, 0.00, 345345.00, 51, 'نحناح', 'تم الإيداع'),
(15, 'إيداع', 'انيس', 'عليه', '', '2025-07-30 00:00:00', 'النجم', 200.00, 30000.00, 'old', 'عيغبع   بهسعيغبس 9سيغبسب ي 9 بص8بغ8صي  8صبغ8 يغب بغص89ص بغيسغبغي8ب0 يب0 ب0س ب يس0غب9ي غبيس بيب8', 710690.00, -90000.00, 345345.00, 51, 'نحناح', NULL);

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
-- Dumping data for table `users`
--

INSERT INTO `users` (`USER_ID`, `USER_NAME`, `PASSWORD`) VALUES
(1, 'ammarabod', '185cb15def7d2a03062979da257ad5a707e36ee9f9b25eb6428441458d728157');

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
  ADD KEY `CLIENT_ID` (`CLIENT_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `CLIENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `debt`
--
ALTER TABLE `debt`
  MODIFY `DEBT_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DEPT_NO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `INCM_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `TRA_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `USER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`DEPT_NO`) REFERENCES `department` (`DEPT_NO`) ON DELETE SET NULL,
  ADD CONSTRAINT `client_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE SET NULL;

--
-- Constraints for table `debt`
--
ALTER TABLE `debt`
  ADD CONSTRAINT `debt_ibfk_1` FOREIGN KEY (`CLIENT_ID`) REFERENCES `client` (`CLIENT_ID`) ON DELETE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE SET NULL;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`CLIENT_ID`) REFERENCES `client` (`CLIENT_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
