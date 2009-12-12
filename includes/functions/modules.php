<?

	$modules = array();
	
	function load_module($name) {
		
		global $modules;
		
		if ( !isset($modules[$name]) ) {
		
			$folder = MODULES_DIR;
			if ($handle = opendir($folder)) {
				while (false !== ($file = readdir($handle)) ) {
					if ($file != "." && $file != ".." && is_dir($folder.$file) && $file === $name ) {
						if ( @file_exists($folder.$file.'/include.php') ) {
							include $folder.$file.'/include.php';
							$modules[$name] = $folder.$file.'/include.php';
						}
					}
				}
			}
		}
		
	}

?>