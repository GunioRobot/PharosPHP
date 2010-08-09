<?

	session_start();
		
		
		
	// Two VERY important defines, used everywhere
	$f = dirname(__FILE__);
	define('SERVER_DIR', $_SERVER['DOCUMENT_ROOT'].'/');
	define('APP_PATH', substr(realpath($f."/../")."/", strlen(SERVER_DIR)));
	
	
	
	// Initialize some path information used throughout the system
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
	define('APPLICATION_LANGUAGES_DIR', APPLICATION_DIR.'languages/');
			
	
	
	// Load in all the exceptions used in the system
	foreach(glob(EXCEPTIONS_DIR.'*.php') as $filename) {
		require_once $filename;
	} 	
		
		
				
	// Load in the few classes that are needed early on in system initialization
	require_once CLASSES_DIR.'Loader.php';
	Loader::load_class('Hooks');
	Hooks::init();
	
	// Load in the next set of classes
	Loader::load_class('YAML/sfYaml.php');
	Loader::load_class('Keypath');
	Loader::load_class('Settings');	
	
	
	// Load in the system defined functions
	foreach(glob(FUNCTIONS_DIR.'*.php') as $filename) {
		require_once $filename;
	}
	
	
	// Load in the remaining classes that offer additional functionality
	Loader::load_class('Language');
	Loader::load_class('Router');
	Loader::load_class('Template');
	Loader::load_class('QueryFactory');
	Loader::load_class('Controller');
	Loader::load_class('ApplicationController');
	Loader::load_class('ApplicationGenericPageController');
	Loader::load_class('TableController');
	Loader::load_class('Modules');
	
	
	// Call any attached actions after the core has been loaded
	Hooks::call_hook(Hooks::HOOK_CORE_CLASSES_LOADED);
	
	
	// Initialization the module system
	Hooks::call_hook(Hooks::HOOK_MODULES_PRE_LOADED);
	Modules::init();
	Hooks::call_hook(Hooks::HOOK_MODULES_POST_LOADED);
	
	
	// Load in all the application defined functions
	foreach(glob(APPLICATION_FUNCTIONS_DIR.'*.php') as $filename) {
		require_once $filename;
	}
	
	
	// Conditionally include support for ActiveRecord
	if ( version_compare(phpversion(), "5.3.0") >= 0 ) {
		require_once CLASSES_DIR.'ActiveRecord/init.php';
	}
	
	
	// Finish initializing the system (load defines, settings, modules, set session variables, etc)
	bootstrap_system();
	
	
	// System action to allow post system-init, pre controller created actions to execute		
	Hooks::call_hook(Hooks::HOOK_CONTROLLER_PRE_CREATED);	
		
?>