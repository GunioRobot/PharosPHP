<?

	session_start();	
	
	
	// Initialize some path information used throughout the system		
	define('CLASSES_PATH', SYSTEM_PATH.'classes/');
	define('FUNCTIONS_PATH', SYSTEM_PATH.'functions/');
	define('EXCEPTIONS_PATH', CLASSES_PATH.'Exceptions/');
	define('LANGUAGES_PATH', SYSTEM_PATH.'languages/');
	
	define('CACHE_PATH', APP_PATH.'cache/');		
	define('VIEWS_PATH', APP_PATH.'views/');	
	define('LAYOUTS_PATH', APP_PATH.'layouts/');
	define('MODELS_PATH', APP_PATH.'models/');
	define('CONFIGURATION_PATH', APP_PATH.'configure/');
	define('CONTROLLER_PATH', APP_PATH.'controllers/');
	define('MODULES_PATH', APP_PATH.'modules/');
	define('APPLICATION_CLASSES_PATH', APP_PATH.'classes/');
	define('APPLICATION_FUNCTIONS_PATH', APP_PATH.'functions/');
	define('APPLICATION_LANGUAGES_PATH', APP_PATH.'languages/');
			
	
	// Load in all the exceptions used in the system
	foreach(glob(EXCEPTIONS_PATH.'*.php') as $filename) {
		require_once $filename;
	} 	
		
		
				
	// Load in the few classes that are needed early on in system initialization
	require_once CLASSES_PATH.'Loader.php';
	Loader::load_class('Hooks');
	Hooks::init();
	
	// Load in the next set of classes
	Loader::load_class('YAML/sfYaml.php');
	Loader::load_class('Keypath');
	Loader::load_class('Settings');	
	
	
	// Load in the system defined functions
	foreach(glob(FUNCTIONS_PATH.'*.php') as $filename) {
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
	Loader::load_class('Application');
	
	
	// Call any attached actions after the core has been loaded
	Hooks::execute(Hooks::HOOK_CORE_CLASSES_LOADED);
	
	
	// Initialization the module system
	Hooks::execute(Hooks::HOOK_MODULES_PRE_LOADED);
	Modules::init();
	Hooks::execute(Hooks::HOOK_MODULES_POST_LOADED);
	
	
	// Load in all the application defined functions
	foreach(glob(APPLICATION_FUNCTIONS_PATH.'*.php') as $filename) {
		require_once $filename;
	}
	
	
	// Conditionally include support for ActiveRecord
	if ( version_compare(phpversion(), "5.3.0") >= 0 ) {
		require_once CLASSES_PATH.'ActiveRecord/init.php';
	}
	
	
	// Finish initializing the system (load defines, settings, modules, set session variables, etc)
	Application::bootstrap();
	
	
	// System action to allow post system-init, pre controller created actions to execute		
	Hooks::execute(Hooks::HOOK_CONTROLLER_PRE_CREATED);	
		
?>