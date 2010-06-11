<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	truncate_str($str, $lenght, $delim)
	//
	//	Nicely truncates text with the delimiter given, if > length
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function truncate_str($str, $length, $delim='...') {
	
		if ( strlen($str) > $length ) {
			$new_str = substr($str, 0, $length - strlen($delim));
			$new_str .= $delim;
			return $new_str;
		} else return $str;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	format_title($string)
	//
	//	Uppercase words, removes underscores and hypens
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function format_title($str){
		return ucwords(str_replace(array("_","-")," ",stripslashes(trim($str))));	
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	insert_substr($exisingString, $position, $stringToInsert)
	//
	// 	Just inserts the string somewhere in the existing string
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function insert_substr($str, $pos, $substr) {
		$s = substr($str,0,$pos);
		$s2 = substr($str,$pos);    
	    return $s.$substr.$s2;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	alt_tag(string $file)
	//
	//	Returns a properly formatted alt tag from a filename, ie
	//		"some_file.jpg" becomes "Some File"
	//
	////////////////////////////////////////////////////////////////////////////////
	
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

		if ( $date != '' AND $date != '0000-00-00 00:00:00' AND $date != '0000-00-00' ) {
		
			$finalDate = new DateTime($date, new DateTimeZone(date_default_timezone_get()));
			
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
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	format_filesize($size)
	//
	//	Expects $size in bytes (int) and retuns a string properly formatted
	//
	////////////////////////////////////////////////////////////////////////////////

	function format_filesize($size,$decimals=1) {
		
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
	


	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	splitCamelCase(string)
	//
	//	Takes "ManageSession" => array('manage', 'session')
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function split_camel_case($str) {
	  return preg_split('/(?<=\\w)(?=[A-Z])/', $str);
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	p_to_br(string)
	//
	//	Returns a string that has all paragraphs removed and uses line breaks instead
	//
	////////////////////////////////////////////////////////////////////////////////

	function p_to_br($s) {
		return str_replace("<p>","",str_replace("</p>","<br />",$s));
	}
	
	
	

	
	
	
	

?>