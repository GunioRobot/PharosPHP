<?
	
	////////////////////////////////////////////////////////////////////////////////
	//
	// basic_where($keywords, $table)
	//
	// Constructs a MySQL " where table.field1 RLIKE $keyword AND table.field2 
	// RLIKE $keyword AND table.field3.....etc 
	//
	//	Returns a string to use in a SQL call
	//
	////////////////////////////////////////////////////////////////////////////////

	function basic_where($keywords, $table) {
	
		global $db;
	
		$keywords = trim($keywords);
		
		$where = ' WHERE (';
		$columns = array_keys(array_change_key_case($db->metaColumns($table)));

		// Search individual words
		$words = explode(' ', $keywords);
		foreach($words as $w) {
			foreach($columns as $c) {
				$where .= "$table.$c RLIKE '$w' OR ";
			}			
		} $where = substr($where,0,-3) . ') ';
				
		return $where;
	}
	
	
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