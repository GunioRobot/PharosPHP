<?

	$components = explode("/", dirname(__FILE__));
	define('SERVER_DIR', array_pop($components));
	define('APP_PATH', dirname($_SERVER['SCRIPT_NAME']).'/');

	// Define Global Vars and Config Information
	require_once SERVER_DIR.APP_PATH.'includes/classes/YAML/sfYaml.php';
	sfYaml::load(SERVER_DIR.APP_PATH.'configure.yml');
	
	require_once SERVER_DIR.APP_PATH.'includes/functions/autoload.php';
	load_static_settings();

?>