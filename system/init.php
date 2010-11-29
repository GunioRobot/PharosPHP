<?

	/**
	 * @file system/init.php
	 * @brief Begins the application initialization process and provides several useful filesystem path constants
	 * @author Matt Brewer
	 */

	session_start();	
	
	define('DS', DIRECTORY_SEPARATOR);
	
	define('CLASSES_PATH', SYSTEM_PATH . 'classes' . DS);
	define('FUNCTIONS_PATH', SYSTEM_PATH . 'functions' . DS);
	define('LANGUAGES_PATH', SYSTEM_PATH . 'languages' . DS);
	
	define('CACHE_PATH', APP_PATH . 'cache' . DS);		
	define('VIEWS_PATH', APP_PATH . 'views/');	
	define('LAYOUTS_PATH', APP_PATH . 'layouts' . DS);
	define('MODELS_PATH', APP_PATH . 'models' . DS);
	define('CONFIGURATION_PATH', APP_PATH . 'configure' . DS);
	define('CONTROLLER_PATH', APP_PATH . 'controllers' . DS);
	define('MODULES_PATH', SYSTEM_PATH . 'modules' . DS);
	define('APPLICATION_MODULES_PATH', APP_PATH . 'modules' . DS);
	define('APPLICATION_CLASSES_PATH', APP_PATH . 'classes' . DS);
	define('APPLICATION_FUNCTIONS_PATH', APP_PATH . 'functions' . DS);
	define('APPLICATION_LANGUAGES_PATH', APP_PATH . 'languages' . DS);
			
	
	// Load in all the exceptions used in the system
	require_once CLASSES_PATH . 'Exceptions.php';
		
				
	// Load in the few classes that are needed early on in system initialization
	require_once CLASSES_PATH . 'Object.php';
	require_once CLASSES_PATH . 'String.php';
	require_once CLASSES_PATH . 'Loader.php';
	Loader::load_class('NotificationCenter');
	NotificationCenter::init();
	
	
	// Load in the next set of classes
	Loader::load_class('YAML' . DS . 'sfYaml.php');
	Loader::load_class('Keypath');
	Loader::load_class('Settings');	
	
	
	// Initialize the Settings API
	Settings::load();
	Settings::load_static_system_settings();
	
	
	// Load in the system defined functions
	foreach(glob(FUNCTIONS_PATH . '*.php') as $filename) {
		require_once $filename;
	}
	
	
	// Load in the Application class & register error/exception handlers
	Loader::load_class('Application');
	set_exception_handler(array("Application", 'exception_handler'));
	set_error_handler(array("Application", 'error_handler'));
	
	
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
	
	
	// Call any attached actions after the core has been loaded & begin the boostrap process
	NotificationCenter::execute(NotificationCenter::CORE_CLASSES_LOADED_NOTIFICATION);
		
?>