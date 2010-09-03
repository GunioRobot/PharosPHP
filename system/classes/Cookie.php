<?


	/**
	 * Cookie
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 *
	 **/
	
	class Cookie {
		
		
		/**
		 * set($key, $value, $expire)
		 *
		 * @param string $key
		 * @param string $value
		 * @param int $expire (in milliseconds)
		 *
		 * @return boolean (true|false) - true if setting the cookie was successful
		 * @author Matthew
		 **/
		public static function set($key, $value, $expire) {
			$_COOKIE[$key] = $value;	// Make it available on this same page load, instead of requiring reload to fill $_COOKIE as PHP behavior dictates
			return @setcookie($key, $value, $expire, "/");//, ROOT_URL, false, false);
		}
		
		/**
		 * get($key)
		 *
		 * @param string $key
		 * @param mixed (optional) $default
		 * @return mixed - returns the value stored in the cookie
		 * @author Matthew
		 **/
		public static function get($key, $default=false) {
			return Input::cookie($key, $default);
		}
		
		/**
		 * delete($key)
		 *
		 * @param string $key
		 * @return boolean (true|false) - true if delete was successful
		 * @author Matthew
		 **/
		public static function delete($key) {
			unset($_COOKIE[$key]);
			return @setcookie($key, "", time() - 3600);
		}
		
	} 

?>