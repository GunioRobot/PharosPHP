<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Intelligent function that formats db fields according to type
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function formatFields(&$item, $key) {
		if ( is_string($item) ) {
			
			if ( $key == "date_added" || $key == "last_updated" || $key == "publish_date" ) {
				try {
					$d = new DateTime($item);
					$item = $d->format('U');
				} catch (Exception $e) {
					$item = html_entity_decode(stripslashes($item));
				}
				
			} else {
				$item = html_entity_decode(stripslashes($item));
			}
			
		}
	}


	////////////////////////////////////////////////////////////////////////////////
	//
	//	Formats incoming db results array as object
	//
	////////////////////////////////////////////////////////////////////////////////

	function clean_object($obj) {		
		array_walk($obj, "formatFields");		
		return (object)$obj;
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Formats incoming db results array as array
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function clean_array($obj) {
		array_walk($obj, "formatFields");
		return $obj;
	}


?>