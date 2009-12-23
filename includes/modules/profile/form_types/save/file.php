<?

	if ( isset($_FILES[$data]) AND $_FILES[$data]['name'] != '' ) {
	
		// If existing entry, look for an existing file.  If there, remove it before moving the new one to keep our directories tidy.
		if ( ($key = get("key")) AND ($id = get($key)) AND ($table = post("table")) ) {
			$existing = $db->Execute("SELECT $data FROM $table WHERE $key = '$id' LIMIT 1");
			if ( defined('DELETE_OLD_WHEN_UPLOADING_NEW') && DELETE_OLD_WHEN_UPLOADING_NEW && $existing->fields[$data] != '' ) {
				@unlink(UPLOAD_DIR.$existing->fields[$data]);
			}
		} 
		
		$options = get_options($input);
	
		// Attempt to save the file
		try {
						
			if ( $options['save_as_image'] && $options['resize_image'] && $options['image_width'] && $options['image_height'] ) { 
				$filename = save_uploaded_file($data, UPLOAD_DIR, array(), true, array('width' => $options['image_width'], 'height' => $options['image_height']));
			} else $filename = save_uploaded_file($data);
			
			$_POST[$data] = $filename;
			
		} catch ( Exception $e ) {
			Console::log($e->getMessage());
			continue;
		}

		// Add the filesize to the sql statement, if it's the main video and not thumbnails, etc		
		if ( isset($options['store_filesize']) && $options['store_filesize'] === true ) {
			$size = filesize(UPLOAD_DIR.post($data));
			$sqlUpdate .= $data.'_file_size = "'.$size.'", ';
			$sqlFields .= $data.'_file_size,';
			$sqlValues .= '"'.$size.'",';
		}
		
		// Possible store the file type
		if ( isset($options['store_file_type']) && $options['store_file_type'] === true ) {

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