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
-- Table structure for table `manufacturer_details`
--

CREATE TABLE `manufacturer_details` (
  `DetailID` int(11) NOT NULL,
  `BrandID` int(11) NOT NULL,
  `HeadquartersAddress` text DEFAULT NULL,
  `ParentCompany` varchar(255) DEFAULT NULL,
  `Subsidiaries` text DEFAULT NULL,
  `KeyExecutives` text DEFAULT NULL,
  `AnnualProduction` int(11) DEFAULT NULL,
  `AnnualRevenue` decimal(15,2) DEFAULT NULL,
  `ManufacturingPlants` text DEFAULT NULL,
  `ResearchCenters` text DEFAULT NULL,
  `MarketCap` decimal(15,2) DEFAULT NULL,
  `EmployeeCount` int(11) DEFAULT NULL,
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manufacturer_details`
--

INSERT INTO `manufacturer_details` (`DetailID`, `BrandID`, `HeadquartersAddress`, `ParentCompany`, `Subsidiaries`, `KeyExecutives`, `AnnualProduction`, `AnnualRevenue`, `ManufacturingPlants`, `ResearchCenters`, `MarketCap`, `EmployeeCount`, `UpdatedAt`) VALUES
(1, 28, 'Seoul, Korea', 'Hyundai Motor Company', 'Kia\r\nHyundai Capital\r\nGenesis Motor', 'Randy Parker\r\nBarry Ratzlaff\r\nCole Stutz', 75000, 9800000.00, 'North Daokota\r\nFlorida\r\nmanchester', 'Hiroshima', 650000.00, 95, '2025-12-03 04:41:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `manufacturer_details`
--
ALTER TABLE `manufacturer_details`
  ADD PRIMARY KEY (`DetailID`),
  ADD UNIQUE KEY `unique_brand_detail` (`BrandID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `manufacturer_details`
--
ALTER TABLE `manufacturer_details`
  MODIFY `DetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `manufacturer_details`
--
ALTER TABLE `manufacturer_details`
  ADD CONSTRAINT `manufacturer_details_ibfk_1` FOREIGN KEY (`BrandID`) REFERENCES `carbrand` (`BrandID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
