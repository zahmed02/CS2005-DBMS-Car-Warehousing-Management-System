-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 08:47 AM
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
-- Table structure for table `carspecifications`
--

CREATE TABLE `carspecifications` (
  `SpecID` int(11) NOT NULL,
  `ModelID` int(11) NOT NULL,
  `EngineType` varchar(255) NOT NULL,
  `FuelType` varchar(255) NOT NULL,
  `Transmission` varchar(255) NOT NULL,
  `DriveType` varchar(255) NOT NULL,
  `BodyType` varchar(255) NOT NULL,
  `TopSpeed` varchar(255) NOT NULL,
  `FuelCapacity` varchar(255) NOT NULL,
  `BatteryCapacity` varchar(255) NOT NULL,
  `Warranty` varchar(255) NOT NULL,
  `DesignedBy` varchar(255) NOT NULL,
  `ManufacturedIn` varchar(255) NOT NULL,
  `LaunchDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carspecifications`
--

INSERT INTO `carspecifications` (`SpecID`, `ModelID`, `EngineType`, `FuelType`, `Transmission`, `DriveType`, `BodyType`, `TopSpeed`, `FuelCapacity`, `BatteryCapacity`, `Warranty`, `DesignedBy`, `ManufacturedIn`, `LaunchDate`) VALUES
(4, 1, '2.0L VTEC Turbo I4', 'Petrol', '6-Speed MT', 'FWD', 'Sport Sedan', '270 km/h', '47 L', 'N/A', '5 yrs / 60,000 km', 'Honda Performance Team', 'Japan', '2025-07-22 19:01:00'),
(5, 3, 'Permanent Magnet Motor', 'Electric', 'Single-Speed', 'AWD', 'Luxury Sedan', '185 km/h', 'N/A', '85 kWh', '6 yrs / 150,000 km', 'BYD Design Studio', 'China', '2025-07-22 19:11:00'),
(7, 6, '5.0L Twin-Turbocharged V8', 'Petrol', '9-Speed Multi-Clutch (Light Speed Transmission - LST)', 'RWD', 'Hypercar Coupe', '530 km/h', '72 liters', 'N/A', '3 yrs / Unlimited km (Manufacturer Dependent)', 'Christian von Koenigsegg', 'Ängelholm, Sweden', '2025-07-29 15:27:00'),
(8, 10, '3.5L Twin-Turbocharged V6', 'Petrol / Hybrid', '9-Speed Dual-Clutch Automatic', 'AWD', 'Sports Coupe', '307 km/h', '59 liters', '1.3 kWh Lithium-Ion', '4 yrs / 80,000 km', 'Acura Design Studio', 'Marysville, Ohio, USA', '2025-07-29 15:29:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carspecifications`
--
ALTER TABLE `carspecifications`
  ADD PRIMARY KEY (`SpecID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carspecifications`
--
ALTER TABLE `carspecifications`
  MODIFY `SpecID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
