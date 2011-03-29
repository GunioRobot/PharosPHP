<?

	/**
	 * @file nslog/library/functions.php
	 * @brief Functions for logging activity to database for a log file
	 *
	 * @author Matt Brewer
	 */


	/**
	 * NSLog
	 * Write message to the database table
	 *
	 * @param va_args
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function NSLog() {	
		$args = func_get_args();
		NSLogv($args);
	}
	
	
	/**
	 * NSLogf
	 * Write message to the default log file
	 *
	 * @param va_args
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function NSLogf() {
		global $db;
		$args = func_get_args();
		$db->Execute("INSERT INTO `nslog_messages` (`message`, `timestamp`), VALUES('%s', NOW())", $db->prepare_input(sNSLog($args)));
	}
	
	
	/**
	 * sNSLog
	 * Returns the formatted string instead of writing out to log file
	 *
	 * @param string $format
	 * @param var_args
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function sNSLog($format) {
		$args = func_get_args();		
		if ( count($args) >= 1 ) {
			return _log_line($format, array_slice($args,2));
		}
	}
	
	
	/**
	 * NSLogvFile
	 * Write to a specified log file, or to the database
	 *
	 * @param mixed $log_file - (string logfile, null to database)
	 * @param var_args
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function NSLogvFile($log_file="default.log", $format="") {
						
		static $dir = null;
		if ( is_null($dir) ) {
			if ( !defined('LOG_PATH') ) {
				$dir = dirname(__FILE__).'/logs/';
			} else $dir = LOG_PATH;
		}
		
		$args = func_get_args();		
		if ( count($args) >= 2 ) {
			@file_put_contents($dir.$log_file, _log_line($format, array_slice($args,2))."\n", FILE_APPEND);
		}
		
	}
	
	
	/**
	 * NSLogv
	 * Similar to NSLog, but takes one array instead of variable list of params
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function NSLogv($args) {
		call_user_func_array("NSLogvFile", array_merge(array("default.log"), $args));
	}
	
	


?>