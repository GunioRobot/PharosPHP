-- phpMyAdmin SQL Dump
-- version 2.11.9.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 14, 2009 at 10:52 AM
-- Server version: 5.0.85
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `claytona_clayton`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_nav`
--

CREATE TABLE IF NOT EXISTS `admin_nav` (
  `id` int(10) NOT NULL auto_increment,
  `parent_id` int(10) NOT NULL default '0',
  `name` varchar(200) collate latin1_general_ci NOT NULL default '',
  `page` text collate latin1_general_ci NOT NULL,
  `min_lvl` int(2) NOT NULL default '0',
  `max_lvl` int(2) NOT NULL default '0',
  `order_num` int(2) NOT NULL default '0',
  `description` text collate latin1_general_ci NOT NULL,
  `display` varchar(20) collate latin1_general_ci NOT NULL default '',
  `date_added` datetime NOT NULL,
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
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
(20, 19, 'User Tracking', '/tacking/users/', 5, 5, 2, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(26, 4, 'Applications', '/applications/manage/', 5, 5, 3, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(30, 15, 'Publish Application', '/applications/publish/%%return $CURRENT_APP_ID;/', 4, 5, 10, '', 'hidden', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(61, 19, 'Dashboard', '/tracking/dashboard/', 4, 5, 1, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),
(55, 10, 'Settings', '/settings/manage/', 4, 5, 3, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `app_id` tinyint(4) NOT NULL auto_increment,
  `app_name` varchar(200) NOT NULL default '',
  `app_notes` text NOT NULL,
  `active` varchar(7) NOT NULL default 'true',
  `xml_version` varchar(20) NOT NULL default '1.0',
  `app_version` varchar(20) NOT NULL default '1.0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`app_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`app_id`, `app_name`, `app_notes`, `active`, `xml_version`, `app_version`, `date_added`, `last_updated`) VALUES
