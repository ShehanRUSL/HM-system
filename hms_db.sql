-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2025 at 12:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appt_date` date NOT NULL,
  `appt_time` time NOT NULL,
  `status` enum('booked','cancelled','completed') DEFAULT 'booked',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appt_date`, `appt_time`, `status`, `notes`, `created_at`) VALUES
(4, 16, 18, '2025-08-28', '22:53:00', 'booked', NULL, '2025-08-26 12:18:06'),
(5, 23, 20, '2025-09-01', '17:45:00', 'booked', NULL, '2025-08-31 09:13:56'),
(6, 23, 22, '2025-09-06', '14:47:00', 'booked', NULL, '2025-08-31 09:14:15'),
(7, 23, 21, '2025-09-04', '14:50:00', 'booked', NULL, '2025-08-31 09:14:23'),
(8, 19, 20, '2025-08-31', '14:47:00', 'booked', NULL, '2025-08-31 09:15:06'),
(9, 19, 22, '2025-09-06', '20:45:00', 'booked', NULL, '2025-08-31 09:15:13'),
(10, 19, 18, '2025-09-04', '14:51:00', 'booked', NULL, '2025-08-31 09:15:20'),
(11, 16, 21, '2025-09-05', '14:48:00', 'booked', NULL, '2025-08-31 09:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `license_no` varchar(50) NOT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `specialization`, `license_no`, `approval_status`) VALUES
(4, 18, 'VP', '', 'approved'),
(5, 20, 'surgeon', '4554', 'approved'),
(6, 21, 'MD', '456', 'approved'),
(7, 22, 'Doc', '1234', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `user_id` int(11) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`user_id`, `gender`, `dob`, `address`) VALUES
(16, NULL, NULL, NULL),
(19, 'Male', '2002-07-30', 'anuradapura'),
(23, 'Male', '1985-05-01', 'kuliyapitiya');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('admin','doctor','patient') NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `email`, `password_hash`, `full_name`, `phone`, `is_active`, `created_at`) VALUES
(11, 'admin', 'admin@gmail.com', '$2y$10$0iwl8Py5qkXq6RxxU.XuVOyeTlLCT6qOe4Ywj7e.IYIf.tqvdMrH.', 'Admin User', '', 1, '2025-08-17 05:24:48'),
(16, 'patient', 'shehan@gmail.com', '$2y$10$em7xBDku8fYN4rusheO43.8VBahmR.ANckcLASHeTmPVY27kafs3i', 'shehan', '0750004589', 1, '2025-08-26 10:14:57'),
(17, 'admin', 'shehanadmin@gmail.com', '$2y$10$.AHpR2T4az1ho5.HIKvdduRuyGfeJnem59d/hO2goplehiyd93vbm', 'shehan', '0750004589', 1, '2025-08-26 10:15:25'),
(18, 'doctor', 'shehandoctor@gmail.com', '$2y$10$ypjQ6bFTrx5u7rJ8B0qaFeJUmJgMJXseZXlGOwXnHJPK.iqq9jT1.', 'shehan', '0711213456', 1, '2025-08-26 10:16:03'),
(19, 'patient', 'shehanhasantha@gmail.com', '$2y$10$qLY5Gik/lMxfCIjJRckYBu3gd9lJLzJGWWs8H/b.92euQ8mzhgacK', 'shehan hasantha chandrasiri', '0780004589', 1, '2025-08-31 09:08:38'),
(20, 'doctor', 'shehandoctor1@gmail.com', '$2y$10$gtZKOVl2QweZCKRTE1HnPegUU.2ajHe.CoffNE3RK/h8PYeabKicm', 'M.D.Shehan', '0761434568', 1, '2025-08-31 09:09:55'),
(21, 'doctor', 'shanika@gmail.com', '$2y$10$vrlhPVJr5LkrCfDrzHKgbepwwCyrhk29gNeTmrDKBWsUWsY76.r/S', 'Shanika Sewwandi', '0711203456', 1, '2025-08-31 09:10:46'),
(22, 'doctor', 'sarath@gmail.com', '$2y$10$/ePGl5GD.gnklDlzxpmHrev3VqUsO/VZQE6LW.soO60IOgPolcSHK', 'Sarath Chandrasiri', '0761299456', 1, '2025-08-31 09:11:42'),
(23, 'patient', 'banuka@gmail.com', '$2y$10$nFUrhIEaosmB76zxPZxq/O0YQ4boWxJ7Qc7Sktuh5n30rEnrMsEiC', 'banuka', '0761235456', 1, '2025-08-31 09:12:34');

-- --------------------------------------------------------

--
-- Table structure for table `visit_notes`
--

CREATE TABLE `visit_notes` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `summary` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_no` (`license_no`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visit_notes`
--
ALTER TABLE `visit_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `visit_notes`
--
ALTER TABLE `visit_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visit_notes`
--
ALTER TABLE `visit_notes`
  ADD CONSTRAINT `visit_notes_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visit_notes_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visit_notes_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
