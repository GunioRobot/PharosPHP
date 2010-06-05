<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Settings API
	//
	// 	The settings API allows for the application to read settings set in the 
	//	"application/configuration/application.yml" file at its basic usage.
	//
	//	The application can register new settings during runtime by providing a 
	//	keypath that does not exist, as well as the value to store.  The application
	//	can update an existing keypath by providing a new value.  This value will
	//	be valid during the length of the application lifetime, but WILL NOT be saved
	//	to disk.
	//
	//	Key-Paths:
	//		A keypath is a string for traversing dictionary contents and retreving
	//		a value.  An example would be "system.site.name" which performs two 
	//		dictionary lookups and returns the value of the last string piece
	//
	//	Usage:
	//
	//		$value = Settings::get($keypath)
	//		The return value will be an array or scalar.
	//
	//		Settings::set($keypath, $newValue)
	//
	////////////////////////////////////////////////////////////////////////////////


	Settings::load();
	class Settings {
			
		private static $config = array();
		
		
		public static function load() {
			self::$config = sfYaml::load(SERVER_DIR.APP_PATH.'includes/application.yml');
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
		
		
		
		
		public static function set($keypath, $value) {
			
			$components = explode(".", trim($path,". "));
			if ( empty($components) ) {
				throw new Exception("Invalid key path ($path)");
			} else if ( count($components) == 1 ) {
				self::$config[$components[0]] = $value;
			} else if ( count($components) == 2 ) {
				self::$config[$components[0]][$components[1]] = $value;
			} else {

				$arr =& self::$config[$components[0]][$components[1]];
				$components = array_slice($components,2);

				if ( !empty($components) ) {
					foreach($components as $c) {
						if ( isset($arr[$c]) ) {
							$arr =& $arr[$c];
						} else $arr = $value;
					} else $arr = $value
				} else $arr = $value;

			}
						
		}
		
			
	}

?>