<?


	/**
	 * Template
	 * Contains several useful functions for generating HTML
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/

	class Template {
		
		
		/**
		 * layout()
		 *
		 * @return mixed (string $filename | boolean $success)
		 * @author Matt Brewer
		 **/
		
		public static function layout() {
			
			global $controller;
			
			if ( !is_null($controller->output->layout) && @file_exists(LAYOUTS_DIR.$controller->output->layout.".php") ) {
				return LAYOUTS_DIR.$controller->output->layout.".php";
			} else {
			
				$layout = self::_layout_file(Router::controller());
				$file = self::_layout_file($layout.Router::method().".php");

				if ( @file_exists(LAYOUTS_DIR.$file) ) {
					return LAYOUTS_DIR.$file;
				} else if ( @file_exists(LAYOUTS_DIR.$layout.".php") ) {
					return LAYOUTS_DIR.$layout.".php";
				} else if ( @file_exists(LAYOUTS_DIR.'application.php') ) {
					return LAYOUTS_DIR.'application.php';
				} else return false;
				
			}
			
		}
		
		
		/**
		 * _layout_file($class)
		 * 
		 * @param string $class
		 * @return string $filename
		 * @author Matt Brewer
		 **/

		private static function _layout_file($class) {
			return strtolower(implode('-',split_camel_case($class)));
		}
		
		

		/**
		 * write_css()
		 * Writes out CSS import lines for all CSS files starting with "style_"
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

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
			$css = $controller->output->css();
			if ( !empty($css) ) {
				foreach($css as $style) {
					echo '<style type="text/css" media="'.$style['type'].'">@import url('.PUBLIC_SERVER.'css/'.$style['path'].');</style>';
				} 
			}
		}




		/**
		 * write_js()
		 * Writes out JS include lines for all JS files starting with "js_"
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

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
			$javascript = $controller->output->javascript();		
			if ( !empty($javascript) ) {
				foreach($javascript as $js) {
					if ( $js['type'] == Output::JAVASCRIPT_INCLUDE ) {
						$data = $js['data']; 
						require PUBLIC_DIR.'js/'.$js['path']; 
					} else {
						echo '<script type="text/javascript" src="'.PUBLIC_SERVER.'js/'.$js['path'].'"></script>';
					}
				}
			}

		}



			
		/**
		 * write_header_meta()
		 * Simply writes out <meta/> tags in the HTML header for any provided for the
		 * active global controller object
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function write_header_meta() {

			global $controller;

			foreach($controller->output->meta() as $meta) {
				echo sprintf('<meta name="%s" content="%s" %s />'."\n", $meta['name'], $meta['content'], ($meta['http-equiv']!="" ? 'http-equiv="'.$meta['http-equiv'].'"' : ""));
			}

		}
	
	
	
	

		/**
		 * page_class($file)
		 * Returns a list of class names to determine whether a navigation item should be active or not
		 *
		 * @param string $filename
		 * @return string $class
		 * @author Matt Brewer
		 **/

		public static function page_class($file) {
			return basename($_SERVER['SCRIPT_FILENAME'], '.php') === basename($file,'.php') ? 'btnOn' : '';
		}


		/**
		 * is_current_parent_nav($page)
		 * Returns true if one of the top level navigation items kid is currently on display
		 *
		 * @param Database Object $Page
		 * @return boolean $is_current
		 * @author Matt Brewer
		 **/

		public static function is_current_parent_nav($page) {

			$controllerClass = substr(Router::controller(), 0, -strlen("Controller"));
			if ( is_array($page->children) ) {
				foreach($page->children as $p) {

					$parts = explode("/", trim($p->page, " /"));
					$controllerName = self::controller_name($parts[0]);
					if ( $controllerClass === $controllerName ) {
						return true;
					}

				}
			}

			return false;

		}
	
	
	
	
	
	
	
	
		/**
		 * site_link($link='')
		 * Prepends the full site path to beginning of link
		 *
		 * @param string (optional) $link
		 * @return string $link
		 * @author Matt Brewer
		 **/

		public static function site_link($link='') {
			return HTTP_SERVER.(substr($link,0,1)==="/"?substr($link,1):$link);
		}
	
		
	
		/**
		 * make_id($string)
		 *
		 * @param string $string
		 * @return string $sanitized_string
		 * @author Matt Brewer
		 **/

		public static function make_id($string) {
			return strtolower(preg_replace('/[_-\s]+/', '-', preg_replace('/[^[A-Za-z0-9_-\s]]+/', '', html_entity_decode($string))));
		}



		/**
		 * controller_name($string)
		 *
		 * @param string $string
		 * @return string $controller
		 * @author Matt Brewer
		 **/

		public static function controller_name($string) {
			return str_replace(' ', '', ucwords(str_replace(array('_','-'), ' ', make_clean_filename($string))));
		}



		/**
		 * controller_link($class, $action='')
		 *
		 * @param string $class
		 * @param string (optional) $optional
		 * @return string $link
		 * @author Matt Brewer
		 **/
		
		public static function controller_link($class, $action='') {
			$action = substr($action,0,1)==="/"?substr($action,1):$action;
			$action = preg_replace('/\/\/+/', '/', $action);
			return self::site_link(self::controller_slug($class)).'/'.$action;
		}
		
		
		/**
		 * controller_slug($class)
		 *
		 * @return string $controller_slug
		 * @author Matt Brewer
		 **/
		public static function controller_slug($class) {
			
			if ( substr(strtolower($class), strlen($class) - strlen("controller")) == "controller" ) {
				$class = substr($class, 0, -strlen("controller"));
			}
			
			return strtolower(implode('-',split_camel_case($class)));
			
		}
	
	
		/**
		 * render()
		 * Renders the template, calling the specific hooks
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function render() {

			global $controller;
			
			// Send HTTP headers to the browser if requested
			foreach($controller->output->header() as $header) {
				header($header);
			}
			
			ob_start();

			Hooks::call_hook(Hooks::HOOK_TEMPLATE_PRE_RENDER);

			if ( ($layout = self::layout()) !== false ) {
				require_once $layout;
			} else {
				echo $controller->output();
			}

			Hooks::call_hook(Hooks::HOOK_TEMPLATE_POST_RENDER);
			
			$output = ob_get_clean();
			if ( $controller->output->cache_enabled() ) $controller->output->cache($output);		// Write the contents of this to the cache
			echo $output;

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