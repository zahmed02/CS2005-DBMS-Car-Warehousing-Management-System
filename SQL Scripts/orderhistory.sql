-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 08:48 AM
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
-- Database: `db_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `orderhistory`
--

CREATE TABLE `orderhistory` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ModelID` int(11) NOT NULL,
  `OrderDate` datetime NOT NULL,
  `Quantity` int(11) NOT NULL,
  `TotalPrice` decimal(10,0) NOT NULL,
  `Status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderhistory`
--

INSERT INTO `orderhistory` (`OrderID`, `UserID`, `ModelID`, `OrderDate`, `Quantity`, `TotalPrice`, `Status`) VALUES
(1, 3, 1, '2025-07-23 10:43:32', 1, 52000, 'Pending'),
(2, 9, 6, '2025-07-23 10:44:43', 1, 3000000, 'Processing'),
(3, 11, 7, '2025-07-23 10:44:57', 1, 95000, 'Delivered'),
(4, 7, 3, '2025-07-23 10:45:06', 1, 45000, 'Pending'),
(5, 10, 4, '2025-07-23 10:45:35', 2, 138000, 'Processing'),
(6, 4, 5, '2025-07-23 10:52:18', 3, 219000, 'Shipped'),
(7, 6, 4, '2025-07-23 10:52:37', 1, 69000, 'Cancelled'),
(9, 14, 3, '2025-11-23 18:38:00', 2, 90000, 'Pending'),
(10, 7, 1, '2025-11-23 18:38:00', 1, 52000, 'Pending'),
(11, 6, 6, '2025-11-23 18:41:00', 1, 3000000, 'Pending'),
(12, 14, 7, '2025-11-23 18:54:00', 2, 190000, 'Processing'),
(13, 8, 7, '2025-11-23 18:56:00', 1, 95000, 'Scheduled'),
(14, 9, 7, '2025-11-23 18:57:00', 1, 95000, 'Scheduled'),
(15, 8, 7, '2025-11-23 18:58:00', 3, 285000, 'Scheduled'),
(16, 17, 11, '2025-11-24 00:35:00', 2, 134000, 'Scheduled');

--
-- Triggers `orderhistory`
--
DELIMITER $$
CREATE TRIGGER `prevent_overbooking` BEFORE INSERT ON `orderhistory` FOR EACH ROW BEGIN
    DECLARE available_qty INT;
    
    SELECT AvailableQty INTO available_qty 
    FROM carmodel 
    WHERE ModelID = NEW.ModelID;
    
    IF available_qty < NEW.Quantity THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Insufficient stock available';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_trending_status` AFTER INSERT ON `orderhistory` FOR EACH ROW BEGIN
    DECLARE order_count INT;
    
    -- Count orders for this model in last 30 days
    SELECT COUNT(*) INTO order_count 
    FROM orderhistory 
    WHERE ModelID = NEW.ModelID 
    AND OrderDate >= DATE_SUB(NOW(), INTERVAL 30 DAY);
    
    -- Mark as trending if more than 5 orders in 30 days
    IF order_count >= 5 THEN
        UPDATE carmodel SET Trending = 1 WHERE ModelID = NEW.ModelID;
    ELSE
        UPDATE carmodel SET Trending = 0 WHERE ModelID = NEW.ModelID;
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderhistory`
--
ALTER TABLE `orderhistory`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `idx_orderhistory_user` (`UserID`),
  ADD KEY `idx_orderhistory_model` (`ModelID`),
  ADD KEY `idx_orderhistory_date` (`OrderDate`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderhistory`
--
ALTER TABLE `orderhistory`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
