-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2017 at 01:59 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hmvc`
--

-- --------------------------------------------------------

--
-- Table structure for table `hmvcsession_management`
--

CREATE TABLE `hmvcsession_management` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `framework_token` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `operating_system` varchar(255) DEFAULT NULL,
  `expired_to` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hmvcsession_management`
--

INSERT INTO `hmvcsession_management` (`id`, `userid`, `token`, `framework_token`, `created_on`, `browser`, `operating_system`, `expired_to`) VALUES
(13, 1, '3917a995dbe8db1eecfb5ec3ebbc399d', '1513600326', '2017-12-18 01:33:20', '0', 'Unknown Platform', '2017-12-19 13:33:20'),
(15, 1, '46f15f537b3e999a3ab316d17f6fd43d', '1513600627', '2017-12-18 01:39:08', '0', 'Unknown Platform', '2017-12-19 13:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `hmvcuser`
--

CREATE TABLE `hmvcuser` (
  `userid` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hmvcuser`
--

INSERT INTO `hmvcuser` (`userid`, `email`, `password`, `created_on`, `modified_on`, `is_deleted`) VALUES
(1, 'subash.diego@gmail.com', 'c30f834bc165cd6751837b87da9e82f7f66986d1', '2017-12-18 03:07:13', '2017-12-18 09:12:36', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hmvcsession_management`
--
ALTER TABLE `hmvcsession_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hmvcuser`
--
ALTER TABLE `hmvcuser`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hmvcsession_management`
--
ALTER TABLE `hmvcsession_management`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `hmvcuser`
--
ALTER TABLE `hmvcuser`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
