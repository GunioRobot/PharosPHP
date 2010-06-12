<?

	class Output {
		
		const CSS_TYPE_ALL = "all";
		const CSS_TYPE_PRINT = "print";
		const CSS_TYPE_SCREEN = "screen";
		const JAVASCRIPT_INCLUDE = "php_include_js";
		const JAVASCRIPT_EXTERNAL = "link_js";
		
		/**
		*
		*	Caching members
		*
		*/
		static protected $cache = CACHE_DIR;
		protected $enabled = false;
		protected $cached_file;
		protected $cache_duration = 60;		// In Minutes
		
		
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
		
		public function cache($time=null) {
						
			if ( is_null($time) ) {
			
				if ( $this->enabled ) {
					return self::cached_content();
				} else return false;
				
			} else {
				
				$this->cache_duration = intval($time);
				if ( $this->enabled ) {
					$this->_write_to_cache();
				} return false;
				
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
			return $this->enabled;
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
			$str = @file(self::$cache.self::cached_name());
			return implode("\n", array_slice($str, 1));		// Return the string without the first line, which is the timestamp
		}
		
		
		
		
		
		/*
		*
		*	Private Caching Functions
		*
		*/
		
		private function _create_cached_content() {
			return sprintf("%s\n%s", time(), $this->content);
		}
		
		private function _write_to_cache() {
			if ( $this->_cache_needs_update() ) {
				return @file_put_contents($this->cached_file, $this->_cached_content());
			} else return true;
		}
		
		private function _cache_needs_update() {
			if ( file_exists($this->cached_file) ) {
				$contents = @file($this->cached_file);
				return ( $contents[0] + ($this->cache_duration*60) < time() );
			} else return true;
		}
				
	}

?>