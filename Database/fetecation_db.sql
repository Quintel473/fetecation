-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2026 at 03:52 AM
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
-- Database: `fetecation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `BookingID` int(11) NOT NULL,
  `Reference` varchar(20) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(40) DEFAULT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `BookingType` enum('Taxi','Tour') NOT NULL DEFAULT 'Taxi',
  `TourID` int(11) DEFAULT NULL,
  `TourSlug` varchar(80) DEFAULT NULL,
  `VehicleID` int(11) DEFAULT NULL,
  `PickupLocation` varchar(255) DEFAULT NULL,
  `DropoffLocation` varchar(255) DEFAULT NULL,
  `BookingDate` date NOT NULL,
  `BookingTime` time DEFAULT NULL,
  `NumberOfPassengers` int(11) NOT NULL DEFAULT 1,
  `SpecialRequests` text DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `PaymentOption` enum('full','deposit','on-day') NOT NULL DEFAULT 'on-day',
  `Source` enum('website','whatsapp','phone','walk-in','referral','other') NOT NULL DEFAULT 'website',
  `CustomPrice` decimal(10,2) DEFAULT NULL,
  `AdminNotes` text DEFAULT NULL,
  `Status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `SubmittedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `IP` varchar(45) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`BookingID`, `Reference`, `Name`, `Email`, `Phone`, `CustomerID`, `BookingType`, `TourID`, `TourSlug`, `VehicleID`, `PickupLocation`, `DropoffLocation`, `BookingDate`, `BookingTime`, `NumberOfPassengers`, `SpecialRequests`, `Notes`, `PaymentOption`, `Source`, `CustomPrice`, `AdminNotes`, `Status`, `SubmittedAt`, `IP`, `CreatedAt`) VALUES
(1, '80159E', 'Test User', 'fetecation9@gmail.com', '1234567890', 1, 'Taxi', NULL, 'taxi', NULL, 'Radisson Hotel', NULL, '2026-10-17', NULL, 3, '', '', 'on-day', 'website', NULL, NULL, 'Pending', '2026-10-10 17:14:46', '::1', '2026-10-10 15:38:25');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `CustomerID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Phone` varchar(30) NOT NULL,
  `AvatarPath` varchar(255) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `EmailVerified` tinyint(1) NOT NULL DEFAULT 0,
  `VerifyToken` varchar(64) DEFAULT NULL,
  `VerifyExpires` datetime DEFAULT NULL,
  `ResetToken` varchar(64) DEFAULT NULL,
  `ResetExpires` datetime DEFAULT NULL,
  `LastLoginAt` datetime DEFAULT NULL,
  `RememberToken` varchar(255) DEFAULT NULL,
  `RememberExpires` datetime DEFAULT NULL,
  `DeletedAt` datetime DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`CustomerID`, `FirstName`, `LastName`, `Phone`, `AvatarPath`, `Email`, `Password`, `EmailVerified`, `VerifyToken`, `VerifyExpires`, `ResetToken`, `ResetExpires`, `LastLoginAt`, `RememberToken`, `RememberExpires`, `DeletedAt`, `CreatedAt`) VALUES
(1, 'Quintel', 'Charles', '+1 ( 473 ) 456-0954', 'u1_a4bd255de6fe99f2.jpg', 'fetecation9@gmail.com', '$2y$10$SZnZbLNjPBweF.muBaGHTOuMa4QuCfdGVwQY6ae71uVCOToriH1Hu', 0, 'e189ce65503dc43fc258e12ffa9506204891b3db1317bc93a74bbfc5ed782b4b', '2026-10-12 15:52:21', NULL, NULL, '2026-10-10 19:38:49', '9003805b0187a8de77857ce202ae483d0449ab3a26b6d501ba9fcc01810224fc', '2026-11-10 00:50:47', NULL, '2026-10-10 13:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(30) DEFAULT NULL,
  `Subject` varchar(200) DEFAULT NULL,
  `Message` text NOT NULL,
  `Status` enum('Unread','Read') NOT NULL DEFAULT 'Unread',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `IP` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`MessageID`, `Name`, `Email`, `Phone`, `Subject`, `Message`, `Status`, `CreatedAt`, `IP`) VALUES
(1, 'Quintel Noah Charles', 'quintelcharles@ccagrenada.com', '4734560954', 'Hotel Pick Up', 'Hotel pickup', 'Unread', '2026-10-10 17:32:46', '::1'),
(2, 'Quintel Noah Charles', 'quintelcharles@ccagrenada.com', '4734560954', 'Hotel Pick Up', 'Hotel pickup', 'Unread', '2026-10-10 17:33:19', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Admin','Staff') NOT NULL DEFAULT 'Staff',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD UNIQUE KEY `uniq_reference` (`Reference`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `TourID` (`TourID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `idx_submitted_at` (`SubmittedAt`),
  ADD KEY `idx_status` (`Status`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerID`),
  ADD KEY `idx_verify_token` (`VerifyToken`),
  ADD KEY `idx_reset_token` (`ResetToken`),
  ADD KEY `idx_deleted_at` (`DeletedAt`),
  ADD KEY `idx_remember_token` (`RememberToken`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`MessageID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
