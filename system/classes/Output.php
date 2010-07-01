<?

	/**
	 * Output
	 * 
	 * Handles all output related functionality for the Controller class and subclasses
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	require_once CLASSES_DIR.'Cache.php';
	class Output {
		
		const CSS_TYPE_ALL = "all";
		const CSS_TYPE_PRINT = "print";
		const CSS_TYPE_SCREEN = "screen";
		const JAVASCRIPT_INCLUDE = "php_include_js";
		const JAVASCRIPT_EXTERNAL = "link_js";
				
		/**
		*
		*	Layout Information
		*
		*/
		public $layout=null;
		
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
		*	Output from the controller
		*
		*/
		protected $content = "";
		protected $meta = array();
		protected $members = array();
		protected $controller;
		protected $headers = array();
		protected $flash = array();
		
		
		/**
		 * Constructor
		 *
		 * @return Output
		 * @author Matt Brewer
		 **/
		
		public function __construct() {
			
			if ( Cache::enabled() ) {
				$this->enabled = true;
				$this->cached_file = self::cached_name();
			}
			
			global $controller;
			$this->controller =& $controller;
		
			if ( ($items = session("pharos_flash")) !== false && is_array($items) ) {
				foreach($items as $obj) {
					$this->flash[] = (object)array("save" => false, "value" => $obj->value);
				}
			}
			
		}
		
	
	
		/**
		*
		*	css
		*
		*	@param path (optional)  - path to CSS file, relative to /public
		*	@param type (optional) - type of CSS media
		*	@return array - array of css files to include
		*
		*/
		
		public function css($path='', $type=self::CSS_TYPE_ALL) {

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

				$this->css[] = array('path' => $path, 'type' => $type);

			} else {
				return $this->css;
			}

		}



		/**
		*
		*	javascript
		*
		*	@param $path (optional) - path to JS file, if empty return array of all js files
		*	@param $data (optional) - data array available to interpreted js files (.php)
		*	@return array - array of js files to include
		*
		*/

		public function javascript($path='',$data=array()) {

			if ( !is_array($data) ) $data = array();

			if ( $path != '' ) {

				// Don't keep adding the same thing
				if ( ($index = array_search($path,$this->javascript)) !== false ) {

					// Replace data array if called a second time
					$this->javascript[$index]['data'] = $data;
					return true;

				} else {
					$this->javascript[] = array('path' => $path, 'type' => (strrpos($path,'.php')===false?self::JAVASCRIPT_EXTERNAL:self::JAVASCRIPT_INCLUDE), 'data' => $data);
				}

			} else {

				return $this->javascript;

			}

		}



		/**
		*
		*	meta
		*
		*	@param $meta (optional) - array with keys for "name", "content" & "http-equiv"
		*	@return mixed - void if $meta was array, array of meta options if null
		*
		*/
		
		public function meta($meta=null) {
			if ( !is_null($meta) ) {
				$this->meta[$meta['name']] = $meta;
			} else return $this->meta;
		}
		
		
		
		/**
		*
		*	@param $output string
		*
		*/
		public function finalize($output) {
			if ( $this->content() === "" ) $this->content($output);
		}
		
		
		/**
		*
		*	content($string)
		*
		*
		*/
		public function content($string='') {
			if ( $string != '' || $string === null ) {
				$this->content = $string;
			} else {
				return $this->content;
			}
		}
		
		
		
		/**
		 * header($str)
		 *
		 * @param string (optional) $header
		 * @return mixed
		 * @author Matt Brewer
		 **/
		
		public function header($str=null) {
			if ( $str ) $this->headers[] = $str;
			else return $this->headers;
		}
		
		
		/**
		 * flash($value)
		 *
		 * @param string $value
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function flash($value) {
			$this->flash[] = (object)array("save" => true, "value" => $value);
		}
		
		
		/**
		 * flash_contents()
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
		*
		*	set()
		*
		*	@param string Key
		*	@param mixed Value
		*	@return void
		*
		*/
		public function set($key, $value) {
			$this->members[$key] = $value;
		}
		
		
		
		/**
		*
		*	view()
		* 
		*	If $str is a PHP file, the file is interpreted with all the data members set in the controller exposed locally
		* 	If $str is any other type of file, the contents of the file are added to internal $content instance var
		* 	If $str is not a file, $str is treated as a static string and added to the protected $content instance var
		*
		*	@throws Exception - if file is not PHP and contents could not be obtained
		*
		*	@param string $str
		*	@param string $directory (optional)
		*	@return void
		*
		*/
		public function view($str, $directory=VIEWS_DIR) {
			
			$file = $directory.$str;
			if ( $str != "" && file_exists($file) ) {
				
				$info = pathinfo($file);
				if ( strtolower($info['extension']) == "php" ) {
					extract($this->members);	// Import the data members into a clean namespace
					ob_start();
					require $file;		// Include the view (which only has access to the local clean namespace )
					$this->content .= ob_get_clean();
				} else {
					if ( ($contents = @file_get_contents($file)) !== false ) {
						$this->content .= $contents;
					} else throw new Exception(sprintf("Provided %s of type %s and was unable to get contents.", $file, $info['extension']));
				}
							
			} else {
				
				$this->content .= $str;
				
			}
			
		}
		
		
		
		
		/**
		*
		* 	cache()
		*
		*	@param time (optional) - null to get string of cached contents, time in minutes to store output to cache
		*	@return mixed - true/false if was written to cache, string for cached contents
		*
		*/
		
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
		*
		*	cache_enabled()
		*
		*	@return bool 
		*
		*/
		
		public function cache_enabled() {
			return Cache::enabled() && $this->cache_duration > 0;
		}
		
		
		/**
		*
		*	cached_name
		*
		*	@return string filename
		*
		*/
		public static function cached_name() {
			return md5(Router::url()).'.cache';
		}
		
		
		/**
		*
		*	cached_content
		*
		*	@return string content
		*
		*/
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
		*
		*	delete
		*
		*	@return void
		*
		*/
		public function delete() {
			Cache::delete($this->cached_file);
		}
		
		
		
		
		/*
		*
		*	Private Caching Functions
		*
		*/
		
		private function _write_to_cache($str) {
			try {
				if ( Cache::expired($this->cached_file) ) {
					Cache::write($str, $this->cached_file, $this->cache_duration);
				}
			} catch(CacheNotEnabledException $e) {}	
		}	
		
		
		
		/**
		 * __destruct()
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public function __destruct() {
			
			function _save($var) {
				return $var->save;
			}
			
			$to_save = array_filter($this->flash, "_save");
			$_SESSION['pharos_flash'] = $to_save;
			
		}	
				
	}

?>