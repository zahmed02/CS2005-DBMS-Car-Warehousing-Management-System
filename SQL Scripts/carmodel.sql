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
-- Table structure for table `carmodel`
--

CREATE TABLE `carmodel` (
  `ModelID` int(11) NOT NULL,
  `ModelName` varchar(255) NOT NULL,
  `BrandID` int(11) NOT NULL,
  `TypeID` int(11) NOT NULL,
  `PriceRange` decimal(10,0) NOT NULL,
  `ModelYear` datetime NOT NULL,
  `ManufacturePlace` varchar(255) NOT NULL,
  `InStock` tinyint(1) NOT NULL,
  `AvailableQty` int(11) NOT NULL,
  `SponsoredBy` varchar(255) NOT NULL,
  `MainImage` varchar(255) NOT NULL,
  `RearImage` varchar(255) NOT NULL,
  `Trending` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carmodel`
--

INSERT INTO `carmodel` (`ModelID`, `ModelName`, `BrandID`, `TypeID`, `PriceRange`, `ModelYear`, `ManufacturePlace`, `InStock`, `AvailableQty`, `SponsoredBy`, `MainImage`, `RearImage`, `Trending`) VALUES
(1, 'Integra Type S1', 10, 1, 52000, '2025-07-22 16:53:00', 'Japan', 1, 8, 'Honda Performance', 'uploads/carmodel_images/687f7cecbbd1e_f5abdb65be514bd499373be9bec46b50.jpg', 'uploads/carmodel_images/687f7cecbc13d_3079645.jpg', 0),
(3, 'Han EV', 15, 2, 45000, '2025-07-22 17:18:00', 'Shenzhen, China', 1, 15, 'BYD Energy', 'uploads/carmodel_images/687f820680c82_byd-han-scaled-1.jpg', 'uploads/carmodel_images/687f82068317d_byd_han_ev_2025_dealer_2_1000-1.jpg', 0),
(4, 'Ocean Ultra', 18, 3, 69000, '2025-07-22 19:20:00', 'California, USA', 1, 3, 'Fisker GreenCo', 'uploads/carmodel_images/687f9e9a46052_2023-Fisker-Ocean-front.jpg', 'uploads/carmodel_images/687f9e9a48ade_fisker-ocean-details-rear-side-reflector-blue-calipers-rear-v0-uje3688z7arb1.jpg', 1),
(5, 'G80 Electrified', 13, 2, 73000, '2025-07-22 19:23:00', 'Ulsan, South Korea', 1, 5, 'Hyundai Premium', 'uploads/carmodel_images/687f9f2d9f454_240905-genesislaunchesnewlydesignedelectrifiedg80inkorea-1.jpg', 'uploads/carmodel_images/687f9f2da2041_220920_genesis_g80_electrified_10.jpg', 1),
(6, 'Jesko Absolut', 14, 4, 3000000, '2025-07-22 19:25:00', 'Ängelholm, Sweden', 1, 1, 'Koenigsegg Lab', 'uploads/carmodel_images/687f9fd5d8f2a_koenigsegg-jesko-101-1551799580.jpg', 'uploads/carmodel_images/687f9fd5dcc9d_2500888.jpg', 0),
(7, 'Air Grand Touring', 12, 2, 95000, '2025-07-22 19:28:00', 'Arizona, USA', 1, 3, 'Lucid DreamDrive', 'uploads/carmodel_images/687fa1443b3b5_Lucid_Air-01@2x.jpg', 'uploads/carmodel_images/687fa1443daef_images.jfif', 0),
(8, 'Toyota Corolla Altis 1.6', 23, 3, 5200000, '2025-07-29 13:22:00', 'Karachi, Pakistan', 1, 24, 'Indus Motors Pakistan', 'uploads/carmodel/688884e08efa4_main_1681626-3060x1722-desktop-hd-toyota-corolla-background-image.jpg', 'uploads/carmodel/688884e091a83_rear_HD-wallpaper-2021-toyota-corolla-rear-view-exterior-white-sedan-new-white-corolla-japanese-cars-toyota.jpg', 1),
(10, 'NSX Type S', 10, 1, 182000, '2025-07-29 14:01:00', 'Marysville, Ohio, USA', 1, 7, 'Acura Performance Division', 'uploads/carmodel/68888d8ec76f8_main_2017-acura-nsx-review.jpg', 'uploads/carmodel/68888d8ec9df2_rear_2016_acura_nsx_15_1600x1200.jpg', 1),
(11, 'mazda rx7', 19, 4, 67000, '2025-11-24 00:31:00', 'Shenzhen, China', 1, 5, 'Acura Performance Division', 'uploads/carmodel/69236122c8fe7_main_5a.jpg', 'uploads/carmodel/69236122cd0a9_rear_f8b90b34-3391-4289-a869-43831a999d8e.jpeg', 0),
(12, 'Random', 12, 1, 70000, '2025-11-24 09:13:00', 'Karachi, Pakistan', 1, 7, 'BYD Energy', 'uploads/carmodel/6923db87b88f4_main_f8b90b34-3391-4289-a869-43831a999d8e.jpeg', 'uploads/carmodel/6923db87bcb23_rear_5a.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carmodel`
--
ALTER TABLE `carmodel`
  ADD PRIMARY KEY (`ModelID`),
  ADD KEY `idx_carmodel_brand` (`BrandID`),
  ADD KEY `idx_carmodel_type` (`TypeID`),
  ADD KEY `idx_carmodel_trending` (`Trending`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carmodel`
--
ALTER TABLE `carmodel`
  MODIFY `ModelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
