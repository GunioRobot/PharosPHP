<?

	class Template {
	
		////////////////////////////////////////////////////////////////////////////////
		//
		//	write_css()
		//
		// Writes out CSS import lines for all CSS files starting with "style_"
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function write_css() {

			global $controller;

			// Grab array of autoloaded CSS files  
			$css = array();
			$folder = PUBLIC_DIR.'css/';
			if ($handle = opendir($folder)) {
				while (false !== ($file = readdir($handle))){
					if ($file != "." && $file != ".." && !is_dir($folder.$file) && preg_match('/^style(.*)/', basename($file)) ) {
						$css[] = PUBLIC_SERVER.'css/'.$file;
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
					echo '<style type="text/css" media="'.$style['type'].'">@import url('.PUBLIC_SERVER.'css/'.$style['path'].');</style>';
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

		public static function write_js() {

			global $controller;

			// Grab all the autoload files from the directory
			$js = array();
			$folder = PUBLIC_DIR.'js/autoload/';
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
				require_once PUBLIC_DIR.'js/autoload/CMSLite.php';

				// Include any .js files (alphabetically sorted)
				if ( !empty($js['js']) ) {
					sort($js['js']);
					foreach($js['js'] as $j) {
						echo '	<script type="text/javascript" src="'.PUBLIC_SERVER.'js/autoload/'.$j.'"></script>'."\n";
					}
				}

				// Include any .php JS files (alphabetically sorted)	
				if ( !empty($js['php']) ) {
					sort($js['php']);
					foreach($js['php'] as $j) {
						require_once PUBLIC_DIR.'js/autoload/'.$j;
					}

				}

			}

			// Now include controller specific files
			$javascript = $controller->javascript();		
			if ( !empty($javascript) ) {
				foreach($javascript as $js) {
					if ( $js['type'] == JAVASCRIPT_INCLUDE ) {
						$data = $js['data']; 
						require PUBLIC_DIR.'js/'.$js['path']; 
					} else {
						echo '<script type="text/javascript" src="'.PUBLIC_SERVER.'js/'.$js['path'].'"></script>';
					}
				}
			}

		}



		////////////////////////////////////////////////////////////////////////////////
		//
		//	write_header_meta()
		//
		//	Simply writes out <meta/> tags in the HTML header for any provided for the 
		//	active global controller object
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function write_header_meta() {

			global $controller;

			foreach($controller->meta() as $meta) {
				echo sprintf('<meta name="%s" content="%s" %s />'."\n", $meta['name'], $meta['content'], ($meta['http-equiv']!="" ? 'http-equiv="'.$meta['http-equiv'].'"' : ""));
			}

		}
	
	
	
	
	
	
	
	
	
		////////////////////////////////////////////////////////////////////////////////
		//
		//	page_class()
		//
		//	Returns a list of class names to determine whether a nav item should be
		//	active or not
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function page_class($file) {
			return basename($_SERVER['SCRIPT_FILENAME'], '.php') === basename($file,'.php') ? 'btnOn' : '';
		}




		////////////////////////////////////////////////////////////////////////////////
		//
		//	is_current_parent_nav($page) - database object
		//
		//	Returns true if one of the top level nav items kid is currently on display
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function is_current_parent_nav($page) {

			$controllerClass = substr(Router::controller(), 0, -strlen("Controller"));
			if ( is_array($page->children) ) {
				foreach($page->children as $p) {

					$parts = explode("/", trim($p->page, " /"));
					$controllerName = controller_name($parts[0]);
					if ( $controllerClass === $controllerName ) {
						return true;
					}

				}
			}

			return false;

		}
	
	
	
	
	
	
	
	
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Prepends the full site path to beginning of link
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function site_link($link='') {
			return HTTP_SERVER.(substr($link,0,1)==="/"?substr($link,1):$link);
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

		public static function make_id($string) {
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

		public static function controller_name($string) {
			return str_replace(' ', '', ucwords(str_replace(array('_','-'), ' ', make_clean_filename($string))));
		}


		////////////////////////////////////////////////////////////////////////////////
		//
		//	controller_link(className)
		//
		//	Takes "ManageSession" => "/admin/manage-session/"
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function controller_link($class,$action='') {
			$action = substr($action,0,1)==="/"?substr($action,1):$action;
			$action = preg_replace('/\/\/+/', '/', $action);
			if ( substr(strtolower($class), strlen($class) - strlen("controller")) == "controller" ) {
				$class = substr($class, 0, -strlen("controller"));
			}		
			return self::site_link(strtolower(implode('-',split_camel_case($class)))).'/'.$action;
		}
	
	
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Renders the template, calling the specific hooks
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function render_template() {

			global $controller;

			Hooks::call_hook(Hooks::HOOK_TEMPLATE_PRE_RENDER);

			if ( ($layout = Router::layout()) !== false ) {
				require_once $layout;
			} else {
				echo $controller->output();
			}

			Hooks::call_hook(Hooks::HOOK_TEMPLATE_POST_RENDER);

		}
	
	
	
	
		/*
		*
		*	The following are helper methods for generating valid site links quickly
		*
		*/
	
	
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Helper function for "class/view/id/" like links
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function view($class,$id) {
			return self::controller_link($class,"view/$id/");
		}


		////////////////////////////////////////////////////////////////////////////////
		//
		//	Quick helper function for edit links
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function edit($class,$id) {
			return self::controller_link($class,"edit/$id/");
		}


		////////////////////////////////////////////////////////////////////////////////
		//
		//	Quick helper function for delete links
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function delete($class,$id) {
			return self::controller_link($class,"delete/$id/");
		}


		////////////////////////////////////////////////////////////////////////////////
		//
		//	Quick helper function for create links
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function create($class) {
			return self::controller_link($class,"create/");
		}


		////////////////////////////////////////////////////////////////////////////////
		//
		//	Quick helper function for save links
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function save($class,$id=0) {
			return self::controller_link($class,"save/".($id>0?"$id/":""));
		}


		////////////////////////////////////////////////////////////////////////////////
		//
		//	Quick helper function for manage links
		//
		////////////////////////////////////////////////////////////////////////////////

		public static function manage($class) {
			return self::controller_link($class,"manage/");
		}
		
			
	}
	
?>