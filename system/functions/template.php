<?

	/**
	 * @file template.php
	 * @brief Functions for formatting output
	 */

	/**
	 * truncate_str
	 * 
	 * @param string $original
	 * @param int $length
	 * @param string $delimiter
	 *
	 * @return string $truncated_string
	 * @author Matt Brewer
	 **/

	function truncate_str($str, $length, $delim='...') {
		if ( strlen($str) > $length ) {
			$new_str = substr($str, 0, $length - strlen($delim));
			$new_str .= $delim;
			return $new_str;
		} else return $str;
	}
	
	/**
	 * format_title
	 *
	 * @param string $original
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function format_title($str) {
		return ucwords(str_replace(array("_","-")," ",stripslashes(trim($str))));	
	}
	
	
	/**
	 * insert_substr
	 * Just inserts the string somewhere in the existing string
	 * 
	 * @param string $subject
	 * @param int $offset
	 * @param string $substr
	 *
	 * @return string $string
	 * @author Matt Brewer
	 **/

	function insert_substr($str, $pos, $substr) {
		$s = substr($str, 0, $pos);
		$s2 = substr($str, $pos);    
	    return $s . $substr . $s2;
	}
	
	
	/**
	 * alt_tag
	 *
	 * @param string $filename
	 * 
	 * @return string $alt
	 * @author Matt Brewer
	 **/

	function alt_tag($file) {
		return ucwords(substr(str_replace(array('_','-'), ' ', $file), 0, strrpos($file, '.')));
	}
		
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	format_date($date, $use_time=null, $pretty=null, $hourOffset=0)
	//
	//	params:
	//		$date	 	- date as a string (formatted as 'YYYY-mm-dd HH:ii:ss')
	//		$use_time	- whether to display time or just the date (boolean)
	//		$pretty		- "Today" or "Today at 1:29pm", or just "8/7/09" (boolean)
	//		$hourOffset	- integer to modify the time ( to effectively sync server and client timezones)
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function format_date($date, $use_time=null, $pretty=null, $hourOffset=0) {
		
		static $timezone = null;
		if ( is_null($timezone) ) {
			$timezone = new DateTimeZone(date_default_timezone_get());
		}

		if ( $date != '' AND $date != '0000-00-00 00:00:00' AND $date != '0000-00-00' ) {
		
			$finalDate = new DateTime($date, $timezone);
			
			if ( is_int($hourOffset) AND $hourOffset != 0 ) $finalDate->modify($hourOffset.' hour');
			$info = date_parse($finalDate->format('Y-m-d H:i:s'));
		    $today = getdate();
	
			if ( !isset($pretty) OR (isset($pretty) AND $pretty) ) {
	     
		    	// If $date is same day as $today
			    if ( $info['year'] == $today['year'] AND $info['month'] == $today['mon'] AND $info['day'] == $today['mday'] ) {
					if ( isset($use_time) AND $use_time )
						$s = $finalDate->format('g:i a	') . " Today";
					else $s = 'Today';
			    } else {
				
					if ( isset($use_time) AND $use_time ) $s = $finalDate->format('m/j/y \a\t g:i a');
		     		else $s = $finalDate->format('m/j/y');
	
		     	}
		
			} else {
				
				$s = $use_time ? $finalDate->format('m/j/y \a\t g:i a') : $finalDate->format('m/j/y');
				
			}
		
			return $s;
			
		} else return '';
	}
	
	
	/**
	 * format_filesize
	 * Expects $size in bytes (int) and returns a string properly formatted
	 * 
	 * @param int $size
	 * @param int $decimal_places
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function format_filesize($size, $decimals=1) {
		
		$sizes = array(
			// ========================= Origin ====
			'TB' => 1099511627776,  // pow( 1024, 4)
			'GB' => 1073741824,     // pow( 1024, 3)
			'MB' => 1048576,        // pow( 1024, 2)
			'kB' => 1024,           // pow( 1024, 1)
			'B ' => 1,              // pow( 1024, 0)
		);
		
		foreach($sizes as $unit => $mag) {
			if ( doubleval($size) >= $mag ) return number_format($size/$mag, $decimals) . ' ' . $unit;
		} return false;
	
	}
	
	
	/**
	 * split_camel_case
	 * Takes string and returns array of all words, split by capital letters
	 * 
	 * @param string $string
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function split_camel_case($str) {
	  return preg_split('/(?<=\\w)(?=[A-Z])/', $str);
	}
	
?>