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
-- Table structure for table `testdrivebookings`
--

CREATE TABLE `testdrivebookings` (
  `BookingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ModelID` int(11) NOT NULL,
  `PreferredDate` datetime NOT NULL,
  `Status` varchar(255) NOT NULL,
  `BookingDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testdrivebookings`
--

INSERT INTO `testdrivebookings` (`BookingID`, `UserID`, `ModelID`, `PreferredDate`, `Status`, `BookingDate`) VALUES
(1, 5, 4, '2025-07-29 05:35:00', 'Pending', '2025-07-28 14:39:53'),
(2, 5, 10, '2025-07-31 17:45:00', 'Processing', '2025-07-29 17:25:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `testdrivebookings`
--
ALTER TABLE `testdrivebookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `idx_testdrive_user` (`UserID`),
  ADD KEY `idx_testdrive_date` (`PreferredDate`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `testdrivebookings`
--
ALTER TABLE `testdrivebookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
