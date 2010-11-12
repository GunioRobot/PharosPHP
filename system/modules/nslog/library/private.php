<?

	/**
	 * _formatted_output_from_var
	 * Formats value for logging (expands arrays/objects, etc)
	 * 
	 * @param mixed $var
	 *
	 * @return string $output
	 * @author Matt Brewer
	 **/
		
	function _formatted_output_from_var($var) {
			
		$boo = $var;
		if ( is_bool($var) ) {
			$var = $var === true ? "true" : "false";
		} else if ( is_scalar($var) ) {
			
			if ( is_numeric($var) ) {
				$var = "(".gettype($var).")".$var;
			} else if ( is_resource($var) ) {
				$var = gettype($var);
			} else {
				$var = strval($var);
			}
			
		} else {
			ob_start();
			var_dump($var);
			$var = ob_get_clean();
		}

		return $var;
		
	}
	
	
	/**
	 * _parse_format
	 * Sanitizes the format string
	 *
	 * @return string $format
	 * @author Matt Brewer
	 **/

	function _parse_format($str) {
		return str_replace("%@", "%s", $str);
	}
	
	
	/**
	 * _log_line
	 * Returns the string to be written to the log file
	 *
	 * @return string $line_contents
	 * @author Matt Brewer
	 **/

	function _log_line($format, $args) {

		preg_match_all("/(%[+-]?[\s0]?[-]?[\.[:digit:]]?[bcdeEufFgGosxX@])/", $format, $params);
		
		$params = $params[0];
		$values = array_values($args);
		$length = count($params);

		$values = ($length < count($values) && $length >= 0) ? array_slice($values, 0, $length) : $values;
		foreach($params as $index => $p) {
			if ( $p == "%@" ) {
				$values[$index] = _formatted_output_from_var($values[$index]);
			}
		}	
		
		return vsprintf(date("Y-m-d H:i:s").' -- '._parse_format($format), $values);
		
	}
	
	
	/**
	 * _register_nslog_database
	 * Registers the database table
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	
	function _register_nslog_database() {
		global $db;
		$db->Execute("CREATE TABLE IF NOT EXISTS  `nslog_messages` (`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY, `message` TEXT NOT NULL, `timestamp` DATETIME NOT NULL) ENGINE = MYISAM ;");
	}

?>