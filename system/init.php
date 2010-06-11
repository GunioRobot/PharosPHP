<?

	session_start();
	
	$f = dirname(__FILE__);
	
	// Two VERY important defines, used everywhere
	define('APP_PATH', substr(dirname($_SERVER['SCRIPT_NAME']), 1).'/');	
	define('SERVER_DIR', substr(substr($f, 0, strrpos($f, "/"))."/", 0, -strlen(APP_PATH)));
	
	// Load in all the functions (.php files) in this folder
	require_once SERVER_DIR.APP_PATH.'system/functions/autoload.php';
	
	// Define Global Vars and Config Information
	require_once SERVER_DIR.APP_PATH.'system/classes/YAML/sfYaml.php';
	require_once SERVER_DIR.APP_PATH.'system/classes/Settings.php';
	
	// Load the Router and request parsing of the URL to controller class, method, and params	
	require_once CLASSES_DIR.'Router.php';
		
	// Load the classes that are barebones
	require_once CLASSES_DIR.'Template.php';
	require_once CLASSES_DIR.'QueryFactory.php';
	require_once CLASSES_DIR.'Controller.php';
	require_once CLASSES_DIR.'Modules.php';
	require_once CLASSES_DIR.'TableController.php';
	require_once CLASSES_DIR.'Hooks.php';
	
	// Conditionally include support for ActiveRecord
	if ( version_compare(phpversion(), "5.3.0") >= 0 ) {
		require_once CLASSES_DIR.'ActiveRecord/init.php';
	}
	
	// Finish initializing the system (load defines, settings, modules, set session variables, etc)
	bootstrap_system();
		
?>