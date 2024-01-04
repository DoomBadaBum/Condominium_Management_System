-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 03, 2024 at 06:07 AM
-- Server version: 5.7.33
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `condo_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_log`
--

CREATE TABLE `access_log` (
  `log_id` int(20) NOT NULL,
  `resident_id` int(20) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `user_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `announcement_id` int(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL,
  `worker_id` int(11) DEFAULT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`announcement_id`, `title`, `content`, `date`, `worker_id`, `media_url`, `time`) VALUES
(3, 'Test123', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tincidunt aliquet nibh, sit amet sollicitudin ipsum gravida ac. Aenean id urna ipsum. Mauris non orci euismod, malesuada sem quis, porta magna. Aliquam ultricies egestas erat eget sollicitudin. Suspendisse tincidunt nec odio vulputate aliquam. Ut in sem id enim gravida rutrum et at risus. Nunc quis nisl in nulla placerat euismod ut a augue. Morbi tempor euismod gravida. Phasellus nec pulvinar dolor. Cras rutrum enim blandit est eleifend, ac sagittis leo vehicula. Nulla sit amet cursus nunc. Nulla quis cursus nisl. Nam porttitor magna id justo volutpat porta. Etiam maximus nulla eget orci sollicitudin, ut venenatis sem euismod. Sed arcu tellus, commodo et tincidunt a, cursus vitae justo.', '2023-12-27', 3, 'media_uploads/658c5bf0c9512_DSAI.jpg', '17:16:32'),
(8, 'Test123', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin ac molestie urna. Praesent feugiat, felis vel condimentum rutrum, turpis lacus tempor urna, at consectetur nulla ligula vel dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis sodales nunc quis sodales interdum. Nulla magna nunc, tristique vel hendrerit quis, tincidunt ut enim. Mauris magna velit, molestie vehicula pretium imperdiet, pharetra eget est. Phasellus et ante nec eros euismod rutrum sit amet euismod elit. In a diam dui.', '2024-01-03', 3, 'media_uploads/65947b5d82047_ahmad maslan.jpg', '05:08:45'),
(10, '123', '123', '2024-01-03', 3, NULL, '05:09:07');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `facility_id` int(20) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `user_id`, `facility_id`, `booking_date`, `start_time`, `end_time`) VALUES
(9, 4, 1, '2023-12-27', '07:59:00', '08:59:00'),
(15, 5, 1, '2024-01-04', '11:42:00', '00:42:00'),
(16, 5, 1, '2024-01-01', '11:42:00', '00:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(3, 'Appliance Repair'),
(2, 'Electrical'),
(1, 'Plumbing');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `document_id` int(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `upload_date` date NOT NULL,
  `resident_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

CREATE TABLE `facility` (
  `facility_id` int(20) NOT NULL,
  `facility_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`facility_id`, `facility_name`) VALUES
(1, 'Badminton Court');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_assignment`
--

CREATE TABLE `maintenance_assignment` (
  `assignment_id` int(20) NOT NULL,
  `request_id` int(20) NOT NULL,
  `worker_id` int(20) NOT NULL,
  `assigned_date` date NOT NULL,
  `completion_date` date NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_request`
--

CREATE TABLE `maintenance_request` (
  `request_id` int(20) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(20) NOT NULL,
  `assigned_worker_id` int(11) DEFAULT NULL,
  `assignment_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `urgency` varchar(50) DEFAULT NULL,
  `user_id` int(20) NOT NULL,
  `category_id` int(20) NOT NULL,
  `request_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `maintenance_request`
--

INSERT INTO `maintenance_request` (`request_id`, `description`, `status`, `assigned_worker_id`, `assignment_date`, `location`, `urgency`, `user_id`, `category_id`, `request_date`, `completion_date`) VALUES
(7, 'Blackout', 'Completed', 3, '2023-12-28 05:37:55', 'A02', 'High', 4, 2, '2023-12-27', '2023-12-28'),
(8, 'Tersumbat', 'Completed', 3, '2023-12-28 07:20:11', 'A03', 'Low', 4, 1, '2023-12-27', '2023-12-28'),
(9, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam dapibus et risus vitae efficitur. Vestibulum in augue eget enim ullamcorper auctor. Morbi sit amet est lorem. Curabitur commodo efficitur justo ut maximus. Aenean dictum rutrum leo at mollis. Praesent viverra interdum malesuada. Ut malesuada sed enim eu fermentum. Nunc rutrum iaculis viverra. Mauris scelerisque sem a odio rutrum hendrerit. Aliquam quis est in metus viverra convallis eu in ante. Aenean eu sem eget purus hendrerit pretium dapibus eu nibh. Proin facilisis tellus sed euismod lacinia. Ut blandit aliquet finibus. Maecenas mauris mi, finibus ut consectetur ut, tempor sed sapien. Praesent viverra sed nisl ac pharetra. Aliquam pulvinar nulla sit amet enim mattis egestas.', 'Pending', NULL, NULL, '02', 'Medium', 5, 3, '2024-01-03', NULL),
(12, 'Kipas Rosak', 'Pending', NULL, NULL, '02', 'Low', 5, 3, '2024-01-03', NULL),
(13, 'Tandas Tersumbat', 'Pending', NULL, NULL, '02', 'High', 5, 1, '2024-01-03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_worker`
--

CREATE TABLE `maintenance_worker` (
  `worker_id` int(20) NOT NULL,
  `worker_name` varchar(100) NOT NULL,
  `skillset` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `maintenance_worker`
--

INSERT INTO `maintenance_worker` (`worker_id`, `worker_name`, `skillset`) VALUES
(1, 'Iman', 'Electrician');

-- --------------------------------------------------------

--
-- Table structure for table `resident`
--

CREATE TABLE `resident` (
  `resident_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(20) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'resident'),
(3, 'worker');

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `unit_id` int(20) NOT NULL,
  `unit_number` varchar(20) NOT NULL,
  `size` float NOT NULL,
  `block_number` int(20) NOT NULL,
  `floor` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`unit_id`, `unit_number`, `size`, `block_number`, `floor`) VALUES
(1, '06', 139, 35, 15),
(5, '02', 140, 35, 14);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role_id` int(20) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `ic_number` varchar(14) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `role_id`, `fullname`, `email`, `phone_number`, `ic_number`, `emergency_contact`, `unit_id`, `profile_pic`) VALUES
(1, 'danish', '123', 1, 'Muhammad Danish', 'danish@gmail.com', '01231111', '012515-05-0083', '999', NULL, NULL),
(3, 'Iman', '123', 3, 'Yasmin Imanina', 'iman@gmail.com', '012-444-2244', '01222-01-2234', '124124124', NULL, NULL),
(4, 'ejai', '123', 2, 'Ejai Leman', 'ejai@gmail.com', '012-444-2222', '011225-05-2213', '015-555-5555', 1, NULL),
(5, 'Yasmin', '123', 2, 'Nur Yasmin Imanina', 'yasmin@gmail.com', '012482844', '151215-05-1223', '123-123-123', 5, 'ayang luvv.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `visitor_id` int(20) NOT NULL,
  `resident_id` int(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_log`
--
ALTER TABLE `access_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `ID_res` (`resident_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `id_user` (`user_id`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `facilityId` (`facility_id`),
  ADD KEY `userId` (`user_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `Id_resident` (`resident_id`);

--
-- Indexes for table `facility`
--
ALTER TABLE `facility`
  ADD PRIMARY KEY (`facility_id`);

--
-- Indexes for table `maintenance_assignment`
--
ALTER TABLE `maintenance_assignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `worker_id` (`worker_id`),
  ADD KEY `FK_requestID` (`request_id`);

--
-- Indexes for table `maintenance_request`
--
ALTER TABLE `maintenance_request`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `FK_categoryId` (`category_id`),
  ADD KEY `FK_userId` (`user_id`);

--
-- Indexes for table `maintenance_worker`
--
ALTER TABLE `maintenance_worker`
  ADD PRIMARY KEY (`worker_id`);

--
-- Indexes for table `resident`
--
ALTER TABLE `resident`
  ADD PRIMARY KEY (`resident_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `roleId` (`role_id`),
  ADD KEY `id_unit` (`unit_id`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`visitor_id`),
  ADD KEY `resId` (`resident_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_log`
--
ALTER TABLE `access_log`
  MODIFY `log_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `announcement_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `document_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_assignment`
--
ALTER TABLE `maintenance_assignment`
  MODIFY `assignment_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_request`
--
ALTER TABLE `maintenance_request`
  MODIFY `request_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `unit_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `visitor_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_log`
--
ALTER TABLE `access_log`
  ADD CONSTRAINT `ID_res` FOREIGN KEY (`resident_id`) REFERENCES `resident` (`resident_id`);

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `id_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `facilityId` FOREIGN KEY (`facility_id`) REFERENCES `facility` (`facility_id`),
  ADD CONSTRAINT `userId` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `document`
--
ALTER TABLE `document`
  ADD CONSTRAINT `Id_resident` FOREIGN KEY (`resident_id`) REFERENCES `resident` (`resident_id`);

--
-- Constraints for table `maintenance_assignment`
--
ALTER TABLE `maintenance_assignment`
  ADD CONSTRAINT `FK_requestID` FOREIGN KEY (`request_id`) REFERENCES `maintenance_request` (`request_id`),
  ADD CONSTRAINT `worker_id` FOREIGN KEY (`worker_id`) REFERENCES `maintenance_worker` (`worker_id`);

--
-- Constraints for table `maintenance_request`
--
ALTER TABLE `maintenance_request`
  ADD CONSTRAINT `FK_categoryId` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `FK_userId` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `id_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`),
  ADD CONSTRAINT `roleId` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `visitor`
--
ALTER TABLE `visitor`
  ADD CONSTRAINT `resId` FOREIGN KEY (`resident_id`) REFERENCES `resident` (`resident_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
