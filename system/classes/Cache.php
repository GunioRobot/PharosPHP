<?

	/**
	 * Cache
	 *
	 * Provides generic interface for using the built in caching system
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	class Cache {
		
		/**
		*
		*	Class Constants for Time Manipulation
		*
		*/
		
		const MINUTES = 1;
		const HOURS = 60;		// 60
		const DAYS = 1440; 		// 24 * 60
		const WEEKS = 10080; 	// 7 * 24 * 60
		
		protected static $enabled = false;
		self::$initialized = true;
		
		
		
		/**
		*
		*	Prevent users from initiating a copy of this object, to be used solely as static
		*
		*/
		private function __construct() {}
		private function __destruct() {}
		public function init() {
			
			// Don't allow more than once
			if ( self::$initialized ) {
				return;
			} 

			self::$initialized = true;
			
			try {
				self::set_enabled(Settings::get("application.system.cache"));
			} catch(CacheNotWritableException $e) {
				if ( class_exists("Console") ) Console::log($e->getMessage());
			}
		}
		
		
		/**
		 * enabled()
		 *
		 * @return boolean $enabled
		 * @author Matt Brewer
		 **/

		public static function enabled() {
			return self::$enabled;
		}
		
		
		/**
		 * set_enabled($bool)
		 *
		 * @throws CacheNotWritableException 
		 *
		 * @param boolean $enabled
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function set_enabled($bool) {
			if ( !(file_exists(CACHE_PATH) && is_dir(CACHE_PATH) && is_writable(CACHE_PATH)) ) throw new CacheNotWritableException();
			self::$enabled = (bool)$bool;
		}
		
		
		/**
		 * write($contents, $file, $duration)
		 *
		 * @throws CacheNotEnabledException - if caching has not been enabled
		 *
		 * @param string $contents
		 * @param string $file_path, relative to the CACHE_PATH
		 * @param int duration in seconds
		 * @return boolean $success
		 * @author Matt Brewer
		 **/

		public static function write($contents, $file, $duration) {
			
			if ( !self::$enabled ) throw new CacheNotEnabledException();
			
			$future = time() + ($duration * 60);	// Convert to seconds
			return @file_put_contents(CACHE_PATH.$file, sprintf("%s\n%s", $future, $contents), LOCK_EX);
			
		}
		
		
		/**
		 * read($file)
		 * 
		 * @throws CacheNotEnabledException - if caching has not been enabled
		 * @throws CachedFileExpiredException - if cached file has expired
		 *
		 * @param string $filename
		 * @return string $contents
		 * @author Matt Brewer
		 **/

		public static function read($file) {
			
			if ( !self::$enabled ) throw new CacheNotEnabledException();
			if ( self::expired($file) ) throw new CachedFileExpiredException();
			
			$contents = @file(CACHE_PATH.$file);
			return is_array($contents) ? implode("\n", array_slice($contents, 1)) : "";
			
		}
		
		
		/**
		 * expired($file)
		 * 
		 * @throws CacheNotEnabledException - if caching has not been enabled
		 *
		 * @param string $filename
		 * @return boolean $expired
		 * @author Matt Brewer
		 **/

		public static function expired($file) {
			
			if ( !self::$enabled ) throw new CacheNotEnabledException();

			$f = CACHE_PATH.$file;
			if ( file_exists($f) ) {
				$contents = @file($f);
				return ( $contents[0] < time() );
			} else return true;
			
		}
		
		
		
		
		/**
		 * delete($file)
		 * 
		 * @param string $filename
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function delete($file) {
			if ( $file != "" && @file_exists(CACHE_PATH.$file) ) {
				@unlink(CACHE_PATH.$file);
			} 
		}	
		
		
		/**
		 * clear_cache()
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function clear_cache() {
			foreach(glob(CACHE_PATH.'*') as $filename) {
				@unlink($filename);
			}
		}			
		
	} 

?>