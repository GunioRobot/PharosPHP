<?

	///////////////////////////////////////////////////////////////////////////
	//
	//	Modules Base Class
	//
	//	To create your own module, subclass this base class
	//
	///////////////////////////////////////////////////////////////////////////

	class Module {
	
		protected static $name = "Example Module";
		protected static $version = "1.0";
		protected static $author = "CMSLite";
		protected static $website = "http://github.com/macfanatic/CMS-Lite/";
		
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