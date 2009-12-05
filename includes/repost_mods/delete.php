<?

	if ( ($type = get("type")) ) {
		
		$file = REPOST_DIR.'delete_types/'.$type.'.php';
		if ( file_exists($file) ) {
			
			include $file;
			
		} else {
			
			Console::log("Delete type not found:($type).");
			if ( ($key = get("key")) AND ($id = get($key)) AND ($table = get("table")) ) {
				$sql = "DELETE FROM $table WHERE $key = '$id'";
				$db->Execute($sql);
			}
			
		}
	
	} else {
		
		Console::log("(".__FILE__.") : type not set.");
		
	}

?>