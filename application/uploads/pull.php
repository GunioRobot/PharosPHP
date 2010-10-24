<?
	
	require_once '../../system/init.php';
	if ( ($table = Input::request("table")) && ($key = Input::request("key")) && ($id = Input::request($key)) ) {

		foreach($_FILES as $name => $meta) {
		
			try {
				
				$image = Input::request("save_as_image") === "true" ? true : false;
				$resizeImage = Input::request("resize_image") === "true" ? true : false;
				$width = Input::request("image_width");
				$height = Input::request("image_height");
				
				if ( $image && $resizeImage && $width && $height ) { 
					$filename = save_uploaded_file($name, array("is_image" => true, "resize" => compact("width", "height")))
				} else $filename = save_uploaded_file($name);
				
			} catch (Exception $e) {
				Console::log($e->getMessage());
			}
			
			if ( $filename != '' ) {
			
				$info = pathinfo($filename);
			
				$filesize = Input::request("store_filesize",'false') == 'true' ? " $name"."_file_size = '".filesize($filename)."', " : '';	
				$filetype = Input::request("store_file_type",'false') == 'true' ? " $name"."_file_type = '".$info['extension']."', " : '';	
					
				$sql = "UPDATE $table SET $name = '".$db->prepare_input($filename)."', $filesize $filetype last_updated = NOW() WHERE $key = '".(int)$id."' LIMIT 1";
				$db->Execute($sql);
			
				echo download_link_href($filename);
				
			} else echo 'false';
		
		}
		
	} else echo 'false';

	
?>