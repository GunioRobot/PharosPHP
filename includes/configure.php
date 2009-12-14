<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Main Site Information
	//
	//	Change with every installation
	//
	////////////////////////////////////////////////////////////////////////////////
	
	define('HTTP_SERVER', 'http://'.$_SERVER['HTTP_HOST'].'/');
	define('ADMIN_DIR', 'cms-lite/');
	define('UPLOAD_TO', 'content/');
	define('XML_TO', 'xml/');	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Basic File System Stuff
	//
	////////////////////////////////////////////////////////////////////////////////
	
	define('HOME_DIR', '/');
	define('SERVER_DIR', $_SERVER["DOCUMENT_ROOT"].'/');
	define('INCLUDES_DIR', SERVER_DIR.ADMIN_DIR.'includes/');
	define('CLASSES_DIR', INCLUDES_DIR.'classes/');
	define('FUNCTIONS_DIR', INCLUDES_DIR.'functions/');
	define('UPLOAD_DIR', SERVER_DIR.ADMIN_DIR.UPLOAD_TO);
	define('XML_DIR', SERVER_DIR.ADMIN_DIR.XML_TO);
	define('DEFINES_DIR', INCLUDES_DIR.'defines/');


	
	////////////////////////////////////////////////////////////////////////////////
	//
	// Main Database Settings
	//
	////////////////////////////////////////////////////////////////////////////////
	
	define('DB_TYPE', 'mysql');
	define('DB_SERVER', 'localhost');
	define('DB_SERVER_USERNAME', 'marines_marines');
	define('DB_SERVER_PASSWORD', 'camotoe99');
	define('DB_DATABASE', 'marines_gigmarkapp');
	define('USE_PCONNECT', 'false'); 
	define('STORE_SESSIONS', 'db');
			
?>