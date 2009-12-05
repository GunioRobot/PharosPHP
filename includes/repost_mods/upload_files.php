<?

	if ( ($table = request("table")) && ($key = request("key")) && ($id = request("id")) ) {

		foreach($_FILES as $name => $meta){
		
			try {
				$filename = save_uploaded_file($name);
			} catch (Exception $e) {
				Console::log($e->getMessage());
			}
			
			$filesize = request("filesize") ? ", $name"."_file_size = '".filesize($filename)."' " : "";
			
			$sql = "UPDATE $table SET file_path = '".$db->prepare_input($filename)."' $filesize, last_updated = NOW() WHERE $key = '".(int)$id."' LIMIT 1";
			$db->Execute($sql);
	
		}
		
	}
	
	echo("thanks");
	exit;
	
?>