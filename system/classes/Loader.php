<?

	/**
	 * Loader
	 *
	 * This class is initialized and used as part of the Controller class, ie 
	 * 
	 * 		public function my_function() {
	 *			try {
	 *				$this->load->klass("MyClass");
	 *			} catch (ClassNotFoundException $e) {}
	 * 		} 
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	final class Loader extends Object {
		
		protected static $shared = null;
		
		/**
		 * sharedLoader
		 * Returns the static instance of the Loader class
		 *
		 * @return Loader $shared
		 * @author Matt Brewer
		 **/
		
		public function sharedLoader() {
			if ( is_null(self::$shared) ) {
				self::$shared = new Loader();
			} return self::$shared;
		}
		
		/**
		 * model
		 * Will load a model from the models directory
		 *
		 * @throws ClassNotFoundException
		 *
		 * @param string $model_name
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function model($model) {
			$info = pathinfo($model);
			$file = $info['filename'];
			$path = $info['dirname'] == "." ? MODELS_PATH . $model . ".php" : $file . ".php";
			self::_load_class($path);
		}
		
				
		/**
		 * module
		 * Attempts to load the requested module. Module placed in application/ has priority over module placed in system/
		 *
		 * @param mixed $module_name (array of module names, or just one module name)
		 *
		 * @throws ModuleNotFoundException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function module($name) {
			Modules::load($name);
		}
		
		
		/**
		 * klass
		 * Attempts to load a class, first from the application directory, then looking inside the system directory
		 *
		 * @param string $class_name
		 *
		 * @throws ClassNotFoundException
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function klass($name) {
			self::load_class($name);
		}
		
		
		/**
		 * load_class
		 * Attempts to load a class, first from the application directory, then looking inside the system directory
		 *
		 * @param (array|string) $class_name
		 *
		 * @throws ClassNotFoundException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function load_class($name) {
			
			if ( is_array($name) ) {
				foreach($name as $class) {
					self::_load_class($class);
				}
			} else self::_load_class($name);
			
		}
		
		
		
		/**
		 * _load_class
		 * Class loader
		 *
		 * @param string $class_name
		 *
		 * @throws ClassNotFoundException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		protected static function _load_class($name) {
		
			$info = pathinfo($name);
			$klass = $info['filename'];
			
			if ( $info['dirname'] == "." ) {
			
				if ( !class_exists($klass) ) {
				
					if ( !file_exists(CLASSES_PATH.$klass.".php") ) {
					
						if ( !file_exists(APPLICATION_CLASSES_PATH.$klass.".php") ) {
							throw new ClassNotFoundException(sprintf("Unable to locate file containing class (%s)", $klass));
						} else {
						
							@include_once APPLICATION_CLASSES_PATH.$klass.".php";
							if ( !class_exists($klass) ) {
								throw new ClassNotFoundException(sprintf("Could not find class (%s): include '%s.php'", $klass, APPLICATION_CLASSES_PATH.$klass));
							}
						
						}
					
					} else {
				
						@include_once CLASSES_PATH.$klass.'.php';
						if ( !class_exists($klass) ) {
							throw new ClassNotFoundException(sprintf("Could not find class (%s): include '%s.php'", $klass, CLASSES_PATH.$klass));
						}
					
					}
				
				}
				
			} else {
				
				if ( !class_exists($klass) ) {
					$name .= stripos($name, ".php") === false ? ".php" : "";
					@include_once $name;
					if ( !class_exists($klass) ) {
						throw new ClassNotFoundException(sprintf("Attempted to load class (%s) from file: %s", $klass, $name));
					}
				}

			}
			
		}
		
		
		/**
		 * config
		 * Loads a specified config file from inside application/configuration
		 *
		 * @param string $filename
		 *
		 * @throws Exception
		 * @throws InvalidFileSystemPathException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function config($filename) {
			Settings::load($filename);
		}
		
		
	}  

?>