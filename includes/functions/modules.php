<?

	load_modules() {
		$folder = MODULES_DIR;
		if ($handle = opendir($folder)) {
			while (false !== ($file = readdir($handle)) ) {
				if ($file != "." && $file != ".." ) {
					if ( is_dir($folder.$file) ) {
						$h2 = opendir($folder.$file);
						while ( false !== ($f2 = readdir($handle)) ) {
							if ( $f2 != "." && $f2 != ".." && !is_dir($folder.$file.$f2) && $f2 === "include.php" ) {
								include $folder.$file.$f2;
							}
						}
					}
				}
			}
		}
	}

?>