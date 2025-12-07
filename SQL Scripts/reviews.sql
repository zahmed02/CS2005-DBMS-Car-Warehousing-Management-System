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
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ModelID` int(11) NOT NULL,
  `Rating` tinyint(4) NOT NULL,
  `Comment` varchar(255) NOT NULL,
  `ReviewDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `UserID`, `ModelID`, `Rating`, `Comment`, `ReviewDate`) VALUES
(9, 3, 1, 5, 'I’ve had my Integra Type S for a few weeks now and it’s been a great experience so far. The car feels sharp and responsive, especially in Sport+ mode. The manual transmission is smooth and really fun to drive, which is hard to find these days. Interior is', '2025-07-24 08:37:33'),
(10, 4, 3, 5, 'I’ve been driving the Han EV for a few months and it’s been a really solid experience. The design is sleek and premium-looking, and the interior feels modern with good tech and materials. Acceleration is smooth and surprisingly quick for an EV of this siz', '2025-07-24 08:39:31'),
(11, 5, 4, 5, 'The Ocean Ultra has a cool design and definitely stands out on the road. The panoramic roof and rotating screen are fun features, and there’s a lot of space inside. Ride quality is decent, and the torque is solid for city driving. Still feels like a new b', '2025-07-24 08:39:41'),
(12, 6, 5, 5, 'Genesis really nailed it with the G80 Electrified. It looks elegant and drives super smooth—feels like a proper luxury car, not just an EV version of a gas model. The interior is gorgeous, with high-end materials and a quiet cabin. It’s quick, but more ab', '2025-07-24 08:40:06'),
(13, 7, 6, 5, 'The Jesko Absolut is an absolute beast. I had the chance to experience one recently, and it’s hard to put into words just how extreme this car is. The acceleration is unreal—smooth but brutally fast. Everything about it feels purpose-built', '2025-07-24 08:40:23'),
(14, 8, 7, 5, 'The Lucid Air Grand Touring is seriously impressive. It has insane range, luxurious interior, and performance that rivals much more expensive cars. Driving it feels futuristic—the acceleration is effortless, and the cabin is quiet and spacious. The tech i', '2025-07-24 08:40:34'),
(15, 5, 10, 5, '“I’ve had my NSX Type S for just over three years now, and I still find myself grinning every time I get behind the wheel. It’s not just fast—it’s precise. The hybrid twin-turbo V6 setup delivers a unique blend of raw power and electric torque that makes ', '2025-07-29 17:22:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `idx_reviews_model` (`ModelID`),
  ADD KEY `idx_reviews_rating` (`Rating`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
