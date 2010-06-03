<?

	session_start();

	// Define Global Vars and Config Information
	require_once 'configure.php';
	
	// Load the functions
	require_once FUNCTIONS_DIR.'autoload.php';
	
	// Load the classes that are barebones
	require_once CLASSES_DIR.'YAML/sfYaml.php';
	require_once CLASSES_DIR.'QueryFactory.php';
	require_once CLASSES_DIR.'Controller.php';
	require_once CLASSES_DIR.'Modules.php';
	require_once CLASSES_DIR.'TableController.php';
	require_once CLASSES_DIR.'Hooks.php';
	
	
	// Database init
	$db = new queryFactory();
	if ( !$db->connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, USE_PCONNECT, false) ) {
		die("Error connecting to database");
	}
		
	// Finish initializing the application (load defines, settings, modules, set session variables, etc)
	Hooks::call_hook(Hooks::HOOK_APPLICATION_BOOTSTRAP);
		
?>