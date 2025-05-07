-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 04:07 AM
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
-- Database: `english_lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL COMMENT 'Table or module affected',
  `entity_id` int(11) DEFAULT NULL COMMENT 'ID of the record affected',
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 11:41:28'),
(2, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:10:04'),
(3, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:24:43'),
(4, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:25:51'),
(5, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:28:14'),
(6, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:28:18'),
(7, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:29:38'),
(8, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:29:43'),
(9, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:30:59'),
(10, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:31:09'),
(11, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:54:35'),
(12, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 13:54:45'),
(13, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-03 15:01:05'),
(14, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 15:11:51'),
(15, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 16:13:36'),
(16, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:15:51'),
(17, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:16:48'),
(18, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:16:55'),
(19, 7, 'logout', 'users', 7, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:26:10'),
(20, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:27:11'),
(21, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:29:06'),
(22, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:30:45'),
(23, 7, 'logout', 'users', 7, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:37:12'),
(24, 10, 'login', 'users', 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:37:23'),
(25, 10, 'logout', 'users', 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:37:31'),
(26, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:38:45'),
(27, 8, 'logout', 'users', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:39:06'),
(28, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 17:39:18'),
(29, 8, 'logout', 'users', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:10:12'),
(30, 9, 'login', 'users', 9, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:10:29'),
(31, 9, 'logout', 'users', 9, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:10:33'),
(32, 10, 'login', 'users', 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:10:47'),
(33, 10, 'logout', 'users', 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:10:52'),
(34, 11, 'login', 'users', 11, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:11:01'),
(35, 11, 'logout', 'users', 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:11:07'),
(36, 9, 'login', 'users', 9, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:11:41'),
(37, 9, 'logout', 'users', 9, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:11:44'),
(38, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:11:51'),
(39, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:12:28'),
(40, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:12:37'),
(41, 8, 'logout', 'users', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:35:22'),
(42, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 18:39:50'),
(43, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 19:35:36'),
(44, 8, 'logout', 'users', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 19:37:53'),
(45, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 19:38:10'),
(46, 8, 'logout', 'users', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 19:55:30'),
(47, 11, 'login', 'users', 11, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 19:55:43'),
(48, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 20:55:46'),
(49, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:01:53'),
(50, 7, 'logout', 'users', 7, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:02:25'),
(51, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:02:36'),
(52, 8, 'logout', 'users', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:02:43'),
(53, 10, 'login', 'users', 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:02:52'),
(54, 10, 'logout', 'users', 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:03:00'),
(55, 9, 'login', 'users', 9, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:03:09'),
(56, 9, 'logout', 'users', 9, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:03:19'),
(57, 11, 'login', 'users', 11, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:03:28'),
(58, 11, 'logout', 'users', 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:16:39'),
(59, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:16:50'),
(60, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:17:09'),
(61, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 22:17:24'),
(62, 8, 'login', 'users', 8, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-04 23:25:37'),
(63, 8, 'logout', 'users', 8, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 00:13:48'),
(64, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-05 00:13:57'),
(65, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-06 23:06:23'),
(66, 7, 'logout', 'users', 7, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-06 23:26:24'),
(67, 23, 'login', 'users', 23, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-07 06:34:10'),
(68, 23, 'logout', 'users', 23, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-07 08:34:05'),
(69, 7, 'login', 'users', 7, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-07 08:34:12'),
(70, 7, 'logout', 'users', 7, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-07 08:40:08'),
(71, 3, 'login', 'users', 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-07 08:53:59'),
(72, 3, 'login', 'users', 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-07 09:40:36');

-- --------------------------------------------------------

--
-- Table structure for table `answer_options`
--

CREATE TABLE `answer_options` (
  `option_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answer_options`
--

INSERT INTO `answer_options` (`option_id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'She works at a bank.', 1),
(2, 1, 'She work at a bank.', 0),
(3, 1, 'She working at a bank.', 0),
(4, 2, 'True', 0),
(5, 2, 'False', 1),
(6, 4, 'To test general conversation skills', 0),
(7, 4, 'To test understanding of everyday social situations', 1),
(8, 4, 'To test academic lecture comprehension', 0),
(9, 11, 'I\'m fine, thanks', 1),
(10, 11, 'Tomorrow', 0),
(11, 11, 'The weather is nice', 0),
(12, 12, 'I\'m a student', 1),
(13, 12, 'In the park', 0),
(14, 13, 'True', 0),
(15, 13, 'False', 1),
(16, 15, 'She go to school yesterday', 0),
(17, 15, 'She went to school yesterday', 1),
(18, 15, 'She going to school yesterday', 0);

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `max_points` int(11) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('active','locked') DEFAULT 'active',
  `sequence_order` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `course_id`, `title`, `description`, `max_points`, `due_date`, `status`, `sequence_order`, `created_at`, `created_by`) VALUES
