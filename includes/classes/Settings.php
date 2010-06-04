<?

	Settings::init();
	class Settings {
			
		private static $config = array();
		
		public static function init() {
			self::$config = sfYaml::load(SERVER_DIR.APP_PATH.'includes/configure.yml');
		}
		
		public static function get($path="") {
			
			$components = explode(".", trim($path,". "));
			if ( empty($components) ) {
				throw new Exception("Invalid key path ($path)");
			} else if ( count($components) == 1 ) {
				return self::$config[$components[0]];
			} else if ( count($components) == 2 ) {
				return self::$config[$components[0]][$components[1]];
			} else {
			
				$arr = self::$config[$components[0]][$components[1]];
				$components = array_slice($components,2);
				
				if ( !empty($components) ) {
					foreach($components as $c) {
						if ( isset($arr[$c]) ) {
							$arr = $arr[$c];
						} else return $arr;
					} return $arr;
				} else return $arr;
				
			}
			
		}		
		
		public static function set($type, $name, $value) {
			if ( isset(self::$config[$type]) ) {
				self::$config[$type][$name] = $value;
			} else throw new Exception("Unknown setting class: ".$type);
		}
		
			
	}

?>