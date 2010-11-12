<?

	/**
	 * HTTPResponse
	 * 
	 * Handles all output related functionality for the Controller class and subclasses
	 * including caching, javascript, stylesheets, flash data, HTTP headers, meta tags, and more
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	class HTTPResponse extends Object {
		
		const CSS_TYPE_ALL = "all";
		const CSS_TYPE_PRINT = "print";
		const CSS_TYPE_SCREEN = "screen";
		const JAVASCRIPT_INCLUDE = "php_include_js";
		const JAVASCRIPT_EXTERNAL = "link_js";
				
		/**
		 * @var string
		 * Determines the layout to be used to render the response
		 * The controller can provide a custom layout by setting the layout property on the controller's output object property (without the file extension)
		 * Alternatively, this method will search for a corresponding file in the application/layouts directory to match the request URL, ie:
		 * /session/login/ will search for a file first in application/layouts/session-controller.php and if not found, then application/layouts/session-controller-login.php
		 * The failsafe default layout is application.php
		 *
		 */
		
		protected $layout=null;
		
		/**
		*
		*	Caching members
		*
		*/
		protected $enabled = false;
		protected $cached_file;
		protected $cache_duration = 0;		// In Minutes
		
		
		/**
		*
		*	HTTP head members
		*
		*/
		protected $css = array();
		protected $javascript = array();
		
		
		/** 
		*
		*	HTTPResponse from the controller
		*
		*/
		protected $content = "";
		protected $meta = array();
		protected $members = array();
		protected $headers = array();
		protected $flash = array();
		
		
		/**
		 * __constuct
		 *
		 * @return HTTPResponse
		 * @author Matt Brewer
		 **/
		
		public function __construct() {
			
			if ( Cache::enabled() ) {
				$this->enabled = true;
				$this->cached_file = self::cached_name();
			}
		
			if ( ($items = Input::session("pharos_flash")) !== false && is_array($items) ) {
				foreach($items as $obj) {
					$this->flash[] = (object)array("save" => false, "value" => $obj->value);
				}
			}
			
		}
		
	
		/**
		 * css
		 * Adds a stylesheet to be rendered with the output object
		 * 
		 * @param string $path
		 * @param string $media_type
		 *
		 * @return array $css
		 * @author Matt Brewer
		 **/

		public function css($path='', $type=self::CSS_TYPE_ALL, $dir=null) {

			if ( $path != '' ) {

				// Don't keep adding the same thing
				if ( in_array($path,$this->css) ) {
					return false;
				}

				switch($type) {
					case self::CSS_TYPE_ALL:
					case self::CSS_TYPE_PRINT:
					case self::CSS_TYPE_SCREEN:
						break;
					default:
						$type = self::CSS_TYPE_ALL;
						break;
				}
				
				if ( stripos($path, "http") === false ) {
					$path = (is_null($dir) ? PUBLIC_URL.'css'.DIRECTORY_SEPARATOR : ROOT_URL.$dir).$path;
				}

				$this->css[] = compact("path","type");

			} else {
				return $this->css;
			}

		}


		/**
		 * javascript
		 * Adds a javascript file to be used when rendering this output object
		 *
		 * @param string $path
		 * @param array $data - provided to .php files 
		 *
		 * @return array $js
		 * @author Matt Brewer
		 **/

		public function javascript($path='', $data=array(), $dir=null) {

			if ( !is_array($data) ) $data = array();

			if ( $path != '' ) {

				// Don't keep adding the same thing
				if ( ($index = array_search($path, $this->javascript)) !== false ) {

					// Replace data array if called a second time
					$this->javascript[$index]['data'] = $data;
					return true;

				} else {
					
					$type = strrpos($path,'.php') === false ? self::JAVASCRIPT_EXTERNAL : self::JAVASCRIPT_INCLUDE;
					if ( $type == self::JAVASCRIPT_EXTERNAL && stripos($path, "http") === false ) {
						$path = ROOT_URL . (is_null($dir) ? APP_DIR . DS . PUBLIC_DIR . DS . 'js' . DS : $dir) . $path;
					} 
					
					$this->javascript[] = compact("path", "type", "data");
				}

			} else {

				return $this->javascript;

			}

		}

		
		/**
		 * meta
		 * Stores an array with keys for "name" & "content" to generate custom HTML meta tags
		 * 
		 * @param array $meta
		 *
		 * @return array $meta
		 * @author Matt Brewer
		 **/

		public function meta($meta=null) {
			if ( !is_null($meta) ) {
				return $this->meta[$meta['name']] = $meta;
			} else return $this->meta;
		}
		
		
		/**
		 * finalize
		 * Conditionally sets the argument to the output buffer, if the output buffer was previously empty
		 *
		 * @param string $output
		 *
		 * @return string $output
		 * @author Matt Brewer
		 **/

		public function finalize($output) {
			if ( $this->content() === "" ) $this->content($output);
			return $this->content();
		}
		
		
		/**
		 * content
		 * Sets the argument as the objects output buffer, or if null, returns the output buffer
		 *
		 * @param (null|string) $string
		 *
		 * @return string $content
		 * @author Matt Brewer
		 **/

		public function content($string='') {
			if ( $string != '' || $string === null ) {
				return $this->content = $string;
			} else {
				return $this->content;
			}
		}
		
		
		
		/**
		 * header
		 * Takes a string to use with the PHP header() function. Send headers to the browser this way
		 * and if the content is cached, the same headers will be sent when viewing the cached content
		 *
		 * @param string $header
		 *
		 * @return (array|void) $headers
		 * @author Matt Brewer
		 **/
		
		public function header($str=null) {
			if ( $str ) $this->headers[] = $str;
			else return $this->headers;
		}
		
		
		/**
		 * flash
		 * Stores a string in the HTTPResponse objects flash storage, meaning it will be available on the next
		 * page load, and then will automatically be removed. Very useful for providing alerts to users.
		 *
		 * @param string $value 
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function flash($value) {
			$this->flash[] = (object)array("save" => true, "value" => $value);
		}
		
		
		/**
		 * flash_contents
		 * Retrieves the contents of flash storage, valid for the current request.
		 * Objects added to the flash storage during the current request will not be returned.
		 *
		 * @return array $contents
		 * @author Matt Brewer
		 **/
		
		public function flash_contents() {
			
			$ret = array();
			foreach($this->flash as $f) {
				if ( !$f->save ) $ret[] = $f->value;
			}
			
			return $ret;
			
		}
		
		
		
		/**
		 * set
		 * Assigns a value to a key, making the variable accessible inside the view context.
		 * Assigning a value to a key that already exists overwrites the previous value.
		 *
		 * @param string $key
		 * @param mixed $value
		 * 
		 * @return void
		 *
		**/
		
		public function set($key, $value) {
			$this->members[$key] = $value;
		}
		
			
		/**
		 * view
		 * If $str is a PHP file, the file is interpreted with all the data members set in the controller exposed locally
		 * If $str is any other type of file, the contents of the file are added to internal $content instance var
		 * If $str is not a file, $str is treated as a static string and added to the protected $content instance var
		 * 
		 * @param string $str
		 * @param string $directory
		 * 
		 * @throws InvalidFileSystemPathException if file is not accessible
		 *
		 * @return string $view_contents
		 * @author Matt Brewer
		 **/
		
		public function view($str, $directory=VIEWS_PATH) {
			
			$file = $directory . $str;
			if ( $str != "" && file_exists($file) ) {
				
				$info = pathinfo($file);
				if ( strtolower($info['extension']) == "php" ) {
					extract($this->members);	// Import the data members into a clean namespace
					ob_start();
					require $file;		// Include the view (which only has access to the local clean namespace )
					$_content = ob_get_clean();
					$this->content .= $_content;
					return $_content;
				} else {
					if ( ($contents = @file_get_contents($file)) !== false ) {
						$this->content .= $contents;
						return $contents;
					} else throw new InvalidFileSystemPathException(sprintf("Provided %s of type %s and was unable to get contents.", $file, $info['extension']));
				}
							
			} else {
				
				$this->content .= $str;
				return $str;
				
			}
			
		}
		
		
		/**
		 * cache
		 * Will write the string to the corresponding cache file, or set the cache's duration if an integer is provided
		 * 
		 * @param (string|int) $contents|$duration
		 *
		 * @return boolean $was_written
		 * @author Matt Brewer
		 **/
		
		public function cache($mixed) {
									
			if ( is_string($mixed) ) {
			
				if ( $this->enabled ) {
					$this->_write_to_cache($mixed);
					return self::cached_content();
				} else return false;
				
			} else {
				
				$this->cache_duration = intval($mixed);
				
			}
			
		}
	
		
		/**
		 * cache_enabled
		 * Determines if caching is enabled for this HTTPResponse object
		 *
		 * @return boolean $caching_enabled
		 * @author Matt Brewer
		 **/

		public function cache_enabled() {
			return Cache::enabled() && $this->cache_duration > 0;
		}
		
		
		/**
		 * cached_name
		 * The corresponding cached file
		 * 
		 * @return string $filename
		 * @author Matt Brewer
		 **/

		public static function cached_name() {
			return md5(Router::url()).'.cache';
		}
		
		
		/**
		 * cached_content
		 * Returns the contents of the cache if available, false if not
		 *
		 * @return (string|false) $contents
		 * @author Matt Brewer
		 **/

		public static function cached_content() {
				
			try {
				return Cache::read(self::cached_name());
			} catch(CacheNotEnabledException $e) {
				return false;
			} catch(CachedFileExpiredException $e2) {
				return false;
			}
				
		}
		
		
		/**
		 * delete
		 * Deletes the corresponding cache file
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function delete() {
			Cache::delete($this->cached_file);
		}
		

		/**
		 * _write_to_cache
		 * Writes the contents to cache
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		private function _write_to_cache($str) {
			try {
				if ( Cache::expired($this->cached_file) ) {
					Cache::write($str, $this->cached_file, $this->cache_duration, $this->headers);
				}
			} catch(CacheNotEnabledException $e) {}	
		}	
		
		
		
		/**
		 * __destruct()
		 * Called when the object is deallocated to save the flash contents to $_SESSION
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function __destruct() {
			
			$to_save = array();
			foreach($this->flash as $f) {
				$to_save[] = $f->save;
			}
		
			$_SESSION['pharos_flash'] = $to_save;
			
		}	
		
		
		/**
		 * __get
		 * Magic method for providing public access to protected instance vars
		 *
		 * @param string $key
		 *
		 * @return mixed $value
		 * @author Matt Brewer
		 **/
		
		public function __get($key) {
			switch ($key) {
				case "layout":
					
					if ( !is_null($this->layout) && @file_exists(LAYOUTS_PATH . $this->layout . ".php") ) {
						return LAYOUTS_PATH . $this->layout . ".php";
					} else {
						
						$layout = strtolower(implode('-', split_camel_case(Router::controller())));
						$file = strtolower(implode('-', split_camel_case($layout . Router::method() . ".php")));

						if ( @file_exists(LAYOUTS_PATH . $file) ) {
							return LAYOUTS_PATH . $file;
						} else if ( @file_exists(LAYOUTS_PATH . $layout . ".php") ) {
							return LAYOUTS_PATH . $layout . ".php";
						} else if ( @file_exists(LAYOUTS_PATH . 'application.php') ) {
							return LAYOUTS_PATH . 'application.php';
						} else return false;
						
					}
					
					break;
					
				default:
					return $this->{$key};
			}
		}
		
		
		/**
		 * __set
		 * Magic method for providing public access to protected instance vars
		 * 
		 * @param string $key
		 * @param mixed $value
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function __set($key, $value) {
			switch ($key) {
				case "layout":
					$this->{$key} = $value;
					break;
				
				default:
					throw new ReadOnlyPropertyException($key);
			}
		}
				
	}

?>