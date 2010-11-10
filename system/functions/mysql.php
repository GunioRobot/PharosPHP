<?

	/**
	 * @file mysql.php
	 * @brief Functions for extending MYSQL functionality
	 */
	
	/**
	 * basic_where
	 * Constructs where part of SQL statement for searching
	 * 
	 * @param string $keywords
	 * @param string $table_name
	 *
	 * @return string $SQL
	 * @author Matt Brewer
	 **/

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
	

	/**
	 * formatFields
	 * Used for a call in array_walk to format an array
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

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


	/**
	 * clean_object
	 * Formats array fields, returns as object (stdClass)
	 *
	 * @uses formatFields
	 *
	 * @return stdClass $obj
	 * @author Matt Brewer
	 **/

	function clean_object($obj) {		
		array_walk($obj, "formatFields");		
		return (object)$obj;
	}
	
	
	/**
	 * clean_array
	 * Formats array fields
	 *
	 * @uses formatFields
	 * 
	 * @return array $arr
	 * @author Matt Brewer
	 **/

	function clean_array($obj) {
		array_walk($obj, "formatFields");
		return $obj;
	}
	
	
?>