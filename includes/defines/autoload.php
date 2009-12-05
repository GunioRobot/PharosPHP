<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Pulls in all the defines as well as extra defines
	//
	////////////////////////////////////////////////////////////////////////////////

	$folder = dirname(__FILE__).'/';
	if ($handle = opendir($folder)) {
		while (false !== ($file = readdir($handle)) ) {
			if ($file != "." && $file != ".." &&!is_dir($folder.$file) && $file != basename(__FILE__) ) {
				include $folder.$file;
			}
		}
	}		
	
?>