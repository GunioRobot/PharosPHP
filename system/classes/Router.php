<?

	///////////////////////////////////////////////////////////////////////////
	//
	//	Router API
	//
	//	The Router API parses the browsers URL to determine which controller
	//	create, which method to call on the controller, and any arguments to 
	// 	be passed to that controller.
	//
	//	By default, a naming convention is used to determine controller & 
	//	method.  To override the default convention, add custom routing actions
	//	to the "application/configuration/application.yml" file under "routes".
	//
	//	When the site is loaded with no obvious URL structure (index.php), 
	//	the Router API uses the "routes.root" as the controller with the default
	//	method of "index()";
	//
	///////////////////////////////////////////////////////////////////////////
	
	Router::parse();
	class Router {

		private static $raw = "";
		private static $components = array();
		
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
			$c = !empty(self::$components) ? self::$components[0]."Controller" : Settings::get('routes.root.controller');
			return controller_name($c);
		}
		
		public static function method() {
			
			$m = count(self::$components) > 1 ? controller_name(self::$components[1]) : "index";
			if ( Router::controller() == Settings::get('routes.root.controller') ) {
				try {
				
					$method = Settings::get('routes.root.action');
					return $method;
				
				} catch (Exception $e) {
					return $m;
				}
			}
			
			return $m;
		}
		
		public static function params() {
			return count(self::$components) > 2 ? array_slice(self::$components,2) : array();
		}
		
		public static function requires_login() {
			
			if ( Router::controller() === "SessionController" && Router::method() === "Login" ) return false;
			else return true;
			
		}
		
		public static function raw_input() {
			return self::$raw;
		}
		
		public static function layout() {
			
			$layout = Router::_layout_file(Router::controller());
			$file = Router::_layout_file($layout.Router::method().".php");

			if ( @file_exists(LAYOUTS_DIR.$file) ) {
				return LAYOUTS_DIR.$file;
			} else if ( @file_exists(LAYOUTS_DIR.$layout.".php") ) {
				return LAYOUTS_DIR.$layout.".php";
			} else if ( @file_exists(LAYOUTS_DIR.'application.php') ) {
				return LAYOUTS_DIR.'application.php';
			} else return false;
			
		}
		
		
		private static function _layout_file($class) {
			return strtolower(implode('-',split_camel_case($class)));
		}
	
	}

?>