<?

	class Output {
		
		const CSS_TYPE_ALL = "all";
		const CSS_TYPE_PRINT = "print";
		const CSS_TYPE_SCREEN = "screen";
		const JAVASCRIPT_INCLUDE = "php_include_js";
		const JAVASCRIPT_EXTERNAL = "link_js";
		
		const MINUTES = 60;
		const HOURS = 3600;		// 60 * 60
		const DAYS = 86400; 	// 24 * 60 * 60
		const WEEKS = 604800; 	// 7 * 24 * 60 * 60
		
		/**
		*
		*	Caching members
		*
		*/
		static protected $cache = CACHE_DIR;
		protected $enabled = false;
		protected $cached_file;
		protected $cache_duration = 0;		// In Seconds
		
		
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
		
		
		public function __construct() {
			
			if ( Settings::get("system.cache") === true && file_exists(self::$cache) && is_dir(self::$cache) && is_writable(self::$cache) ) {
				$this->enabled = true;
				$this->cached_file = self::$cache.self::cached_name();
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
		*
		*
		*
		*/
		public function content($string='') {
			if ( $string != '' ) {
				$this->content = $string;
			} else {
				return $this->content;
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
		*	Enabled()
		*
		*	@return bool 
		*
		*/
		
		public function enabled() {
			return $this->enabled && $this->cache_duration > 0;
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
			if ( !self::cache_expired() ) {
				$f = self::$cache.self::cached_name();
				if ( file_exists($f) ) {
					$arr = @file($f);
					if ( is_array($arr) ) return implode("\n", array_slice($arr, 1));		// Return the string without the first line, which is the timestamp
					else return false;
				} else return false;
			} else return false;
		}
		
		
		
		/**
		*
		*	cache_expired
		*
		*	@return true/false
		*
		*/
		
		public static function cache_expired() {
			$f = self::$cache.self::cached_name();
			if ( file_exists($f) ) {
				$contents = @file($f);
				return ( $contents[0] < time() );
			} else return true;
		}
		
		
		
		
		/*
		*
		*	Private Caching Functions
		*
		*/
		
		private function _create_cached_content($str) {
			$future = time() + ($this->cache_duration);
			return sprintf("%s\n%s", $future, $str);
		}
		
		private function _write_to_cache($str) {
			if ( self::cache_expired() ) {
				return @file_put_contents($this->cached_file, $this->_create_cached_content($str), LOCK_EX);
			} else return true;
		}		
				
	}

?>