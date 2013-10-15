-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 15, 2013 at 10:31 PM
-- Server version: 5.5.20
-- PHP Version: 5.4.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `aes`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE IF NOT EXISTS `conversation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `initiator_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_initiator_id` (`initiator_id`),
  KEY `ix_created_ts` (`created_ts`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `conversation`
--

INSERT INTO `conversation` (`id`, `title`, `created_ts`, `initiator_id`) VALUES
(1, '', '2013-10-09 04:46:21', 1),
(2, '', '2013-10-09 04:47:37', 1),
(3, '', '2013-10-09 20:23:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `conversation_participant`
--

CREATE TABLE IF NOT EXISTS `conversation_participant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_view_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_conversation_id_user_id` (`conversation_id`,`user_id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `conversation_participant`
--

INSERT INTO `conversation_participant` (`id`, `conversation_id`, `user_id`, `last_view_ts`) VALUES
(1, 1, 1, '0000-00-00 00:00:00'),
(3, 2, 1, '0000-00-00 00:00:00'),
(4, 2, 3, '0000-00-00 00:00:00'),
(5, 3, 1, '0000-00-00 00:00:00'),
(6, 3, 4, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `election`
--

CREATE TABLE IF NOT EXISTS `election` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `mandate` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `quote` int(11) NOT NULL,
  `validity` int(11) NOT NULL,
  `cand_reg_type` tinyint(4) NOT NULL DEFAULT '0',
  `cand_reg_confirm` tinyint(4) NOT NULL DEFAULT '0',
  `voter_reg_type` tinyint(4) NOT NULL DEFAULT '0',
  `voter_reg_confirm` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `text` text COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  KEY `fk_message_conversation_id` (`conversation_id`),
  KEY `fk_message_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `conversation_id`, `user_id`, `created_ts`, `text`) VALUES
(1, 2, 1, '2013-10-09 04:48:04', 'Привет! Это тест.'),
(2, 3, 1, '2013-10-09 20:23:48', 'Первое тестовое сообщение.');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `created_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_update_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `fk_post_user_id` (`user_id`),
  KEY `fk_post_reply_to` (`reply_to`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `reply_to`, `content`, `created_ts`, `last_update_ts`) VALUES
(2, 1, NULL, 'Hello world! Again', '2013-09-07 03:45:51', '0000-00-00 00:00:00'),
(4, 1, 2, 'ываываываываыва', '2013-09-07 03:52:51', '0000-00-00 00:00:00'),
(5, 1, NULL, 'Трутутататтыва ', '2013-09-07 03:52:58', '0000-00-00 00:00:00'),
(6, 3, NULL, 'тест\n', '2013-09-11 19:57:53', '2013-09-11 19:58:12'),
(7, 3, NULL, 'ывап', '2013-09-11 19:59:16', '0000-00-00 00:00:00'),
(8, 3, 6, 'тест\nфыв', '2013-10-01 21:33:19', '2013-10-01 21:33:33'),
(9, 4, 6, 'why?\n', '2013-10-02 00:34:25', '0000-00-00 00:00:00'),
(10, 4, 6, 'ага вижу', '2013-10-02 01:13:36', '0000-00-00 00:00:00'),
(12, 5, NULL, 'test message 3', '2013-10-13 18:13:25', '2013-10-13 18:51:26'),
(13, 5, 12, '3', '2013-10-13 21:25:35', '0000-00-00 00:00:00'),
(14, 5, NULL, '1', '2013-10-13 21:27:11', '0000-00-00 00:00:00'),
(15, 5, 12, '2', '2013-10-13 21:27:15', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `post_placement`
--

CREATE TABLE IF NOT EXISTS `post_placement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `target_id` int(11) unsigned NOT NULL,
  `target_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `placed_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `placer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_post_placement_post_id` (`post_id`),
  KEY `fk_post_placement_placer_id` (`placer_id`),
  KEY `ix_post_placement_target_id_target_type` (`target_id`,`target_type`),
  KEY `ix_post_placement_placed_ts` (`placed_ts`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- Dumping data for table `post_placement`
--

INSERT INTO `post_placement` (`id`, `post_id`, `target_id`, `target_type`, `placed_ts`, `placer_id`) VALUES
(2, 2, 1, 0, '2013-09-07 03:45:51', 1),
(3, 5, 1, 0, '2013-09-07 03:52:58', 1),
(4, 6, 3, 0, '2013-09-11 19:57:53', 3),
(5, 7, 1, 0, '2013-09-11 19:59:16', 3),
(7, 12, 5, 0, '2013-10-13 18:13:25', 5),
(8, 14, 5, 0, '2013-10-13 21:27:11', 5);

-- --------------------------------------------------------

--
-- Table structure for table `post_rate`
--

CREATE TABLE IF NOT EXISTS `post_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `score` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_post_rate_user_id_post_id` (`user_id`,`post_id`),
  KEY `fk_post_post_id` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=25 ;

--
-- Dumping data for table `post_rate`
--

INSERT INTO `post_rate` (`id`, `user_id`, `post_id`, `created_ts`, `score`) VALUES
(1, 1, 7, '2013-09-11 20:48:55', 1),
(3, 1, 4, '2013-09-11 20:49:49', 1),
(8, 3, 6, '2013-10-01 21:33:04', 1),
(9, 3, 8, '2013-10-01 21:33:40', -1),
(10, 4, 10, '2013-10-02 01:13:40', 1),
(11, 3, 10, '2013-10-09 01:48:32', 1),
(23, 5, 12, '2013-10-13 21:27:29', 1),
(24, 5, 15, '2013-10-13 21:27:33', -1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) COLLATE utf8_bin NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  `module` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `tbl_migration`
--

INSERT INTO `tbl_migration` (`version`, `apply_time`, `module`) VALUES
('m000000_000000_base_core', 1376078833, 'core'),
('m000000_000000_base_userAccount', 1376078833, 'userAccount'),
('m130701_160911_userAccount', 1376078833, 'userAccount'),
('m130808_144831_add_users_photo', 1376078833, 'core'),
('m130819_093549_add_users_photo_thumbnail', 1376908903, 'core'),
('m130904_094231_add_post', 1378495953, 'core'),
('m130906_083257_add_post_placement', 1378495953, 'core'),
('m130910_121812_add_indexes', 1378881690, 'core'),
('m131004_143954_add_messaging', 1381265001, 'core');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_visit_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `i_user_login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `login`, `password`, `created_ts`, `last_visit_ts`, `superuser`, `status`) VALUES
(1, NULL, 'd8578edf8458ce06fbc5bb76a58c5ca4', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 2),
(3, NULL, '137795f7f754b7455687e9e9bd2f6372', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 2),
(4, NULL, '52ae34d92900492beaac5dee0b5d76ab', '2013-10-02 00:29:48', '0000-00-00 00:00:00', 0, 2),
(5, NULL, 'a3b4249d28808859e24922d62acd632d', '2013-10-09 02:00:26', '0000-00-00 00:00:00', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_identity`
--

CREATE TABLE IF NOT EXISTS `user_identity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `identity` varchar(128) COLLATE utf8_bin NOT NULL,
  `type` varchar(128) COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_user_identity_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user_identity`
--

INSERT INTO `user_identity` (`id`, `user_id`, `identity`, `type`, `status`) VALUES
(1, 1, 'truvazia@gmail.com', 'email', 2),
(3, 3, 'rufige@yandex.ru', 'email', 2),
(4, 4, 'dmitry@zudov.com', 'email', 2),
(5, 5, 'mobirp@yandex.ru', 'email', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_identity_confirmation`
--

CREATE TABLE IF NOT EXISTS `user_identity_confirmation` (
  `user_identity_id` int(11) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `key` varchar(128) COLLATE utf8_bin NOT NULL,
  `sent_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_identity_id`),
  UNIQUE KEY `i_user_identity_confirmation_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE IF NOT EXISTS `user_profile` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `last_name` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `birth_place` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `birth_day` date DEFAULT NULL,
  `gender` int(1) DEFAULT NULL,
  `mobile_phone` varchar(18) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `photo` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `photo_thmbnl_64` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `i_first_name` (`first_name`),
  KEY `i_last_name` (`last_name`),
  KEY `i_mobile_phone` (`mobile_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `first_name`, `last_name`, `birth_place`, `birth_day`, `gender`, `mobile_phone`, `email`, `photo`, `photo_thmbnl_64`) VALUES
(1, 'Vasiliy', 'Pedak', 'Ust-Katav, Cheliabinskaia oblast, Russia', '1989-10-23', 1, '+3(066)066-46-57', 'truvazia@gmail.com', '1_0_97349300_1377012019.jpg', '1_0_97349300_1377012019_64x64.jpg'),
(3, 'Alexeydsfg', 'Makurin', 'Samara', '1979-01-02', 1, '+7(111)111-11-22', 'rufige@yandex.ru', '3_0_23311200_1378216039.png', '3_0_23311200_1378216039_64x64.png'),
(4, 'Dmitry', 'Viktorovich', 'Chilyabinsk', '1978-08-24', 1, '', 'dmitry@zudov.com', NULL, NULL),
(5, 'Ivan', 'Moskvin', 'Moscow', '1983-10-05', 1, '+7(585)685-68-56', 'mobirp@yandex.ru', '5_0_74449300_1381853495.png', '5_0_74449300_1381853495_64x64.png');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `fk_initiator_id` FOREIGN KEY (`initiator_id`) REFERENCES `user_profile` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `conversation_participant`
--
ALTER TABLE `conversation_participant`
  ADD CONSTRAINT `fk_conversation_id` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `election`
--
ALTER TABLE `election`
  ADD CONSTRAINT `election_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message_conversation_id` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_message_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_post_reply_to` FOREIGN KEY (`reply_to`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_post_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `post_placement`
--
ALTER TABLE `post_placement`
  ADD CONSTRAINT `fk_post_placement_placer_id` FOREIGN KEY (`placer_id`) REFERENCES `user_profile` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_post_placement_post_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `post_rate`
--
ALTER TABLE `post_rate`
  ADD CONSTRAINT `fk_post_post_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_post_rate_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_identity`
--
ALTER TABLE `user_identity`
  ADD CONSTRAINT `fk_user_identity_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_identity_confirmation`
--
ALTER TABLE `user_identity_confirmation`
  ADD CONSTRAINT `fk_uic_u_identity_id` FOREIGN KEY (`user_identity_id`) REFERENCES `user_identity` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `fk_user_profile_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
