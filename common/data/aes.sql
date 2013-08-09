-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 09 2013 г., 18:11
-- Версия сервера: 5.5.31
-- Версия PHP: 5.3.10-1ubuntu3.6

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `aes`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_migration`
--

DROP TABLE IF EXISTS `tbl_migration`;
CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tbl_migration`
--

INSERT INTO `tbl_migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1372705839),
('m130701_160911_userAccount', 1372938231),
('m130808_144831_add_users_photo', 1375973606);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) DEFAULT NULL,
  `password` varchar(128) NOT NULL DEFAULT '',
  `created_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_visit_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `i_user_login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `login`, `password`, `created_ts`, `last_visit_ts`, `superuser`, `status`) VALUES
(6, NULL, 'd8578edf8458ce06fbc5bb76a58c5ca4', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 2),
(7, NULL, 'd8578edf8458ce06fbc5bb76a58c5ca4', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 2),
(9, NULL, 'd8578edf8458ce06fbc5bb76a58c5ca4', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `user_identity`
--

DROP TABLE IF EXISTS `user_identity`;
CREATE TABLE IF NOT EXISTS `user_identity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `identity` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_user_identity_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Дамп данных таблицы `user_identity`
--

INSERT INTO `user_identity` (`id`, `user_id`, `identity`, `type`, `status`) VALUES
(21, 7, 'vptester@mail.ru', 'email', 2),
(23, 6, 'truvazia@gmail.com', 'email', 2),
(25, 9, 'test1@mail.com', 'email', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `user_identity_confirmation`
--

DROP TABLE IF EXISTS `user_identity_confirmation`;
CREATE TABLE IF NOT EXISTS `user_identity_confirmation` (
  `user_identity_id` int(11) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `key` varchar(128) NOT NULL,
  `sent_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_identity_id`),
  UNIQUE KEY `i_user_identity_confirmation_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE IF NOT EXISTS `user_profile` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(128) NOT NULL DEFAULT '',
  `last_name` varchar(128) NOT NULL DEFAULT '',
  `birth_place` varchar(128) NOT NULL DEFAULT '',
  `birth_day` date DEFAULT NULL,
  `gender` int(1) DEFAULT NULL,
  `mobile_phone` varchar(18) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `photo` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `i_first_name` (`first_name`),
  KEY `i_last_name` (`last_name`),
  KEY `i_mobile_phone` (`mobile_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `first_name`, `last_name`, `birth_place`, `birth_day`, `gender`, `mobile_phone`, `email`, `photo`) VALUES
(6, 'Василий', 'Педак', 'Усть-Катав, Челябенская область, Россия', '1989-10-23', 1, '+3(066)066-46-57', 'truvazia@gmail.com', '6_0_65473800_1375978477.jpg'),
(7, 'Tester', 'Testest', 'asdasd', '2013-07-15', 2, '+1(212)121-21-21', 'vptester@mail.ru', NULL),
(9, 'Timoty', 'Trunker', 'NYC, USA', '1990-03-14', 1, '+1(111)111-11-12', 'test1@mail.com', NULL);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `user_identity`
--
ALTER TABLE `user_identity`
  ADD CONSTRAINT `fk_user_identity_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_identity_confirmation`
--
ALTER TABLE `user_identity_confirmation`
  ADD CONSTRAINT `fk_uic_u_identity_id` FOREIGN KEY (`user_identity_id`) REFERENCES `user_identity` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `fk_user_profile_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
