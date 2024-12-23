-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2024 at 06:34 PM
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
-- Database: `sa`
--

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `dept_name`) VALUES
(1, 'Engineer'),
(2, 'IT'),
(3, 'Marketing'),
(4, 'Finance'),
(5, 'HR');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `dept_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `dated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`dept_id`, `p_id`, `detail`, `dated`) VALUES
(2, 1, 'ทำได้งานดีมาก ผมจะตบเงินให้อย่างงาม', '2024-10-13');

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE `form` (
  `form_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `evaluator` int(11) NOT NULL,
  `evaluatee` int(11) NOT NULL,
  `score1` int(11) NOT NULL,
  `score2` int(11) NOT NULL,
  `score3` int(11) NOT NULL,
  `score4` int(11) NOT NULL,
  `score5` int(11) NOT NULL,
  `score6` int(11) NOT NULL,
  `score7` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form`
--

INSERT INTO `form` (`form_id`, `topic_id`, `evaluator`, `evaluatee`, `score1`, `score2`, `score3`, `score4`, `score5`, `score6`, `score7`) VALUES
(1, 1, 2, 1, 5, 5, 4, 5, 4, 4, 5),
(2, 2, 2, 1, 4, 4, 4, 5, 4, 3, 4),
(3, 1, 1, 2, 3, 3, 2, 2, 3, 2, 3),
(4, 2, 1, 2, 3, 2, 3, 3, 3, 3, 2),
(5, 1, 2, 3, 5, 5, 5, 3, 4, 3, 4),
(6, 2, 2, 3, 4, 3, 4, 4, 4, 4, 4),
(7, 1, 4, 5, 2, 3, 2, 2, 2, 2, 3),
(8, 2, 4, 5, 2, 1, 2, 1, 2, 1, 2),
(9, 1, 5, 4, 2, 1, 1, 2, 1, 2, 2),
(10, 2, 5, 4, 1, 2, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `p_id` int(10) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `sal` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pos_id` int(10) NOT NULL,
  `dept_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`p_id`, `fname`, `lname`, `sal`, `email`, `password`, `pos_id`, `dept_id`) VALUES
(1, 'โอสธี', 'สุขภูตานนท์', 20000, 'sukputanond_o@gmail.com', '1234', 2, 2),
(2, 'พร้อมเพชร', 'อัมพันจันทร์', 60000, 'aumpunjun_p@gmail.com', '1234', 1, 5),
(3, 'สุรศักดิ์', 'สุวรรณรัตน์', 90000, 'suwannarat_s@gmail.com', '1234', 3, 2),
(4, 'สมชาย', 'วิเศษ', 30000, 'somchai@gmail.com', '1234', 2, 1),
(5, 'สมหญิง', 'ทองดี', 28000, 'somying@gmail.com', '1234', 2, 2),
(6, 'อภิชาติ', 'เศรษฐกิจ', 35000, 'apichat@gmail.com', '1234', 2, 3),
(7, 'สุพรรณ', 'ทองคำ', 25000, 'suphan@gmail.com', '1234', 2, 1),
(8, 'มนต์ชัย', 'ศรีเจริญ', 40000, 'montchai@gmail.com', '1234', 2, 2),
(9, 'นิรันดร์', 'พัฒนกิจ', 32000, 'nirun@gmail.com', '1234', 2, 3),
(10, 'พิชิต', 'สุขสำราญ', 29000, 'pichit@gmail.com', '1234', 2, 4),
(11, 'จิราภรณ์', 'ปรีดี', 27000, 'jiraporn@gmail.com', '1234', 2, 1),
(12, 'รัตนาภรณ์', 'บัวงาม', 31000, 'rattanaporn@gmail.com', '1234', 2, 2),
(13, 'ธีรพัฒน์', 'ทองพูน', 33000, 'theerapat@gmail.com', '1234', 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `pos_id` int(11) NOT NULL,
  `pos_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`pos_id`, `pos_name`) VALUES
(1, 'HR'),
(2, 'User'),
(3, 'Chief');

-- --------------------------------------------------------

--
-- Table structure for table `topic`
--

CREATE TABLE `topic` (
  `topic_id` int(11) NOT NULL,
  `topic_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topic`
--

INSERT INTO `topic` (`topic_id`, `topic_name`) VALUES
(1, 'ประเมินด้านการทำงาน'),
(2, 'ประเมินความประพฤติ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD KEY `FK_dept` (`dept_id`),
  ADD KEY `FK_person` (`p_id`);

--
-- Indexes for table `form`
--
ALTER TABLE `form`
  ADD PRIMARY KEY (`form_id`),
  ADD KEY `FK_topic` (`topic_id`),
  ADD KEY `FK_evaluator` (`evaluator`),
  ADD KEY `FK_evaluatee` (`evaluatee`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `FK_pos` (`pos_id`),
  ADD KEY `FK_dept_id` (`dept_id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`pos_id`);

--
-- Indexes for table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`topic_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form`
--
ALTER TABLE `form`
  MODIFY `form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `FK_dept` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `FK_person` FOREIGN KEY (`p_id`) REFERENCES `person` (`p_id`);

--
-- Constraints for table `form`
--
ALTER TABLE `form`
  ADD CONSTRAINT `FK_evaluatee` FOREIGN KEY (`evaluatee`) REFERENCES `person` (`p_id`),
  ADD CONSTRAINT `FK_evaluator` FOREIGN KEY (`evaluator`) REFERENCES `person` (`p_id`),
  ADD CONSTRAINT `FK_topic` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`topic_id`);

--
-- Constraints for table `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `FK_dept_id` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `FK_pos` FOREIGN KEY (`pos_id`) REFERENCES `position` (`pos_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
