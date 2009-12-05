<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	get_template($filename, $path='/profiles/', $desired_extension='.html')
	//
	//	Opens the corresponding template file and returns as string
	//	Can customize directory, as well as file extension to grab
	//
	////////////////////////////////////////////////////////////////////////////////

	function get_template($filename, $path='profiles/', $desired_extension='.html') {

		$html_file = explode('?',$filename);
		$html_file = str_replace('.php', $desired_extension, $html_file[0]);
		$filename = TEMPLATE_DIR.$path.$html_file;
		$f = @fopen($filename, "r");
		if ( $f ) {
			$template = fread($f, filesize($filename));
			fclose($f);
		} unset($f);
		
		return $template;
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	make_clean_filename($filename)
	//
	//	Returns a filename with filesystem friendly characters only, allowing only:
	//	lower and uppercase letters, digits 0-9, underscore, period, and hyphen
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function make_clean_filename($filename) {
	 	return str_replace(' ', '_', preg_replace('/[^[A-Za-z0-9_\s\.-]]*/', '', $filename));
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	chmod_dir($dir, $changeSubdirs, $permissionNum, $output)
	//
	//	Will change permissions on specified folder and all contents, including
	//	recursively moving through subfolders if specified.
	//
	//	Optional output
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function chmod_dir($dir, $subdirs=true, $perm=0644, $output=true) {
	
		if ( is_dir($dir) ) {
			
			if ( $output ) echo "Changing: &quot;".$dir."&quot;...<br />\n";			
			if ( @chmod($dir, $perm) ) {
				if ( $output ) echo '<strong><span style="color:#009900;">success</span></strong><br />'."\n";				
			} else {
				if ( $output ) echo '<strong><span style="color:#cc3300;">failure</span></strong><br />'."\n";
			}
			
			if ( $h = opendir($dir) ) {
				while ( false !== ($f = readdir($h)) ) {
					if ( $f != '.' && $f != '..' ) {
						if ( $subdirs && is_dir($dir.'/'.$f) ) chmod_dir($dir.'/'.$f, $perm);
						else {
							if ( $output ) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Changing &quot;$dir/$f...\n";
							if ( @chmod($dir.'/'.$f, $perm) ) {
								if ( $output ) echo '<strong><span style="color:#009900;">success</span></strong><br />'."\n";
							} else {
								if ( $output ) echo '<strong><span style="color:#cc3300;">failure</span></strong><br />'."\n";
							}
						}
					}
				}
			} echo "<br />\n";
		}
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	download_link_href($filename)
	//
	//	Simply returns the full download path to a file
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function download_link_href($file) {
		if ( $file != '' ) {
			return UPLOAD_SERVER.'push.php?f='.$file;
		} else return '';
//		return UPLOAD_SERVER.$file;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	internal_external_link($src, $prefix)
	//
	//	Takes something like "index.php?pid=36" or "http://www.google.com" and
	//	figures out which ones are already a full link.  If not full, prepends
	//	the HTTP_SERVER (or your own prefix, if passed)
	//
	//	Returns a string for using in an anchor href attribute
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function internal_external_link($link, $prefix=HTTP_SERVER) {
		return (strpos($link, 'http://') !== false) ? $link : $prefix.$link;
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	redirect($url)
	//
	// 	Wrapper for header() and exit calls
	//
	////////////////////////////////////////////////////////////////////////////////	
	
	function redirect($url) {

		header("Location: $url");
		exit;
		
	}
	

?>