(1, 1, 'Self-Introduction Video', 'Record a 1-minute video introducing yourself', 50, '2023-03-15 23:59:00', 'active', 1, '2023-02-10 09:00:00', 2),
(2, 1, 'Daily Routine Essay', 'Write 150 words about your daily routine', 100, '2023-03-25 23:59:00', 'active', 2, '2023-02-15 09:00:00', 2),
(3, 2, 'Past Experiences Essay', 'Write 250 words about a past experience using different past tenses', 100, '2023-03-20 23:59:00', 'active', 1, '2023-02-16 10:00:00', 2),
(4, 5, 'IELTS Writing Task 1', 'Write a 150-word report describing a graph', 100, '2023-04-05 23:59:00', 'active', 1, '2023-03-05 14:00:00', 3),
(5, 6, 'Dialogue Recording', 'Record a 2-minute conversation with a partner', 50, '2023-04-10 23:59:00', 'active', 1, '2023-03-05 10:00:00', 2),
(6, 7, 'Academic Essay', 'Write a 500-word essay on given topic', 100, '2023-04-15 23:59:00', 'active', 1, '2023-03-10 11:00:00', 3),
(7, 8, 'TOEFL Speaking Response', 'Record responses to 3 TOEFL speaking questions', 75, '2023-04-20 23:59:00', 'active', 1, '2023-03-15 12:00:00', 3),
(8, 9, 'Children\'s Story', 'Create a simple illustrated story for kids', 60, '2023-04-05 23:59:00', 'active', 1, '2023-03-20 13:00:00', 2),
(9, 10, 'Pronunciation Journal', 'Record yourself practicing difficult sounds', 40, '2023-04-12 23:59:00', 'active', 1, '2023-03-25 14:00:00', 4),
(10, 11, 'Patient Case Study', 'Write a medical case report in English', 100, '2023-04-18 23:59:00', 'active', 1, '2023-04-01 15:00:00', 4),
(11, 12, 'Tour Guide Script', 'Create a tour guide script for local attraction', 80, '2023-04-08 23:59:00', 'active', 1, '2023-04-05 16:00:00', 2),
(12, 13, 'Slang Presentation', 'Prepare presentation on 5 English idioms', 70, '2023-04-22 23:59:00', 'active', 1, '2023-04-10 17:00:00', 3),
(13, 14, 'Literary Analysis', 'Analyze themes in assigned short story', 90, '2023-04-25 23:59:00', 'active', 1, '2023-04-15 18:00:00', 3),
(14, 15, 'Technical Manual', 'Write a simplified technical manual excerpt', 85, '2023-04-30 23:59:00', 'active', 1, '2023-04-20 19:00:00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `assignment_content`
--

CREATE TABLE `assignment_content` (
  `content_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `sequence_order` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment_content`
--

INSERT INTO `assignment_content` (`content_id`, `assignment_id`, `content_type`, `content_title`, `content_data`, `file_path`, `sequence_order`, `uploaded_by`, `uploaded_at`) VALUES
(11, 5, 'document', 'Dialogue Guidelines', 'Instructions for recording conversation', '/assignments/dialogue_guide.pdf', 1, 2, '2023-03-05 10:15:00'),
(12, 6, 'document', 'Essay Rubric', 'Grading criteria for academic essay', '/assignments/essay_rubric.pdf', 1, 3, '2023-03-10 11:15:00'),
(13, 7, '', 'Sample Responses', 'Example TOEFL speaking responses', '/assignments/toefl_samples.mp3', 1, 3, '2023-03-15 12:15:00'),
(14, 8, 'image', 'Story Template', 'Blank storybook template', '/assignments/story_template.jpg', 1, 2, '2023-03-20 13:15:00'),
(15, 9, 'video', 'Pronunciation Guide', 'Video demonstrating sounds', '/assignments/pronunciation_guide.mp4', 1, 4, '2023-03-25 14:15:00'),
(16, 10, 'document', 'Medical Terms', 'List of medical vocabulary', '/assignments/medical_terms.pdf', 1, 4, '2023-04-01 15:15:00'),
(17, 11, 'document', 'Tourism Vocabulary', 'Essential tourism terms', '/assignments/tourism_vocab.pdf', 1, 2, '2023-04-05 16:15:00'),
(18, 12, 'video', 'Idiom Examples', 'Video explaining idioms', '/assignments/idiom_examples.mp4', 1, 3, '2023-04-10 17:15:00'),
(19, 13, 'document', 'Short Story', 'The Lottery by Shirley Jackson', '/assignments/the_lottery.pdf', 1, 3, '2023-04-15 18:15:00'),
(20, 14, 'document', 'Technical Writing Tips', 'Guide to technical writing', '/assignments/tech_writing.pdf', 1, 4, '2023-04-20 19:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `issue_date` datetime DEFAULT current_timestamp(),
  `certificate_number` varchar(50) NOT NULL,
  `status` enum('issued','revoked') DEFAULT 'issued',
  `issued_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` enum('beginner','elementary','intermediate','upper-intermediate','advanced','proficiency') NOT NULL,
  `credits` int(11) DEFAULT 1,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in weeks',
  `creator_id` int(11) NOT NULL COMMENT 'Teacher who created the course',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT 'If inactive, only visible to creator',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_code`, `title`, `description`, `level`, `credits`, `duration`, `creator_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ENG101', 'English for Beginners', 'Basic English grammar and vocabulary for daily communication', 'beginner', 3, 8, 2, 'active', '2023-02-05 10:00:00', NULL),
(2, 'ENG201', 'Intermediate English', 'Developing fluency and accuracy in English', 'intermediate', 4, 10, 2, 'active', '2023-02-10 11:00:00', NULL),
(3, 'ENG301', 'Advanced English', 'Mastering complex grammar and academic vocabulary', 'advanced', 4, 12, 3, 'active', '2023-02-15 12:00:00', NULL),
(4, 'BUSENG', 'Business English', 'English for professional communication and workplace', 'upper-intermediate', 3, 8, 4, 'active', '2023-02-20 13:00:00', NULL),
(5, 'IELTS', 'IELTS Preparation', 'Comprehensive preparation for IELTS exam', 'advanced', 5, 12, 3, 'active', '2023-02-25 14:00:00', NULL),
(6, 'ENG102', 'English Conversation', 'Developing speaking skills for everyday communication', 'elementary', 3, 8, 2, 'active', '2023-03-01 10:00:00', NULL),
(7, 'ENG202', 'Academic Writing', 'Writing essays and research papers in English', 'upper-intermediate', 4, 10, 3, 'active', '2023-03-05 11:00:00', NULL),
(8, 'TOEFL', 'TOEFL Preparation', 'Comprehensive preparation for TOEFL exam', 'advanced', 5, 12, 3, 'active', '2023-03-10 12:00:00', NULL),
(9, 'ENGKIDS', 'English for Children', 'Fun English lessons for young learners', 'beginner', 2, 6, 2, 'active', '2023-03-15 13:00:00', NULL),
(10, 'PHONICS', 'English Pronunciation', 'Mastering English sounds and phonetics', 'elementary', 3, 8, 4, 'active', '2023-03-20 14:00:00', NULL),
(11, 'ENGMED', 'Medical English', 'English for healthcare professionals', 'intermediate', 4, 10, 4, 'active', '2023-03-25 15:00:00', NULL),
(12, 'ENGTOUR', 'English for Tourism', 'Language skills for travel industry', 'intermediate', 3, 8, 2, 'active', '2023-04-01 16:00:00', NULL),
(13, 'ENGSLANG', 'English Slang & Idioms', 'Understanding informal English expressions', 'upper-intermediate', 2, 6, 3, 'active', '2023-04-05 17:00:00', NULL),
(14, 'ENGLIT', 'English Literature', 'Reading and analyzing English literary works', 'advanced', 4, 12, 3, 'active', '2023-04-10 18:00:00', NULL),
(15, 'ENGTECH', 'Technical English', 'English for engineering and technology', 'upper-intermediate', 3, 8, 4, 'active', '2023-04-15 19:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_schedule`
--

CREATE TABLE `course_schedule` (
  `schedule_id` int(11) NOT NULL,
  `course_teacher_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_schedule`
--

INSERT INTO `course_schedule` (`schedule_id`, `course_teacher_id`, `day_of_week`, `start_time`, `end_time`, `location`) VALUES
(257, 5, 'Monday', '08:00:00', '09:00:00', 'Room 103'),
(258, 12, 'Monday', '09:00:00', '10:00:00', 'Room 207'),
(259, 20, 'Monday', '10:00:00', '11:00:00', 'Room 305'),
(260, 6, 'Monday', '11:00:00', '12:00:00', 'Room 410'),
(261, 14, 'Monday', '12:00:00', '13:00:00', 'Room 108'),
(262, 22, 'Monday', '13:00:00', '14:00:00', 'Room 203'),
(263, 10, 'Monday', '14:00:00', '15:00:00', 'Room 309'),
(264, 16, 'Monday', '15:00:00', '16:00:00', 'Room 402'),
(265, 4, 'Monday', '16:00:00', '17:00:00', 'Room 105'),
(266, 19, 'Monday', '17:00:00', '18:00:00', 'Room 210'),
(267, 11, 'Monday', '18:00:00', '19:00:00', 'Room 306'),
(268, 25, 'Monday', '19:00:00', '20:00:00', 'Room 404'),
(269, 13, 'Monday', '20:00:00', '21:00:00', 'Room 107'),
(270, 3, 'Monday', '21:00:00', '22:00:00', 'Room 208'),
(271, 15, 'Tuesday', '08:00:00', '09:00:00', 'Room 310'),
(272, 6, 'Tuesday', '09:00:00', '10:00:00', 'Room 104'),
(273, 23, 'Tuesday', '10:00:00', '11:00:00', 'Room 205'),
(274, 10, 'Tuesday', '11:00:00', '12:00:00', 'Room 301'),
(275, 17, 'Tuesday', '12:00:00', '13:00:00', 'Room 408'),
(276, 4, 'Tuesday', '13:00:00', '14:00:00', 'Room 102'),
(277, 20, 'Tuesday', '14:00:00', '15:00:00', 'Room 209'),
(278, 12, 'Tuesday', '15:00:00', '16:00:00', 'Room 307'),
(279, 5, 'Tuesday', '16:00:00', '17:00:00', 'Room 403'),
(280, 14, 'Tuesday', '17:00:00', '18:00:00', 'Room 106'),
(281, 26, 'Tuesday', '18:00:00', '19:00:00', 'Room 202'),
(282, 11, 'Tuesday', '19:00:00', '20:00:00', 'Room 304'),
(283, 3, 'Tuesday', '20:00:00', '21:00:00', 'Room 401'),
(284, 18, 'Tuesday', '21:00:00', '22:00:00', 'Room 110'),
(285, 13, 'Wednesday', '08:00:00', '09:00:00', 'Room 206'),
(286, 21, 'Wednesday', '09:00:00', '10:00:00', 'Room 303'),
(287, 6, 'Wednesday', '10:00:00', '11:00:00', 'Room 407'),
(288, 16, 'Wednesday', '11:00:00', '12:00:00', 'Room 109'),
(289, 24, 'Wednesday', '12:00:00', '13:00:00', 'Room 204'),
(290, 10, 'Wednesday', '13:00:00', '14:00:00', 'Room 308'),
(291, 5, 'Wednesday', '14:00:00', '15:00:00', 'Room 405'),
(292, 19, 'Wednesday', '15:00:00', '16:00:00', 'Room 101'),
(293, 14, 'Wednesday', '16:00:00', '17:00:00', 'Room 207'),
(294, 3, 'Wednesday', '17:00:00', '18:00:00', 'Room 302'),
(295, 22, 'Wednesday', '18:00:00', '19:00:00', 'Room 406'),
(296, 11, 'Wednesday', '19:00:00', '20:00:00', 'Room 110'),
(297, 17, 'Wednesday', '20:00:00', '21:00:00', 'Room 203'),
(298, 4, 'Wednesday', '21:00:00', '22:00:00', 'Room 309'),
(299, 18, 'Thursday', '08:00:00', '09:00:00', 'Room 104'),
(300, 10, 'Thursday', '09:00:00', '10:00:00', 'Room 208'),
(301, 25, 'Thursday', '10:00:00', '11:00:00', 'Room 301'),
(302, 12, 'Thursday', '11:00:00', '12:00:00', 'Room 409'),
(303, 20, 'Thursday', '12:00:00', '13:00:00', 'Room 105'),
(304, 5, 'Thursday', '13:00:00', '14:00:00', 'Room 210'),
(305, 15, 'Thursday', '14:00:00', '15:00:00', 'Room 304'),
(306, 12, 'Thursday', '15:00:00', '16:00:00', 'Room 401'),
(307, 23, 'Thursday', '16:00:00', '17:00:00', 'Room 107'),
(308, 6, 'Thursday', '17:00:00', '18:00:00', 'Room 202'),
(309, 14, 'Thursday', '18:00:00', '19:00:00', 'Room 305'),
(310, 3, 'Thursday', '19:00:00', '20:00:00', 'Room 408'),
(311, 21, 'Thursday', '20:00:00', '21:00:00', 'Room 103'),
(312, 10, 'Thursday', '21:00:00', '22:00:00', 'Room 206'),
(313, 16, 'Friday', '08:00:00', '09:00:00', 'Room 309'),
(314, 24, 'Friday', '09:00:00', '10:00:00', 'Room 102'),
(315, 11, 'Friday', '10:00:00', '11:00:00', 'Room 205'),
(316, 5, 'Friday', '11:00:00', '12:00:00', 'Room 308'),
(317, 19, 'Friday', '12:00:00', '13:00:00', 'Room 404'),
(318, 13, 'Friday', '13:00:00', '14:00:00', 'Room 106'),
(319, 22, 'Friday', '14:00:00', '15:00:00', 'Room 201'),
(320, 6, 'Friday', '15:00:00', '16:00:00', 'Room 303'),
(321, 14, 'Friday', '16:00:00', '17:00:00', 'Room 407'),
(322, 3, 'Friday', '17:00:00', '18:00:00', 'Room 110'),
(323, 20, 'Friday', '18:00:00', '19:00:00', 'Room 204'),
(324, 10, 'Friday', '19:00:00', '20:00:00', 'Room 307'),
(325, 17, 'Friday', '20:00:00', '21:00:00', 'Room 401'),
(326, 4, 'Friday', '21:00:00', '22:00:00', 'Room 105'),
(327, 15, 'Saturday', '08:00:00', '09:00:00', 'Room 208'),
(328, 26, 'Saturday', '09:00:00', '10:00:00', 'Room 302'),
(329, 12, 'Saturday', '10:00:00', '11:00:00', 'Room 405'),
(330, 5, 'Saturday', '11:00:00', '12:00:00', 'Room 108'),
(331, 21, 'Saturday', '12:00:00', '13:00:00', 'Room 203'),
(332, 15, 'Saturday', '13:00:00', '14:00:00', 'Room 306'),
(333, 18, 'Saturday', '14:00:00', '15:00:00', 'Room 409'),
(334, 20, 'Saturday', '15:00:00', '16:00:00', 'Room 101'),
(335, 23, 'Saturday', '16:00:00', '17:00:00', 'Room 204'),
(336, 14, 'Saturday', '17:00:00', '18:00:00', 'Room 310'),
(337, 3, 'Saturday', '18:00:00', '19:00:00', 'Room 103'),
(338, 20, 'Saturday', '19:00:00', '20:00:00', 'Room 206'),
(339, 11, 'Saturday', '20:00:00', '21:00:00', 'Room 309'),
(340, 4, 'Saturday', '21:00:00', '22:00:00', 'Room 402'),
(341, 22, 'Sunday', '08:00:00', '09:00:00', 'Room 105'),
(342, 13, 'Sunday', '09:00:00', '10:00:00', 'Room 207'),
(343, 6, 'Sunday', '10:00:00', '11:00:00', 'Room 304'),
(344, 19, 'Sunday', '11:00:00', '12:00:00', 'Room 408'),
(345, 10, 'Sunday', '12:00:00', '13:00:00', 'Room 102'),
(346, 25, 'Sunday', '13:00:00', '14:00:00', 'Room 205'),
(347, 16, 'Sunday', '14:00:00', '15:00:00', 'Room 301'),
(348, 5, 'Sunday', '15:00:00', '16:00:00', 'Room 406'),
(349, 14, 'Sunday', '16:00:00', '17:00:00', 'Room 109'),
(350, 3, 'Sunday', '17:00:00', '18:00:00', 'Room 203'),
(351, 21, 'Sunday', '18:00:00', '19:00:00', 'Room 306'),
(352, 12, 'Sunday', '19:00:00', '20:00:00', 'Room 410'),
(353, 11, 'Sunday', '20:00:00', '21:00:00', 'Room 104'),
(354, 4, 'Sunday', '21:00:00', '22:00:00', 'Room 208');

-- --------------------------------------------------------

--
-- Table structure for table `course_sharing`
--

CREATE TABLE `course_sharing` (
  `sharing_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `shared_with_id` int(11) NOT NULL COMMENT 'Teacher ID with whom the course is shared',
  `shared_by_id` int(11) NOT NULL COMMENT 'Teacher ID who shared the course',
  `shared_date` datetime DEFAULT current_timestamp(),
  `permission` enum('view','edit','full_access') DEFAULT 'view'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_sharing`
--

INSERT INTO `course_sharing` (`sharing_id`, `course_id`, `shared_with_id`, `shared_by_id`, `shared_date`, `permission`) VALUES
(5, 5, 15, 14, '2023-04-21 14:00:00', 'view'),
(6, 6, 14, 15, '2023-04-26 15:00:00', 'edit'),
(7, 7, 17, 16, '2023-05-02 16:00:00', 'view'),
(8, 8, 16, 17, '2023-05-06 17:00:00', 'full_access'),
(9, 9, 19, 18, '2023-05-11 18:00:00', 'view'),
(10, 10, 18, 19, '2023-05-16 19:00:00', 'edit');

-- --------------------------------------------------------

--
-- Table structure for table `course_teachers`
--

CREATE TABLE `course_teachers` (
  `course_teacher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `assigned_date` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_teachers`
--

INSERT INTO `course_teachers` (`course_teacher_id`, `course_id`, `teacher_id`, `assigned_date`, `is_active`) VALUES
(3, 3, 3, '2023-02-16 11:00:00', 1),
(4, 4, 4, '2023-02-21 12:00:00', 1),
(5, 5, 3, '2023-02-26 13:00:00', 1),
(6, 2, 3, '2023-02-12 14:00:00', 1),
(10, 4, 13, '2023-04-15 12:00:00', 1),
(11, 5, 14, '2023-04-20 13:00:00', 1),
(12, 6, 15, '2023-04-25 14:00:00', 1),
(13, 7, 16, '2023-05-01 15:00:00', 1),
(14, 8, 17, '2023-05-05 16:00:00', 1),
(15, 9, 18, '2023-05-10 17:00:00', 1),
(16, 10, 19, '2023-05-15 18:00:00', 1),
(17, 1, 13, '2023-05-01 09:00:00', 1),
(18, 2, 14, '2023-05-02 10:00:00', 1),
(19, 3, 15, '2023-05-03 11:00:00', 1),
(20, 4, 16, '2023-05-04 12:00:00', 1),
(21, 5, 17, '2023-05-05 13:00:00', 1),
(22, 6, 18, '2023-05-06 14:00:00', 1),
(23, 7, 19, '2023-05-07 15:00:00', 1),
(24, 8, 3, '2023-05-08 16:00:00', 1),
(25, 9, 4, '2023-05-09 17:00:00', 1),
(26, 10, 5, '2023-05-10 18:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL COMMENT 'Teacher teaching this student in this course',
  `enrollment_date` datetime DEFAULT current_timestamp(),
  `status` enum('ongoing','completed','dropped') DEFAULT 'ongoing',
  `completion_date` datetime DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `student_rating` int(11) DEFAULT NULL COMMENT 'Course rating by student (1-5 stars)',
  `rating_date` datetime DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `course_id`, `teacher_id`, `enrollment_date`, `status`, `completion_date`, `final_grade`, `student_rating`, `rating_date`, `schedule_id`) VALUES
(11, 7, 1, 13, '2023-06-01 09:00:00', 'ongoing', NULL, NULL, NULL, NULL, 266),
(12, 8, 2, 14, '2023-06-05 10:00:00', 'ongoing', NULL, NULL, NULL, NULL, 258),
(13, 9, 3, 15, '2023-06-10 11:00:00', 'ongoing', NULL, NULL, NULL, NULL, 310),
(14, 10, 4, 16, '2023-06-15 12:00:00', 'ongoing', NULL, NULL, NULL, NULL, 266),
(15, 11, 5, 17, '2023-06-20 13:00:00', 'ongoing', NULL, NULL, NULL, NULL, 258),
(16, 7, 6, 18, '2023-05-01 14:00:00', 'completed', '2023-08-15 00:00:00', 85.50, 4, '2023-08-16 00:00:00', 310),
(17, 8, 7, 19, '2023-05-05 15:00:00', 'completed', '2023-08-20 00:00:00', 92.00, 5, '2023-08-21 00:00:00', 266),
(18, 9, 8, 3, '2023-05-10 16:00:00', 'completed', '2023-08-25 00:00:00', 78.00, 3, '2023-08-26 00:00:00', 258),
(19, 10, 9, 4, '2023-05-15 17:00:00', 'completed', '2023-08-30 00:00:00', 89.50, 5, '2023-08-31 00:00:00', 310),
(20, 11, 10, 5, '2023-05-20 18:00:00', 'completed', '2023-09-05 00:00:00', 91.00, 4, '2023-09-06 00:00:00', 266),
(21, 7, 11, 6, '2023-07-01 09:00:00', 'dropped', '2023-07-15 00:00:00', NULL, NULL, NULL, 258),
(22, 8, 12, 13, '2023-07-05 10:00:00', 'dropped', '2023-07-20 00:00:00', NULL, NULL, NULL, 310),
(23, 9, 13, 14, '2023-07-10 11:00:00', 'dropped', '2023-07-25 00:00:00', NULL, NULL, NULL, 266),
(24, 10, 14, 15, '2023-07-15 12:00:00', 'dropped', '2023-07-30 00:00:00', NULL, NULL, NULL, 258),
(25, 11, 15, 16, '2023-07-20 13:00:00', 'dropped', '2023-08-05 00:00:00', NULL, NULL, NULL, 310);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `exam_date` datetime NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in minutes',
  `total_marks` int(11) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  `status` enum('active','locked','completed','cancelled') DEFAULT 'active',
  `sequence_order` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `course_id`, `title`, `description`, `exam_date`, `duration`, `total_marks`, `passing_marks`, `status`, `sequence_order`, `created_by`, `created_at`) VALUES
(1, 6, 'Midterm Speaking Exam', 'Oral proficiency assessment', '2023-04-15 09:00:00', 30, 100, 70, 'completed', 1, 2, '2023-03-25 10:00:00'),
(2, 7, 'Final Writing Exam', 'Comprehensive writing assessment', '2023-05-10 10:00:00', 120, 100, 75, 'active', 1, 3, '2023-04-01 11:00:00'),
(3, 8, 'TOEFL Practice Test', 'Full-length TOEFL simulation', '2023-05-05 09:00:00', 180, 120, 80, 'active', 1, 3, '2023-04-05 12:00:00'),
(4, 9, 'Final Kids\' Test', 'Fun assessment for young learners', '2023-04-20 10:00:00', 45, 50, 40, 'completed', 1, 2, '2023-03-30 13:00:00'),
(5, 10, 'Pronunciation Final', 'Comprehensive pronunciation test', '2023-05-08 11:00:00', 60, 100, 75, 'active', 1, 4, '2023-04-10 14:00:00'),
(6, 11, 'Medical English Final', 'Assessment of medical terminology', '2023-05-12 09:00:00', 90, 100, 70, 'active', 1, 4, '2023-04-15 15:00:00'),
(7, 12, 'Tourism Certification', 'Practical tourism language exam', '2023-05-15 10:00:00', 90, 100, 75, 'active', 1, 2, '2023-04-20 16:00:00'),
(8, 13, 'Slang & Idioms Final', 'Assessment of informal language', '2023-05-18 11:00:00', 60, 80, 60, 'active', 1, 3, '2023-04-25 17:00:00'),
(9, 14, 'Literature Final', 'Comprehensive literature exam', '2023-05-20 09:00:00', 120, 100, 70, 'active', 1, 3, '2023-04-30 18:00:00'),
(10, 15, 'Technical English Exam', 'Assessment of technical vocabulary', '2023-05-22 10:00:00', 90, 100, 75, 'active', 1, 4, '2023-05-05 19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `exam_content`
--

CREATE TABLE `exam_content` (
  `content_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `sequence_order` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_content`
--

INSERT INTO `exam_content` (`content_id`, `exam_id`, `content_type`, `content_title`, `content_data`, `file_path`, `sequence_order`, `uploaded_by`, `uploaded_at`) VALUES
(21, 1, 'document', 'Speaking Rubric', 'Grading criteria for speaking exam', '/exams/speaking_rubric.pdf', 1, 2, '2023-03-25 10:15:00'),
(22, 2, 'document', 'Essay Prompts', 'Possible essay questions for final', '/exams/essay_prompts.pdf', 1, 3, '2023-04-01 11:15:00'),
(23, 3, '', 'Listening Section', 'TOEFL listening comprehension', '/exams/toefl_listening.mp3', 1, 3, '2023-04-05 12:15:00'),
(24, 4, 'image', 'Picture Prompts', 'Visual prompts for kids\' test', '/exams/kids_prompts.jpg', 1, 2, '2023-03-30 13:15:00'),
(25, 5, 'video', 'Pronunciation Samples', 'Example words to pronounce', '/exams/pronunciation_samples.mp4', 1, 4, '2023-04-10 14:15:00'),
(26, 6, 'document', 'Case Studies', 'Medical case studies for exam', '/exams/medical_cases.pdf', 1, 4, '2023-04-15 15:15:00'),
(27, 7, 'document', 'Scenario Cards', 'Tourism scenarios to respond to', '/exams/tourism_scenarios.pdf', 1, 2, '2023-04-20 16:15:00'),
(28, 8, 'video', 'Idiom Contexts', 'Videos showing idiom usage', '/exams/idiom_contexts.mp4', 1, 3, '2023-04-25 17:15:00'),
(29, 9, 'document', 'Literary Excerpts', 'Passages for analysis', '/exams/literary_excerpts.pdf', 1, 3, '2023-04-30 18:15:00'),
(30, 10, 'document', 'Technical Diagrams', 'Diagrams to label and describe', '/exams/technical_diagrams.pdf', 1, 4, '2023-05-05 19:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `exam_response_content`
--

CREATE TABLE `exam_response_content` (
  `content_id` int(11) NOT NULL,
  `result_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `result_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `marks_obtained` decimal(5,2) NOT NULL,
  `status` enum('pass','fail') NOT NULL,
  `feedback` text DEFAULT NULL,
  `evaluated_by` int(11) DEFAULT NULL,
  `evaluated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `sequence_order` int(11) NOT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `status` enum('active','locked') DEFAULT 'active' COMMENT 'Locked lessons are hidden from students',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `course_id`, `title`, `content`, `sequence_order`, `duration`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Introductions', 'Basic greetings and self-introductions', 1, 45, 'active', '2023-02-07 10:00:00', NULL),
(2, 1, 'Present Simple', 'Forming and using present simple tense', 2, 60, 'active', '2023-02-08 11:00:00', NULL),
(3, 1, 'Daily Routines', 'Vocabulary for daily activities', 3, 45, 'active', '2023-02-09 12:00:00', NULL),
(4, 2, 'Past Tenses', 'Comparing past simple and past continuous', 1, 60, 'active', '2023-02-13 10:00:00', NULL),
(5, 2, 'Present Perfect', 'Using present perfect for experiences', 2, 60, 'active', '2023-02-14 11:00:00', NULL),
(6, 2, 'Modals', 'Modal verbs for ability and permission', 3, 60, 'active', '2023-02-15 12:00:00', NULL),
(7, 5, 'IELTS Listening', 'Strategies for listening section', 1, 90, 'active', '2023-02-27 14:00:00', NULL),
(8, 5, 'IELTS Reading', 'Skimming and scanning techniques', 2, 90, 'active', '2023-02-28 14:00:00', NULL),
(9, 5, 'IELTS Writing Task 1', 'Describing graphs and charts', 3, 120, 'active', '2023-03-01 14:00:00', NULL),
(10, 6, 'Small Talk', 'Practicing casual conversations', 1, 45, 'active', '2023-03-02 10:00:00', NULL),
(11, 6, 'Asking Questions', 'Forming different types of questions', 2, 60, 'active', '2023-03-03 11:00:00', NULL),
(12, 7, 'Thesis Statements', 'Crafting strong thesis statements', 1, 60, 'active', '2023-03-06 12:00:00', NULL),
(13, 7, 'Paragraph Structure', 'Organizing academic paragraphs', 2, 60, 'active', '2023-03-07 13:00:00', NULL),
(14, 8, 'TOEFL Reading Strategies', 'Skimming and scanning techniques', 1, 90, 'active', '2023-03-11 14:00:00', NULL),
(15, 8, 'TOEFL Listening Tips', 'Note-taking for listening section', 2, 90, 'active', '2023-03-12 15:00:00', NULL),
(16, 9, 'Animal Vocabulary', 'Learning animal names and sounds', 1, 30, 'active', '2023-03-16 16:00:00', NULL),
(17, 9, 'Colors and Shapes', 'Basic color and shape vocabulary', 2, 30, 'active', '2023-03-17 17:00:00', NULL),
(18, 10, 'Vowel Sounds', 'Practicing English vowel sounds', 1, 45, 'active', '2023-03-21 18:00:00', NULL),
(19, 10, 'Consonant Clusters', 'Difficult consonant combinations', 2, 45, 'active', '2023-03-22 19:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_completion`
--

CREATE TABLE `lesson_completion` (
  `completion_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `completed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_content`
--

CREATE TABLE `lesson_content` (
  `content_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `sequence_order` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_content`
--

INSERT INTO `lesson_content` (`content_id`, `lesson_id`, `content_type`, `content_title`, `content_data`, `file_path`, `sequence_order`, `uploaded_by`, `uploaded_at`) VALUES
(31, 1, 'text', 'Greetings and Self-Introductions', 'Learn how to greet and introduce yourself in English.', NULL, 1, 12, '2025-05-04 21:36:55'),
(32, 1, 'video', 'Greetings Video', NULL, '../librarian/resources/greetings_video.mp4', 2, 12, '2025-05-04 21:36:55'),
(33, 2, 'text', 'Present Simple Tense Rules', 'Explanation of subject-verb agreement and usage of present simple.', NULL, 1, 12, '2025-05-04 21:36:55'),
(34, 2, 'document', 'Present Simple Worksheet', NULL, '../librarian/resources/present_simple_worksheet.pdf', 2, 12, '2025-05-04 21:36:55'),
(35, 3, 'text', 'Daily Routines Vocabulary', 'Common daily activities like wake up, brush teeth, go to school.', NULL, 1, 12, '2025-05-04 21:36:55'),
(36, 3, 'image', 'Daily Routine Chart', NULL, '../librarian/resources/daily_routine_chart.png', 2, 12, '2025-05-04 21:36:55'),
(37, 4, 'text', 'Past Tenses Comparison', 'Understand when to use past simple vs past continuous.', NULL, 1, 12, '2025-05-04 21:36:55'),
(38, 4, 'video', 'Past Tenses Video', NULL, '../librarian/resources/past_tenses_video.mp4', 2, 12, '2025-05-04 21:36:55'),
(39, 5, 'text', 'Present Perfect Usage', 'Learn to express life experiences using present perfect.', NULL, 1, 12, '2025-05-04 21:36:55'),
(40, 5, 'document', 'Present Perfect Exercises', NULL, '../librarian/resources/present_perfect_exercises.pdf', 2, 12, '2025-05-04 21:36:55'),
(41, 6, 'text', 'Using Modals for Ability and Permission', 'Can, could, may, might – how and when to use them.', NULL, 1, 12, '2025-05-04 21:36:55'),
(42, 6, 'image', 'Modals Infographic', NULL, '../librarian/resources/modals_infographic.jpg', 2, 12, '2025-05-04 21:36:55'),
(43, 7, 'text', 'IELTS Listening Strategies', 'Tips to improve your score in the listening section.', NULL, 1, 12, '2025-05-04 21:36:55'),
(44, 7, 'video', 'IELTS Listening Sample', NULL, '../librarian/resources/ielts_listening_sample.mp4', 2, 12, '2025-05-04 21:36:55'),
(45, 8, 'text', 'IELTS Reading Techniques', 'How to skim, scan, and manage your time effectively.', NULL, 1, 12, '2025-05-04 21:36:55'),
(46, 8, 'document', 'Reading Techniques Guide', NULL, '../librarian/resources/reading_techniques_guide.pdf', 2, 12, '2025-05-04 21:36:55'),
(47, 9, 'text', 'IELTS Writing Task 1 Overview', 'Learn to describe line graphs, bar charts, and more.', NULL, 1, 12, '2025-05-04 21:36:55'),
(48, 9, 'image', 'Graph Sample Image', NULL, '../librarian/resources/graph_sample_image.png', 2, 12, '2025-05-04 21:36:55'),
(49, 10, 'text', 'Small Talk in English', 'Engage in polite and casual conversations.', NULL, 1, 12, '2025-05-04 21:36:55'),
(50, 10, 'video', 'Small Talk Demo', NULL, '../librarian/resources/small_talk_demo.mp4', 2, 12, '2025-05-04 21:36:55'),
(51, 11, 'text', 'How to Ask Questions in English', 'Form yes/no, WH-, and tag questions.', NULL, 1, 12, '2025-05-04 21:36:55'),
(52, 11, 'document', 'Question Forms Worksheet', NULL, '../librarian/resources/question_forms_worksheet.pdf', 2, 12, '2025-05-04 21:36:55'),
(53, 12, 'text', 'Creating Strong Thesis Statements', 'Thesis statement purpose and how to write one.', NULL, 1, 12, '2025-05-04 21:36:55'),
(54, 12, 'video', 'Thesis Writing Tutorial', NULL, '../librarian/resources/thesis_writing_tutorial.mp4', 2, 12, '2025-05-04 21:36:55'),
(55, 13, 'text', 'Academic Paragraph Structure', 'Topic sentence, supporting details, and conclusion.', NULL, 1, 12, '2025-05-04 21:36:55'),
(56, 13, 'document', 'Paragraph Structure Guide', NULL, '../librarian/resources/paragraph_structure_guide.pdf', 2, 12, '2025-05-04 21:36:55'),
(57, 14, 'text', 'TOEFL Reading Skills', 'Efficient reading and question strategies.', NULL, 1, 12, '2025-05-04 21:36:55'),
(58, 14, 'image', 'TOEFL Reading Flowchart', NULL, '../librarian/resources/toefl_reading_flowchart.png', 2, 12, '2025-05-04 21:36:55'),
(59, 15, 'text', 'TOEFL Listening Techniques', 'Effective note-taking while listening.', NULL, 1, 12, '2025-05-04 21:36:55'),
(60, 15, 'video', 'TOEFL Listening Sample', NULL, '../librarian/resources/toefl_listening_sample.mp4', 2, 12, '2025-05-04 21:36:55'),
(61, 16, 'text', 'Animal Names and Sounds', 'Basic vocabulary for common animals.', NULL, 1, 12, '2025-05-04 21:36:55'),
(62, 16, 'image', 'Animal Chart', NULL, '../librarian/resources/animal_chart.jpg', 2, 12, '2025-05-04 21:36:55'),
(63, 17, 'text', 'Colors and Shapes in English', 'Learn basic colors and common shapes.', NULL, 1, 12, '2025-05-04 21:36:55'),
(64, 17, 'document', 'Colors and Shapes Worksheet', NULL, '../librarian/resources/colors_shapes_worksheet.pdf', 2, 12, '2025-05-04 21:36:55'),
(65, 18, 'text', 'Practicing Vowel Sounds', 'Short and long vowel pronunciation.', NULL, 1, 12, '2025-05-04 21:36:55'),
(66, 18, '', 'Vowel Practice Audio', NULL, '../librarian/resources/vowel_practice_audio.mp3', 2, 12, '2025-05-04 21:36:55'),
(67, 19, 'text', 'Consonant Clusters Practice', 'Improve clarity with tricky consonant sounds.', NULL, 1, 12, '2025-05-04 21:36:55'),
(68, 19, '', 'Consonant Clusters Audio', NULL, '../librarian/resources/consonant_clusters_audio.mp3', 2, 12, '2025-05-04 21:36:55');

-- --------------------------------------------------------

--
-- Table structure for table `library_resources`
--

CREATE TABLE `library_resources` (
  `resource_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `resource_type` enum('text','image','video','document','audio','other') NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `external_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `language` varchar(50) DEFAULT 'English',
  `level` enum('beginner','elementary','intermediate','upper-intermediate','advanced','proficiency') DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('available','unavailable') DEFAULT 'available' COMMENT 'If unavailable, only visible to creator',
  `usage_count` int(11) DEFAULT 0 COMMENT 'Number of courses using this resource'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `library_resources`
--

INSERT INTO `library_resources` (`resource_id`, `title`, `author`, `resource_type`, `file_path`, `external_url`, `description`, `language`, `level`, `category`, `tags`, `added_by`, `added_at`, `updated_at`, `status`, `usage_count`) VALUES
(1, 'Essential English Grammar', 'Raymond Murphy', 'document', '/librarian/resources/english_grammar.pdf', NULL, 'Comprehensive grammar reference', 'English', 'intermediate', 'Grammar', 'grammar,reference', 9, '2025-05-03 11:37:55', NULL, 'available', 0),
(2, 'IELTS Practice Tests', 'Cambridge', 'document', '/librarian/resources/ielts_practice.pdf', NULL, 'Official IELTS practice materials', 'English', 'advanced', 'Exam Preparation', 'ielts,exam', 9, '2025-05-03 11:37:55', NULL, 'available', 0),
(3, 'Business English Podcasts', 'Various', 'audio', '/librarian/resources/business_podcasts.mp3', NULL, 'Collection of business English podcasts', 'English', 'upper-intermediate', 'Listening', 'business,listening', 9, '2025-05-03 11:37:55', NULL, 'available', 0),
(4, 'English Pronunciation Guide', 'BBC Learning', 'video', '/librarian/resources/pronunciation.mp4', NULL, 'Video guide to English pronunciation', 'English', 'beginner', 'Pronunciation', 'pronunciation,video', 9, '2025-05-03 11:37:55', NULL, 'available', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `related_id` int(11) DEFAULT NULL COMMENT 'ID related to the notification type (course_id, exam_id, etc.)',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `notification_type`, `related_id`, `is_read`, `created_at`) VALUES
(1, 5, 'New Lesson Available', 'A new lesson \"Present Simple\" is available in English for Beginners', 'lesson', 2, 0, '2023-02-08 11:05:00'),
(2, 8, 'Assignment Graded', 'Your IELTS Writing Task 1 has been graded', 'assignment', 4, 1, '2023-04-06 10:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','fill_blank','essay','matching') NOT NULL,
  `points` int(11) DEFAULT 1,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `quiz_id`, `question_text`, `question_type`, `points`, `difficulty`) VALUES
(1, 1, 'Which sentence is correct?', 'multiple_choice', 10, 'easy'),
(2, 1, 'True or False: \"He go to school\" is grammatically correct.', 'true_false', 5, 'easy'),
(3, 1, 'Write three sentences about your family using present simple.', 'essay', 15, 'medium'),
(4, 3, 'What is the main purpose of IELTS Listening Section 1?', 'multiple_choice', 10, 'medium'),
(5, 3, 'Fill in the blank: In IELTS Listening, you hear the recording ____ time(s).', 'fill_blank', 10, 'easy'),
(6, 6, 'Which is the appropriate response to \"How are you?\"', 'multiple_choice', 10, 'easy'),
(7, 6, 'Complete the dialogue: A: \"What do you do?\" B: \"________________\"', 'fill_blank', 10, 'medium'),
(8, 7, 'True or False: \"Moreover\" is used to introduce a contrasting idea', 'true_false', 5, 'medium'),
(9, 7, 'Rewrite the sentence in academic style: \"The results were really good.\"', 'essay', 15, 'hard'),
(10, 8, 'Which sentence is grammatically correct?', 'multiple_choice', 10, 'medium'),
(11, 9, 'Match the animal to its name', 'matching', 15, 'easy'),
(12, 10, 'Listen and identify the vowel sound you hear', 'multiple_choice', 10, 'medium'),
(13, 11, 'What does \"diagnosis\" mean?', 'multiple_choice', 10, 'medium'),
(14, 12, 'What would you say to a tourist who lost their passport?', 'essay', 15, 'medium'),
(15, 13, 'What does \"hit the books\" mean?', 'multiple_choice', 10, 'easy');

-- --------------------------------------------------------

--
-- Table structure for table `question_content`
--

CREATE TABLE `question_content` (
  `content_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL COMMENT 'Time limit in minutes',
  `passing_score` int(11) DEFAULT 60 COMMENT 'Passing score in percentage',
  `attempts_allowed` int(11) DEFAULT 1,
  `is_randomized` tinyint(1) DEFAULT 0,
  `status` enum('active','locked') DEFAULT 'active',
  `sequence_order` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `course_id`, `title`, `description`, `time_limit`, `passing_score`, `attempts_allowed`, `is_randomized`, `status`, `sequence_order`, `created_at`, `created_by`) VALUES
(1, 1, 'Basic Grammar Quiz', 'Assessment on simple present and basic vocabulary', 30, 70, 2, 1, 'active', 1, '2023-02-12 09:00:00', 2),
(2, 2, 'Past Tenses Quiz', 'Assessment on past simple and past continuous', 45, 75, 1, 0, 'active', 1, '2023-02-18 10:00:00', 2),
(3, 5, 'IELTS Listening Practice', 'Practice test for listening section', 60, 65, 2, 1, 'active', 1, '2023-03-10 14:00:00', 3),
(4, 6, 'Conversation Quiz', 'Assessment on basic conversation skills', 20, 70, 2, 1, 'active', 1, '2023-03-04 10:00:00', 2),
(5, 7, 'Academic Vocabulary', 'Test of academic words and phrases', 30, 75, 1, 0, 'active', 1, '2023-03-08 11:00:00', 3),
(6, 8, 'TOEFL Structure Quiz', 'Grammar and structure for TOEFL', 45, 70, 2, 1, 'active', 1, '2023-03-13 12:00:00', 3),
(7, 9, 'Children\'s Vocabulary', 'Basic vocabulary recognition', 15, 80, 3, 0, 'active', 1, '2023-03-18 13:00:00', 2),
(8, 10, 'Pronunciation Test', 'Identifying and producing sounds', 25, 75, 2, 0, 'active', 1, '2023-03-23 14:00:00', 4),
(9, 11, 'Medical Terms Quiz', 'Test of medical vocabulary', 30, 70, 1, 1, 'active', 1, '2023-03-28 15:00:00', 4),
(10, 12, 'Tourism Quiz', 'Vocabulary for travel industry', 20, 75, 2, 1, 'active', 1, '2023-04-02 16:00:00', 2),
(11, 13, 'Idioms Quiz', 'Understanding common idioms', 25, 70, 2, 0, 'active', 1, '2023-04-07 17:00:00', 3),
(12, 14, 'Literary Terms', 'Test of literary devices', 35, 70, 1, 1, 'active', 1, '2023-04-12 18:00:00', 3),
(13, 15, 'Technical English', 'Vocabulary for engineering', 30, 75, 2, 1, 'active', 1, '2023-04-17 19:00:00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `status` enum('in_progress','completed','timed_out') DEFAULT 'in_progress',
  `attempt_number` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `quiz_id`, `student_id`, `start_time`, `end_time`, `score`, `status`, `attempt_number`) VALUES
(1, 2, 8, '2025-05-04 16:08:21', NULL, NULL, 'in_progress', 1),
(2, 1, 7, '2025-05-04 16:14:43', NULL, NULL, 'in_progress', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_content`
--

CREATE TABLE `quiz_content` (
  `content_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `sequence_order` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_ratings`
--

CREATE TABLE `resource_ratings` (
  `rating_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL COMMENT 'Rating between 1 and 5',
  `comment` text DEFAULT NULL,
  `rated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_usage`
--

CREATE TABLE `resource_usage` (
  `usage_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `added_by` int(11) NOT NULL,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource_usage`
--

INSERT INTO `resource_usage` (`usage_id`, `resource_id`, `course_id`, `added_by`, `added_at`) VALUES
(1, 1, 2, 2, '2023-02-19 10:00:00'),
(2, 2, 5, 3, '2023-03-01 14:00:00'),
(3, 3, 4, 4, '2023-02-25 13:00:00'),
(4, 4, 1, 2, '2023-02-11 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `response_content`
--

CREATE TABLE `response_content` (
  `content_id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `setting_name` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_assignment_access`
--

CREATE TABLE `student_assignment_access` (
  `access_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `is_accessible` tinyint(1) DEFAULT 0,
  `unlocked_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_exam_access`
--

CREATE TABLE `student_exam_access` (
  `access_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `is_accessible` tinyint(1) DEFAULT 0,
  `unlocked_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_lesson_access`
--

CREATE TABLE `student_lesson_access` (
  `access_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `is_accessible` tinyint(1) DEFAULT 0,
  `unlocked_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_lesson_access`
--

INSERT INTO `student_lesson_access` (`access_id`, `student_id`, `lesson_id`, `is_accessible`, `unlocked_at`) VALUES
(1, 7, 1, 1, '2025-05-04 22:17:33'),
(2, 7, 2, 1, '2025-05-04 21:44:47'),
(4, 7, 3, 1, '2025-05-04 22:17:35'),
(17, 7, 4, 1, '2025-05-04 21:44:41'),
(18, 7, 5, 1, '2025-05-04 21:44:42'),
(19, 7, 6, 1, '2025-05-04 21:43:47'),
(29, 7, 18, 1, '2025-05-04 22:02:04'),
(30, 7, 19, 1, '2025-05-04 22:02:08');

-- --------------------------------------------------------

--
-- Table structure for table `student_progress`
--

CREATE TABLE `student_progress` (
  `progress_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  `lessons_completed` int(11) DEFAULT 0,
  `assignments_completed` int(11) DEFAULT 0,
  `quizzes_completed` int(11) DEFAULT 0,
  `exams_completed` int(11) DEFAULT 0,
  `start_date` datetime DEFAULT current_timestamp(),
  `completion_date` datetime DEFAULT NULL,
  `last_accessed` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_progress`
--

INSERT INTO `student_progress` (`progress_id`, `student_id`, `course_id`, `progress_percentage`, `lessons_completed`, `assignments_completed`, `quizzes_completed`, `exams_completed`, `start_date`, `completion_date`, `last_accessed`) VALUES
(1, 5, 1, 30.00, 1, 0, 0, 0, '2023-03-01 08:00:00', NULL, '2023-06-21 14:00:00'),
(2, 6, 1, 15.00, 1, 0, 0, 0, '2023-03-01 08:05:00', NULL, '2023-06-20 15:00:00'),
(3, 8, 5, 20.00, 0, 1, 0, 0, '2023-03-05 12:00:00', NULL, '2025-05-04 19:50:05'),
(4, 7, 2, 0.00, 0, 0, 0, 0, '2025-05-04 16:56:39', NULL, '2025-05-04 17:18:41'),
(12, 7, 4, 0.00, 0, 0, 0, 0, '2025-05-04 17:18:25', NULL, '2025-05-04 17:18:25'),
(16, 8, 2, 0.00, 0, 0, 0, 0, '2025-05-04 18:26:36', NULL, '2025-05-05 00:11:19'),
(27, 11, 10, 0.00, 0, 0, 0, 0, '2025-05-04 20:04:02', NULL, '2025-05-04 20:44:35'),
(29, 11, 5, 0.00, 0, 0, 0, 0, '2025-05-04 20:14:19', NULL, '2025-05-04 20:44:21'),
(38, 7, 1, 0.00, 0, 0, 0, 0, '2025-05-04 20:56:54', NULL, '2025-05-07 08:34:18'),
(45, 7, 6, 0.00, 0, 0, 0, 0, '2025-05-04 22:01:57', NULL, '2025-05-07 08:35:39');

-- --------------------------------------------------------

--
-- Table structure for table `student_quiz_access`
--

CREATE TABLE `student_quiz_access` (
  `access_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `is_accessible` tinyint(1) DEFAULT 0,
  `unlocked_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_quiz_access`
--

INSERT INTO `student_quiz_access` (`access_id`, `student_id`, `quiz_id`, `is_accessible`, `unlocked_at`) VALUES
(1, 8, 2, 1, NULL),
(2, 7, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_responses`
--

CREATE TABLE `student_responses` (
  `response_id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option_id` int(11) DEFAULT NULL,
  `text_response` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_date` datetime DEFAULT current_timestamp(),
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `status` enum('submitted','graded','late','resubmitted') DEFAULT 'submitted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`submission_id`, `assignment_id`, `student_id`, `submission_date`, `grade`, `feedback`, `graded_by`, `graded_at`, `status`) VALUES
(1, 4, 8, '2023-04-04 18:30:00', 85.00, 'Good analysis but watch your word count', 3, '2023-04-06 10:00:00', 'graded');

-- --------------------------------------------------------

--
-- Table structure for table `submission_content`
--

CREATE TABLE `submission_content` (
  `content_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `content_type` enum('text','image','video','document','other') NOT NULL,
  `content_title` varchar(100) NOT NULL,
  `content_data` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_ratings`
--

CREATE TABLE `teacher_ratings` (
  `rating_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL COMMENT 'Rating between 1 and 5',
  `comment` text DEFAULT NULL,
  `rated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','teacher','student','librarian') NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `date_registered` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`, `role`, `profile_picture`, `bio`, `date_registered`, `last_login`, `status`, `reset_token`, `reset_expires`) VALUES
(1, 'admin1', 'admin123', 'admin1@gmail.com', 'Red', 'Avenue', 'admin', NULL, 'System administrator with full access', '2023-01-15 09:00:00', '2025-05-04 22:16:50', 'active', NULL, NULL),
(2, 'admin2', 'admin123', 'admin2@gmail.com', 'Jamie', 'Jaime', 'admin', NULL, 'IT department head', '2023-01-20 10:15:00', '2023-06-21 09:45:00', 'active', NULL, NULL),
(3, 'teacher1', 'teacher123', 'teacher1@gmail.com', 'Michael', 'Jackson', 'teacher', NULL, 'king of pop', '2023-02-01 08:00:00', '2025-05-07 09:40:36', 'active', NULL, NULL),
(4, 'teacher2', 'teacher123', 'teacher2@gmail.com', 'Bongbong', 'Marcos', 'teacher', NULL, 'English literature specialist', '2023-02-05 09:30:00', '2023-06-20 16:45:00', 'active', NULL, NULL),
(5, 'teacher3', 'teacher123', 'teacher3@gmail.com', 'Divine', 'Aguilar', 'teacher', NULL, ' ', '2023-02-10 10:45:00', '2023-06-21 11:30:00', 'active', NULL, NULL),
(6, 'teacher4', 'teacher123', 'teacher4@gmail.com', 'Jennifer', 'Lawrence', 'teacher', NULL, 'jennifer lawrence', '2023-02-15 11:00:00', '2023-06-19 14:15:00', 'active', NULL, NULL),
(7, 'student1', 'student123', 'student1@gmail.com', 'Bob', 'Hitler', 'student', NULL, 'Love english', '2023-03-01 13:00:00', '2025-05-07 08:34:12', 'active', NULL, NULL),
(8, 'student2', 'student123', 'student2@gmail.com', 'Eren', 'Yeager', 'student', NULL, 'Duolingo starter', '2023-03-05 14:15:00', '2025-05-04 23:25:36', 'active', NULL, NULL),
(9, 'student3', 'student123', 'student3@gmail.com', 'Chris', 'Evan', 'student', NULL, 'business english', '2023-03-10 15:30:00', '2025-05-04 22:03:09', 'active', NULL, NULL),
(10, 'student4', 'student123', 'student4@gmail.com', 'Sabrina', 'Carpenter', 'student', NULL, 'Business english', '2023-03-15 16:45:00', '2025-05-04 22:02:52', 'active', NULL, NULL),
(11, 'student5', 'student123', 'student5@gmail.com', 'Ethan', 'Carter', 'student', NULL, 'Psychology major', '2023-03-20 17:00:00', '2025-05-04 22:03:28', 'active', NULL, NULL),
(12, 'librarian', 'librarian123', 'librarian123@gmail.com', 'Leni', 'Robredo', 'librarian', NULL, 'Head librarian', '2023-04-01 09:00:00', '2023-06-21 12:30:00', 'active', NULL, NULL),
(13, 'teacher5', 'teacher123', 'teacher5@gmail.com', 'Juan', 'Dela Cruz', 'teacher', NULL, 'TESOL certified with 8 years experience', '2023-03-01 09:00:00', '2023-06-22 14:00:00', 'active', NULL, NULL),
(14, 'teacher6', 'teacher123', 'teacher6@gmail.com', 'Maria', 'Reyes', 'teacher', NULL, 'IELTS specialist and pronunciation coach', '2023-03-05 10:00:00', '2023-06-21 15:30:00', 'active', NULL, NULL),
(15, 'teacher7', 'teacher123', 'teacher7@gmail.com', 'Carlos', 'Santos', 'teacher', NULL, 'Business English expert', '2023-03-10 11:00:00', '2023-06-22 10:45:00', 'active', NULL, NULL),
(16, 'teacher8', 'teacher123', 'teacher8@gmail.com', 'Sofia', 'Gonzales', 'teacher', NULL, 'Children\'s English specialist', '2023-03-15 12:00:00', '2023-06-21 16:15:00', 'active', NULL, NULL),
(17, 'teacher9', 'teacher123', 'teacher9@gmail.com', 'Kobe', 'Bryan', 'teacher', NULL, 'TOEFL and academic writing instructor', '2023-03-20 13:00:00', '2023-06-22 11:30:00', 'active', NULL, NULL),
(18, 'teacher10', 'teacher123', 'teacher10@gmail.com', 'Anna', 'Tan', 'teacher', NULL, 'Conversational English expert', '2023-03-25 14:00:00', '2023-06-21 17:00:00', 'active', NULL, NULL),
(19, 'teacher11', 'teacher123', 'teacher11@gmail.com', 'Michael', 'Ong', 'teacher', NULL, 'Technical English instructor', '2023-04-01 15:00:00', '2023-06-22 09:15:00', 'active', NULL, NULL),
(20, 'teacher12', 'teacher123', 'teacher12@gmail.com', 'Lisa', 'Chua', 'teacher', NULL, 'Medical English professional', '2023-04-05 16:00:00', '2023-06-21 14:45:00', 'active', NULL, NULL),
(21, 'teacher13', 'teacher123', 'teacher13@gmail.com', 'Robert', 'Uy', 'teacher', NULL, 'Literature and creative writing teacher', '2023-04-10 17:00:00', '2023-06-22 13:00:00', 'active', NULL, NULL),
(22, 'teacher14', 'teacher123', 'teacher14@gmail.com', 'Grace', 'Lee', 'teacher', NULL, 'English for tourism specialist', '2023-04-15 18:00:00', '2023-06-21 18:30:00', 'active', NULL, NULL),
(23, 'student10', 'student123', 'student10@gmail.com', 'student', '10', 'student', NULL, NULL, '2025-05-07 06:33:51', '2025-05-07 06:34:10', 'active', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `answer_options`
--
ALTER TABLE `answer_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `assignment_content`
--
ALTER TABLE `assignment_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`course_id`,`date`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `issued_by` (`issued_by`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `course_schedule`
--
ALTER TABLE `course_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `course_teacher_id` (`course_teacher_id`);

--
-- Indexes for table `course_sharing`
--
ALTER TABLE `course_sharing`
  ADD PRIMARY KEY (`sharing_id`),
  ADD UNIQUE KEY `course_id` (`course_id`,`shared_with_id`),
  ADD KEY `shared_with_id` (`shared_with_id`),
  ADD KEY `shared_by_id` (`shared_by_id`);

--
-- Indexes for table `course_teachers`
--
ALTER TABLE `course_teachers`
  ADD PRIMARY KEY (`course_teacher_id`),
  ADD UNIQUE KEY `course_id` (`course_id`,`teacher_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `fk_enrollments_schedule` (`schedule_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `exam_content`
--
ALTER TABLE `exam_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `exam_response_content`
--
ALTER TABLE `exam_response_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `result_id` (`result_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `evaluated_by` (`evaluated_by`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `lesson_completion`
--
ALTER TABLE `lesson_completion`
  ADD PRIMARY KEY (`completion_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`lesson_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `lesson_content`
--
ALTER TABLE `lesson_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `lesson_id` (`lesson_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `library_resources`
--
ALTER TABLE `library_resources`
  ADD PRIMARY KEY (`resource_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `question_content`
--
ALTER TABLE `question_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `quiz_content`
--
ALTER TABLE `quiz_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `resource_ratings`
--
ALTER TABLE `resource_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `resource_id` (`resource_id`,`teacher_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `resource_usage`
--
ALTER TABLE `resource_usage`
  ADD PRIMARY KEY (`usage_id`),
  ADD UNIQUE KEY `resource_id` (`resource_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `response_content`
--
ALTER TABLE `response_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `response_id` (`response_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `student_assignment_access`
--
ALTER TABLE `student_assignment_access`
  ADD PRIMARY KEY (`access_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`assignment_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `student_exam_access`
--
ALTER TABLE `student_exam_access`
  ADD PRIMARY KEY (`access_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`exam_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `student_lesson_access`
--
ALTER TABLE `student_lesson_access`
  ADD PRIMARY KEY (`access_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`lesson_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `student_quiz_access`
--
ALTER TABLE `student_quiz_access`
  ADD PRIMARY KEY (`access_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`quiz_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `student_responses`
--
ALTER TABLE `student_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_option_id` (`selected_option_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `graded_by` (`graded_by`);

--
-- Indexes for table `submission_content`
--
ALTER TABLE `submission_content`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `teacher_ratings`
--
ALTER TABLE `teacher_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`,`student_id`,`course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `answer_options`
--
ALTER TABLE `answer_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `assignment_content`
--
ALTER TABLE `assignment_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `course_schedule`
--
ALTER TABLE `course_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

--
-- AUTO_INCREMENT for table `course_sharing`
--
ALTER TABLE `course_sharing`
  MODIFY `sharing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_teachers`
--
ALTER TABLE `course_teachers`
  MODIFY `course_teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `exam_content`
--
ALTER TABLE `exam_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `exam_response_content`
--
ALTER TABLE `exam_response_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `lesson_completion`
--
ALTER TABLE `lesson_completion`
  MODIFY `completion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_content`
--
ALTER TABLE `lesson_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `library_resources`
--
ALTER TABLE `library_resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `question_content`
--
ALTER TABLE `question_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quiz_content`
--
ALTER TABLE `quiz_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_ratings`
--
ALTER TABLE `resource_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_usage`
--
ALTER TABLE `resource_usage`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `response_content`
--
ALTER TABLE `response_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_assignment_access`
--
ALTER TABLE `student_assignment_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_exam_access`
--
ALTER TABLE `student_exam_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_lesson_access`
--
ALTER TABLE `student_lesson_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `student_quiz_access`
--
ALTER TABLE `student_quiz_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_responses`
--
ALTER TABLE `student_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `submission_content`
--
ALTER TABLE `submission_content`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_ratings`
--
ALTER TABLE `teacher_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `answer_options`
--
ALTER TABLE `answer_options`
  ADD CONSTRAINT `answer_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_content`
--
ALTER TABLE `assignment_content`
  ADD CONSTRAINT `assignment_content_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_content_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_3` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_schedule`
--
ALTER TABLE `course_schedule`
  ADD CONSTRAINT `course_schedule_ibfk_1` FOREIGN KEY (`course_teacher_id`) REFERENCES `course_teachers` (`course_teacher_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_sharing`
--
ALTER TABLE `course_sharing`
  ADD CONSTRAINT `course_sharing_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_sharing_ibfk_2` FOREIGN KEY (`shared_with_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_sharing_ibfk_3` FOREIGN KEY (`shared_by_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_teachers`
--
ALTER TABLE `course_teachers`
  ADD CONSTRAINT `course_teachers_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_teachers_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollments_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `course_schedule` (`schedule_id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_content`
--
ALTER TABLE `exam_content`
  ADD CONSTRAINT `exam_content_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_content_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_response_content`
--
ALTER TABLE `exam_response_content`
  ADD CONSTRAINT `exam_response_content_ibfk_1` FOREIGN KEY (`result_id`) REFERENCES `exam_results` (`result_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_3` FOREIGN KEY (`evaluated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_completion`
--
ALTER TABLE `lesson_completion`
  ADD CONSTRAINT `lesson_completion_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_completion_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_content`
--
ALTER TABLE `lesson_content`
  ADD CONSTRAINT `lesson_content_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_content_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `library_resources`
--
ALTER TABLE `library_resources`
  ADD CONSTRAINT `library_resources_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `question_content`
--
ALTER TABLE `question_content`
  ADD CONSTRAINT `question_content_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_content_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_content`
--
ALTER TABLE `quiz_content`
  ADD CONSTRAINT `quiz_content_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_content_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `resource_ratings`
--
ALTER TABLE `resource_ratings`
  ADD CONSTRAINT `resource_ratings_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `library_resources` (`resource_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_ratings_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `resource_usage`
--
ALTER TABLE `resource_usage`
  ADD CONSTRAINT `resource_usage_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `library_resources` (`resource_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_usage_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_usage_ibfk_3` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `response_content`
--
ALTER TABLE `response_content`
  ADD CONSTRAINT `response_content_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `student_responses` (`response_id`) ON DELETE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `student_assignment_access`
--
ALTER TABLE `student_assignment_access`
  ADD CONSTRAINT `student_assignment_access_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_assignment_access_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_exam_access`
--
ALTER TABLE `student_exam_access`
  ADD CONSTRAINT `student_exam_access_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_exam_access_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_lesson_access`
--
ALTER TABLE `student_lesson_access`
  ADD CONSTRAINT `student_lesson_access_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_lesson_access_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD CONSTRAINT `student_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_progress_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_quiz_access`
--
ALTER TABLE `student_quiz_access`
  ADD CONSTRAINT `student_quiz_access_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_quiz_access_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_responses`
--
ALTER TABLE `student_responses`
  ADD CONSTRAINT `student_responses_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`attempt_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_responses_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_responses_ibfk_3` FOREIGN KEY (`selected_option_id`) REFERENCES `answer_options` (`option_id`) ON DELETE SET NULL;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `submission_content`
--
ALTER TABLE `submission_content`
  ADD CONSTRAINT `submission_content_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_ratings`
--
ALTER TABLE `teacher_ratings`
  ADD CONSTRAINT `teacher_ratings_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_ratings_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_ratings_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
