<?
	
	/**
	 * HTML
	 *
	 * @package PharosPHP.Modules
	 * @author Matt Brewer
	 **/
	
	class HTML {
		
		protected $classes = array();
		
		
		/**
		 * __get
		 * Used when accessing a non-public instance var
		 *
		 * @throws HTMLPluginNotFoundException
		 *
		 * @return HTMLPlugin $plugin
		 * @author Matt Brewer
		 **/

		public function __get($key) {
			
			if ( !class_exists($key) ) {
				self::load($key);
			}
			
			$HTML->anchor->
			
			return new $key();
			
		}
		
		
		/**
		 * load
		 * Loads the HTMLPlugin
		 * 
		 * @throws HTMLPluginNotFoundException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		protected static function load($key) {
			$path = dirname(__FILE__) . DS . 'plugins' . DS . $key . '.php';
			if ( file_exists($path) ) {
				require_once $path;
			} else {
				try {
					Loader::load_class($key);
				} catch (ClassNotFoundException $e) {
					throw new HTMLPluginNotFoundException($key);
				}
			}
		}
		
	}  
	
	
	/**
	 * HTMLPluginNotFoundException
	 *
	 * @package PharosPHP.Modules.HTML
	 * @author Matt Brewer
	 **/
	
	class HTMLPluginNotFoundException extends PharosBaseException {
		public function __construct($plugin="") {
			$this->message = sprintf("Unable to locate HTMLPlugin: [%s]", $plugin);
		}
	} 
	
	
	/**
	 * iHTMLPlugin
	 * Interface all HTML plugins must implement
	 *
	 * @package PharosPHP.Modules
	 * @author Matt Brewer
	 **/

	interface iHTMLPlugin {
		public function save();
		public function html();
	}
	
	
	/**
	 * HTMLPlugin
	 * Recommended superclass for all HTML Plugins
	 *
	 * @package default
	 * @author Matt Brewer
	 **/
	
	class HTMLPlugin implements iHTMLPlugin {
		
		public function __construct() {}
		
		public function save() {
			
		}
		
		public function html() {
			
		}
		
		public function __toString() {
			return $this->html();
		}
		
	} 

?>