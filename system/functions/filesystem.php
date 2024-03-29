<?

	/**
	 * @file filesystem.php
	 * @brief Functions for common filesystem actions
	 */

	/**
	 * check_and_remove_file
	 * If the file exists, remove it from the directory
	 *
	 * @param string $filename
	 * @param string $directory
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function check_and_remove_file($file, $dir=UPLOAD_PATH) {
		if ( $dir.$file != "" && @file_exists($dir.$file) ) {
			@unlink($dir.$file);
		}
	}
	
	
	/**
	 * check_and_remove_files_from_array
	 * Given an array of files, will loop through second parameter and if that file exists in $dir.$file, will be removed
	 *
	 * @param array $index_keys_into_second_param
	 * @param array $fields (such as $info->fields from database call)
	 * @param string $directory
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function check_and_remove_files_from_array($files, $arr, $dir=UPLOAD_PATH) {
		foreach($files as $f) {
			if ( isset($arr[$f]) ) check_and_remove_file($arr[$f], $dir);
		}
	}
	
	
	/**
	 * constrain_to_fit
	 * Returns information regarding the ratio & new width/height after constraining to fit in a certain bounding box
	 * 
	 * @param array ($width, $height)
	 * @param array ($width, $height)
	 *
	 * @return array $dimensions
	 * @author Matt Brewer
	 **/
	
	function constrain_to_fit(array $current, array $constraint) {
		
		$width = $current[0];
		$height = $current[1];
		$ratio = 1;
				
		// Check width, size down
		if ( $current[0] > $constraint[0] ) {
			$width = $constraint[0];
			$ratio = $constraint[0] / $current[0];
			$height = $ratio * $current[1];
		}
		
		// If height was too large, and also the larger of the large dimensions, resize off of instead of width
		if ( $current[1] > $constraint[1] && ($constraint[1] / $current[1]) > $ratio ) {
			$height = $constraint[1];
			$ratio = $constraint[1] / $current[1];
			$width = $ratio * $current[0];
		}
				
		return array($width, $height, $ratio);
		
	}
	
	
	/**
	 * constrain_image_to_fit
	 * Takes full path to an image file and will return the new dimensions (including resize ratio) after constraining to fit in the bounding box
	 * 
	 * @uses constrain_to_fit
	 * @throws InvalidArgumentException
	 *
	 * @param string $path
	 * @param array ($width, $height)
	 *
	 * @return array $dimensions
	 * @author Matt Brewer
	 **/
	
	function constrain_image_to_fit($path, array $constraints) {
		if ( $path != "" && file_exists($path) ) {
			list($width, $height) = getimagesize($path);
			return constrain_to_fit(array($width, $height), $constraints);
		} else {
			throw new InvalidArgumentException(sprintf("constrain_image_to_fit: argument 1 expected to be full path to file which exists."));
		}
	}
		

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