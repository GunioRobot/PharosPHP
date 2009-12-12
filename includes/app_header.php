<?

	session_start();

	// Define Global Vars and Config Information
	require_once 'configure.php';
	
	// Load the classes that are barebones
	require_once CLASSES_DIR.'query_factory.php';
	require_once CLASSES_DIR.'Image.php';
	require_once CLASSES_DIR.'Controller.php';
	
	// Load the functions
	require_once FUNCTIONS_DIR.'autoload.php';
	
	// Database init
	$db = new queryFactory();
	if ( !$db->connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, USE_PCONNECT, false) ) {
		die("Error connecting to database");
	}
	
	// Load settings from the database
	load_dynamic_system_settings();
	
	// Load in all the content types
	load_content_types();
		
	// Load in extra defines in files
	require_once DEFINES_DIR.'autoload.php';
	
	// Profiler support
	require_once CLASSES_DIR.'PhpQuickProfiler.php';
	$profiler = new PhpQuickProfiler(PhpQuickProfiler::getMicroTime());
	
	// Setting the app values for use sitewide
	app_bootstrap();
		
?>