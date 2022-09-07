-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 04, 2022 at 06:33 PM
-- Server version: 5.7.36
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tv`
--
CREATE DATABASE IF NOT EXISTS `tv` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `tv`;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `login` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `roles` varchar(64) NOT NULL DEFAULT 'user',
  `firstname` varchar(32) NOT NULL,
  `secondname` varchar(32) NOT NULL,
  `postcode` varchar(6) NOT NULL,
  `signup_date` int(11) NOT NULL,
  `state` enum('not_active','active','block') NOT NULL DEFAULT 'not_active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `login`, `password`, `roles`, `firstname`, `secondname`, `postcode`, `signup_date`, `state`) VALUES
(1, '', 'user_1', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'user', 'UserName', 'UserSecondName', '00-000', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `video_data`
--

CREATE TABLE `video_data` (
  `video_id` int(11) NOT NULL,
  `part` int(11) NOT NULL,
  `data` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `video_metadata`
--

CREATE TABLE `video_metadata` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(512) NOT NULL,
  `image` varchar(64) NOT NULL,
  `access` enum('public','private') NOT NULL DEFAULT 'private'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `video_mpd`
--

CREATE TABLE `video_mpd` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `codecs` varchar(128) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `duration` varchar(12) NOT NULL,
  `mime_type` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `video_upload`
--

CREATE TABLE `video_upload` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(128) NOT NULL,
  `file_type` varchar(32) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `part_size` int(11) NOT NULL,
  `expect_data_from` int(11) DEFAULT '0',
  `expect_data_to` int(11) NOT NULL,
  `parts_count` int(11) NOT NULL,
  `last_updated_part` int(11) NOT NULL,
  `date_update` int(11) NOT NULL,
  `percent` tinyint(4) NOT NULL DEFAULT '0',
  `process_state` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UqUserEmailIndex` (`email`);

--
-- Indexes for table `video_data`
--
ALTER TABLE `video_data`
  ADD UNIQUE KEY `unique_index_videodata` (`video_id`,`part`),
  ADD KEY `video_id` (`video_id`);

--
-- Indexes for table `video_metadata`
--
ALTER TABLE `video_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `video_mpd`
--
ALTER TABLE `video_mpd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`);

--
-- Indexes for table `video_upload`
--
ALTER TABLE `video_upload`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `video_metadata`
--
ALTER TABLE `video_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_mpd`
--
ALTER TABLE `video_mpd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_upload`
--
ALTER TABLE `video_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `video_data`
--
ALTER TABLE `video_data`
  ADD CONSTRAINT `video_data_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `video_metadata` (`id`);

--
-- Constraints for table `video_metadata`
--
ALTER TABLE `video_metadata`
  ADD CONSTRAINT `video_metadata_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `video_mpd`
--
ALTER TABLE `video_mpd`
  ADD CONSTRAINT `video_mpd_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `video_metadata` (`id`);

--
-- Constraints for table `video_upload`
--
ALTER TABLE `video_upload`
  ADD CONSTRAINT `video_upload_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
