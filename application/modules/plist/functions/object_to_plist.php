<?
	
	
	function CFDictionary_from_object($obj) {
		
		$td = new CFTypeDetector();  
		
		$arr = is_array($obj) ? $obj : get_object_vars($obj);
		$arr = clean_array($arr);
		
		foreach($arr as $key => &$value) {

			if ( $key === "date_added" || $key === "last_updated" || $key === "publish_date" ) {
				
				$value = new CFDate($value);
				
			} else {				
				
				if ( is_array($value) ) {
					
					foreach($value as $k => &$i) {
						if ( is_array($i) || is_object($i) ) {
							$i = CFDictionary_from_object($i);
						} else {
							$i = $td->toCFType($i);
						}
					}
					
				} else if ( is_object($value) ) {
					
					$value = CFDictionary_from_object($value);
					
				} else if ( is_numeric($value) ) {

					$value = $td->toCFType($value);
					
				} else {

					$value = $td->toCFType(iphone_ready_text($value));
					
				}
				
			}
			
		}
		
		return $td->toCFType($arr);
		
	}

?>
