-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 03:29 PM
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
  `MultiStops` text DEFAULT NULL,
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
(1, 'Quintel', 'Charles', '+1 ( 473 ) 456-0954', 'u1_bbe192d6dc5862f1.jpg', 'qunitelcharles@gmail.com', '$2y$10$SZnZbLNjPBweF.muBaGHTOuMa4QuCfdGVwQY6ae71uVCOToriH1Hu', 0, 'e189ce65503dc43fc258e12ffa9506204891b3db1317bc93a74bbfc5ed782b4b', '2026-10-12 15:52:21', NULL, NULL, '2026-10-27 20:58:07', NULL, NULL, NULL, '2026-10-10 13:52:21');

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

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `type` enum('tour','party') NOT NULL,
  `name` varchar(150) NOT NULL,
  `tag` varchar(50) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `emoji` varchar(10) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `group_size` varchar(100) DEFAULT NULL,
  `price_display` varchar(100) DEFAULT NULL,
  `price_amount` decimal(10,2) DEFAULT NULL,
  `price_type` enum('per_person','per_group','custom') DEFAULT 'per_person',
  `pickup` varchar(150) DEFAULT NULL,
  `multi_stop_price` decimal(10,2) DEFAULT NULL,
  `about` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`about`)),
  `included` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`included`)),
  `bring` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bring`)),
  `itinerary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`itinerary`)),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `how_it_works` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`how_it_works`)),
  `ideas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ideas`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `slug`, `type`, `name`, `tag`, `tagline`, `emoji`, `image`, `duration`, `group_size`, `price_display`, `price_amount`, `price_type`, `pickup`, `multi_stop_price`, `about`, `included`, `bring`, `itinerary`, `updated_at`, `how_it_works`, `ideas`) VALUES
