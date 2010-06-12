-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 11, 2010 at 10:08 PM
-- Server version: 5.1.47
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `macfanat_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_nav`
--

CREATE TABLE IF NOT EXISTS `admin_nav` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `page` text COLLATE latin1_general_ci NOT NULL,
  `min_lvl` int(2) NOT NULL DEFAULT '0',
  `max_lvl` int(2) NOT NULL DEFAULT '0',
  `order_num` int(2) NOT NULL DEFAULT '0',
  `description` text COLLATE latin1_general_ci NOT NULL,
  `display` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=62 ;

--
-- Dumping data for table `admin_nav`
--

INSERT INTO `admin_nav` (`id`, `parent_id`, `name`, `page`, `min_lvl`, `max_lvl`, `order_num`, `description`, `display`, `date_added`, `last_updated`) VALUES
(1, 4, 'Navigation', '/navigation/manage/', 5, 5, 4, '', 'visible', '2009-12-12 11:04:32', '2009-12-12 11:04:32'),
(2, 10, 'Admin Accounts', '/users/manage/', 4, 5, 1, '', 'visible', '2009-12-12 11:04:55', '2009-12-12 11:07:28'),
(4, 0, 'Super Settings', '', 5, 5, 50, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(6, 10, 'Sign Out', '/session/logout/', 1, 5, 4, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(8, 0, 'Help', '', 4, 5, 51, '', 'hidden', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(9, 10, 'View Help', '/help/', 1, 5, 3, '', 'hidden', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(10, 0, 'Admin Settings', '', 1, 5, 99, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(11, 10, 'Edit My Account', '/users/edit/%%return $_SESSION[''uid''];/', 1, 5, 1, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(15, 0, 'Media Library', '', 1, 5, 0, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(19, 0, 'Tracking', '', 5, 5, 1, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(20, 19, 'User Tracking', '/dashboard/manage/', 5, 5, 2, '', 'visible', '2009-12-12 10:43:47', '2009-12-14 14:34:05'),
(26, 4, 'Applications', '/applications/manage/', 5, 5, 3, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(30, 15, 'Publish Application', '/applications/publish/%%return $CURRENT_APP_ID;/', 4, 5, 10, '', 'hidden', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(61, 19, 'Dashboard', '/dashboard/', 4, 5, 1, '', 'visible', '2009-12-12 10:43:47', '2009-12-14 14:20:57'),
(55, 10, 'Settings', '/settings/manage/', 4, 5, 3, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `app_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(200) NOT NULL DEFAULT '',
  `app_notes` text NOT NULL,
  `active` varchar(7) NOT NULL DEFAULT 'true',
  `xml_version` varchar(20) NOT NULL DEFAULT '1.0',
  `app_version` varchar(20) NOT NULL DEFAULT '1.0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`app_id`, `app_name`, `app_notes`, `active`, `xml_version`, `app_version`, `date_added`, `last_updated`) VALUES
(1, 'General Application', '<p>notes</p>', 'true', '1.0', '1.0', '2009-12-12 08:35:44', '2009-12-14 09:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `applications_to_content`
--

CREATE TABLE IF NOT EXISTS `applications_to_content` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `app_id` tinyint(4) NOT NULL DEFAULT '0',
  `table_index` bigint(20) NOT NULL DEFAULT '0',
  `content_type_id` bigint(20) NOT NULL DEFAULT '0',
  `content_type_name` varchar(75) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `applications_to_content`
--

INSERT INTO `applications_to_content` (`id`, `app_id`, `table_index`, `content_type_id`, `content_type_name`) VALUES
(1, 1, 1, 1, 'user'),
(19, 0, 23, 2, 'setting'),
(3, 1, 11, 0, 'setting'),
(4, 1, 12, 0, 'setting'),
(5, 1, 13, 0, 'setting'),
(6, 1, 14, 0, 'setting'),
(7, 0, 19, 0, 'setting'),
(8, 0, 20, 0, 'setting'),
(9, 0, 21, 0, 'setting'),
(10, 0, 22, 2, 'setting'),
(20, 0, 24, 2, 'setting'),
(21, 0, 25, 2, 'setting'),
(22, 0, 26, 2, 'setting'),
(25, 0, 13, 1, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `content_types`
--

CREATE TABLE IF NOT EXISTS `content_types` (
  `type_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(200) NOT NULL DEFAULT '',
  `type_table` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `content_types`
--

INSERT INTO `content_types` (`type_id`, `type_name`, `type_table`) VALUES
(1, 'user', 'users'),
(2, 'setting', 'settings'),
(3, 'application', 'applications');

-- --------------------------------------------------------

--
-- Table structure for table `cron_jobs`
--

CREATE TABLE IF NOT EXISTS `cron_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text NOT NULL,
  `type` varchar(200) NOT NULL,
  `repeats` varchar(7) NOT NULL DEFAULT 'false',
  `interval` int(11) NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `in_progress` varchar(7) NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cron_jobs`
--


-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE IF NOT EXISTS `general_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(200) NOT NULL DEFAULT '',
  `setting_value` text NOT NULL,
  `setting_level` int(11) NOT NULL,
  `setting_notes` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`setting_id`, `setting_name`, `setting_value`, `setting_level`, `setting_notes`, `date_added`, `last_updated`) VALUES
(23, 'Show Profiler Results', '<p>false</p>', 4, '', '2009-12-14 10:17:08', '2010-06-06 15:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `input1` varchar(200) NOT NULL DEFAULT '',
  `input2` varchar(200) NOT NULL DEFAULT '',
  `input3` varchar(200) NOT NULL DEFAULT '',
  `input4` varchar(200) NOT NULL DEFAULT '',
  `input5` varchar(200) NOT NULL DEFAULT '',
  `input6` varchar(200) NOT NULL DEFAULT '',
  `text1` text NOT NULL,
  `text2` text NOT NULL,
  `text3` text NOT NULL,
  `text4` text NOT NULL,
  `text5` text NOT NULL,
  `text6` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `tracking`
--

CREATE TABLE IF NOT EXISTS `tracking` (
  `track_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `app_id` int(11) NOT NULL DEFAULT '0',
  `content_type_id` bigint(20) NOT NULL DEFAULT '0',
  `table_index` bigint(20) NOT NULL DEFAULT '0',
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`track_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tracking`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_first_name` varchar(75) NOT NULL DEFAULT '',
  `user_middle_name` varchar(75) NOT NULL DEFAULT '',
  `user_last_name` varchar(75) NOT NULL DEFAULT '',
  `user_address_line_1` varchar(50) NOT NULL DEFAULT '',
  `user_address_line_2` varchar(50) NOT NULL DEFAULT '',
  `user_city` varchar(30) NOT NULL DEFAULT '',
  `user_state` char(2) NOT NULL DEFAULT '',
  `user_zip` varchar(10) NOT NULL DEFAULT '',
  `user_phone_number` varchar(15) NOT NULL DEFAULT '',
  `user_cell_number` varchar(15) NOT NULL DEFAULT '',
  `user_fax_number` varchar(15) NOT NULL DEFAULT '',
  `user_primary_email` varchar(100) NOT NULL DEFAULT '',
  `user_secondary_email` varchar(100) NOT NULL DEFAULT '',
  `user_username` varchar(50) NOT NULL DEFAULT '',
  `user_password` varchar(50) NOT NULL DEFAULT '',
  `user_position` varchar(50) NOT NULL DEFAULT '',
  `user_level` int(11) NOT NULL DEFAULT '1',
  `user_notes` text NOT NULL,
  `user_industry` int(11) NOT NULL DEFAULT '0',
  `user_company` varchar(200) NOT NULL DEFAULT '',
  `company_id` bigint(20) NOT NULL DEFAULT '0',
  `user_birthday` date NOT NULL DEFAULT '0000-00-00',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_first_name`, `user_middle_name`, `user_last_name`, `user_address_line_1`, `user_address_line_2`, `user_city`, `user_state`, `user_zip`, `user_phone_number`, `user_cell_number`, `user_fax_number`, `user_primary_email`, `user_secondary_email`, `user_username`, `user_password`, `user_position`, `user_level`, `user_notes`, `user_industry`, `user_company`, `company_id`, `user_birthday`, `date_added`, `user_last_login`, `last_updated`) VALUES
(1, 'Matt', '', 'Brewer', '', '', 'Knoxville', '0', '37923', '', '', '', 'matt@dmgx.com', '', 'super', 'dmgx', '', 5, '<p>mine ass</p>', 0, '', 0, '2009-01-01', '2009-12-12 13:12:58', '2010-06-11 21:42:48', '2010-06-11 21:42:48'),
(13, 'Basic', '', 'User', '', '', '', '0', '', '', '', '', 'matt@dmgx.com', '', 'matt', 'dmgx', '', 1, '', 0, '', 0, '2009-12-14', '2009-12-14 14:36:41', '0000-00-00 00:00:00', '2009-12-14 14:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE IF NOT EXISTS `user_levels` (
  `user_level_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `user_level_name` varchar(75) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_level_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user_levels`
--

INSERT INTO `user_levels` (`user_level_id`, `user_level_name`) VALUES
(1, 'Basic User'),
(4, 'Administrator'),
(5, 'Super Admin');
