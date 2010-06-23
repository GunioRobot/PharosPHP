<?

	///////////////////////////////////////////////////////////////////////////
	//
	//	Modules Base Class
	//
	//	To create your own module, subclass this base class
	//
	///////////////////////////////////////////////////////////////////////////

	/**
	 * Module Superclass
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	class Module {
	
		protected static $name = "Example Module";
		protected static $version = "1.0";
		protected static $author = "PharosPHP";
		protected static $website = "http://github.com/macfanatic/PharosPHP/";
		
		public static function load() {
			
			return true;
		}
		
		public static function version() {
			return $this->version;
		}
		
		public static function info() {
			return array("version" => $this->version, "name" => $this->name, "author" => $this->author, "website" => $this->website);
		}
	
	}
	
?>