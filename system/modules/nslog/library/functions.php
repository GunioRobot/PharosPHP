<?

	/**
	 * Write to the default log
	 *
	 * @param var_args
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function NSLog() {	
		$args = func_get_args();
		NSLogv($args);
	}
	
	
	/**
	 * Returns the formatted string instead of writing out to log file
	 *
	 * @param string $format
	 * @param var_args
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function sNSLog($format) {
		$args = func_get_args();		
		if ( count($args) >= 1 ) {
			return _log_line($format, array_slice($args,2));
		}
	}
	
	
	/**
	 * Write to a specified log file
	 *
	 * @param string $log_file
	 * @param var_args
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function NSLogvFile($log_file="default.log", $format="") {
						
		static $dir = null;
		if ( is_null($dir) ) {
			if ( !defined('LOG_PATH') ) {
				$dir = dirname(__FILE__).'/logs/';
			}
		}
		
		$args = func_get_args();		
		if ( count($args) >= 2 ) {
			@file_put_contents($dir.$log_file, _log_line($format, array_slice($args,2))."\n", FILE_APPEND);
		}
		
	}
	
	
	/**
	 * Similar to NSLog, but takes one array instead of variable list of params
	 *
	 * @return void
	 * @author Matt Brewer
	 **/
	function NSLogv($args) {
		call_user_func_array("NSLogvFile", array_merge(array("default.log"), $args));
	}
	
	
	/**
	 * Formats value for logging (expands arrays/objects, etc)
	 *
	 * @return void
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
	
	function _parse_format($str) {
		return str_replace("%@", "%s", $str);
	}
	
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

?>