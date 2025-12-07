-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 08:46 AM
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
-- Table structure for table `brand_affiliations`
--

CREATE TABLE `brand_affiliations` (
  `AffiliationID` int(11) NOT NULL,
  `BrandID` int(11) NOT NULL,
  `AffiliatedBrandID` int(11) NOT NULL,
  `AffiliationType` varchar(50) DEFAULT 'partnership',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand_affiliations`
--

INSERT INTO `brand_affiliations` (`AffiliationID`, `BrandID`, `AffiliatedBrandID`, `AffiliationType`, `CreatedAt`) VALUES
(2, 11, 10, 'partnership', '2025-12-02 18:57:02'),
(3, 10, 11, 'partnership', '2025-12-02 18:57:42'),
(5, 28, 15, 'partnership', '2025-12-03 04:41:17'),
(6, 15, 28, 'partnership', '2025-12-03 04:41:17'),
(7, 28, 13, 'partnership', '2025-12-03 04:41:17'),
(8, 13, 28, 'partnership', '2025-12-03 04:41:17'),
(9, 28, 11, 'partnership', '2025-12-03 04:41:17'),
(10, 11, 28, 'partnership', '2025-12-03 04:41:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand_affiliations`
--
ALTER TABLE `brand_affiliations`
  ADD PRIMARY KEY (`AffiliationID`),
  ADD UNIQUE KEY `unique_affiliation` (`BrandID`,`AffiliatedBrandID`),
  ADD KEY `AffiliatedBrandID` (`AffiliatedBrandID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand_affiliations`
--
ALTER TABLE `brand_affiliations`
  MODIFY `AffiliationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brand_affiliations`
--
ALTER TABLE `brand_affiliations`
  ADD CONSTRAINT `brand_affiliations_ibfk_1` FOREIGN KEY (`BrandID`) REFERENCES `carbrand` (`BrandID`) ON DELETE CASCADE,
  ADD CONSTRAINT `brand_affiliations_ibfk_2` FOREIGN KEY (`AffiliatedBrandID`) REFERENCES `carbrand` (`BrandID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
