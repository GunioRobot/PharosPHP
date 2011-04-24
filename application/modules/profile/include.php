<?

	// Simple setup, load in the one class file and all function files (injecting into application)
	require_once dirname(__FILE__).'/classes/Profile.php';
	foreach(glob(dirname(__FILE__) . DS . 'functions' . DS . '*.php') as $file) {
		require_once $file;
	}
		
	define('FORM_TYPE_DIR', dirname(__FILE__).'/form_types/');
	
	define_profile_module_hooks();

?>