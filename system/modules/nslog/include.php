<?

	/**
	 * @file nslog/include.php
	 * @brief Module provides a few useful logging functions (to file or database) such as NSLog 
	 *
	 * @author Matt Brewer
	 */
	
	if ( !defined("LOG_PATH") ) {
		define("LOG_PATH", dirname(__FILE__) . DS . 'logs' . DS);
	}
	
	require_once dirname(__FILE__) . DS . 'library' . DS . 'private.php';
	require_once dirname(__FILE__) . DS . 'library' . DS . 'functions.php';
	
	_register_nslog_database();

?>