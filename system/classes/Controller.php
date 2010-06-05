<?

	///////////////////////////////////////////////////////////////////////////
	//
	//	Controller.php
	//
	//	Parent class for all pages - subclass to load in particular page
	//
	///////////////////////////////////////////////////////////////////////////

	class Controller {
		
		const CSS_TYPE_ALL = "all";
		const CSS_TYPE_PRINT = "print";
		const CSS_TYPE_SCREEN = "screen";
		const JAVASCRIPT_INCLUDE = "php_include_js";
		const JAVASCRIPT_EXTERNAL = "link_js";
	
		///////////////////////////////////////////////////////////////////////////
		//
		//	Available to this class and all subclasses
		//
		///////////////////////////////////////////////////////////////////////////
		protected $css;
		protected $javascript;
		protected $output;
		protected $db;
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Publically accessible properties
		//
		///////////////////////////////////////////////////////////////////////////
		public $name;
		public $title;
		public $keywords;
		public $description;
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	self::$modules[$name] for loaded modules in the system
		//
		///////////////////////////////////////////////////////////////////////////
		protected static $modules = array();
		
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Constructor, optionally takes array of CSS and JS files
		//
		///////////////////////////////////////////////////////////////////////////
		public function __construct($css=array(),$javascript=array()) {
			
			global $db;
		
			$this->css = ( is_array($css) ) ? $css : array();
			$this->javascript = ( is_array($javascript) ) ? $javascript : array();
			
			$this->db =& $db;
						
			$this->name = get_class($this);
			$this->title = "";
			$this->keywords = DEFAULT_KEYWORDS;
			$this->description = DEFAULT_DESCRIPTION;
			
			$this->output = "";
		
		}
		
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Either retuns the output, or sets the output if given input
		//
		///////////////////////////////////////////////////////////////////////////
		public function output($string='') {
			if ( $string != '' ) {
				$this->output = $string;
			} else {
				return $this->output;
			}
		}
		
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Either returns array, or adds a stylesheet to be used
		//
		///////////////////////////////////////////////////////////////////////////
		public function css($path='', $type=self::CSS_TYPE_ALL) {
			
			if ( $path != '' ) {
				
				// Don't keep adding the same thing
				if ( in_array($path,$this->css) ) {
					return false;
				}
			
				switch($type) {
					case self::CSS_TYPE_ALL:
					case self::CSS_TYPE_PRINT:
					case self::CSS_TYPE_SCREEN:
						break;
					default:
						$type = self::CSS_TYPE_ALL;
						break;
				}
			
				$this->css[] = array('path' => $path, 'type' => $type);
				
			} else {
				return $this->css;
			}
			
		}
	
	
	
		///////////////////////////////////////////////////////////////////////////
		//
		//	Returns array of javascript files, or if given input, adds to array
		//	If given same path for 2nd time, just updates $data array
		//
		///////////////////////////////////////////////////////////////////////////
		public function javascript($path='',$data=array()) {
			
			if ( !is_array($data) ) $data = array();
		
			if ( $path != '' ) {
				
				// Don't keep adding the same thing
				if ( ($index = array_search($path,$this->javascript)) !== false ) {
					
					// Replace data array if called a second time
					$this->javascript[$index]['data'] = $data;
					return true;
					
				} else {
					$this->javascript[] = array('path' => $path, 'type' => (strrpos($path,'.php')===false?self::JAVASCRIPT_EXTERNAL:self::JAVASCRIPT_INCLUDE), 'data' => $data);
				}
				
			} else {
				
				return $this->javascript;
				
			}
			
		}
		
		
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Called by index.php, sets the $output var conditionally
		//
		///////////////////////////////////////////////////////////////////////////
	
		public function finalize($output) {
			if ( $this->output() === "" ) $this->output($output);
		} 
				
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	When destroying the object
		//
		///////////////////////////////////////////////////////////////////////////
		public function __destruct() {
					
			unset($this->css);
			unset($this->javascript);
			
			Console::log("Unloading (".get_class($this).")");
		
		}
	
	}
	
?>