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
	//	w($input:*)
	//
	//	Simple way to escape for the database
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function w($input) {
		return $db->prepare_input($w);
	}
	
	
?>