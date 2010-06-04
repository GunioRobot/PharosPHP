<?

	$f = dirname(__FILE__);
	
	define('APP_PATH', substr(dirname($_SERVER['SCRIPT_NAME']), 1).'/');	
	define('SERVER_DIR', substr(substr($f, 0, strrpos($f, "/"))."/", 0, -strlen(APP_PATH)));
	
	// Define Global Vars and Config Information
	require_once SERVER_DIR.APP_PATH.'includes/classes/YAML/sfYaml.php';
	require_once SERVER_DIR.APP_PATH.'includes/classes/Settings.php';
	
	require_once SERVER_DIR.APP_PATH.'includes/functions/autoload.php';
	load_static_settings();

?>