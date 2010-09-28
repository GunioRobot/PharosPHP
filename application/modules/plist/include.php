<?

	foreach(glob(dirname(__FILE__).'/functions/*.php') as $file) {
		require_once $file;
	}

	require_once dirname(__FILE__).'/classes/CFPropertyList.php';

?>