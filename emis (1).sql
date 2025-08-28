-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2025 at 06:39 PM
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
-- Database: `emis`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Leave') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `class_id`, `teacher_id`, `attendance_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, '2025-08-25', 'Present', '2025-08-25 11:39:18', '2025-08-26 15:28:07'),
(2, 2, 1, 3, '2025-08-25', 'Present', '2025-08-25 11:39:18', '2025-08-26 15:28:07'),
(3, 3, 1, 3, '2025-08-25', 'Present', '2025-08-25 11:39:18', '2025-08-26 15:28:07'),
(19, 1, 1, 3, '2025-08-26', 'Absent', '2025-08-26 14:48:52', '2025-08-26 15:22:34'),
(20, 2, 1, 3, '2025-08-26', 'Present', '2025-08-26 14:48:52', '2025-08-26 15:22:38'),
(21, 3, 1, 3, '2025-08-26', 'Absent', '2025-08-26 14:48:52', '2025-08-26 15:22:32'),
(64, 1, 1, 3, '2025-08-27', 'Present', '2025-08-26 15:28:39', '2025-08-26 15:28:39'),
(65, 2, 1, 3, '2025-08-27', 'Present', '2025-08-26 15:28:39', '2025-08-26 15:28:39'),
(66, 3, 1, 3, '2025-08-27', 'Present', '2025-08-26 15:28:39', '2025-08-26 15:28:39'),
(67, 1, 1, 3, '2025-08-28', 'Present', '2025-08-26 15:28:46', '2025-08-26 15:28:46'),
(68, 2, 1, 3, '2025-08-28', 'Present', '2025-08-26 15:28:46', '2025-08-26 15:28:46'),
(69, 3, 1, 3, '2025-08-28', 'Present', '2025-08-26 15:28:46', '2025-08-26 15:28:46'),
(73, 1, 1, 3, '2025-08-29', 'Present', '2025-08-26 15:28:57', '2025-08-26 15:28:57'),
(74, 2, 1, 3, '2025-08-29', 'Absent', '2025-08-26 15:28:57', '2025-08-26 15:29:00'),
(75, 3, 1, 3, '2025-08-29', 'Present', '2025-08-26 15:28:57', '2025-08-26 15:28:57'),
(82, 1, 1, 3, '2025-08-01', 'Present', '2025-08-26 15:41:54', '2025-08-26 15:41:54'),
(83, 2, 1, 3, '2025-08-01', 'Present', '2025-08-26 15:41:54', '2025-08-26 15:41:54'),
(84, 3, 1, 3, '2025-08-01', 'Present', '2025-08-26 15:41:54', '2025-08-26 15:41:54'),
(85, 1, 1, 3, '2025-08-02', 'Present', '2025-08-26 15:41:58', '2025-08-26 15:41:58'),
(86, 2, 1, 3, '2025-08-02', 'Present', '2025-08-26 15:41:58', '2025-08-26 15:41:58'),
(87, 3, 1, 3, '2025-08-02', 'Present', '2025-08-26 15:41:58', '2025-08-26 15:41:58'),
(88, 1, 1, 3, '2025-08-03', 'Present', '2025-08-26 15:42:02', '2025-08-26 15:42:02'),
(89, 2, 1, 3, '2025-08-03', 'Present', '2025-08-26 15:42:02', '2025-08-26 15:42:02'),
(90, 3, 1, 3, '2025-08-03', 'Present', '2025-08-26 15:42:02', '2025-08-26 15:42:02'),
(91, 1, 1, 3, '2025-08-04', 'Present', '2025-08-26 15:42:07', '2025-08-26 15:42:07'),
(92, 2, 1, 3, '2025-08-04', 'Absent', '2025-08-26 15:42:07', '2025-08-26 15:42:07'),
(93, 3, 1, 3, '2025-08-04', 'Present', '2025-08-26 15:42:07', '2025-08-26 15:42:07'),
(94, 1, 1, 3, '2025-08-05', 'Absent', '2025-08-26 15:42:13', '2025-08-26 15:42:13'),
(95, 2, 1, 3, '2025-08-05', 'Present', '2025-08-26 15:42:13', '2025-08-26 15:42:13'),
(96, 3, 1, 3, '2025-08-05', 'Present', '2025-08-26 15:42:13', '2025-08-26 15:42:13');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `bid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `btitle` varchar(255) NOT NULL,
  `bcode` varchar(50) NOT NULL,
  `bauthor` varchar(255) NOT NULL,
  `bpublisher` varchar(255) NOT NULL,
  `blevel` enum('first year','second year') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `importance` enum('optional','compulsory') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`bid`, `gid`, `btitle`, `bcode`, `bauthor`, `bpublisher`, `blevel`, `status`, `importance`) VALUES
(3, 1, 'Mathematics asfasdfa sfas fas asdf', 'Math256asd fa sdfa sd fasd asd fasd fasdfasd fasd ', 'Shahida sd afsd asdfas dfas df', 'NBFP', 'first year', 'active', 'optional');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `CID` int(11) NOT NULL,
  `class_name` varchar(256) NOT NULL,
  `class_short` varchar(12) NOT NULL,
  `class_status` enum('active','inactive','deleted') NOT NULL DEFAULT 'active',
  `session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`CID`, `class_name`, `class_short`, `class_status`, `session_id`) VALUES
