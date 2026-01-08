-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 08:29 AM
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
-- Database: `attendance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `attendance_id` int(11) NOT NULL,
  `student_id` varchar(13) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL,
  `notes` text DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`attendance_id`, `student_id`, `attendance_date`, `status`, `notes`, `recorded_at`) VALUES
(1, '12345-67-8901', '2026-01-02', 'present', NULL, '2026-01-06 04:34:52'),
(2, '12345-67-8902', '2026-01-02', 'present', NULL, '2026-01-06 04:34:52'),
(3, '12345-67-8903', '2026-01-02', 'absent', 'Sick leave', '2026-01-06 04:34:52'),
(4, '12345-67-8904', '2026-01-02', 'late', 'Traffic', '2026-01-06 04:34:52'),
(5, '12345-67-8905', '2026-01-02', 'present', NULL, '2026-01-06 04:34:52'),
(6, '12345-67-8906', '2026-01-02', 'present', NULL, '2026-01-06 04:34:52'),
(7, '12345-67-8907', '2026-01-02', 'present', NULL, '2026-01-06 04:34:52'),
(8, '12345-67-8908', '2026-01-02', 'late', 'Transportation', '2026-01-06 04:34:52'),
(9, '12345-67-8901', '2026-01-03', 'present', NULL, '2026-01-06 04:34:52'),
(10, '12345-67-8902', '2026-01-03', 'late', NULL, '2026-01-06 04:34:52'),
(11, '12345-67-8903', '2026-01-03', 'absent', 'Still sick', '2026-01-06 04:34:52'),
(12, '12345-67-8904', '2026-01-03', 'present', NULL, '2026-01-06 04:34:52'),
(13, '12345-67-8905', '2026-01-03', 'present', NULL, '2026-01-06 04:34:52'),
(14, '12345-67-8906', '2026-01-03', 'present', NULL, '2026-01-06 04:34:52'),
(15, '12345-67-8907', '2026-01-03', 'absent', 'Family emergency', '2026-01-06 04:34:52'),
(16, '12345-67-8908', '2026-01-03', 'present', NULL, '2026-01-06 04:34:52'),
(17, '12345-67-8901', '2026-01-04', 'present', NULL, '2026-01-06 04:34:52'),
(18, '12345-67-8902', '2026-01-04', 'present', NULL, '2026-01-06 04:34:52'),
(19, '12345-67-8903', '2026-01-04', 'present', NULL, '2026-01-06 04:34:52'),
(20, '12345-67-8904', '2026-01-04', 'present', NULL, '2026-01-06 04:34:52'),
(21, '12345-67-8905', '2026-01-04', 'late', 'Transportation issue', '2026-01-06 04:34:52'),
(22, '12345-67-8906', '2026-01-04', 'present', NULL, '2026-01-06 04:34:52'),
(23, '12345-67-8907', '2026-01-04', 'present', NULL, '2026-01-06 04:34:52'),
(24, '12345-67-8908', '2026-01-04', 'present', NULL, '2026-01-06 04:34:52'),
(25, '12345-67-8901', '2026-01-05', 'present', NULL, '2026-01-06 04:34:52'),
(26, '12345-67-8902', '2026-01-05', 'present', NULL, '2026-01-06 04:34:52'),
(27, '12345-67-8903', '2026-01-05', 'present', NULL, '2026-01-06 04:34:52'),
(28, '12345-67-8904', '2026-01-05', 'late', 'Heavy traffic', '2026-01-06 04:34:52'),
(29, '12345-67-8905', '2026-01-05', 'present', NULL, '2026-01-06 04:34:52'),
(30, '12345-67-8906', '2026-01-05', 'present', NULL, '2026-01-06 04:34:52'),
(31, '12345-67-8907', '2026-01-05', 'present', NULL, '2026-01-06 04:34:52'),
(32, '12345-67-8908', '2026-01-05', 'present', NULL, '2026-01-06 04:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `section_name`, `grade_level`, `room_number`, `created_at`) VALUES
(1, 'BSCS 2A', '2nd Year', 'CS-101', '2026-01-06 04:34:52'),
(2, 'BSCS 2B', '2nd Year', 'CS-102', '2026-01-06 04:34:52'),
(3, 'BSCS 2C', '2nd Year', 'CS-103', '2026-01-06 04:34:52'),
(4, 'BSCS 3A', '3rd Year', 'CS-201', '2026-01-06 04:34:52'),
(5, 'BSCS 3B', '3rd Year', 'CS-202', '2026-01-06 04:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(13) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `section_id` int(11) NOT NULL,
  `enrollment_date` date DEFAULT curdate(),
  `status` enum('active','dropped') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `section_id`, `enrollment_date`, `status`) VALUES
('12345-67-8901', 'Juan', 'Dela Cruz', 1, '2026-01-06', 'active'),
('12345-67-8902', 'Maria', 'Santos', 1, '2026-01-06', 'active'),
('12345-67-8903', 'Pedro', 'Reyes', 2, '2026-01-06', 'active'),
('12345-67-8904', 'Ana', 'Garcia', 2, '2026-01-06', 'active'),
('12345-67-8905', 'Jose', 'Martinez', 3, '2026-01-06', 'active'),
('12345-67-8906', 'Sofia', 'Lopez', 3, '2026-01-06', 'active'),
('12345-67-8907', 'Miguel', 'Fernandez', 4, '2026-01-06', 'active'),
('12345-67-8908', 'Isabel', 'Torres', 4, '2026-01-06', 'active'),
('12345-67-8909', 'Carlos', 'Ramos', 5, '2026-01-06', 'active'),
('12345-67-8910', 'Elena', 'Morales', 5, '2026-01-06', 'active'),
('12345-67-8911', 'Rico', 'Valdez', 1, '2026-01-06', 'active'),
('12345-67-8912', 'Liza', 'Navarro', 2, '2026-01-06', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`attendance_date`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`),
  ADD UNIQUE KEY `section_name` (`section_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `section_id` (`section_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
