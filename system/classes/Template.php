<?


	/**
	 * Template
	 * Contains several useful functions for generating HTML
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/

	final class Template extends Object {
		
		
		/**
		 * layout
		 * Determines the layout to be used to render the response
		 * The controller can provide a custom layout by setting the layout property on the controller's output object property (without the file extension)
		 * Alternatively, this method will search for a corresponding file in the application/layouts directory to match the request URL, ie:
		 * /session/login/ will search for a file first in application/layouts/session-controller.php and if not found, then application/layouts/session-controller-login.php
		 * The failsafe default layout is application.php
		 *
		 * @return mixed (string $filename | boolean $success)
		 * @author Matt Brewer
		 **/
		
		public static function layout() {
			return Application::controller()->output->layout;
		}
		
		
		/**
		 * write_css
		 * Writes out CSS import lines for all CSS files auto loaded and requested by the controller
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function write_css() {

			// Grab array of autoloaded CSS files  
			$css = array();
			$folder = PUBLIC_PATH.'css/';
			if ($handle = opendir($folder)) {
				while (false !== ($file = readdir($handle))){
					if ($file != "." && $file != ".." && !is_dir($folder.$file) && preg_match('/^style(.*)/', basename($file)) ) {
						$css[] = PUBLIC_URL.'css/'.$file;
					}
				}
			}		

			// Sort the CSS alphatbetically, then include
			if ( !empty($css) ) sort($css);
			
			foreach($css as $c) {
				echo sprintf('	<link rel="stylesheet" type="text/css" media="screen" href="%s" />'."\n", $c);
			}

			// Grab CSS files the controller requested
			$css = Application::controller()->output->css();
			if ( !empty($css) ) {
				foreach($css as $style) {
					echo sprintf('<link rel="stylesheet" type="text/css" media="%s" href="%s" />'."\n", $style['type'], $style['path']);
				} 
			}

		}




		/**
		 * write_js
		 * Writes out JS include lines for all JS files auto loaded and requested by the controller
		 * NOTE: This method always includes PharosPHP.php, to include jQuery & setup a global CMSLite javascript object
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function write_js() {

			// Grab all the autoload files from the directory
			$js = array();
			$folder = PUBLIC_PATH.'js/autoload/';
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
				require_once PUBLIC_PATH.'js/autoload/PharosPHP.php';

				// Include any .js files (alphabetically sorted)
				if ( !empty($js['js']) ) {
					sort($js['js']);
					foreach($js['js'] as $j) {
						echo '	<script type="text/javascript" src="'.PUBLIC_URL.'js/autoload/'.$j.'"></script>'."\n";
					}
				}

				// Include any .php JS files (alphabetically sorted)	
				if ( !empty($js['php']) ) {
					sort($js['php']);
					foreach($js['php'] as $j) {
						require_once PUBLIC_PATH.'js/autoload/'.$j;
					}

				}

			}

			// Now include controller specific files
			$javascript = Application::controller()->output->javascript();		
			if ( !empty($javascript) ) {
				foreach($javascript as $js) {
					if ( $js['type'] == HTTPResponse::JAVASCRIPT_INCLUDE ) {
						$data = $js['data']; 
						require PUBLIC_PATH.'js/'.$js['path']; 
					} else {
						echo '<script type="text/javascript" src="'.$js['path'].'"></script>';
					}
				}
			}

		}



			
		/**
		 * write_header_meta
		 * Simply writes out <meta/> tags in the HTML header for any provided for the
		 * active global controller object
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function write_header_meta() {

			foreach(Application::controller()->output->meta() as $meta) {
				echo sprintf('<meta name="%s" content="%s" %s />'."\n", $meta['name'], $meta['content'], ($meta['http-equiv']!="" ? 'http-equiv="'.$meta['http-equiv'].'"' : ""));
			}

		}
	
	
	
	

		/**
		 * page_class
		 * Returns a list of class names to determine whether a navigation item should be active or not
		 *
		 * @param string $filename
		 * @param string $class
		 *
		 * @return string $class
		 * @author Matt Brewer
		 **/

		public static function page_class($file, $class="btnOn") {
			return basename($_SERVER['SCRIPT_FILENAME'], '.php') === basename($file,'.php') ? $class : '';
		}


		/**
		 * is_current_parent_nav
		 * Returns true if one of the top level navigation items kid is currently on display
		 *
		 * @param Database Object $Page
		 *
		 * @return boolean $is_current
		 * @author Matt Brewer
		 **/

		public static function is_current_parent_nav($page) {

			$controllerClass = substr(Router::controller(), 0, -strlen("Controller"));
			if ( isset($page->children) && is_array($page->children) ) {
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
		 * site_link
		 * Prepends the full site path to beginning of link
		 *
		 * @param string (optional) $link
		 *
		 * @return string $link
		 * @author Matt Brewer
		 **/

		public static function site_link($link='') {
			return ROOT_URL.(substr($link,0,1)==="/"?substr($link,1):$link);
		}
	
		
	
		/**
		 * make_id
		 *
		 * @param string $string 
		 *
		 * @return string $sanitized_string
		 * @author Matt Brewer
		 **/

		public static function make_id($string) {
			return strtolower(preg_replace('/[_-\s]+/', '-', preg_replace('/[^[A-Za-z0-9_-\s]]+/', '', html_entity_decode($string))));
		}



		/**
		 * controller_name
		 *
		 * @param string $string
		 *
		 * @return string $controller
		 * @author Matt Brewer
		 **/

		public static function controller_name($string) {
			return str_replace(' ', '', ucwords(str_replace(array('_','-'), ' ', String::clean_filename($string))));
		}



		/**
		 * controller_link
		 *
		 * @param string $class
		 * @param string (optional) $optional
		 *
		 * @return string $link
		 * @author Matt Brewer
		 **/
		
		public static function controller_link($class, $action='') {
			$action = substr($action,0,1)==="/"?substr($action,1):$action;
			$action = preg_replace('/\/\/+/', '/', $action);
			return self::site_link(self::controller_slug($class)).'/'.$action;
		}
		
		
		/**
		 * controller_slug
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
		 * render
		 * Renders the template, calling the specific hooks
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function render() {
			
			// Send HTTP headers to the browser if requested
			foreach(Application::controller()->output->header() as $header) {
				header($header);
			}
			
			ob_start();

			NotificationCenter::execute(NotificationCenter::TEMPLATE_PRE_RENDER_NOTIFICATION);

			// If a layout is available, render using the layout. Otherwise just use the response from the controller
			if ( ($layout = Application::controller()->output->layout) !== false ) {
				require_once $layout;
			} else {
				echo Application::controller()->output();
			}
			
			// Give module developers a chance to filter the output generated from the controller
			$output = NotificationCenter::execute(NotificationCenter::TEMPLATE_POST_RENDER_NOTIFICATION, ob_get_clean());
			
			// Write the contents of this to the cache
			if ( Application::controller()->output->cache_enabled() ) {
				Application::controller()->output->cache($output);		
			}
			
			echo $output;

		}
		
		
		/**
		 * view
		 * Helper function to generate links such as "class/view/id/"
		 *
		 * @param string $class
		 * @param int $id
		 *
		 * @return string $URL
		 * @author Matt Brewer
		 **/

		public static function view($class,$id) {
			return self::controller_link($class, sprintf("view/%d/", $id));
		}


		/**
		 * edit
		 * Helper function to generate links such as "class/edit/id/"
		 *
		 * @param string $class
		 * @param int $id
		 *
		 * @return string $URL
		 * @author Matt Brewer
		 **/
		
		public static function edit($class,$id) {
			return self::controller_link($class, sprintf("edit/%d/", $id));
		}


		/**
		 * delete
		 * Helper function to generate links such as "class/delete/id/"
		 *
		 * @param string $class
		 * @param int $id
		 *
		 * @return string $URL
		 * @author Matt Brewer
		 **/
		
		public static function delete($class,$id) {
			return self::controller_link($class, sprintf("delete/%d/", $id));
		}


		/**
		 * create
		 * Helper function to generate links such as "class/create/id/"
		 *
		 * @param string $class
		 *
		 * @return string $URL
		 * @author Matt Brewer
		 **/
		
		public static function create($class) {
			return self::controller_link($class, "create/");
		}


		/**
		 * save
		 * Helper function to generate links such as "class/save/id/"
		 *
		 * @param string $class
		 * @param int $id
		 *
		 * @return string $URL
		 * @author Matt Brewer
		 **/
		
		public static function save($class, $id=0) {
			return self::controller_link($class,"save/".($id>0?"$id/":""));
		}


		/**
		 * manage
		 * Helper function to generate links such as "class/manage/id/"
		 *
		 * @param string $class
		 * @param int $id
		 *
		 * @return string $URL
		 * @author Matt Brewer
		 **/
		
		public static function manage($class) {
			return self::controller_link($class, "manage/");
		}
		
	
		/**
		 * icon
		 * Returns an <img /> tag with the specified icon file in PUBLIC_URL.images/dev-icons/
		 *
		 * @param string $icon_name
		 * @param string $alt_text
		 * @return string $html
		 * @author Matt Brewer
		 **/

		public static function icon($name, $alt="") {
			return sprintf('<img src="%simages/dev-icons/%s" alt="%s" border="0" />', PUBLIC_URL, $name, $alt);
		}



		/**
		 * button
		 * Returns <a /> containing an <img /> generated from Template::icon()
		 *
		 * @param string $href
		 * @param string $title
		 * @param string $image_name
		 * @param array $options
		 *
		 * @return string $html
		 * @author Matt Brewer
		 **/

		public static function button($href, $title, $image, $options=array()) {
			return sprintf('<a class="actions-button %s" href="%s" title="%s" rel="%s" target="%s" %s>%s</a>', $options['class'], $href, $title, $options['rel'], $options['target'], $options['user-defined'], icon($image, $options['alt']));
		}


		/**
		 * file_icon
		 * Returns <img /> for the appropriate file type
		 *
		 * @param string $file_type
		 * @param string $alt_text
		 *
		 * @return string $html
		 * @author Matt Brewer
		 **/
		
		public static function file_icon($file_type, $alt="") {
			
			if ( @is_dir($file_type) ) {
				$file_type = "folder";
			} else if ( stripos($file_type, ".") !== false ) {
				$info = pathinfo($file_type);
				$file_type = $info['extension'];			
			}
						
			$file = sprintf("images/icons/icon_%s.png", strtolower($file_type));
			if ( !@file_exists(PUBLIC_PATH.$file) ) {
				$file = "images/icons/icon_default.png";
			}
			
			return sprintf('<img src="%s" alt="%s" border="0" />', PUBLIC_URL.$file, $alt);
		}
		
		
		/**
		 * keywords
		 * Returns the keywords for a response, after filtering
		 *
		 * @uses NotificationCenter::FILTER_META_KEYWORDS
		 *
		 * @return string $keywords
		 * @author Matt Brewer
		 **/

		public static function keywords() {
			$value = Application::controller()->keywords;
			return NotificationCenter::execute(NotificationCenter::FILTER_META_KEYWORDS, $value);
		}
		
		
		/**
		 * description
		 * Returns the description for a response, after filtering
		 *
		 * @uses NotificationCenter::FILTER_META_DESCRIPTION
		 *
		 * @return string $description
		 * @author Matt Brewer
		 **/

		public static function description() {
			$value = Application::controller()->description;
			return NotificationCenter::execute(NotificationCenter::FILTER_META_DESCRIPTION, $value);
		}
		
		
		/**
		 * title
		 * Returns the page title for a response, after filtering
		 * 
		 * @uses NotificationCenter::FILTER_META_TITLE
		 *
		 * @return string $title
		 * @author Matt Brewer
		 **/
		
		public static function title() {
			$value = Settings::get('application.system.site.name').TITLE_SEPARATOR.Application::controller()->title;
			return NotificationCenter::execute(NotificationCenter::FILTER_META_TITLE, $value);
		}

			
	}
	
?>