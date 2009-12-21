<?

	if ( isset($_FILES[$data]) AND $_FILES[$data]['name'] != '' ) {
	
		// If existing entry, look for an existing file.  If there, remove it before moving the new one to keep our directories tidy.
		if ( ($key = get("key")) AND ($id = get($key)) AND ($table = post("table")) ) {
			$existing = $db->Execute("SELECT $data FROM $table WHERE $key = '$id' LIMIT 1");
			if ( defined('DELETE_OLD_WHEN_UPLOADING_NEW') && DELETE_OLD_WHEN_UPLOADING_NEW && $existing->fields[$data] != '' ) {
				@unlink(UPLOAD_DIR.$existing->fields[$data]);
			}
		} 
	
		// Attempt to save the file, if get an error will print error xml, otherwise continue by printing good xml
		try {
			$_POST[$data] = save_uploaded_file($data);
		} catch ( Exception $e ) {
			Console::log($e->getMessage());
			continue;
		}
		
	
		// Create the options array
		$options = explode(':', trim($input['varx']));

		// Add the filesize to the sql statement, if it's the main video and not thumbnails, etc		
		if ( in_array('store_filesize', $options) ) {
			$size = filesize(UPLOAD_DIR.post($data));
			$sqlUpdate .= $data.'_file_size = "'.$size.'", ';
			$sqlFields .= $data.'_file_size,';
			$sqlValues .= '"'.$size.'",';
		}
		
		// Possible store the file type
		if ( in_array('store_file_type', $options) ) {

			$info = pathinfo(UPLOAD_DIR.post($data));
			$ext = $info['extension'];
			if ( $ext != "" ) {
				$sqlUpdate .= $data.'_file_type = "'.$ext.'", ';
				$sqlFields .= $data.'_file_type,';
				$sqlValues .= '"'.$ext.'",';
			}
			
		}
		
	
		
		// Store the filetype if given the option to
		
	} else $data = false;	// To skip
	
?>