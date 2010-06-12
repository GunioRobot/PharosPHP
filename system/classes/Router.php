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
		private static $routes = array();
		private static $keys = array(":controller", ":action", ":id");
		
		public static function parse() {
			
			// Grab the defined routes
			self::$routes = Settings::get("routes.connections");
			foreach(self::$routes as &$route) {
				
				// Escape the pattern & apply the 3 default filters of (:controller, :action, & :id)
				$route['pattern'] = str_replace("#", "\#", $route['pattern']);		// Escape the reserved '#' char
				$route['parsed_pattern'] = str_replace(array(':controller', ':action'), '([[:alnum:]]+)', $route['pattern']);	// 
				$route['parsed_pattern'] = str_replace(':id', '([[:digit:]]+)', $route['parsed_pattern']);
				
				// Apply the user defined filters
				foreach($route['params'] as $key => $pattern) {
					$route['parsed_pattern'] = str_replace($key, $pattern, $route['parsed_pattern']);
				}
								
			}
						
			
			// Grab the input string, parsing the pieces into the $components array (which is used to decide the :controller, :action & :arguments)
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
									
			// Attempt to find a match based upon the custom routing defined
			if ( ($route = self::_matching_route()) === false ) {
			
				// Didn't have custom routing option setup, so use basic routing (the first portion of URL) or the root controller defined			
				$c = !empty(self::$components) ? self::$components[0]."Controller" : Settings::get('routes.root.controller');
				return Template::controller_name($c);
				
			} else {
				if ( $route['param_values'][':controller'] != "" ) return Template::controller_name($route['param_values'][':controller'])."Controller";
				else return $route['controller'];
			}
			
		}
		
		
		
		
		public static function method() {
			
			// Attempt to find a match based upon the custom routing defined
			if ( ($route = self::_matching_route()) === false ) {
								
				$m = count(self::$components) > 1 ? Template::controller_name(self::$components[1]) : "index";
				if ( Router::controller() == Settings::get('routes.root.controller') ) {
					try {
				
						$method = Settings::get('routes.root.action');
						return $method;
				
					} catch (Exception $e) {
						return $m;
					}
				}
			
				return $m;
				
			} else {
				if ( $route['param_values'][':action'] != "" ) return Template::controller_name($route['param_values'][':action']);
				else return Template::controller_name($route['action']);
			}
			
		}
		
		
		
		
		public static function params() {
			
			if ( ($route = self::_matching_route()) !== false ) {
				return array_diff_key($route['param_values'], array(":controller" => true, ":action" => true));				
			} else return count(self::$components) > 2 ? array_slice(self::$components,2) : array();
			
		}
		
		
		
		
		public static function requires_login() {
			
			if ( Router::controller() === "SessionController" && Router::method() === "Login" ) return false;
			else return true;
			
		}
		
		
		
		public static function url() {
			return self::$raw;
		}
		
		
		
		private static function _matching_route() {
			
			static $route = null;
			if ( $route != null ) return $route;
									
			foreach(self::$routes as $r) {
							
				// Check to see if the URL matched - if it does, $matches will contain the captured sequences
				$matches = array();
				if ( preg_match('#'.$r['parsed_pattern'].'#', self::$raw, $matches) ) {
					
					$matches = array_slice($matches,1);		// Get rid of first element
					
					// Now find the sequence of the captures
					$search_keys = array_merge(self::$keys, array_keys($r['params']));
					$keys = array();
					foreach($search_keys as $key) {
						$pos = strpos($r['pattern'], $key);
						if ( $pos !== false ) {
							$keys[$pos] = $key;
						}
					}
					
					ksort($keys);
										
					$route = $r;
					$route['param_values'] = array_combine($keys, $matches);
						
					return $route;
					
				}
				
			}
			
			$route = false;
			return $route;
			
		}
		
		
		public static function using_named_params() {
			
			if ( ($route = self::_matching_route()) !== false ) {
			
				$keys = array_keys($route['param_values']);
				if ( !empty($keys) && substr($keys[0],0,1) === ":" ) {
					return true;
				} else return false;
				
			} else return false;
			
		}
			
	}

?>