-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: 192.168.186.128
-- Generation Time: Jul 11, 2015 at 04:28 PM
-- Server version: 5.5.43-0ubuntu0.14.04.1
-- PHP Version: 5.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_helpdesk`
--
CREATE DATABASE IF NOT EXISTS `my_helpdesk` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `my_helpdesk`;

-- --------------------------------------------------------

--
-- Table structure for table `application_user`
--

CREATE TABLE IF NOT EXISTS `application_user` (
  `id` bigint(20) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Holds the application users';

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Holds a category e.g. Software, hardware, enterprise etc';

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Software'),
(2, 'Hardware');

-- --------------------------------------------------------

--
-- Table structure for table `comment_type`
--

CREATE TABLE IF NOT EXISTS `comment_type` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Holds comment types, e.g. work note, simple comment etc';

--
-- Dumping data for table `comment_type`
--

INSERT INTO `comment_type` (`id`, `name`) VALUES
(1, 'Comment'),
(2, 'Work Note');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `project_lead` bigint(20) unsigned NOT NULL,
  `description` text,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds the projects';

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE IF NOT EXISTS `ticket` (
  `id` bigint(20) unsigned NOT NULL,
  `ticket_key` varchar(10) NOT NULL,
  `ticket_category` bigint(20) unsigned NOT NULL,
  `workflow` bigint(20) unsigned NOT NULL,
  `type` bigint(20) unsigned NOT NULL,
  `category` bigint(20) unsigned NOT NULL,
  `assignee` bigint(20) unsigned NOT NULL,
  `reporter` bigint(20) unsigned NOT NULL,
  `project` bigint(20) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `description` text,
  `update_on` timestamp NULL DEFAULT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ticket table';

-- --------------------------------------------------------

--
-- Table structure for table `ticket_category`
--

CREATE TABLE IF NOT EXISTS `ticket_category` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Holds the category of ticket eg work rwquest';

--
-- Dumping data for table `ticket_category`
--

INSERT INTO `ticket_category` (`id`, `name`) VALUES
(1, 'Work Request'),
(2, 'Incident');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comment`
--

CREATE TABLE IF NOT EXISTS `ticket_comment` (
  `id` bigint(20) unsigned NOT NULL,
  `ticket` bigint(20) unsigned NOT NULL,
  `type` bigint(20) unsigned NOT NULL,
  `comment` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Holds comment records for the tickets';

-- --------------------------------------------------------

--
-- Table structure for table `ticket_type`
--

CREATE TABLE IF NOT EXISTS `ticket_type` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Table holds ticket types e.g. task, defect etc';

--
-- Dumping data for table `ticket_type`
--

INSERT INTO `ticket_type` (`id`, `name`) VALUES
(1, 'Task'),
(2, 'Defect');

-- --------------------------------------------------------

--
-- Table structure for table `user_group_mapping`
--

CREATE TABLE IF NOT EXISTS `user_group_mapping` (
  `id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='MApping table for users and groups.';

-- --------------------------------------------------------

--
-- Table structure for table `work_group`
--

CREATE TABLE IF NOT EXISTS `work_group` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Application user groups';

-- --------------------------------------------------------

--
-- Table structure for table `workflow`
--

CREATE TABLE IF NOT EXISTS `workflow` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Workflows';

--
-- Dumping data for table `workflow`
--

INSERT INTO `workflow` (`id`, `name`) VALUES
(1, 'New'),
(2, 'Open');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application_user`
--
ALTER TABLE `application_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_type`
--
ALTER TABLE `comment_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_lead` (`project_lead`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`ticket_key`),
  ADD KEY `type` (`type`),
  ADD KEY `category` (`ticket_category`),
  ADD KEY `workflow` (`workflow`),
  ADD KEY `category_2` (`category`),
  ADD KEY `assignee` (`assignee`),
  ADD KEY `reporter` (`reporter`),
  ADD KEY `project` (`project`);

--
-- Indexes for table `ticket_category`
--
ALTER TABLE `ticket_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_comment`
--
ALTER TABLE `ticket_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket` (`ticket`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `ticket_type`
--
ALTER TABLE `ticket_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_group_mapping`
--
ALTER TABLE `user_group_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `work_group`
--
ALTER TABLE `work_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workflow`
--
ALTER TABLE `workflow`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application_user`
--
ALTER TABLE `application_user`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `comment_type`
--
ALTER TABLE `comment_type`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ticket_category`
--
ALTER TABLE `ticket_category`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ticket_comment`
--
ALTER TABLE `ticket_comment`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ticket_type`
--
ALTER TABLE `ticket_type`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user_group_mapping`
--
ALTER TABLE `user_group_mapping`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `work_group`
--
ALTER TABLE `work_group`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `workflow`
--
ALTER TABLE `workflow`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`project_lead`) REFERENCES `application_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_13` FOREIGN KEY (`ticket_category`) REFERENCES `ticket_category` (`id`),
  ADD CONSTRAINT `ticket_ibfk_14` FOREIGN KEY (`workflow`) REFERENCES `workflow` (`id`),
  ADD CONSTRAINT `ticket_ibfk_15` FOREIGN KEY (`type`) REFERENCES `ticket_type` (`id`),
  ADD CONSTRAINT `ticket_ibfk_16` FOREIGN KEY (`category`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `ticket_ibfk_17` FOREIGN KEY (`assignee`) REFERENCES `application_user` (`id`),
  ADD CONSTRAINT `ticket_ibfk_18` FOREIGN KEY (`reporter`) REFERENCES `application_user` (`id`),
  ADD CONSTRAINT `ticket_ibfk_19` FOREIGN KEY (`project`) REFERENCES `project` (`id`);

--
-- Constraints for table `ticket_comment`
--
ALTER TABLE `ticket_comment`
  ADD CONSTRAINT `ticket_comment_ibfk_1` FOREIGN KEY (`ticket`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_comment_ibfk_2` FOREIGN KEY (`type`) REFERENCES `comment_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_group_mapping`
--
ALTER TABLE `user_group_mapping`
  ADD CONSTRAINT `user_group_mapping_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `work_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_group_mapping_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `application_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
