<?

	
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
			return UPLOAD_URL.'push.php?f='.$file;
		} else return '';
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	internal_external_link($src, $prefix)
	//
	//	Takes something like "index.php?pid=36" or "http://www.google.com" and
	//	figures out which ones are already a full link.  If not full, prepends
	//	the ROOT_URL (or your own prefix, if passed)
	//
	//	Returns a string for using in an anchor href attribute
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function internal_external_link($link, $prefix=ROOT_URL) {
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
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Removes all extraneous files from the content directory when publishing
	//	XML for an application
	//
	////////////////////////////////////////////////////////////////////////////////

	function clean_upload_dir($app) {
	
		global $db;
		
		// Trash all the files that are earmarked in the table
		$sql = "SELECT * FROM trashed_files WHERE app_id = '".(int)$app."'";
		for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
			@unlink(UPLOAD_PATH.$info->fields['path']);
		}
		
		// Have removed those files now, so remove them from the table
		$db->Execute("DELETE FROM trashed_files WHERE app_id = '".(int)$app."'");
		
		return $info->RecordCount();
		
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Places a path in the "trashed_files" table, creating the table if needed
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function remove_file($path) {
		
		global $db, $CURRENT_APP_ID;
		
		/*
		$sql = "CREATE TABLE IF NOT EXISTS `trashed_files` (
		  `id` int(11) NOT NULL auto_increment,
		  `path` text NOT NULL,
		  `app_id` int(11) NOT NULL default '0',
		  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		
		$db->Execute($sql);
		*/
		
		$sql = "INSERT INTO trashed_files VALUES(NULL,'".$path."','".$CURRENT_APP_ID."',NOW())";
		$db->Execute($sql);
		
		return $db->insert_ID();
		
	}
	
	
	Hooks::register_callback(Hooks::HOOK_CORE_CLASSES_LOADED, 'register_trashed_files_table');
	function register_trashed_files_table() {
		
		global $db;

		$sql = "CREATE TABLE IF NOT EXISTS `trashed_files` (
		  `id` int(11) NOT NULL auto_increment,
		  `path` text NOT NULL,
		  `app_id` int(11) NOT NULL default '0',
		  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

		$db->Execute($sql);
		
	}
	
	
	/**
	 * sanitize_incoming_xml
	 * Sanitizes incoming XML, stripping out invalid characters. Would be fine if values were wrapped in CDATA tags...
	 *
	 * @return string $XML
	 * @author Matt Brewer
	 **/
	
	function sanitize_incoming_xml() {
		return str_replace(array("&", "&&amp;"), "&amp;", file_get_contents("php://input"));
	}

?>