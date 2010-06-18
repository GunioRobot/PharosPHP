<?


	/**
	 * Cookie
	 *
	 * @package PharosPHP
	 * @author Matthew
	 **/
	
	class Cookie {
		
		public static function set($key, $value, $expire) {
			return @setcookie($key, $value, $expire, "/", HTTP_SERVER, false, false);
		}
		
		public static function get($key, $default=false) {
			return cookie($key, $default);
		}
		
	} 

?>