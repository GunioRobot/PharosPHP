<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	MODULES API
	//
	// 	The modules API allows developers to easily extend core functionality and
	// 	provide that functionality across applications.
	//
	// 	A module developer should start by subclassing the "Module" class and 
	//	overriding the documented methods within.  Then the application code can 
	//	explicity request for the system to load the method at runtime, or the
	//	module can be configured to be autoloaded during application init by 
	// 	entering it in the "application.yml" configuration file under "modules -> user"
	//
	////////////////////////////////////////////////////////////////////////////////
	

	require_once CLASSES_DIR.'Module.base.php';
	class Modules {
	
		private static $modules = array();
		private static $config = array();
		
		public function init() {
			self::$config = Settings::get("modules");
			self::load_automatic_modules();
		}
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Modules::load($name) loads a module into the sysetm
		//
		///////////////////////////////////////////////////////////////////////////
		
		public static function load($name) {
			
			if ( !isset(self::$modules[$name]) ) {
				
				$folder = MODULES_DIR.$name;
				if ( @file_exists($folder) && is_dir($folder) && @file_exists($folder.$name.".php") ) {
					
					include $folder.$name.".php";
					$class = controllerName($name);
					var_dump($class);
					
					if ( !class_exists($class) ) {
						throw new Error("Error loading module ($name). Class ($class) did not exist.");
					} else {
						
						if ( call_user_func($class,"load") === true ) {
							self::$modules[$name] = $folder.$name.".php";
							Hooks::call_hook(Hooks::HOOK_MODULE_LOADED, array($name));
						} else {
							throw new Exception("Error loading module ($name). ".$module."::load() returned value other than true.");
						}
						
					}
					
				}

			}
		}
		
		
		
		
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Private function for loading all the automatic modules at startup
		//
		///////////////////////////////////////////////////////////////////////////
				
		private static function load_automatic_modules() {

			foreach(self::$config['system'] as $m) {
				try {
					self::load($m);
				} catch (Exception $e) {
					if ( class_exists("Console") ) {
						Console::log($e->getMessage());
					} else {
						echo $e->getMessage();
					}
				}
			}
				
		}
	
	}

?>