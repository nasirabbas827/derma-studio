-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2025 at 07:37 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `derma_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `about_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`about_id`, `title`, `content`) VALUES
(3, 'Our Mission', 'Providing advanced skincare solutions through technology and research.'),
(4, 'Who We Are', 'A team of dermatologists and AI specialists working to improve skin health.'),
(5, 'Our Vision', 'To revolutionize skincare with AI-driven diagnostics.'),
(6, 'Why Choose Us', 'Accurate, AI-powered skin assessments tailored to your needs.'),
(7, 'Our Technology', 'Leveraging machine learning to analyze skin health patterns.'),
(8, 'Customer Commitment', 'Dedicated to providing personalized skincare insights.'),
(9, 'Sustainability', 'Using eco-friendly methods to enhance skincare innovation.'),
(10, 'Research & Development', 'Continuously improving skin testing methodologies.'),
(11, 'Our Success Stories', 'Read testimonials from users who transformed their skincare journey.'),
(12, 'Get in Touch', 'Contact us for inquiries, partnerships, and support.');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-11-27 06:00:00', 'confirmed', '2024-05-19 08:51:56', '2024-11-26 05:15:35'),
(2, 1, 1, '2024-12-04 00:00:00', 'pending', '2024-11-26 05:32:31', '2024-11-26 05:32:31'),
(3, 1, 1, '2025-03-13 00:00:00', 'pending', '2025-03-04 06:37:59', '2025-03-04 06:37:59'),
(4, 2, 2, '2025-03-19 00:00:00', 'confirmed', '2025-03-04 15:57:15', '2025-03-04 15:57:41'),
(5, 2, 1, '2025-03-21 00:00:00', 'pending', '2025-03-21 05:42:52', '2025-03-21 05:42:52'),
(6, 2, 3, '2025-03-21 00:00:00', 'completed', '2025-03-21 05:43:10', '2025-03-21 06:21:18'),
(7, 2, 3, '2025-03-21 11:19:00', 'pending', '2025-03-21 06:19:29', '2025-03-21 06:19:29'),
(8, 2, 3, '2025-03-22 16:26:00', 'pending', '2025-03-21 06:20:46', '2025-03-21 06:20:46');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `clearance_certificate` varchar(255) NOT NULL,
  `certificate_number` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `specialization` varchar(255) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `username`, `password`, `email`, `profile_pic`, `full_name`, `clearance_certificate`, `certificate_number`, `status`, `created_at`, `updated_at`, `specialization`, `experience`) VALUES
