-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 02:55 PM
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
-- Database: `credit`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminc`
--

CREATE TABLE `adminc` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `branch` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminc`
--

INSERT INTO `adminc` (`id`, `admin_id`, `password`, `email`, `branch`) VALUES
(1, '138', '138', 'muddanavenkatesh87@gmail.com', 'Information Technology');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` varchar(50) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `room` varchar(50) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `section` varchar(10) NOT NULL,
  `year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `academic_year` varchar(9) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `course_name`, `course_code`, `course_id`, `teacher_id`, `room`, `day`, `start_time`, `end_time`, `section`, `year`, `semester`, `academic_year`, `department`) VALUES
('1', 'Basics of Electrical and Electronics Engineering', '22EE101', 3, 138, 'ATF-12', 'Wednesday', '14:00:00', '15:00:00', 'B', 2, 2, '2024-2025', 'Information Technology');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `L` int(11) DEFAULT 0,
  `T` int(11) DEFAULT 0,
  `P` int(11) DEFAULT 0,
  `SL` int(11) DEFAULT 0,
  `C` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `year`, `semester`, `code`, `title`, `L`, `T`, `P`, `SL`, `C`) VALUES
(1, 1, 1, '22MT103', 'Linear Algebra and Ordinary Differential Equations', 3, 2, 0, 0, 4),
(2, 1, 1, '22PY105', 'Semiconductor Physics and Electromagnetics', 2, 0, 2, 0, 3),
(3, 1, 1, '22EE101', 'Basics of Electrical and Electronics Engineering', 2, 0, 2, 0, 3),
(4, 1, 1, '22IT102', 'IT Workshop and Tools', 0, 2, 4, 0, 3),
(5, 1, 1, '22TP103', 'Programming in C', 2, 0, 4, 0, 4),
(6, 1, 1, '22EN102', 'English Proficiency and Communication Skills', 0, 0, 2, 0, 1),
(7, 1, 1, '22SA101', 'Physical Fitness, Sports and Games – I', 0, 0, 3, 0, 1),
(8, 1, 1, '22TP101', 'Constitution of India', 0, 2, 0, 0, 1),
(9, 1, 2, '22MT106', 'Algebra', 3, 2, 0, 0, 4),
(10, 1, 2, '22MT107', 'Discrete Mathematical Structures', 2, 2, 0, 0, 3),
(11, 1, 2, '22TP104', 'Basic Coding Competency', 0, 1, 3, 0, 2),
(12, 1, 2, '22ME101', 'Engineering Graphics', 2, 0, 2, 0, 3),
(13, 1, 2, '22EN104', 'Technical Communication English', 1, 2, 2, 0, 3),
(14, 1, 2, '22IT101', 'Web Technologies', 1, 2, 2, 0, 3),
(15, 1, 2, '22SA103', 'Physical Fitness, Sports and Games – II', 0, 0, 3, 0, 1),
(16, 2, 1, '22ST202', 'Probability and Statistics', 3, 2, 0, 0, 4),
(17, 2, 1, '22CT201', 'Environmental Studies', 1, 1, 0, 0, 1),
(18, 2, 1, '22TP201', 'Data Structures', 2, 2, 2, 0, 4),
(19, 2, 1, '22MS201', 'Management Science', 2, 2, 0, 0, 3),
(20, 2, 1, '22IT201', 'Database Systems', 3, 0, 2, 0, 4),
(21, 2, 1, '22IT202', 'Digital Logic Design', 2, 2, 0, 0, 3),
(22, 2, 1, '22IT203', 'Object Oriented Programming', 3, 0, 2, 0, 4),
(23, 2, 1, '22SA201', 'Life Skills - I', 0, 0, 2, 0, 1),
(24, 2, 2, '22TP203', 'Advanced Coding Competency', 0, 0, 2, 0, 1),
(25, 2, 2, '22TP204', 'Professional Communication', 0, 0, 2, 0, 1),
(26, 2, 2, '22IT204', 'Design and Analysis of Algorithms', 3, 0, 2, 0, 4),
(27, 2, 2, '22IT205', 'Operating Systems', 3, 0, 2, 0, 4),
(28, 2, 2, '22IT206', 'Python Programming', 2, 0, 2, 0, 3),
(29, 2, 2, 'Department Elective-', 'Department Elective-I', 2, 2, 0, 0, 3),
(30, 2, 2, 'Open Elective – 1', 'Open Elective – 1', 2, 2, 0, 0, 3),
(31, 2, 2, '22SA202', 'Life Skills - II', 0, 0, 2, 0, 1),
(32, 3, 1, '22TP301', 'Soft Skills Lab', 0, 0, 2, 0, 1),
(33, 3, 1, '22IT301', 'Computer Networks', 3, 0, 2, 0, 4),
(34, 3, 1, '22IT302', 'Data Mining Techniques', 3, 0, 2, 0, 4),
(35, 3, 1, '22IT303', 'Software Engineering', 3, 0, 2, 0, 4),
(36, 3, 1, 'Department Elective ', 'Department Elective – 2', 2, 2, 0, 0, 3),
(37, 3, 1, 'Open Elective – 2', 'Open Elective – 2', 2, 2, 0, 0, 3),
(38, 3, 1, '22IT304', 'Inter-Disciplinary Project – Phase I', 0, 0, 2, 0, 0),
(39, 3, 1, '22IT305', 'Industry Interface Course', 1, 0, 0, 0, 1),
(40, 3, 2, '22TP302', 'Quantitative Aptitude and Logical Reasoning', 1, 2, 0, 0, 2),
(41, 3, 2, '22IT306', 'Cloud Computing', 3, 0, 2, 0, 4),
(42, 3, 2, '22IT307', 'Machine Learning', 3, 0, 2, 0, 4),
(43, 3, 2, 'Department Elective ', 'Department Elective – 3', 2, 2, 0, 0, 3),
(44, 3, 2, 'Department Elective ', 'Department Elective – 4', 2, 2, 0, 0, 3),
(45, 3, 2, 'Open Elective – 3', 'Open Elective – 3', 2, 2, 0, 0, 3),
(46, 3, 2, '22IT308', 'Inter-Disciplinary Project – Phase II', 0, 0, 2, 0, 2),
(47, 4, 1, '22IT401', 'Cryptography and Network Security', 3, 0, 2, 0, 4),
(48, 4, 1, '22IT402', 'Internet of Things', 3, 0, 2, 0, 4),
(49, 4, 1, 'Department Elective ', 'Department Elective – 5', 3, 0, 2, 0, 4),
(50, 4, 1, 'Department Elective ', 'Department Elective – 6', 3, 0, 2, 0, 4),
(51, 4, 1, 'Department Elective ', 'Department Elective – 7', 3, 2, 0, 0, 4),
(52, 4, 2, '22IT403', 'Project Work', 0, 2, 0, 0, 22),
(53, 4, 2, '22IT404', 'Internship', 0, 0, 12, 0, 0),
(54, 4, 2, 'Minor / Honours – 5', 'Minor / Honours – 5 (for project)', 0, 2, 6, 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `employee_id` int(10) NOT NULL,
  `password` varchar(250) NOT NULL,
  `emp_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `sub_name` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `Email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`employee_id`, `password`, `emp_name`, `role`, `sub_name`, `profile_image`, `department`, `Email`) VALUES
