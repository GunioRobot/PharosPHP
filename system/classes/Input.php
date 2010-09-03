<?

	/**
	 * Input
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/

	class Input {
		
		
		/**
		 * get
		 * Retrieves value from $_GET, or returns default if not set
		 * 
		 * @param string $key
		 * @param mixed $default_value
		 *		
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function get($key, $default=false) {
			return self::retrieve(__FUNCTION__, $key, $default);
		}
		
		
		/**
		 * post
		 * Retrieves value from $_POST, or returns default if not set
		 *
		 * @param string $key
		 * @param mixed $default_value
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function post($key, $default=false) {
			return self::retrieve(__FUNCTION__, $key, $default);
		}
		
		
		/**
		 * request
		 * Retrieves value from $_REQUEST, or returns default if not set
		 *
		 * @param string $key
		 * @param mixed $default_value
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function request($key, $default=false) {
			return self::retrieve(__FUNCTION__, $key, $default);
		}
		
		
		/**
		 * server
		 * Retrieves value from $_SERVER, or returns default if not set
		 *
		 * @param string $key
		 * @param mixed $default_value
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function server($key, $default=false) {
			return self::retrieve(__FUNCTION__, $key, $default);
		}
		
		
		/**
		 * session
		 * Retrieves value from $_SERVER, or returns default if not set
		 *
		 * @param string $key
		 * @param mixed $default_value
		 *		
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function session($key, $default=false) {
			return self::retrieve(__FUNCTION__, $key, $default);
		}
	
	
		/**
		 * cookie
		 * Retrieves value from $_COOKIE, or returns default if not set
		 *
		 * @param string $key
		 * @param mixed $default_value
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function cookie($key, $default=false) {
			return self::retrieve(__FUNCTION__, $key, $default);
		}
		
		
		/**
		 * retrieve
		 * Method to dynamically retrieve value from given input variable
		 *
		 * @param string $var (get|post|request|server)
		 * @param string $key
		 * @param mixed $default_value
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		protected static function retrieve($var, $key, $default=false) {
			$var = "_".strtoupper($var);
			return ( isset($$var) && $$var != "" ) ? $var : $default;
		}
		
	}

?>