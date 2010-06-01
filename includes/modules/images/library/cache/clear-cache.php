<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Deletes all the files in this folder
	//
	////////////////////////////////////////////////////////////////////////////////
	
	if ( !defined("SITE_NAME") ) die("Restricted Access");

	$folder = dirname(__FILE__).'/';
	if ($handle = opendir($folder)) {
		while (false !== ($file = readdir($handle)) ) {
			if ($file != "." && $file != ".." &&!is_dir($folder.$file) && $file != basename(__FILE__) ) {
				@unlink($folder.$file);
			}
		}
	}		
	
?>