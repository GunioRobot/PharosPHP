<?


	/**
	 * Cookie
	 *
	 * @package PharosPHP
	 * @author Matthew
	 **/
	
	class Cookie {
		
		public static function set($key, $value, $expire) {
			$_COOKIE[$key] = $value;	// Make it available on this same page load, instead of requiring reload to fill $_COOKIE as PHP behavior dictates
			return @setcookie($key, $value, $expire, "/", HTTP_SERVER, false, false);
		}
		
		public static function get($key, $default=false) {
			return cookie($key, $default);
		}
		
		public static function delete($key) {
			return @setcookie($key, "", time() - 3600);
		}
		
	} 

?>