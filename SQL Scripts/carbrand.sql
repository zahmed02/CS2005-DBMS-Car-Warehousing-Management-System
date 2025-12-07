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
-- Table structure for table `carbrand`
--

CREATE TABLE `carbrand` (
  `BrandID` int(11) NOT NULL,
  `BrandName` varchar(255) NOT NULL,
  `LogoImage` varchar(255) NOT NULL,
  `FoundedYear` datetime NOT NULL,
  `Country` varchar(255) NOT NULL,
  `CEO` varchar(255) NOT NULL,
  `Affiliations` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carbrand`
--

INSERT INTO `carbrand` (`BrandID`, `BrandName`, `LogoImage`, `FoundedYear`, `Country`, `CEO`, `Affiliations`) VALUES
(10, 'Acura', 'uploads/carbrand_logos/687f6c3cb6ceb_MxAiTt.jpg', '2025-07-22 15:39:00', 'Japan', 'Toshihiro Mibe', 'Lexus'),
(11, 'Rimac', 'uploads/carbrand_logos/687f6e41c885c_Rimac_Automobili_Logo_horizontally_color-dark.png', '2025-07-22 15:55:00', 'Croatia', 'Mate Rimac', 'Bugatti Rimac, Porsche AG'),
(12, 'Lucid Motors', 'uploads/carbrand_logos/687f702e7d20e_CvPS62cVUAYVL2S.jfif', '2025-07-22 16:03:00', 'United States', 'Peter Rawlinson', 'Public Investment Fund (Saudi Arabia)'),
(13, 'Genesis', 'uploads/carbrand_logos/687f707d993bd_hyundai-genesis-watered-logo-vjjosvrbvdium9cp.jpg', '2025-07-22 16:05:00', 'South Korea', 'Jaehoon Chang', 'Hyundai Motor Group'),
(14, 'Koenigsegg', 'uploads/carbrand_logos/687f709a31d54_stock-photo-london-uk-circa-july-koenigsegg-logo-on-the-back-of-an-agera-r-in-black-and-white-1907541007.jpg', '2025-07-22 16:05:00', 'Sweden', 'Christian von Koenigsegg', 'NEVS'),
(15, 'BYD Auto', 'uploads/carbrand_logos/687f70b3565d5_logo-byd-blanc-1024x431-1.png', '2025-07-22 16:06:00', 'China', 'Wang Chuanfu', 'BYD Company, Toyota'),
(16, 'Polestar', 'uploads/carbrand_logos/687f70cf5d38a_Polestar_logo_2020.svg.png', '2025-07-22 16:06:00', 'Sweden', 'Thomas Ingenlath', 'Volvo Cars, Geely'),
(17, 'Tata Motors', 'uploads/carbrand_logos/687f70ea6d64e_TATA.png', '2025-07-22 16:07:00', 'India', 'Guenter Butschek', 'Tata Group, Jaguar Land Rover'),
(18, 'Fisker', 'uploads/carbrand_logos/687f7102b2cea_Fisker Car and Trucks.png', '2025-07-22 16:07:00', 'United States', 'Henrik Fisker', 'Magna Steyr'),
(19, 'Venturi', 'uploads/carbrand_logos/687f711b4c60f_Venturi.jpg', '2025-07-22 16:07:00', 'Monaco', 'Gildo Pallanca Pastor', 'Venturi Formula E Team'),
(23, 'Toyota', 'uploads/carbrand_logos/68888489cca5c_Toyota-logo.png', '2025-07-29 13:20:00', 'Japan', 'Koji Sato', 'Tsuho Corporations'),
(27, 'Nissan', 'uploads/carbrand_logos/692f3433715c7_images.png', '2024-07-02 23:46:00', 'Japan', 'Ivan Espinosa', '{\"parent\":\"23\",\"subsidiaries\":[\"Scion\"],\"partnerships\":[\"Renault\"],\"alliances\":[\"Stellantis\"]}'),
(28, 'Hyundai Nishat', 'uploads/carbrand_logos/692fbf6d8b3a7_Hyundai-Logo-500x281.png', '2017-06-15 09:35:00', 'Pakistan', 'Hassan Mansha', 'Hyundai Motor Group');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carbrand`
--
ALTER TABLE `carbrand`
  ADD PRIMARY KEY (`BrandID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carbrand`
--
ALTER TABLE `carbrand`
  MODIFY `BrandID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
