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
	define('CACHE_DIR', SYSTEM_DIR.'cache/');	
	define('PUBLIC_DIR', SERVER_DIR.APP_PATH.'public/');
	
	define('VIEWS_DIR', APPLICATION_DIR.'views/');	
	define('LAYOUTS_DIR', APPLICATION_DIR.'layouts/');
	define('MODELS_DIR', APPLICATION_DIR.'models/');
	define('CONFIGURATION_DIR', APPLICATION_DIR.'configure/');
	define('CONTROLLER_DIR', APPLICATION_DIR.'controllers/');
	define('MODULES_DIR', APPLICATION_DIR.'modules/');
	
	
	
	


	////////////////////////////////////////////////////////////////////////////////
	//
	//	Pulls in all the files in this folder
	//
	////////////////////////////////////////////////////////////////////////////////

	$folder = dirname(__FILE__).'/';
	if ($handle = opendir($folder)) {
		while (false !== ($file = readdir($handle)) ) {
			if ($file != "." && $file != ".." &&!is_dir($folder.$file) && $file != basename(__FILE__) && preg_match("/.*.php$/", $file) ) {
				include $folder.$file;
			}
		}
	}		
	
?>