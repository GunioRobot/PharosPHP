<?

	require_once dirname(__FILE__).'/classes/EmailAddressValidator.php';
	foreach(glob(dirname(__FILE__) . DS . 'functions' . DS . '*.php') as $file) {
		require_once $file;
	}
	
?>