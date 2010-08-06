<?

	session_start();
		
	// Two VERY important defines, used everywhere
	$f = dirname(__FILE__);
	define('SERVER_DIR', $_SERVER['DOCUMENT_ROOT'].'/');
	define('APP_PATH', substr(realpath($f."/../")."/", strlen(SERVER_DIR)));
	
	// Load in the few classes that are needed early on in system initialization
	require_once SERVER_DIR.APP_PATH.'system/classes/Hooks.php';
	require_once SERVER_DIR.APP_PATH.'Loader.php';
	
	// Load in all the functions (.php files) in this folder
	require_once SERVER_DIR.APP_PATH.'system/functions/autoload.php';
	
	autoload(EXCEPTIONS_DIR);				// Load in all the system defined Exception classes
	autoload(APPLICATION_FUNCTIONS_DIR);	// Load in all the application defined functions
	
	// Must be the first 2 classes loaded in the system
	require_once SERVER_DIR.APP_PATH.'system/classes/YAML/sfYaml.php';
	require_once SERVER_DIR.APP_PATH.'system/classes/Settings.php';
	
	// Now load the remaining core classes
	// Also, autload a few classes in the /application/classes/ directory
	Loader::load_class('Router');
	Loader::load_class('Template');
	Loader::load_class('QueryFactory');
	Loader::load_class('Controller');
	Loader::load_class('ApplicationController');
	Loader::load_class('ApplicationGenericPageController');
	Loader::load_class('TableController');
	Loader::load_class('Modules');
	Loader::load_class('Cron');
	Loader::load_class('Browser');
	
	// Conditionally include support for ActiveRecord
	if ( version_compare(phpversion(), "5.3.0") >= 0 ) {
		require_once CLASSES_DIR.'ActiveRecord/init.php';
	}
	
	// Finish initializing the system (load defines, settings, modules, set session variables, etc)
	bootstrap_system();
	
	// System action to allow post system-init, pre controller created actions to execute		
	Hooks::call_hook(Hooks::HOOK_CONTROLLER_PRE_CREATED);	
		
?>