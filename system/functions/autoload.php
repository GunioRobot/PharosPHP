<?

	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Basic File System Stuff
	//
	////////////////////////////////////////////////////////////////////////////////

	define('SYSTEM_DIR', SERVER_DIR.APP_PATH.'system/');
	define('APPLICATION_DIR', SERVER_DIR.APP_PATH.'application/');
	
	define('CLASSES_DIR', SYSTEM_DIR.'classes/');
	define('FUNCTIONS_DIR', SYSTEM_DIR.'functions/');
	define('EXCEPTIONS_DIR', CLASSES_DIR.'Exceptions/');
	define('CACHE_DIR', APPLICATION_DIR.'cache/');	
	define('PUBLIC_DIR', SERVER_DIR.APP_PATH.'public/');
	
	define('VIEWS_DIR', APPLICATION_DIR.'views/');	
	define('LAYOUTS_DIR', APPLICATION_DIR.'layouts/');
	define('MODELS_DIR', APPLICATION_DIR.'models/');
	define('CONFIGURATION_DIR', APPLICATION_DIR.'configure/');
	define('CONTROLLER_DIR', APPLICATION_DIR.'controllers/');
	define('MODULES_DIR', APPLICATION_DIR.'modules/');
	define('LANGUAGES_DIR', SYSTEM_DIR.'languages/');
	
	define('APPLICATION_CLASSES_DIR', APPLICATION_DIR.'classes/');
	define('APPLICATION_FUNCTIONS_DIR', APPLICATION_DIR.'functions/');
	define('APPLICATION_LANGUAGES_DIR', APPLICIATION_DIR.'languages/');
	
	


	////////////////////////////////////////////////////////////////////////////////
	//
	//	Pulls in all the files in this folder
	//
	////////////////////////////////////////////////////////////////////////////////
	foreach(glob(dirname(__FILE__).'/*.php') as $filename) {
		if ( $filename != __FILE__ ) {
			include $filename;
		}
	}		

?>