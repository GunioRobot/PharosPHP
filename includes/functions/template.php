<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	truncate_str($str, $lenght, $delim)
	//
	//	Nicely truncates text with the delimiter given, if > length
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function truncate_str($str, $length, $delim) {
	
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
	
	function format_title($title){
		$tite = trim($title);
		$title = stripslashes($title);
		$title = str_replace('_',' ',$title);
		$title = str_replace('-', ' ', $title);
		$title = ucwords($title);
		return $title;
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
	//	write_css()
	//
	// Writes out CSS import lines for all CSS files starting with "style_"
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function write_css() {
		$css = array();
		$folder = TEMPLATE_DIR.'css/';
		if ($handle = opendir($folder)) {
			while (false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".." && !is_dir($folder.$file) && preg_match('/^style_(.*)/', basename($file)) ) {
					$css[] = TEMPLATE_SERVER.'css/'.$file;
				}
			}
		}		

		sort($css);
		foreach($css as $c) {
			echo '	<style type="text/css" media="screen">@import "'.$c.'";</style>'."\n";
		}
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	write_js()
	//
	// Writes out JS include lines for all JS files starting with "js_"
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function write_js() {
		$js = array();
		$folder = TEMPLATE_DIR.'js/';
		if ($handle = opendir($folder)) {
			while (false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".." && !is_dir($folder.$file) && $file != 'pngfix.js' && preg_match('/^js_(.*)/', basename($file)) ) {
					$js[] = TEMPLATE_SERVER.'js/'.$file;
				}
			}
		}
		
		sort($js);
		foreach($js as $j) {
			echo '	<script type="text/javascript" src="'.$j.'"></script>'."\n";
		}
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
	
	
	function site_link($link='') {
		return '/'.ADMIN_DIR.$link;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	format_filesize($size)
	//
	//	Expects $size in bytes (int) and retuns a string properly formatted
	//
	////////////////////////////////////////////////////////////////////////////////

	function format_filesize($size) {
		if ( $size > 1000000 ) $size = round($size/1000000,1) . ' MB';
		else if ( $size > 1000 ) $size = round($size/1000,1) . ' KB';
		else $size = $size . ' Bytes';
		return $size;
	}

	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	page_class()
	//
	//	Returns a list of class names to determine whether a nav item should be
	//	active or not
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function page_class($file) {
		return basename($_SERVER['SCRIPT_FILENAME'], '.php') === basename($file,'.php') ? 'btnOn' : '';
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	choose_form_type($form_array, $id, $value, $field_id, $link_value='null')
	//
	//	Most of the params are passed through to the require() form_type
	//	Returns a string of html to insert into the template
	//
	////////////////////////////////////////////////////////////////////////////////

 	function choose_form_type($form_array,$id,$value,$field_id,$link_value = 'null') {
	
		global $db;
				
		$file = CLASSES_DIR.'form_types/'.$form_array['type'].'.php';
		
		if ( file_exists($file) )  require($file);
		else $item = ' Form Function Not Set ('.$form_array['type'].')';
		
		return $item;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	process_repost_type($string)
	//
	//	Runs the corresponding code to process the original "form_type" after form
	//	submittal.
	//
	////////////////////////////////////////////////////////////////////////////////	
	
	function process_repost_type($type) {
		
		$file = INCLUDES_DIR.'repost_mods/form_types/'.$type.'.php';
		if ( file_exists($file) ) return $file;
		else throw new Exception("Repost form_type did not exist: ($file)");
		
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	make_id($string)
	//
	//	Returns sanitized string for use as DOM element id attribute
	//	ie, "Some title of my brother's 2nd Birtday &amp; my 3rd Party" becomes:
	//		"some-title-of-my-brothers-2nd-birthday my 3rd party"
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function make_id($string) {
		return strtolower(preg_replace('/[_-\s]+/', '-', preg_replace('/[^[A-Za-z0-9_-\s]]+/', '', html_entity_decode($string))));
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	controller_name(String)
	//
	//	Returns properly formatted controller name, ie 
	//		/admin/applications/ => 'Applications'
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function controller_name($string) {
		return str_replace(' ', '', ucwords(str_replace(array('_','-'), ' ', make_clean_filename($string))));
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	controller_link(className)
	//
	//	Takes "ManageSession" => "/admin/manage-session/"
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function controller_link($class,$action='') {
		return site_link(strtolower(implode('-',split_camel_case($class)))).$action;
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
	
	
	
	function view($class,$id) {
		return controller_link($class,"/view/$id/");
	}
	
	function edit($class,$id) {
		return controller_link($class,"/edit/$id/");
	}
	
	function delete($class,$id) {
		return controller_link($class,"/delete/$id/");
	}
	
	function create($class) {
		return controller_link($class,"/create/");
	}
	


?>