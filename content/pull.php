<?

	require_once '../includes/app_header.php';

	if ( ($table = request("table")) && ($key = request("key")) && ($id = request($key)) ) {

		foreach($_FILES as $name => $meta) {
		
			try {
				
				$image = request("save_as_image") === "true" ? true : false;
				$resizeImage = request("resize_image") === "true" ? true : false;
				$width = request("image_width");
				$height = request("image_height");
				
				if ( $image && $resizeImage && $width && $height ) { 
					$filename = save_uploaded_file($name, UPLOAD_DIR, array(), true, array('width' => $width, 'height' => $height));
				} else $filename = save_uploaded_file($name);
				
			} catch (Exception $e) {
				Console::log($e->getMessage());
			}
			
			if ( $filename != '' ) {
			
				$info = pathinfo($filename);
			
				$filesize = !request("store_filesize") ? "" : " $name"."_file_size = '".filesize($filename)."', ";	
				$filetype = !request("store_file_type") ? "" : " $name"."_file_type = '".$info['extension']."', ";	
					
				$sql = "UPDATE $table SET $name = '".$db->prepare_input($filename)."', $filesize $filetype last_updated = NOW() WHERE $key = '".(int)$id."' LIMIT 1";
				$db->Execute($sql);
			
				echo download_link_href($filename);
				
			} else echo 'false';
		
		}
		
	} else echo 'false';

	
?>