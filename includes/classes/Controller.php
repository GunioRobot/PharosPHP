<?

	///////////////////////////////////////////////////////////////////////////
	//
	//	Controller.php
	//
	//	Parent class for all pages - subclass to load in particular page
	//
	///////////////////////////////////////////////////////////////////////////
	
	define('CSS_TYPE_ALL', 'all');
	define('CSS_TYPE_PRINT', 'print');
	define('CSS_TYPE_MEDIA', 'media');
	define('JAVASCRIPT_INCLUDE', 'php_include_js');
	define('JAVASCRIPT_EXTERNAL', 'link_js');

	class Controller {
	
		protected $css;
		protected $javascript;
		protected $view;
		protected $db;
		
		public $name;
		public $title;
		public $keywords;
		public $description;
		
		public function __construct($css=array(),$javascript=array()) {
			
			global $db;
		
			$this->css = ( is_array($css) ) ? $css : array();
			$this->javascript = ( is_array($javascript) ) ? $javascript : array();
			
			$this->db =& $db;
						
			$this->name = __CLASS__;
			$this->title = $this->title;
			$this->keywords = DEFAULT_KEYWORDS;
			$this->description = DEFAULT_DESCRIPTION;
			
			$this->view = "";
		
		}
		
		public function view($string='') {
			if ( $string != '' ) {
				$this->view = $string;
			} else {
				return $this->view;
			}
		}
		
		public function css($path, $type=CSS_TYPE_ALL) {
			
			if ( $path != '' ) {
			
				switch($type) {
					case CSS_TYPE_ALL:
					case CSS_TYPE_PRINT:
					case CSS_TYPE_MEDIA:
						break;
					default:
						$type = CSS_TYPE_ALL;
						break;
				}
			
				$this->css[] = array('path' => $path, 'type' => $type);
				
			} else {
				return $this->css;
			}
			
		}
	
		
		public function javascript($path) {
			if ( $path != '' ) {
				$this->javascript[] = array('path' => $path, 'type' => (strrpos('.php')===false?JAVASCRIPT_EXTERNAL:JAVASCRIPT_INCLUDE));
			} else {
				return $this->javascript;
			}
		}
		
		public function __destruct() {
					
			unset($this->css);
			unset($this->javascript);
			
			Console::log("Unloading (".__CLASS__.")");
		
		}
	
	}
	
?>