<?

	
	if ( ($removeImage = Input::post($data.'_remove_image')) === "true" ) {
		$sql = "SELECT * FROM ".Input::post("table")." WHERE ".Input::post("data_key")." = '".(int)$id."' LIMIT 1";
		$info = $db->Execute($sql);
		if ( !$info->EOF && $info->fields[$data] != "" ) {
			
			if ( defined('DELETE_OLD_WHEN_UPLOADING_NEW') && DELETE_OLD_WHEN_UPLOADING_NEW === true ) {
				@unlink(UPLOAD_PATH.$info->fields[$data]);
			} else {
				remove_file($info->fields[$data]);	// Places in "trashed_files" table
			}
			
			$sqlUpdate .= $data.' = "", ';
			$sqlFields .= $data.',';
			$sqlValues .= '"",';
						
			if ( isset($options['store_filesize']) && $options['store_filesize'] == "true" ) {
				$sqlUpdate .= $data.'_file_size = "0", ';
				$sqlFields .= $data.'_file_size,';
				$sqlValues .= '"0",';
			}
			
			if ( isset($options['store_file_type']) && $options['store_file_type'] == "true" ) {
				$sqlUpdate .= $data.'_file_type = "", ';
				$sqlFields .= $data.'_file_type,';
				$sqlValues .= '"",';
			}
			
			return;
			
		}
		
	}
	
	if ( isset($_FILES[$data]) AND $_FILES[$data]['name'] != '' ) {	
		
		$options = get_options($input);
			
		// Attempt to save the file
		try {
						
			if ( $options['save_as_image'] && $options['resize_image'] && $options['image_width'] && $options['image_height'] ) { 
				$filename = save_uploaded_file($data, array("is_image" => true, "resize" => array("width" => $options['image_width'], "height" => $options['image_height'])));
			} else $filename = save_uploaded_file($data);
						
			$_POST[$data] = $filename;
			
		} catch ( Exception $e ) {
			Console::log($e->getMessage());
			$data = null;
		}
		
		if ( $data ) {

			// Add the filesize to the sql statement, if it's the main video and not thumbnails, etc		
			if ( isset($options['store_filesize']) && $options['store_filesize'] == "true" ) {
				$size = filesize(UPLOAD_PATH.Input::post($data));
				$sqlUpdate .= $data.'_file_size = "'.$size.'", ';
				$sqlFields .= $data.'_file_size,';
				$sqlValues .= '"'.$size.'",';
			}
		
			// Possible store the file type
			if ( isset($options['store_file_type']) && $options['store_file_type'] == "true" ) {

				$info = pathinfo(UPLOAD_PATH.Input::post($data));
				$ext = $info['extension'];
				if ( $ext != "" ) {
					$sqlUpdate .= $data.'_file_type = "'.$ext.'", ';
					$sqlFields .= $data.'_file_type,';
					$sqlValues .= '"'.$ext.'",';
				}
			
			}
			
		}
		
	
		
		// Store the filetype if given the option to
		
	} else $data = false;	// To skip
	
?>