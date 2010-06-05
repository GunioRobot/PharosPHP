<?

	class Router {

		private static $raw;
		private static $components;
		
		public static function parse() {
			
			if ( ($input = get("args")) !== false ) {
				
				self::$raw = trim($input, " /");
				
				$components = explode("/", self::$raw);
				if ( !empty($components) && substr(strtolower($components[0]), strlen($components[0]) - strlen("-controller")) == "-controller" ) {
					$components[0] = substr($components[0], 0, -strlen("-controller"));
				}
								
				self::$components = $components;
				return self::$components;
				
			} else return "";
			
		}
		
		public static function controller() {
			$c = !empty(self::$components) ? self::$components[0]."Controller" : Settings::get('routes.root');
			return controller_name($c);
		}
		
		public static function method() {
			$m = count(self::$components) > 1 ? controller_name(self::$components[1]) : "index";
			return $m;
		}
		
		public static function params() {
			return count(self::$components) > 2 ? array_slice(self::$components,2) : array();
		}
		
		public static function raw_input() {
			return self::$raw;
		}
	
	}

?>