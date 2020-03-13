SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `cloud_disc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cloud_disc`;

DROP TABLE IF EXISTS `discs`;
CREATE TABLE IF NOT EXISTS `discs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `space` bigint(20) NOT NULL DEFAULT 10737418240,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `temporary` tinyint(1) NOT NULL DEFAULT 0,
  `visibility` enum('private','protected','public','') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private' COMMENT 'Public: accesible by everyone Protected: accesible by password Private: Accesible only by the owner',
  `permission_id` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_permid` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `discs_users`;
CREATE TABLE IF NOT EXISTS `discs_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `disc_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_id` (`user_id`),
  KEY `fk_discu_id` (`disc_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `visible` enum('listed','unlisted') NOT NULL,
  `key_name` varchar(256) NOT NULL,
  `isDir` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indicates whether this is a directory or not',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT 'The id of the parent directory',
  `size` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name_unique` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `files_discs`;
CREATE TABLE IF NOT EXISTS `files_discs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `disc_id` int(11) NOT NULL,
  `file_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_discs_id` (`disc_id`),
  KEY `fk_files_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(320) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `discs_users`
  ADD CONSTRAINT `fk_dusers_id` FOREIGN KEY (`disc_id`) REFERENCES `discs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `files_discs`
  ADD CONSTRAINT `fk_discs_id` FOREIGN KEY (`disc_id`) REFERENCES `discs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_files_id` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
