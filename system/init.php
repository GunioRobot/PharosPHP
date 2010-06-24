<?

	session_start();
		
	// Two VERY important defines, used everywhere
	$f = dirname(__FILE__);
	define('APP_PATH', substr(dirname($_SERVER['SCRIPT_NAME']), 1).'/');	
	define('SERVER_DIR', substr(substr($f, 0, strrpos($f, "/"))."/", 0, -strlen(APP_PATH)));
	
	// Load in all the functions (.php files) in this folder
	require_once SERVER_DIR.APP_PATH.'system/functions/autoload.php';
	
	autoload(EXCEPTIONS_DIR);			// Load in all the system defined Exception classes
	autoload(APPLICATION_FUNCTIONS_DIR);	// Load in all the application defined functions
	
	// Must be the first 2 classes loaded in the system
	require_once SERVER_DIR.APP_PATH.'system/classes/YAML/sfYaml.php';
	require_once SERVER_DIR.APP_PATH.'system/classes/Settings.php';
	
	// Now load the remaining core classes
	require_once CLASSES_DIR.'Router.php';
	require_once CLASSES_DIR.'Template.php';
	require_once CLASSES_DIR.'QueryFactory.php';
	require_once APPLICATION_CLASSES_DIR.'ApplicationController.php';
	require_once APPLICATION_CLASSES_DIR.'ApplicationGenericPageController.php';
	require_once APPLICATION_CLASSES_DIR.'TableController.php';
	require_once CLASSES_DIR.'Modules.php';
	require_once CLASSES_DIR.'Hooks.php';
	
	// Conditionally include support for ActiveRecord
	if ( version_compare(phpversion(), "5.3.0") >= 0 ) {
		require_once CLASSES_DIR.'ActiveRecord/init.php';
	}
	
	// Finish initializing the system (load defines, settings, modules, set session variables, etc)
	bootstrap_system();
		
?>