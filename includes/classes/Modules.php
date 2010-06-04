<?

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
				$folder = MODULES_DIR;
				if ($handle = opendir($folder)) {
					while (false !== ($file = readdir($handle)) ) {
						if ($file != "." && $file != ".." && is_dir($folder.$file) && $file === $name ) {
							if ( @file_exists($folder.$file.'/include.php') ) {

								// Make sure the version of the module is correct, then load
								if ( @file_exists($folder.$file.'/version.php') ) {

									unset($module_version);
									include $folder.$file.'/version.php';
									if ( isset($module_version) ) {

										include $folder.$file.'/include.php';
										$modules[$name] = $folder.$file.'/include.php';

										// Try to validate using the newer string version comparison instead of nasty defines
										if ( ($comp = version_compare($module_version, Settings::get("system.version"))) >= 0 ) {
											Console::log("Loaded ($name) successfully");
											Hooks::call_hook(Hooks::HOOK_MODULE_LOADED, array($name));
										} else {
											$err = "Unable to load ($name) -- Incorrect Version (".$module_version." < ".Settings::get("system.version").")";
											Console::log($err);
										}

									} else {
										$err = "Unable to load ($name) -- Missing Version Information.";
										Console::log($err);
									}

								}

							}
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