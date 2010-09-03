<?

	// Simple setup, load in the one class file and all function files (injecting into application)
	require_once dirname(__FILE__).'/classes/Profile.php';
	require_once dirname(__FILE__).'/functions/autoload.php';
	
	define('FORM_TYPE_DIR', dirname(__FILE__).'/form_types/');
	
	define_profile_module_hooks();

?>