<?

	/**
	 * Index
	 * 
	 * Handles all incoming requests
	 *
	 **/
	
	
	
	
	/**
	 * Edit the following information to customize the framework installation
	 *
	 **/

	// Full path to the directory enclosing the framework & application
	if ( !defined("ROOT") ) {
		define("ROOT", dirname(dirname(dirname(__FILE__)))."/");
	}
	
	
	// Path to the "application" directory (without trailing slash)
	if ( !defined("APP_DIR") ) {
		define("APP_DIR", basename(dirname(dirname(__FILE__))));
	}
	
	
	// Path to the system core
	if ( !defined("PHAROS_CORE_INCLUDE_PATH") ) {
		define("PHAROS_CORE_INCLUDE_PATH", ROOT);
	}
	
	
	
	/**
	 * Leave the following information alone, unless you have good reason to edit it
	 *
	 **/
	
	
	// Name of the "public" directory (without trailing slash)
	if ( !defined("PUBLIC_DIR") ) {
		define("PUBLIC_DIR", basename(dirname(__FILE__)));
	}
	
	// Path of the "public" directory
	if ( !defined("PUBLIC_PATH") ) {
		define("PUBLIC_PATH", dirname(__FILE__)."/");
	}
	
	// Core path
	if ( !defined("SYSTEM_PATH") ) {
		define("SYSTEM_PATH", PHAROS_CORE_INCLUDE_PATH."system/");
	}
	
	// App Path
	if ( !defined("APP_PATH") ) {
		define("APP_PATH", ROOT.APP_DIR."/");
	}

	// Begin loading the system
	require_once SYSTEM_PATH.'init.php';
				
	// System is initialized, now find & load controller & execute			
	Application::run();
	
?>