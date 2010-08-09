<?

	/**
	 * Loader
	 *
	 * This class is initialized and used as part of the Controller class, ie 
	 * 
	 * 		public function my_function() {
	 *			try {
	 *				$this->load->class("MyClass");
	 *			} catch (ClassNotFoundException $e) {}
	 * 		} 
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	class Loader {
		
				
		/**
		 * module
		 *
		 * @param mixed $module_name (array of module names, or just one module name)
		 *
		 * @throws Exception - if module failed to load
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function module($name) {
			Modules::load($name);
		}
		
		
		/**
		 * klass
		 *
		 * @param string $class_name
		 *
		 * @throws ClassNotFoundException();
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function klass($name) {
			self::load_class($name);
		}
		
		
		/**
		 * load_class
		 *
		 * @param string $class_name
		 *
		 * @throws ClassNotFoundException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function load_class($name) {
			
			$info = pathinfo($name);
			if ( $info['dirname'] == "." ) {
			
				if ( !class_exists($name) ) {
				
					if ( !file_exists(CLASSES_DIR.$name.".php") ) {
					
						if ( !file_exists(APPLICATION_CLASSES_DIR.$name.".php") ) {
							throw new ClassNotFoundException(sprintf("Unable to locate file containing class (%s)", $name));
						} else {
						
							@include_once APPLICATION_CLASSES_DIR.$name.".php";
							if ( !class_exists($name) ) {
								throw new ClassNotFoundException(sprintf("Could not find class (%s): include '%s.php'", $name, APPLICATION_CLASSES_DIR.$name));
							}
						
						}
					
					} else {
				
						@include_once CLASSES_DIR.$name.'.php';
						if ( !class_exists($name) ) {
							throw new ClassNotFoundException(sprintf("Could not find class (%s): include '%s.php'", $name, CLASSES_DIR.$name));
						}
					
					}
				
				}
				
			} else {
				
				$klass = $info['filename'];
				if ( !class_exists($klass) ) {
					@include_once $name;
				}
				
				if ( !class_exists($klass) ) {
					throw new ClassNotFoundException(sprintf("Attempted to load class (%s) from file: %s", $klass, $name));
				}
				
			}
			
		}
		
		
		/**
		 * config
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