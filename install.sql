CREATE TABLE `assets` (`id` int(11) NOT NULL auto_increment,`title` varchar(200) NOT NULL default '',`filename` text NOT NULL,`filename_file_size` bigint(20) NOT NULL default '0',`filename_file_type` varchar(15) NOT NULL default '',`width` int(11) NOT NULL default '0',`height` int(11) NOT NULL default '0',`assoc_table` varchar(200) NOT NULL default '',`assoc_id` int(11) NOT NULL default '0',`notes` text NOT NULL,`date_added` datetime NOT NULL default '0000-00-00 00:00:00',`last_updated` datetime NOT NULL default '0000-00-00 00:00:00',PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `admin_nav` (  `id` int(10) NOT NULL AUTO_INCREMENT,  `parent_id` int(10) NOT NULL DEFAULT '0',  `name` varchar(200) COLLATE latin1_general_ci NOT NULL DEFAULT '',  `page` text COLLATE latin1_general_ci NOT NULL,  `min_lvl` int(2) NOT NULL DEFAULT '0',  `max_lvl` int(2) NOT NULL DEFAULT '0', `order_num` int(2) NOT NULL DEFAULT '0',  `description` text COLLATE latin1_general_ci NOT NULL,  `display` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',  `date_added` datetime NOT NULL,  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=62 ;
INSERT INTO `admin_nav` (`id`, `parent_id`, `name`, `page`, `min_lvl`, `max_lvl`, `order_num`, `description`, `display`, `date_added`, `last_updated`) VALUES(1, 4, 'Navigation', '/navigation/manage/', 5, 5, 4, '', 'visible', '2009-12-12 11:04:32', '2009-12-12 11:04:32'),(2, 10, 'Admin Accounts', '/users/manage/', 4, 5, 1, '', 'visible', '2009-12-12 11:04:55', '2009-12-12 11:07:28'),(4, 0, 'Super Settings', '', 5, 5, 50, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),(6, 10, 'Sign Out', '/session/logout/', 1, 5, 4, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),(8, 0, 'Help', '', 4, 5, 51, '', 'hidden', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),(9, 10, 'View Help', '/help/', 1, 5, 3, '', 'hidden', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),(10, 0, 'Admin Settings', '', 1, 5, 99, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),(11, 10, 'Edit My Account', '/users/edit/%%return Authentication::get()->user()->user_id;/', 1, 5, 1, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),(15, 0, 'Media Library', '', 1, 5, 0, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54'),(55, 10, 'Settings', '/settings/manage/', 4, 5, 3, '', 'visible', '2009-12-12 10:43:47', '2009-12-12 10:43:54');
CREATE TABLE IF NOT EXISTS `cron_jobs` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `task` text NOT NULL,  `type` varchar(200) NOT NULL,  `repeats` varchar(7) NOT NULL DEFAULT 'false',  `interval` int(11) NOT NULL DEFAULT '0',  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `in_progress` varchar(7) NOT NULL DEFAULT 'false',  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `general_settings` (  `setting_id` int(11) NOT NULL AUTO_INCREMENT,  `setting_name` varchar(200) NOT NULL DEFAULT '',  `setting_value` text NOT NULL,  `setting_level` int(11) NOT NULL,  `setting_notes` text NOT NULL,  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  PRIMARY KEY (`setting_id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;
INSERT INTO `general_settings` (`setting_id`, `setting_name`, `setting_value`, `setting_level`, `setting_notes`, `date_added`, `last_updated`) VALUES(23, 'Show Profiler Results', '<p>false</p>', 4, '', '2009-12-14 10:17:08', '2010-06-06 15:05:06');
CREATE TABLE IF NOT EXISTS `pages` (`id` bigint(20) NOT NULL AUTO_INCREMENT,`title` varchar(200) NOT NULL DEFAULT '',`slug` varchar(200) NOT NULL DEFAULT '',`text` text NOT NULL,`template` varchar(50) NOT NULL DEFAULT 'oneCol',`notes` text NOT NULL,`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',`last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS `users` (  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,  `user_first_name` varchar(75) NOT NULL DEFAULT '',  `user_middle_name` varchar(75) NOT NULL DEFAULT '',  `user_last_name` varchar(75) NOT NULL DEFAULT '',  `user_address_line_1` varchar(50) NOT NULL DEFAULT '',  `user_address_line_2` varchar(50) NOT NULL DEFAULT '',  `user_city` varchar(30) NOT NULL DEFAULT '',  `user_state` char(2) NOT NULL DEFAULT '',  `user_zip` varchar(10) NOT NULL DEFAULT '',  `user_phone_number` varchar(15) NOT NULL DEFAULT '',  `user_cell_number` varchar(15) NOT NULL DEFAULT '',  `user_fax_number` varchar(15) NOT NULL DEFAULT '',  `user_primary_email` varchar(100) NOT NULL DEFAULT '',  `user_secondary_email` varchar(100) NOT NULL DEFAULT '',  `user_username` varchar(50) NOT NULL DEFAULT '',  `user_password` varchar(32) NOT NULL DEFAULT '',  `user_position` varchar(50) NOT NULL DEFAULT '',  `user_level` int(11) NOT NULL DEFAULT '1',  `user_notes` text NOT NULL,  `user_industry` int(11) NOT NULL DEFAULT '0',  `user_company` varchar(200) NOT NULL DEFAULT '',  `company_id` bigint(20) NOT NULL DEFAULT '0',  `user_birthday` date NOT NULL DEFAULT '0000-00-00',  `logged_in` VARCHAR(7) NOT NULL DEFAULT 'false',  `last_logout` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `registered_ip_address` varchar(75) NOT NULL DEFAULT 'xxx.xxx.xxx.xxx',  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `user_last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  PRIMARY KEY (`user_id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;
INSERT INTO `users` (`user_id`, `user_first_name`, `user_middle_name`, `user_last_name`, `user_address_line_1`, `user_address_line_2`, `user_city`, `user_state`, `user_zip`, `user_phone_number`, `user_cell_number`, `user_fax_number`, `user_primary_email`, `user_secondary_email`, `user_username`, `user_password`, `user_position`, `user_level`, `user_notes`, `user_industry`, `user_company`, `company_id`, `user_birthday`, `date_added`, `user_last_login`, `last_updated`) VALUES(1, 'Matt', '', 'Brewer', '', '', 'Knoxville', '0', '37923', '', '', '', 'matt@pharosphp.com', '', 'admin', '0829bee38f057d25838d8975d4f1d09c', '', 5, '', 0, '', 0, '2009-01-01', '2009-12-12 13:12:58', '2010-06-11 21:42:48', '2010-06-11 21:42:48');
CREATE TABLE IF NOT EXISTS `user_levels` (  `user_level_id` tinyint(4) NOT NULL AUTO_INCREMENT,  `user_level_name` varchar(75) NOT NULL DEFAULT '',  PRIMARY KEY (`user_level_id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;
INSERT INTO `user_levels` (`user_level_id`, `user_level_name`) VALUES(1, 'Basic User'),(4, 'Administrator'),(5, 'Super Admin');