(138, '138', 'M.Venkatesh', 'Associate Professor', 'OSWT', 'uploads/profiles/138_68d6c8627ca8b.jpg', 'Information Technology', 'venkateshallu809@gmail.com'),
(143, '1234', 'Mr. K. Praveen Kumar', 'Assistant Professor', 'DL', NULL, 'Information Technology', ''),
(195, '195', 'Dr. Ch. Siva Koteswara Rao', 'Associate Professor', 'DBMS', NULL, 'Information Technology', ''),
(213, '213', 'Dr. N. Veeranjaneyulu', 'Professor', 'Subject1', '', 'Information Technology', ''),
(247, '247', 'Dr. P. Subba Rao', 'Associate Professor', 'Subject3', NULL, 'Information Technology', ''),
(1203, '1203', 'Mrs. Nazma Sultana Shaik', 'Assistant Professor', 'Subject8', NULL, 'Information Technology', ''),
(1768, '$2y$10$IYHH85ilMleMBdlRp0/NUeNzNmsu1IEj/x8RxTCvvPz6WPBCDTby6', 'Dr. K. Sujatha', 'Professor', 'ML', NULL, 'Information Technology', ''),
(1772, '1772', 'Mr. D. Anandha Kumar', 'Assistant Professor', 'Subject9', NULL, 'Information Technology', ''),
(2076, '2076', 'Dr. Hemanta Kumar Bhuyan', 'Associate Professor', 'Subject5', NULL, 'Information Technology', ''),
(20083, '20083', 'Dr. V. Nagi Reddy', 'Assistant Professor', 'Subject10', NULL, 'Information Technology', '');

-- --------------------------------------------------------

--
-- Table structure for table `important_dates`
--

CREATE TABLE `important_dates` (
  `id` int(11) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `event_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `important_dates`
--

INSERT INTO `important_dates` (`id`, `employee_id`, `event_date`, `event_time`, `event_name`, `created_at`) VALUES
(3, 138, '2025-10-09', '17:30:00', 'Holiday', '2025-10-08 07:00:37');

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(11) NOT NULL,
  `meeting_id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `purpose` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in minutes',
  `location` varchar(255) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `meeting_id`, `title`, `purpose`, `date`, `time`, `duration`, `location`, `department`, `created_by`, `created_at`, `status`) VALUES
(5, '1', 'hi', 'what are u doing ', '2025-11-12', '20:25:00', 60, 'ATF-10', 'Information Technology', 'Admin', '2025-11-11 13:54:15', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_attendance`
--

CREATE TABLE `meeting_attendance` (
  `id` int(11) NOT NULL,
  `meeting_id` varchar(50) NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `status` enum('invited','attended','absent','excused') DEFAULT 'invited',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meeting_attendance`
--

INSERT INTO `meeting_attendance` (`id`, `meeting_id`, `faculty_id`, `status`, `notes`) VALUES
(9, '1', '195', 'invited', NULL),
(10, '1', '2076', 'invited', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `todo_items`
--

CREATE TABLE `todo_items` (
  `id` int(11) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `task_text` varchar(255) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminc`
--
ALTER TABLE `adminc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- Indexes for table `important_dates`
--
ALTER TABLE `important_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meeting_id` (`meeting_id`);

--
-- Indexes for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_id` (`meeting_id`);

--
-- Indexes for table `todo_items`
--
ALTER TABLE `todo_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminc`
--
ALTER TABLE `adminc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `important_dates`
--
ALTER TABLE `important_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `todo_items`
--
ALTER TABLE `todo_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `credentials` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `important_dates`
--
ALTER TABLE `important_dates`
  ADD CONSTRAINT `important_dates_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `credentials` (`employee_id`);

--
-- Constraints for table `meeting_attendance`
--
ALTER TABLE `meeting_attendance`
  ADD CONSTRAINT `meeting_attendance_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`meeting_id`) ON DELETE CASCADE;

--
-- Constraints for table `todo_items`
--
ALTER TABLE `todo_items`
  ADD CONSTRAINT `todo_items_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `credentials` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
