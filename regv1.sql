-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2019 at 09:39 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `regv1`
--

-- --------------------------------------------------------

--
-- Table structure for table `debtor`
--

CREATE TABLE `debtor` (
  `studentid` varchar(20) NOT NULL,
  `typeId` int(10) NOT NULL,
  `amount` double DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `debttype`
--

CREATE TABLE `debttype` (
  `id` int(10) NOT NULL,
  `type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `feepaid`
--

CREATE TABLE `feepaid` (
  `session` int(10) NOT NULL,
  `studentid` varchar(20) NOT NULL,
  `paymentType` int(10) NOT NULL,
  `amount` double DEFAULT NULL,
  `refNumber` varchar(20) DEFAULT NULL,
  `datePaid` date DEFAULT NULL,
  `filename` varchar(20) DEFAULT NULL,
  `Updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paymenttype`
--

CREATE TABLE `paymenttype` (
  `id` int(10) NOT NULL,
  `type` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permadd`
--

CREATE TABLE `permadd` (
  `id` varchar(20) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `postcode` varchar(6) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `regstat`
--

CREATE TABLE `regstat` (
  `session` int(10) NOT NULL,
  `studentid` varchar(20) NOT NULL,
  `currentsemester` int(10) DEFAULT NULL,
  `bkr` tinyint(3) DEFAULT NULL,
  `fileReg` varchar(255) DEFAULT NULL,
  `hep` int(10) DEFAULT NULL,
  `finance` int(10) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rentadd`
--

CREATE TABLE `rentadd` (
  `id` varchar(20) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `postcode` varchar(6) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `restype`
--

CREATE TABLE `restype` (
  `id` int(10) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `id` int(10) DEFAULT NULL,
  `session` varchar(9) DEFAULT NULL,
  `default` tinyint(3) DEFAULT NULL,
  `startMonth` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `id` varchar(20) NOT NULL,
  `department` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `session` varchar(20) NOT NULL,
  `studentid` varchar(20) NOT NULL,
  `coursestat` varchar(10) DEFAULT NULL,
  `resultstat` varchar(10) DEFAULT NULL,
  `prevsem` int(10) DEFAULT NULL,
  `modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `listat` tinyint(3) DEFAULT NULL,
  `setA` tinyint(3) DEFAULT NULL,
  `residence` int(10) NOT NULL,
  `preregstatus` tinyint(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` varchar(20) NOT NULL,
  `ic` varchar(20) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `prgcode` varchar(8) NOT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `roleid` int(10) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `active` tinyint(3) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `debtor`
--
ALTER TABLE `debtor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `debttype`
--
ALTER TABLE `debttype`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `feepaid`
--
ALTER TABLE `feepaid`
  ADD PRIMARY KEY (`session`,`studentid`);

--
-- Indexes for table `paymenttype`
--
ALTER TABLE `paymenttype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permadd`
--
ALTER TABLE `permadd`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `regstat`
--
ALTER TABLE `regstat`
  ADD PRIMARY KEY (`session`,`studentid`);

--
-- Indexes for table `rentadd`
--
ALTER TABLE `rentadd`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `restype`
--
ALTER TABLE `restype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`session`,`studentid`),
  ADD KEY `session` (`session`),
  ADD KEY `studentid` (`studentid`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ic` (`ic`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `debtor`
--
ALTER TABLE `debtor`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debttype`
--
ALTER TABLE `debttype`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paymenttype`
--
ALTER TABLE `paymenttype`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restype`
--
ALTER TABLE `restype`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
