<?
	/**
	 * chmod_dir
	 * Will change permissions on specified folder and all contents, including
	 * recursively moving through subfolders if specified.
	 *
	 * @param string $directory
	 * @param boolean $recursive
	 * @param octal $permissions
	 * @param boolean $output_to_buffer
	 *
	 * @return void
	 * @author Matt Brewer
	 **/	
	
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
	

	/**
	 * download_link_href
	 * URL to force a download of this file
	 *
	 * @param string $filename (relative to UPLOAD_URL)
	 *
	 * @return string $href
	 * @author Matt Brewer
	 **/
	
	function download_link_href($file) {
		if ( $file != '' ) {
			return UPLOAD_URL.'push.php?f='.$file;
		} else return '#';
	}
	

	/**
	 * internal_external_link
	 * Returns original if is valid URI, else prepends $prefix to make full URI
	 *
	 * @param string $href
	 * @param string $prefix
	 *
	 * @return string $href
	 * @author Matt Brewer
	 **/
	
	function internal_external_link($link, $prefix=ROOT_URL) {
		return (stripos($link, 'http://') !== false) ? $link : $prefix . $link;
	}
		

	/**
	 * redirect
	 * Issues page redirect request. ENDS SCRIPT EXECUTION
	 * 
	 * @param string $URL
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function redirect($url) {
		header("Location: $url");
		exit;
	}
	
	
	/**
	 * clean_upload_dir
	 * Removes all files marked for deletion during the next app publication
	 *
	 * @return int $num_removed
	 * @author Matt Brewer
	 **/
	
	Hooks::register_callback(Hooks::HOOK_APPLICATION_PUBLISH, 'clean_upload_dir');
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
	
	
	/**
	 * remove_file
	 * Marks a file for deletion at a later date (clean_upload_dir function performs deletion)
	 *
	 * @return int $database_id
	 * @author Matt Brewer
	 **/

	function remove_file($path) {
		
		global $db, $CURRENT_APP_ID;
				
		$sql = "INSERT INTO trashed_files VALUES(NULL,'".$path."','".$CURRENT_APP_ID."',NOW())";
		$db->Execute($sql);
		
		return $db->insert_ID();
		
	}
	
	
	/**
	 * register_trashed_files_table
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

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
	 * save_uploaded_file
	 * Saves an uploaded file to the specified directory, returns filename
	 * 
	 * USAGE:
	 * try {
	 * 		$filename = save_uploaded_file("input-name", array("is_image" => true, "resize" => array("width" => 100, "height" => 100)));
	 * } catch (Exception $e) {
	 * 		echo $e->getMessage();
	 * }
	 * 
	 * @throws InvalidFileSystemPathException
	 *
	 * @return (string|false) $filename if success, false if couldn't move file to location
	 * @author Matt Brewer
	 **/

	function save_uploaded_file($key, array $options=array()) {
		
		// Merge defaults & options for this function
		$options = (object)extend(array(
			"dir" => UPLOAD_PATH,
			"filetypes" => array(),
			"is_image" => false,
			"resize" => array('width' => false, 'height' => 'false')
		), $options);
		
		global $db;
		if ( isset($_FILES[$key]) && $_FILES[$key]['tmp_name'] ) {
												
			// If it's just a file, do extension checking
			if ( !$options->is_image ) {
				
				$info = pathinfo($_FILES[$key]['name']);
				if ( $info['extension'] == "" ) {
					throw new InvalidFileSystemPathException("Unknown filetype - extension not found!");
				}			
				
				// If checking for filetypes	
				if ( count($options->filetypes) > 0 ) {
				
					if ( !in_array($info['extension'], $options->filetypes) ) {
						throw new InvalidFileSystemPathException("Unsupported filetype of (".$info['extension'].")!");
					}
				}
				
			} else { // Since it's an image, do image type checking
				
				if ( !class_exists("Image") ) {
					Loader::load_class("Image");
				}
				
				try {
					
					if ( $options->resize['width'] && $options->resize['height'] ) {
						$image = new Image($_FILES[$key]['tmp_name'], $options->resize['width'], $options->resize['height']);
					} else $image = new Image($_FILES[$key]['tmp_name']);
					
					$image->save_img($_FILES[$key]['tmp_name']);
							
				} catch ( Exception $e ) {
					unset($image);	// Cleanup
					throw new Exception($e->getMessage());
				}
				
			}


			$item_name = String::clean_filename($_FILES[$key]['name']);
			$info = pathinfo($item_name);
			$location = $options->dir . $item_name;
						
			// If already exists, create a new destination
			if( file_exists($location) ) {
				$item_name = $info['filename'] . '_' . rand(0,99) . '.' . $info['extension'];
				$location = $options->dir . $item_name;
			}
							
			// Save the thumbnail (size previously set) in final destination (original hasn't been moved)
			if ( move_uploaded_file($_FILES[$key]['tmp_name'], $location) ) {
				return $item_name;
			} else return false;
			
		} else throw new Exception('$_FILES['.$key.'] was not set.');
		
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