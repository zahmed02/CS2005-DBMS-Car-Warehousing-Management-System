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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `UserImage` varchar(255) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `PhoneNumber` varchar(30) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `CreatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `UserImage`, `FullName`, `Email`, `Password`, `PhoneNumber`, `Address`, `CreatedAt`) VALUES
(4, 'uploads/istockphoto-1355051102-612x612.jpg', 'Muhammad Zayan Iqbal', 'zayan.iqbal@outlook.com', '$2y$10$BzX1cVvUy.Fv1/MSEHD0tOZwf5UfaKgBE71ZvU4VsL4KYDH.Gfkay', '0300-2458999', 'Flat 12B, Gulshan View, Karachi', '2025-07-22 09:48:01'),
(5, 'uploads/Untitled design (3).png', 'Emily Grace Thompson', 'emilygt@icloud.com', '$2y$10$woBBZLPazO433I4saoRDseHXEDW1j5P4O/fuKR3kzc2/FpK3RbuLu', '044-8815224', '88 Hillview Rd, Sydney, Australia', '2025-07-22 09:48:31'),
(6, 'uploads/istockphoto-1369199360-612x612.jpg', 'Rajeev Kumar Sharma', 'rajeevkumar@gmail.com', '$2y$10$zJmyIlfWYWKQRNPUhavkH.HcygARL11mHqjap/f.uFisIyU2jxkFe', '+91-8765443210', 'C-47, Sector 22, Noida, UP, India', '2025-07-22 09:48:52'),
(7, 'uploads/young-serious-asian-female-ceo-lawyer-businesswoman-sitting-at-desk-working-typing-on-laptop-computer-in-contemporary-corporation-office-business-technologies-concept-free-photo.png', 'Fatima Noor', 'fatima.noor@humtv.com.pk', '$2y$10$wvr4ZbAyvE2SCbRSWzI.yu0NHBWCtOPh3xnIUVcpQ1pj.Tn4WqGay', '0312-7896541', 'House 57-A, Phase 6, DHA Lahore', '2025-07-22 09:49:14'),
(8, 'uploads/360_F_55608895_s70yiwyB2yNS2nxEF2eO4mVHYuvQrxfZ.jpg', 'James Oliver Wright', 'jameswright@protonmail.com', '$2y$10$zFBNhqHwjdv6cyKjUwyWlef8VgQ0/FMyJRs.O7GWm5v4ecvKYlWv6', '020-84441551', '12B Baker Street, London', '2025-07-22 09:49:37'),
(9, 'uploads/Untitled design (4).png', 'Ayesha Khan', 'ayesha.khan@yahoo.com', '$2y$10$fLI.W/5AFWjlKcYodAYJOuPY0eie148RVq76gcmJnZ5rsycfdl.Oq', '0333-0011224', 'D-19, Satellite Town, Rawalpindi', '2025-07-22 09:49:56'),
(10, 'uploads/istockphoto-1497069312-612x612.jpg', 'Takeshi Yamamoto', 'takeshi@softbank.jp', '$2y$10$hMz6v.yCGU02sp5A9o3k5eKBN0lCCvVkIIjdZzf3.VQgi6ywgk9HC', '080-1234-5678', '3-2-1, Tokyo Midtown, Akasaka, Tokyo', '2025-07-22 09:50:22'),
(11, 'uploads/focused-young-german-male-office-worker-sits-at-desk-at-home-workplace-distracted-from-laptop-screen-free-photo.jpg', 'Carlos Eduardo Ramos', 'carlos.ramos@uol.com.br', '$2y$10$1F.LlwTkjbpRicc3B7GIbegXkSqse0v1MioCXaUhof0fLGOijn5vq', '+55 11 94562-1187', 'Rua Oscar Freire, São Paulo, Brazil', '2025-07-22 09:50:57'),
(12, 'uploads/360_F_358930412_rodvr4vvY4LG0bUG8MKC3wwCZhWGozcW.jpg', 'Hannah Müller', 'hannah.mueller@mail.de', '$2y$10$MKwVur39TxrB8lkdQMKvquVW0Tce7538sasNrfozm0fAXdOeYbzBW', '+49 1512 556788', 'Berliner Straße 23, Berlin, Germany', '2025-07-22 09:51:24'),
(14, 'uploads/Untitled design (1).png', 'Willow Liu', 'Willow1999@LiuCorp.edu.uk', '$2y$10$Pw80NdobRHmCIYSXhCVJa.U15cIz4UAHvfArYsWek2OX.thK4Hwie', '+73 11 93662-1047', '88 Tier Street, Birmingham, United Kingdom', '2025-07-29 15:08:44'),
(15, 'uploads/Untitled design (2).png', 'Alexandra Liu Rovers', 'Alex@hum.tv', '$2y$10$xOYcgIdUJUvp8GdX5TuBAughZmdp/5sjkmDsVqfw4.3PPjh73S8R.', '+43 14 96782-1947', 'Surrey GU1 1AA', '2025-07-29 19:24:44'),
(16, 'uploads/CS2005 DBS A2 Q2.drawio.png', 'sad', 'zaysdn.iqbal@outlook.com', '$2y$10$4L421lzq0k7Z8KsZJZ0Qhuj.PKO7r6hQ/G0NnrKka9LVqSgvlPxPS', '454654654-fds', 'Trafalgar Sqsguare, U-Apartment Complex', '2025-10-27 04:44:13'),
(17, '', 'Admin User', 'admin@impel.com', '$2y$10$72eYCZuCKcNw7iH54PO8RuE8Fwt3CrsEaqopNfj1QCSVXpt7FNHIy', '1234567890', 'Test Address', '2025-11-23 15:54:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `idx_users_email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
