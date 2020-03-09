-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2020 at 02:22 PM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloud_disc`
--
CREATE DATABASE IF NOT EXISTS `cloud_disc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cloud_disc`;

-- --------------------------------------------------------

--
-- Table structure for table `discs`
--

DROP TABLE IF EXISTS `discs`;
CREATE TABLE `discs` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `space` bigint(20) NOT NULL DEFAULT 10737418240,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `temporary` tinyint(1) NOT NULL DEFAULT 0,
  `visibility` enum('private','protected','public','') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private' COMMENT 'Public: accesible by everyone Protected: accesible by password Private: Accesible only by the owner'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discs_users`
--

DROP TABLE IF EXISTS `discs_users`;
CREATE TABLE `discs_users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `disc_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visible` enum('listed','unlisted') NOT NULL,
  `key_name` varchar(256) NOT NULL,
  `isDir` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indicates whether this is a directory or not',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT 'The id of the parent directory',
  `size` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `files_discs`
--

DROP TABLE IF EXISTS `files_discs`;
CREATE TABLE `files_discs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `disc_id` int(11) NOT NULL,
  `file_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(320) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `discs`
--
ALTER TABLE `discs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discs_users`
--
ALTER TABLE `discs_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_id` (`user_id`),
  ADD KEY `fk_discu_id` (`disc_id`) USING BTREE;

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name_unique` (`key_name`);

--
-- Indexes for table `files_discs`
--
ALTER TABLE `files_discs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_discs_id` (`disc_id`),
  ADD KEY `fk_files_id` (`file_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD UNIQUE KEY `perm_id_unique` (`permission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `discs`
--
ALTER TABLE `discs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discs_users`
--
ALTER TABLE `discs_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files_discs`
--
ALTER TABLE `files_discs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `discs_users`
--
ALTER TABLE `discs_users`
  ADD CONSTRAINT `fk_dusers_id` FOREIGN KEY (`disc_id`) REFERENCES `discs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `files_discs`
--
ALTER TABLE `files_discs`
  ADD CONSTRAINT `fk_discs_id` FOREIGN KEY (`disc_id`) REFERENCES `discs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_files_id` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
DROP EVENT `clean_temp_discs`$$
CREATE DEFINER=`root`@`localhost` EVENT `clean_temp_discs` ON SCHEDULE EVERY 6 HOUR STARTS '2020-02-27 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Deletes all disc rows older than 30 minutes from all tables.' DO DELETE fd, d, f FROM files_discs fd LEFT JOIN discs d ON fd.disc_id=d.id LEFT JOIN files f ON fd.file_id=f.id WHERE d.temporary=TRUE AND d.date_created < CURRENT_TIMESTAMP - INTERVAL 30 MINUTE$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
