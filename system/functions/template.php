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
	//	write_css()
	//
	// Writes out CSS import lines for all CSS files starting with "style_"
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function write_css() {
		
		global $controller;
		
		// Grab array of autoloaded CSS files  
		$css = array();
		$folder = TEMPLATE_DIR.'css/';
		if ($handle = opendir($folder)) {
			while (false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".." && !is_dir($folder.$file) && preg_match('/^style(.*)/', basename($file)) ) {
					$css[] = TEMPLATE_SERVER.'css/'.$file;
				}
			}
		}		

		// Sort the CSS alphatbetically, then include
		if ( !empty($css) ) sort($css);
		foreach($css as $c) {
			echo '	<style type="text/css" media="screen">@import "'.$c.'";</style>'."\n";
		}
		
		// Grab CSS files the controller requested
		$css = $controller->css();
		if ( !empty($css) ) {
			foreach($css as $style) {
				echo '<style type="text/css" media="'.$style['type'].'">@import url('.TEMPLATE_SERVER.'css/'.$style['path'].');</style>';
			} 
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
	
		global $controller;
	
		// Grab all the autoload files from the directory
		$js = array();
		$folder = TEMPLATE_DIR.'js/autoload/';
		if ($handle = opendir($folder)) {
			while (false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".." && !is_dir($folder.$file) && $file != 'pngfix.js' && $file != "CMSLite.php" && $file != "jquery.js" ) {
					$info = pathinfo($folder.$file);
					$js[$info['extension']][] = $file;
				}
			}
		}
		
		
		// Now include those autoloaded JS files
		if ( !empty($js) ) {
			
			// Always first
			require_once TEMPLATE_DIR.'js/autoload/CMSLite.php';
			
			// Include any .js files (alphabetically sorted)
			if ( !empty($js['js']) ) {
				sort($js['js']);
				foreach($js['js'] as $j) {
					echo '	<script type="text/javascript" src="'.TEMPLATE_SERVER.'js/autoload/'.$j.'"></script>'."\n";
				}
			}
				
			// Include any .php JS files (alphabetically sorted)	
			if ( !empty($js['php']) ) {
				sort($js['php']);
				foreach($js['php'] as $j) {
					require_once TEMPLATE_DIR.'js/autoload/'.$j;
				}
				
			}
			
		}
		
		// Now include controller specific files
		$javascript = $controller->javascript();		
		if ( !empty($javascript) ) {
			foreach($javascript as $js) {
				if ( $js['type'] == JAVASCRIPT_INCLUDE ) {
					$data = $js['data']; 
					require TEMPLATE_DIR.'js/'.$js['path']; 
				} else {
					echo '<script type="text/javascript" src="'.TEMPLATE_SERVER.'js/'.$js['path'].'"></script>';
				}
			}
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
	//	is_current_parent_nav($page) - database object
	//
	//	Returns true if one of the top level nav items kid is currently on display
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function is_current_parent_nav($page) {
		
		global $controller;
		
		$controllerClass = get_class($controller);
		foreach($page->children as $p) {
			
			$parts = explode("/", trim($p->page, " /"));
			$controllerName = controller_name($parts[0]);
			if ( $controllerClass === $controllerName ) {
				return true;
			}
			
		}
		
		return false;
		
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
		$action = substr($action,0,1)==="/"?substr($action,1):$action;
		$action = preg_replace('/\/\/+/', '/', $action);
		if ( substr(strtolower($class), strlen($class) - strlen("controller")) == "controller" ) {
			$class = substr($class, 0, -strlen("controller"));
		}		
		return site_link(strtolower(implode('-',split_camel_case($class)))).'/'.$action;
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
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Prepends the full site path to beginning of link
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function site_link($link='') {
		return HTTP_SERVER.(substr($link,0,1)==="/"?substr($link,1):$link);
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Renders the template, calling the specific hooks
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function render_template() {
		
		global $controller;
		
		Hooks::call_hook(Hooks::HOOK_TEMPLATE_PRE_RENDER);
		
		require_once TEMPLATE_DIR.'tpl_HTML_header.php';
		require_once TEMPLATE_DIR.'tpl_header.php';
		require_once TEMPLATE_DIR.'tpl_body.php';
		require_once TEMPLATE_DIR.'tpl_footer.php';
		
		Hooks::call_hook(Hooks::HOOK_TEMPLATE_POST_RENDER);
		
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Helper function for "class/view/id/" like links
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function view($class,$id) {
		return controller_link($class,"view/$id/");
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Quick helper function for edit links
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function edit($class,$id) {
		return controller_link($class,"edit/$id/");
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Quick helper function for delete links
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function delete($class,$id) {
		return controller_link($class,"delete/$id/");
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Quick helper function for create links
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function create($class) {
		return controller_link($class,"create/");
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Quick helper function for save links
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function save($class,$id=0) {
		return controller_link($class,"save/".($id>0?"$id/":""));
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Quick helper function for manage links
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function manage($class) {
		return controller_link($class,"manage/");
	}

?>