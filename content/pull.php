<?

	require_once '../includes/app_header.php';

	if ( ($table = request("table")) && ($key = request("key")) && ($id = request($key)) ) {

		foreach($_FILES as $name => $meta){
		
			try {
				$filename = save_uploaded_file($name);
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