(1, 'Third Year', 'XIII', 'active', 6),
(2, 'Second Year', 'XII', 'active', 6),
(3, 'First Year', 'XI', 'active', 3),
(4, 'First Year', 'XI', 'active', 4);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `gid` int(11) NOT NULL,
  `gname` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`gid`, `gname`, `status`) VALUES
(1, 'Pred Medical', 'active'),
(2, 'Pred Emgg', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expiry`) VALUES
(21, 1, '323c8c6e419fb5fda522b698c161d7fa0f234b538d95b0be6c85a0b0dd5d4579', '2025-08-24 18:03:37');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `session_name` varchar(9) DEFAULT NULL,
  `starting_date` date NOT NULL,
  `status` enum('active','completed') NOT NULL,
  `remarks` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `session_name`, `starting_date`, `status`, `remarks`) VALUES
(3, '2025-2026', '2025-09-01', 'completed', 'For Inter Mediate Classes'),
(4, '2026-2027', '2025-04-01', 'completed', 'fasdf'),
(5, '2028-2029', '2025-04-11', 'completed', 'afsdfasdf'),
(6, '2024-2027', '2024-01-01', 'active', 'FOR BSIT');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_name` varchar(256) NOT NULL,
  `father_name` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `class` int(11) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('registered','admitted','banned','suspended') DEFAULT 'registered',
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `student_cnic` varchar(15) DEFAULT NULL,
  `father_cnic` varchar(15) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_name`, `father_name`, `email`, `phone`, `address`, `gender`, `dob`, `class`, `registration_date`, `status`, `city`, `state`, `photo`, `student_cnic`, `father_cnic`, `session_id`) VALUES
(1, 'Muddasar Khan', 'Sultan Muqqarab Khan', 'niazi587@gmail.com', '03361000830', 'FG Liaqat Ali Degree College, Peshawar Road, Rawalpindi\\r\\nRawalpindi', 'Male', '1981-11-12', 1, '2025-04-22 11:35:08', 'admitted', 'Rawalpindi', 'Punjab', '1756119886_1746012045_1745321708_muddasar.khan.jpg', '33100-0333226-5', '33100-0724226-5', 6),
(2, 'Muddasar Khan', 'Sultan Muqqarab Khan', 'niazi58e7@gmail.com', '03361000830', 'FG Liaqat Ali Degree College, Peshawar Road, Rawalpindi', 'Male', '2018-01-30', 1, '2025-04-30 11:18:11', 'admitted', 'Faisalabad', 'Punjab', '1756119918_1746011843_1745321664_SSC_975.jpg', '33100-0333225-5', '33100-0724226-5', 6),
(3, 'Umar Nawaz Khan', 'Sultan Muqqarab Khan', 'niazi58w7@gmail.com', '03361000830', 'C/O Al Syed Public Model School, 215 R.B, Hamayoon Nagar, Jaranwala Road', 'Male', '2008-02-07', 1, '2025-08-25 11:06:25', 'admitted', 'Faisalabad', 'Punjab', '1756119995_1746011843_1745321664_SSC_975.jpg', '33100-0724227-5', '33100-0724226-5', 6);

-- --------------------------------------------------------

--
-- Table structure for table `study_materials`
--

CREATE TABLE `study_materials` (
  `material_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `cnic` varchar(15) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `highest_qualification` varchar(100) NOT NULL,
  `bps` int(11) DEFAULT NULL,
  `department` enum('Computer Science','Physics','Chemistry','Mathematics') NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `job_nature` enum('Permanent','Contract','Visiting') DEFAULT 'Contract',
  `joining` date NOT NULL,
  `job_status` enum('Active','Resigned','Retired','Suspended') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `teacher_name`, `cnic`, `photo`, `designation`, `highest_qualification`, `bps`, `department`, `subject`, `job_nature`, `joining`, `job_status`) VALUES
