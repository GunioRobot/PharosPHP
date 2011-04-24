<?

	// Simple setup, load in the one class file and all function files (injecting into application)
	require_once dirname(__FILE__).'/classes/Console.php';
	require_once dirname(__FILE__).'/classes/PhpQuickProfiler.php';
	foreach(glob(dirname(__FILE__) . DS . 'functions' . DS . '*.php') as $file) {
		require_once $file;
	}
	
	global $profiler;
	
	// Profiler support
	$profiler = new PhpQuickProfiler(PhpQuickProfiler::getMicroTime());
	
?>