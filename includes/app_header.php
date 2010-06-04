<?

	session_start();
	
	// Start loading the system
	require_once dirname(__FILE__).'/load.php';
	
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