(1, 'island-highlights', 'tour', 'Tipsy Tourist Tour', 'POPULAR', 'See the best of the island in one unforgettable day.', '🌅', '/fetecation/images/tours/island-highlights-1.jpg', '4–6 hours', 'Up to 6 people', 'From $120 / person', 120.00, 'per_person', 'Hotel or cruise port', NULL, '[\"Discover the island\'s most beautiful spots on a relaxed, private tour designed around you.\",\"Visit scenic viewpoints, golden beaches and cultural landmarks while your local guide shares stories about the island\'s history and way of life.\",\"Perfect for first-time visitors who want to see it all without the stress of planning.\"]', '[\"Air-conditioned private vehicle\",\"Local English-speaking guide\",\"Bottled water throughout the day\",\"Hotel or cruise port pickup & drop-off\",\"Flexible stops for photos\"]', '[\"Comfortable walking shoes\",\"Sunscreen & sunglasses\",\"Swimsuit & towel\",\"Camera or phone\",\"Cash for souvenirs & snacks\"]', '[{\"time\":\"9:00 AM\",\"title\":\"Pickup from your hotel\",\"text\":\"Your guide meets you at your accommodation or cruise port.\"},{\"time\":\"9:30 AM\",\"title\":\"Scenic viewpoint stop\",\"text\":\"Panoramic photo stop overlooking the coastline.\"},{\"time\":\"11:00 AM\",\"title\":\"Beach time\",\"text\":\"Relax on a pristine beach with time to swim.\"},{\"time\":\"1:00 PM\",\"title\":\"Local lunch (optional)\",\"text\":\"Stop at a favourite local spot for authentic cuisine.\"},{\"time\":\"3:00 PM\",\"title\":\"Cultural landmark visit\",\"text\":\"Learn about the island\'s history at a key landmark.\"},{\"time\":\"5:00 PM\",\"title\":\"Return to your accommodation\",\"text\":\"Drop-off at your hotel or cruise port.\"}]', '2026-10-28 00:09:02', NULL, NULL),
(2, 'nature-adventure', 'tour', 'Nature & Adventure', 'EXPERIENCE', 'Get off the beaten path and into the wild.', '🌴', '/fetecation/images/tours/nature-adventure-1.jpg', '5–7 hours', 'Up to 6 people', 'From $150 / person', 150.00, 'per_person', 'Hotel or cruise port', NULL, '[\"For those who want more than a beach day \\u2014 this tour takes you deep into the island\'s natural beauty.\",\"Hike to hidden waterfalls, swim in natural pools and discover local flora and fauna with a guide who knows every trail.\",\"A perfect blend of adventure, scenery and local culture.\"]', '[\"Air-conditioned private vehicle\",\"Local adventure guide\",\"Bottled water & snacks\",\"Entrance fees to natural sites\",\"Hotel or cruise port pickup & drop-off\"]', '[\"Sturdy walking or hiking shoes\",\"Swimsuit & towel\",\"Sunscreen & insect repellent\",\"Change of clothes\",\"Camera or phone\"]', '[{\"time\":\"8:30 AM\",\"title\":\"Pickup from your hotel\",\"text\":\"Meet your guide and head inland.\"},{\"time\":\"9:30 AM\",\"title\":\"Rainforest trail hike\",\"text\":\"Guided hike through lush tropical vegetation.\"},{\"time\":\"11:00 AM\",\"title\":\"Hidden waterfall swim\",\"text\":\"Cool off in a natural freshwater pool.\"},{\"time\":\"12:30 PM\",\"title\":\"Scenic picnic lunch\",\"text\":\"Enjoy a packed lunch overlooking the valley.\"},{\"time\":\"2:30 PM\",\"title\":\"Local farm or rum stop\",\"text\":\"Taste local produce or rum at a family-run spot.\"},{\"time\":\"5:00 PM\",\"title\":\"Return to your accommodation\",\"text\":\"Drop-off at your hotel or cruise port.\"}]', '2026-10-28 00:09:02', NULL, NULL),
(3, 'custom-island', 'tour', 'Custom Island Experience', 'CUSTOM', 'Your island, your way — built around what you love.', '🌅', '/fetecation/images/tours/custom-island-1.jpg', 'Custom schedule', 'Up to 12 people', 'Custom quote', NULL, 'custom', 'Anywhere on the island', NULL, '[\"Want something completely different? Tell us what you\'re dreaming of and we\'ll build the perfect day around it \\u2014 no fixed itinerary, no rushing, no compromises.\",\"Wedding parties, family reunions, photography trips, food tours, sunset cruises, corporate retreats \\u2014 if it\'s possible on the island, we\'ll make it happen.\",\"Just share your ideas, group size and preferred date and we\'ll send you a tailored itinerary and price within 24 hours.\"]', '[\"A fully personalised itinerary built around your group\",\"Dedicated local guide for the entire experience\",\"Air-conditioned private vehicle (sedan, SUV or van)\",\"Bottled water and refreshments throughout the day\",\"Pickup and drop-off anywhere on the island\",\"Flexible timing \\u2014 you set the pace\",\"Coordination with restaurants, venues or activity providers\"]', '[\"Your ideas, wishlist and must-see spots\",\"Group size and any special requests\",\"Comfortable shoes for the day\",\"Sunscreen and a hat\",\"Camera or phone (you\'ll want photos)\"]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(4, 'friday-night-lime', 'party', 'Friday Night Lime Party Bus', 'PARTY PACKAGE', 'Club hopping, drinks, and vibes all night long.', '🍾', '/fetecation/images/parties/friday-lime.jpg', '5 hours', 'Up to 15 people', '$350 / group', 350.00, 'per_group', 'Hotel or cruise port', 25.00, '[\"Let us handle the driving while you handle the party!\",\"We pick up your group, take you to the hottest local spots, keep the party going in the vehicle, and get everyone home safely.\"]', '[\"Private party vehicle\",\"Dedicated driver for the night\",\"Custom multi-stop itinerary\",\"Ice and cooler provided\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(5, 'saturday-beach-bash', 'party', 'Saturday Sunset Beach Bash', 'PARTY PACKAGE', 'Golden hour vibes, live music, and bonfires.', '🔥', '/fetecation/images/parties/beach-bash.jpg', '4 hours', 'Up to 20 people', '$400 / group', 400.00, 'per_group', 'Hotel or cruise port', 20.00, '[\"Experience the best sunset views on the island with your crew.\",\"We provide the transport, the cooler, and the driver. You bring the vibes.\"]', '[\"Private party vehicle\",\"Driver on standby\",\"Cooler with ice\",\"Beach drop-off and pickup\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(6, 'ladies-night-out', 'party', 'Ladies Night Out', 'PARTY PACKAGE', 'VIP treatment, cocktails, and the best clubs.', '🍸', '/fetecation/images/parties/ladies-night.jpg', '5 hours', 'Up to 12 people', '$300 / group', 300.00, 'per_group', 'Hotel or cruise port', 15.00, '[\"Grab your girls and let us take you on a VIP tour of the island nightlife.\",\"Complimentary bubbly on arrival to get the night started right.\"]', '[\"Private SUV or van\",\"Professional driver\",\"Complimentary bubbly\",\"Multi-stop club hopping\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(7, 'boat-party-transfer', 'party', 'Boat Party Shuttle', 'PARTY PACKAGE', 'Direct transport to the marina and back.', '🛥️', '/fetecation/images/parties/boat-party.jpg', '6 hours', 'Up to 25 people', '$500 / group', 500.00, 'per_group', 'Hotel or cruise port', 30.00, '[\"Heading to a boat party? Avoid the hassle of parking and taxis.\",\"We handle all the ground transport so you can focus on the sea.\"]', '[\"Large party bus or van\",\"Round-trip transport\",\"Driver waits for your return\",\"Cooler provided\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(8, 'bachelor-party', 'party', 'Bachelor Party Blowout', 'PARTY PACKAGE', 'The ultimate guys trip around the island.', '🥃', '/fetecation/images/tours/img_6ae13f498ceb8.jpg', '6 hours', 'Up to 15 people', '$450 / group', 450.00, 'per_group', 'Hotel or cruise port', 25.00, '[\"Celebrate the groom in style. From beach bars to clubs, we know the best spots.\",\"We keep the party safe and moving all night long.\"]', '[\"Private party vehicle\",\"Local guide\\/driver\",\"Cooler with ice\",\"Custom itinerary\"]', '[]', '[]', '2026-10-28 00:18:17', NULL, NULL),
(9, 'bachelorette-party', 'party', 'Bachelorette Party Bash', 'PARTY PACKAGE', 'Unforgettable fun for the bride-to-be.', '👑', '/fetecation/images/parties/bachelorette.jpg', '5 hours', 'Up to 12 people', '$350 / group', 350.00, 'per_group', 'Hotel or cruise port', 20.00, '[\"Give the bride the send-off she deserves.\",\"We coordinate with clubs and restaurants to make sure your group gets VIP treatment.\"]', '[\"Private party vehicle\",\"Dedicated driver\",\"Custom party playlist\",\"Multi-stop transport\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(10, 'birthday-party', 'party', 'Birthday Party Cruiser', 'PARTY PACKAGE', 'Celebrate another year in island style.', '🎂', '/fetecation/images/parties/birthday.jpg', '4 hours', 'Up to 20 people', '$300 / group', 300.00, 'per_group', 'Hotel or cruise port', 15.00, '[\"Whether it is a sweet 16 or a 50th, we make sure the birthday star arrives in style.\",\"Perfect for getting the whole family or friend group to the party safely.\"]', '[\"Private party vehicle\",\"Driver on standby\",\"Custom pickup and drop-offs\",\"Balloon decoration available\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(11, 'carnival-jouvert', 'party', 'Carnival J\'ouvert Express', 'PARTY PACKAGE', 'Get to the street party before sunrise.', '🎉', '/fetecation/images/parties/carnival.jpg', '8 hours', 'Up to 20 people', '$600 / group', 600.00, 'per_group', 'Hotel or cruise port', 35.00, '[\"Carnival is the biggest party of the year. Beat the traffic and the parking nightmare.\",\"We drop you right at the action and pick you up when you are ready to go home.\"]', '[\"Party bus transport\",\"Early morning pickup\",\"Cooler provided\",\"Flexible return time\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL),
(12, 'karaoke-night-cruise', 'party', 'Karaoke Night Cruise', 'PARTY PACKAGE', 'Sing your heart out as we cruise the island.', '🎤', '/fetecation/images/parties/karaoke.jpg', '4 hours', 'Up to 12 people', '$300 / group', 300.00, 'per_group', 'Hotel or cruise port', 15.00, '[\"No crowd, no stage fright. Just you, your friends, and your favorite songs.\",\"Our driver provides the mic and the aux cord. You provide the entertainment.\"]', '[\"Private party vehicle\",\"Bluetooth sound system\",\"Wireless microphones\",\"Cooler with ice\"]', '[]', '[]', '2026-10-28 00:09:02', NULL, NULL);

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
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

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
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
