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
	 * clean_object
	 * Formats array fields, returns as object (stdClass)
	 *	 *
	 * @return stdClass $obj
	 * @author Matt Brewer
	 **/

	function clean_object(array $obj) {		
		foreach($obj as $key => &$value) {
			if ( is_string($value) ) {

				if ( $key == "date_added" || $key == "last_updated" || $key == "publish_date" ) {
					try {
						$d = new DateTime($value);
						$value = $d->format('U');
					} catch (Exception $e) {
						$value = html_entity_decode(stripslashes($value), ENT_QUOTES, 'UTF-8');
					}

				} else {
					$value = html_entity_decode(stripslashes($value), ENT_QUOTES, 'UTF-8');
				}

			} else if ( is_array($value) ) {
				$value = clean_object($value);
			}
		}
		return (object)$obj;
	}
	
	
	/**
	 * results_array
	 * Returns an array containing simple stdClass objects that have been sanitized, recursively from the database.
	 * If you provide a $pkid, the array keys will be the pkid for even quicker indexing of the array
	 * 
	 * @param string $sql
	 * @param string $pkid (optional)
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function results_array($sql, $pkid=null) {
		global $db;
		for ( $ret = array(), $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
			$obj = clean_object($info->fields);
			if ( $pkid !== null ) {
				$ret[$obj->{$pkid}] = $obj;
			} else {
				$ret[] = $obj;
			}
		} return $ret;
	}
	
	
?>