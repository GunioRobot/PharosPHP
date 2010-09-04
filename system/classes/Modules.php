<?
	
	/**
	 * 	Modules API
	 *
	 * 	The modules API allows developers to easily extend core functionality and
	 * 	provide that functionality across applications.
	 *
	 * 	A module developer should start by subclassing the "Module" class and 
	 *	overriding the documented methods within.  Then the application code can 
	 *	explicitly request for the system to load the method at runtime, or the
	 *	module can be configured to be auto loaded during application init by 
	 * 	entering it in the "application.yml" configuration file under "modules -> user"
	 * 
	 * 	@package PharosPHP.Core.Classes
	 * 	@author Matt Brewer
	 **/
	
	final class Modules extends Object {
	
		private static $modules = array();
		private static $config = array();
		
		
		/**
		 * init()
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function init() {
			self::$config = Settings::get("application.modules");
			self::load_automatic_modules();
		}
		
		
		/**
		 * load($name)
		 * Modules::load($name) loads a module into the system
		 * 
		 * @throws Exception - if loading a module failed
		 * 
		 * @param mixed $module_name (array of module names, or just one module name)
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function load($name) {
			
			if ( is_array($name) ) {
				foreach($name as $n) {
					self::_load($n);
				}
			} else {
				self::_load($name);
			}
		
		}
		
	
	
		/**
		 * _load($name)
		 *
		 * @throws Exception - if loading a module failed
		 *
		 * @param string $module_name
		 * @return void
		 * @author Matt Brewer
		 **/
		
		protected static function _load($name) {
		
			if ( !isset(self::$modules[$name]) ) {

				$folder = MODULES_PATH.$name;
				$file = $folder."/include.php";

				if ( @file_exists($folder) && is_dir($folder) && @file_exists($file) ) {

					include $file;					
					self::$modules[$name] = $file;
					Hooks::execute(Hooks::HOOK_MODULE_LOADED, array($name));

				} else {
					throw new Exception("Error loading module ($name).  File did not exist.");
				}

			}
			
		}
		
		
		
		
		
		/**
		 * load_automatic_modules()
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		protected static function load_automatic_modules() {

			foreach(self::$config['autoload'] as $m) {
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