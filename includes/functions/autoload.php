<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Pulls in all the core function files as well as user defined functions
	//
	////////////////////////////////////////////////////////////////////////////////

	$folder = dirname(__FILE__).'/';
	if ($handle = opendir($folder)) {
		while (false !== ($file = readdir($handle)) ) {
			
			if ($file != "." && $file != ".." ) {
				
				if ( !is_dir($folder.$file) ) {
					if ( $file != basename(__FILE__) ) {
						include $folder.$file;
					}
				} else if ( $folder.$file === EXTRA_FUNCTIONS_DIR ) {

					$h2 = opendir(EXTRA_FUNCTIONS_DIR);
					while ( false !== ($f2 = readdir($handle)) ) {
						if ( $f2 != "." && $f2 != ".." && !is_dir(EXTRA_FUNCTIONS_DIR.$f2) ) {
							include EXTRA_FUNCTIONS_DIR.$f2;
						}
					}

				}
				
			}
			
		}
	}		
	
?>