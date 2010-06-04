<?

	session_start();
	
	// Start loading the system
	require_once dirname(__FILE__).'/load.php';
	
	// Load the classes that are barebones
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
		
	// Finish initializing the system (load defines, settings, modules, set session variables, etc)
	bootsrap_system();
		
?>