(1, 'Default Application', '', 'true', '1.0', '1.0', '2009-12-12 08:35:44', '2009-12-12 15:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `applications_to_content`
--

CREATE TABLE IF NOT EXISTS `applications_to_content` (
  `id` bigint(20) NOT NULL auto_increment,
  `app_id` tinyint(4) NOT NULL default '0',
  `table_index` bigint(20) NOT NULL default '0',
  `content_type_id` bigint(20) NOT NULL default '0',
  `content_type_name` varchar(75) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `applications_to_content`
--

INSERT INTO `applications_to_content` (`id`, `app_id`, `table_index`, `content_type_id`, `content_type_name`) VALUES
(1, 1, 1, 1, 'user'),
(2, 1, 2, 1, 'user'),
(10, 0, 22, 2, 'setting');

-- --------------------------------------------------------

--
-- Table structure for table `content_types`
--

CREATE TABLE IF NOT EXISTS `content_types` (
  `type_id` tinyint(4) NOT NULL auto_increment,
  `type_name` varchar(200) NOT NULL default '',
  `type_table` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `content_types`
--

INSERT INTO `content_types` (`type_id`, `type_name`, `type_table`) VALUES
(1, 'user', 'users'),
(2, 'setting', 'settings'),
(3, 'application', 'applications'),
(4, 'navigation', 'admin_nav'),
(5, 'page', 'pages');

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE IF NOT EXISTS `general_settings` (
  `setting_id` int(11) NOT NULL auto_increment,
  `setting_name` varchar(200) NOT NULL default '',
  `setting_value` text NOT NULL,
  `setting_notes` text NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`setting_id`, `setting_name`, `setting_value`, `setting_notes`, `date_added`, `last_updated`) VALUES
(22, 'Site Name', '<p>CMS Testing Center</p>', '', '2009-12-12 08:54:05', '2009-12-12 08:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `input1` varchar(200) NOT NULL default '',
  `input2` varchar(200) NOT NULL default '',
  `input3` varchar(200) NOT NULL default '',
  `input4` varchar(200) NOT NULL default '',
  `input5` varchar(200) NOT NULL default '',
  `input6` varchar(200) NOT NULL default '',
  `text1` text NOT NULL,
  `text2` text NOT NULL,
  `text3` text NOT NULL,
  `text4` text NOT NULL,
  `text5` text NOT NULL,
  `text6` text NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `input1`, `input2`, `input3`, `input4`, `input5`, `input6`, `text1`, `text2`, `text3`, `text4`, `text5`, `text6`, `date_added`, `last_updated`) VALUES
(1, 'Social Networking', 'http://www.twitter.com/eric_barton', 'http://www.linkedin.com/in/ericbbarton', 'http://www.flickr.com/photos/ericbarton/', '', '', '', '<p>Follow Eric on Twitter</p>', '<p>Network with Eric on LinkedIn</p>', '<p>See Eric&rsquo;s photos on Flickr</p>', '', '', '', '2009-11-18 16:40:10', '2009-11-25 12:11:30'),
(2, 'Biography Page', '', '', '', '', '', '', '<p>Since my first days in the U.S. Marine Corps at the age of 17, I&rsquo;ve been on the go. Initially, my pursuits were simple enough&mdash;join the military and get a good education. Maybe I&rsquo;d even become a success one day.<br /><br />Over the years, I have happily learned there is a greater mission to life than the pursuits that generally dominate our time. I have also learned that success cannot be calculated by the sums in your bank accounts.<br /><br />Success, as I see it, is something you have to build with your own two hands. That doesn&rsquo;t mean you necessarily build it alone. There are other hands reaching out to help you, if you are willing to also help yourself, and I have been blessed to have many hands reach out to help me on my missions. Each hand that has reached out to guide me and steady me&mdash; whether from my mother, my brothers and sisters in the Corps, my wife and children, and countless others in the communities where I have lived and worked&mdash;has provided me with an opportunity to also reach out to others.<br /><br />I am fortunate that my work has allowed me the opportunity to see the need people all over the world have for the most basic elements&mdash;food, shelter, security, and compassion&mdash;and to provide me with the opportunity to address those needs.<br /><br />I am still on the go. On a mission, you might say. I want to make the world safer for my children, for your children, and for the children who have no one to speak for them yet. It is why I proudly work with non-governmental organizations to create new international adoption programs for children. And, here, as well as any other aspect of my life&mdash;I try to lead by example. My family is fortunate to include the loving addition of two children Mechelle and I adopted from Ethiopia. I have had many job titles &ndash; Marine, chaplain, senior operations manager, and President&mdash; but the title of father is the one I cherish and protect more than any other. <br /><br />Through work in companies I own, my teams have had the honor of taking on goodwill opportunities around the globe. Some recent work includes establishing school complexes in Palestine and Pakistan, the creation of emergency villages in Greece and Saudi Arabia, and setting up medical centers in Burma.<br /><br />When I need to re-energize, I turn to the love and comfort of my family in East Tennessee, where my wife, five kids and I spend time working on our horse ranch, and breeding and raising show Dobermans.<br /><br />Thank you for visiting my blog. I hope you&rsquo;ll join and enjoy the conversations as I share news of my life at home and in the world. It seems I&rsquo;m always on the go to a new mission, but I&rsquo;m happy to have you along.</p>', 'bioHead.jpg', '<p>Since my first days in the U.S. Marine Corps at the age of 17, I&rsquo;ve been on the go. Initially, my pursuits were simple enough&mdash;join the military and get a good education. Maybe I&rsquo;d even become a success one day.</p>\r\n<p>Over the years, I have happily learned there is a greater mission to life than the pursuits that generally dominate our time. I have also learned that success cannot be calculated by the sums in your bank accounts.</p>', '', '', '', '2009-11-18 16:52:00', '2009-11-19 16:00:28');

-- --------------------------------------------------------

--
-- Table structure for table `tracking`
--

CREATE TABLE IF NOT EXISTS `tracking` (
  `track_id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL default '0',
  `app_id` int(11) NOT NULL default '0',
  `content_type_id` bigint(20) NOT NULL default '0',
  `table_index` bigint(20) NOT NULL default '0',
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`track_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tracking`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) NOT NULL auto_increment,
  `user_first_name` varchar(75) NOT NULL default '',
  `user_middle_name` varchar(75) NOT NULL default '',
  `user_last_name` varchar(75) NOT NULL default '',
  `user_address_line_1` varchar(50) NOT NULL default '',
  `user_address_line_2` varchar(50) NOT NULL default '',
  `user_city` varchar(30) NOT NULL default '',
  `user_state` char(2) NOT NULL default '',
  `user_zip` varchar(10) NOT NULL default '',
  `user_phone_number` varchar(15) NOT NULL default '',
  `user_cell_number` varchar(15) NOT NULL default '',
  `user_fax_number` varchar(15) NOT NULL default '',
  `user_primary_email` varchar(100) NOT NULL default '',
  `user_secondary_email` varchar(100) NOT NULL default '',
  `user_username` varchar(50) NOT NULL default '',
  `user_password` varchar(50) NOT NULL default '',
  `user_position` varchar(50) NOT NULL default '',
  `user_level` int(11) NOT NULL default '1',
  `user_notes` text NOT NULL,
  `user_industry` int(11) NOT NULL default '0',
  `user_company` varchar(200) NOT NULL default '',
  `company_id` bigint(20) NOT NULL default '0',
  `user_birthday` date NOT NULL default '0000-00-00',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_first_name`, `user_middle_name`, `user_last_name`, `user_address_line_1`, `user_address_line_2`, `user_city`, `user_state`, `user_zip`, `user_phone_number`, `user_cell_number`, `user_fax_number`, `user_primary_email`, `user_secondary_email`, `user_username`, `user_password`, `user_position`, `user_level`, `user_notes`, `user_industry`, `user_company`, `company_id`, `user_birthday`, `date_added`, `user_last_login`, `last_updated`) VALUES
(1, 'Matt', '', 'Brewer', '', '', 'Knoxville', '0', '37923', '', '', '', 'matt@dmgx.com', '', 'super', 'dmgx', '', 5, 'mine ass', 0, '', 0, '2009-01-01', '2009-12-12 13:12:58', '2009-12-14 08:06:54', '2009-12-14 08:06:54'),
(2, 'Wade', '', 'Austin', '', '', '', '0', '', '', '', '', 'wade@dmgx.com', '', 'admin', 'dmgx', '', 4, '', 0, '', 0, '2009-11-19', '2009-11-19 16:26:51', '2009-12-04 12:06:55', '2009-12-04 12:06:55');

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE IF NOT EXISTS `user_levels` (
  `user_level_id` tinyint(4) NOT NULL auto_increment,
  `user_level_name` varchar(75) NOT NULL default '',
  PRIMARY KEY  (`user_level_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user_levels`
--

INSERT INTO `user_levels` (`user_level_id`, `user_level_name`) VALUES
(1, 'Basic User'),
(4, 'Administrator'),
(5, 'Super Admin');