(1, 'Muddasar Khan', '3310007242265', 'uploads/1746011843_1745321664_SSC_975.jpg', 'Computer Instructor', 'MSCS', 17, 'Physics', 'Computer Science', 'Permanent', '2025-04-07', 'Active'),
(2, 'Rustam Ali', '3310007242245', 'uploads/B.Ed.jpg', 'Computer Instructor', 'MSCS', 34, 'Computer Science', 'Computer Science', 'Visiting', '2025-04-02', 'Active'),
(3, 'Agha', '3630232101309', 'uploads/1745321664_SSC_975.jpg', 'Computer Instructor', 'MSCS', 2, 'Computer Science', 'Computer Science', 'Visiting', '2025-08-05', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_classes`
--

CREATE TABLE `teacher_classes` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `status` enum('Active','Deleted') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_classes`
--

INSERT INTO `teacher_classes` (`id`, `class_id`, `teacher_id`, `session_id`, `status`) VALUES
(1, 1, 1, 3, 'Active'),
(3, 1, 2, 3, 'Active'),
(4, 4, 1, 4, 'Active'),
(5, 4, 2, 4, 'Active'),
(6, 1, 3, 3, 'Active'),
(7, 2, 3, 3, 'Active'),
(8, 3, 3, 3, 'Active'),
(9, 1, 3, 6, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `uname` varchar(256) NOT NULL,
  `uemail` varchar(256) NOT NULL,
  `CNIC` varchar(13) DEFAULT NULL,
  `upassword` varchar(256) NOT NULL,
  `urole` enum('Admin','Teacher','Student','Parents') DEFAULT NULL,
  `creationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `verification_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `uname`, `uemail`, `CNIC`, `upassword`, `urole`, `creationdate`, `status`, `verification_token`) VALUES
(1, 'Muddasar Khan Niazi', 'niazi587@gmail.com', '3310007242265', '$2y$10$8UhdXBwtj8RBegJfLPgn2uBYYBfz5PSge535XOLGbaPXwaMgNODOu', 'Admin', '2025-04-22 11:06:53', 'active', NULL),
(2, 'Agha', 'Agha5387@gmail.com', '3630232101309', '$2y$10$IPe7h1xqxvi.FhreGa7rr.7hEHBWw17PEECZdONZBA2P8rxQdDfEW', 'Teacher', '2025-04-30 05:45:49', 'active', NULL),
(3, 'Muddasar Khan Niazi', 'niazi5e4347@gmail.com', '3310034724226', '$2y$10$4ay0NJON0VplKHi1jwF/8erpZCUo1QlN1uwbeMPamRrOxa9I5JT.q', 'Student', '2025-04-30 05:48:54', 'active', NULL),
(4, 'Muddasar Khan', 'niazi5847@gmail.com', '3310004242265', '$2y$10$4ay0NJON0VplKHi1jwF/8erpZCUo1QlN1uwbeMPamRrOxa9I5JT.q', 'Parents', '2025-04-30 06:01:20', 'inactive', NULL),
(5, 'Noman Ali', 'nomanali@gmail.com', '3310007247265', '$2y$10$hrWy2A9a6dwAo/My4COCx.H4WG5c349fmVt/Xk9lHWw5jQM6Fm7Fq', 'Student', '2025-07-04 06:22:40', 'active', NULL),
(9, 'AwaisKhalid', 'mobilephonesinn@gmail.com', '3310807242265', '$2y$10$gifwqdTePHNNvM29zTqYxOVwGHUdiJmsDzenJOgieumjopowH8ltm', 'Teacher', '2025-08-24 15:10:20', 'active', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`class_id`,`attendance_date`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`CID`),
  ADD KEY `fk_session` (`session_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`gid`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD UNIQUE KEY `session_name` (`session_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `study_materials`
--
ALTER TABLE `study_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `fk_teacher` (`teacher_id`),
  ADD KEY `fk_class` (`class_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `cnic` (`cnic`);

--
-- Indexes for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uemail` (`uemail`),
  ADD UNIQUE KEY `CNIC` (`CNIC`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `CID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `study_materials`
--
ALTER TABLE `study_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `study_materials`
--
ALTER TABLE `study_materials`
  ADD CONSTRAINT `fk_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`CID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
