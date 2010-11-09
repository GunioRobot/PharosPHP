<?


	/**
	 * Cookie
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 *
	 **/
	
	final class Cookie extends Object {
		
		
		/**
		 * set
		 * Stores a key/value pair on the client machine for a specified amount of time
		 * NOTE: This method stores the value in the $_COOKIE array on the current request for convenience
		 *
		 * @param string $key
		 * @param string $value
		 * @param int $expire (in milliseconds)
		 *
		 * @return boolean (true|false) - true if setting the cookie was successful
		 * @author Matt Brewer
		 **/
		
		public static function set($key, $value, $expire) {
			$_COOKIE[$key] = $value;	// Make it available on this same page load, instead of requiring reload to fill $_COOKIE as PHP behavior dictates
			return @setcookie($key, $value, $expire, "/");//, ROOT_URL, false, false);
		}
		
		
		/**
		 * get
		 * Attempts to retrieve a value from the $_COOKIE array
		 *
		 * @param string $key
		 * @param mixed (optional) $default
		 *
		 * @return mixed - returns the value stored in the cookie
		 * @author Matt Brewer
		 **/
		
		public static function get($key, $default=false) {
			return Input::cookie($key, $default);
		}
		
		
		/**
		 * delete
		 * Removes all values from a particular key
		 *
		 * @param string $key
		 *
		 * @return boolean (true|false) - true if delete was successful
		 * @author Matt Brewer
		 **/
		
		public static function delete($key) {
			unset($_COOKIE[$key]);
			return @setcookie($key, "", time() - 3600);
		}
		
	} 

?>