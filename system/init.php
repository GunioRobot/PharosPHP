<?

	session_start();
	
	// Start loading the system
	$f = dirname(__FILE__);
	
	define('APP_PATH', substr(dirname($_SERVER['SCRIPT_NAME']), 1).'/');	
	define('SERVER_DIR', substr(substr($f, 0, strrpos($f, "/"))."/", 0, -strlen(APP_PATH)));
	
	// Define Global Vars and Config Information
	require_once SERVER_DIR.APP_PATH.'includes/classes/YAML/sfYaml.php';
	require_once SERVER_DIR.APP_PATH.'includes/classes/Settings.php';
	
	require_once SERVER_DIR.APP_PATH.'includes/functions/autoload.php';
	load_static_settings();
	
		
	require_once CLASSES_DIR.'Router.php';
	Router::parse();
	
	// Load the classes that are barebones
	require_once CLASSES_DIR.'QueryFactory.php';
	require_once CLASSES_DIR.'Controller.php';
	require_once CLASSES_DIR.'Modules.php';
	require_once CLASSES_DIR.'TableController.php';
	require_once CLASSES_DIR.'Hooks.php';
	
	// Database init
	$db = new queryFactory();
	if ( !$db->connect(Settings::get('database.host'), Settings::get('database.username'), Settings::get('database.password'), Settings::get('database.name'), false, false) ) {
		die("Error connecting to database");
	}
		
	// Finish initializing the system (load defines, settings, modules, set session variables, etc)
	bootstrap_system();
		
?>