(1, 'doc1', '$2y$10$WW1gaOUubbJWXmvCESeuU.JjB8kNkSXvE3OBTvk0UtSvFBX5lp4sC', 'nasiryt.827@gmail.com', 'uploads/download.png', 'NASIR ABBAS', 'uploads/images.jpg', '123', 'approved', '2024-05-19 08:12:35', '2024-11-26 09:03:41', 'Chest ', 3),
(2, 'testdoc1', '', 'testdoc@gmail.com', 'uploads/Blue Back.png', 'NASIR ABBAS', 'uploads/DATESHEET_Ayesha.pdf', 'daread33', 'approved', '2025-03-04 15:53:02', '2025-03-04 15:54:02', 'Skin Specialist', 5),
(3, 'dochaider', '$2y$10$P6UXng7tkheqDh6bmb6QuOgRxMXkxxGu4xE9b8or5e0G945cR4WKy', 'dochaider@gmail.com', 'uploads/Linkdin.png', 'Agnet Haider', 'uploads/thumbnail.jpeg', '43dsdd', 'approved', '2025-03-21 05:09:36', '2025-03-21 06:05:21', 'Chest ', 3);

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `feedback_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`feedback_id`, `appointment_id`, `patient_id`, `doctor_id`, `rating`, `comment`, `created_at`) VALUES
(3, 1, 1, 1, 2, 'gg', '2024-05-20 04:57:33'),
(4, 1, 1, 1, 5, 'The Doctor is very good', '2024-05-20 04:58:06'),
(5, 4, 2, 2, 2, 'ds', '2025-03-21 05:59:11'),
(6, 6, 2, 3, 2, 'ds', '2025-03-21 06:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `patient_records`
--

CREATE TABLE `patient_records` (
  `record_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `visit_date` datetime DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `lab_tests` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_records`
--

INSERT INTO `patient_records` (`record_id`, `patient_id`, `doctor_id`, `visit_date`, `diagnosis`, `prescription`, `lab_tests`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-05-20 09:25:00', 'None', 'None', 'None', 'None', '2024-05-20 04:41:08', '2024-05-20 04:47:00'),
(2, 1, 1, '2024-05-20 09:25:00', 'None', 'None', 'None', 'None', '2024-05-20 04:45:07', '2024-05-20 04:47:06'),
(6, 1, 1, '2024-05-20 09:25:00', 'None', 'None', 'None', 'None', '2024-05-20 04:45:09', '2024-05-20 04:45:09'),
(7, 1, 1, '2024-05-20 09:25:00', 'None', 'None', 'None', 'None', '2024-05-20 04:45:12', '2024-05-20 04:45:12'),
(11, 2, 3, '2025-03-21 11:13:00', 'NEWA', 'NEWA', 'NEWA', 'NEWA', '2025-03-21 06:13:35', '2025-03-21 06:13:35');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `created_at`, `updated_at`, `image`) VALUES
(1, 'Service One', 'Sercice One', '2024-05-20 05:46:41', '2025-03-21 05:21:09', 'services/1742534469_51182da6-fa86-4bd5-b758-0510bd8f0d8b.jpeg'),
(2, 'ii.	Skin care Treatments', 'ii.	Skin care Treatmentsii.	Skin care Treatmentsii.	Skin care Treatmentsii.	Skin care Treatments', '2025-03-04 15:49:43', '2025-03-21 05:21:18', 'services/1742534478_8be58b54-64d3-4ce2-bec5-8b40e7814f81.jpeg'),
(3, 'Skin care Treatments', 'ds', '2025-03-21 05:19:01', '2025-03-21 05:19:01', 'services/1742534341_ce6f15d1-a181-40b2-bdb3-7bd08f65ca0f.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `test_id` int(11) NOT NULL,
  `test_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `test_name`, `description`, `created_at`, `updated_at`) VALUES
(3, 'Skin Hydration Test', 'Measures skin moisture levels', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(4, 'UV Sensitivity Test', 'Determines skin reaction to UV exposure', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(5, 'Allergy Patch Test', 'Identifies possible skin allergens', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(6, 'Sebum Level Test', 'Analyzes oil production on the skin', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(7, 'Skin pH Test', 'Checks skin acidity for balanced care', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(8, 'Melanin Level Test', 'Determines skin pigmentation levels', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(9, 'Elasticity Test', 'Evaluates skin firmness and elasticity', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(10, 'Wrinkle Assessment', 'Analyzes fine lines and aging signs', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(11, 'Acne Prone Test', 'Identifies acne-causing factors', '2025-03-21 06:28:52', '2025-03-21 06:28:52'),
(12, 'Sensitive Skin Test', 'Detects skin sensitivity levels', '2025-03-21 06:28:52', '2025-03-21 06:28:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `cnic` varchar(20) NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `password`, `email`, `phone`, `mobile`, `cnic`, `state`, `city`, `age`) VALUES
(1, 'Nasir12', 'NASIR', 'ABBAS', '$2y$10$WW1gaOUubbJWXmvCESeuU.JjB8kNkSXvE3OBTvk0UtSvFBX5lp4sC', 'nasiryt.827@gmail.com', '3176526827', '3176526827', '3240284436381', 'BA', 'Salvador', 22),
(2, 'test patient', 'NASIR', 'Abbas', '$2y$10$nTy1nm28QjH.PI0q2XyjnOo2NCOloPn6z.mHkbiJP01zh42J1uiN6', 'test@gmail.com', '3176526826', '03266029050', '3240284436385', 'd', 'Lahore', 23);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`about_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `about_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patient_records`
--
ALTER TABLE `patient_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`),
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `feedbacks_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD CONSTRAINT `patient_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `patient_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
