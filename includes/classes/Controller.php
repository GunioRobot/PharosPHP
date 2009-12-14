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
	define('CSS_TYPE_SCREEN', 'screen');
	define('JAVASCRIPT_INCLUDE', 'php_include_js');
	define('JAVASCRIPT_EXTERNAL', 'link_js');

	class Controller {
	
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
			$this->title = $this->title;
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
		public function css($path='', $type=CSS_TYPE_ALL) {
			
			if ( $path != '' ) {
				
				// Don't keep adding the same thing
				if ( in_array($path,$this->css) ) {
					return false;
				}
			
				switch($type) {
					case CSS_TYPE_ALL:
					case CSS_TYPE_PRINT:
					case CSS_TYPE_SCREEN:
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
					$this->javascript[] = array('path' => $path, 'type' => (strrpos($path,'.php')===false?JAVASCRIPT_EXTERNAL:JAVASCRIPT_INCLUDE), 'data' => $data);
				}
				
			} else {
				
				return $this->javascript;
				
			}
			
		}
		
		
		
		///////////////////////////////////////////////////////////////////////////
		//
		//	Controller::loadModule() loads a module into the sysetm
		//
		///////////////////////////////////////////////////////////////////////////
		public static function loadModule($name) {
			if ( !isset(self::$modules[$name]) ) {
				$folder = MODULES_DIR;
				if ($handle = opendir($folder)) {
					while (false !== ($file = readdir($handle)) ) {
						if ($file != "." && $file != ".." && is_dir($folder.$file) && $file === $name ) {
							if ( @file_exists($folder.$file.'/include.php') ) {
								
								// Make sure the version of the module is correct, then load
								if ( @file_exists($folder.$file.'/version.php') ) {
									include $folder.$file.'/version.php';
									$upperName = strtoupper($name);
									if ( defined($upperName."_VERSION_MAJOR") && defined($upperName."_VERSION_MINOR") ) {
										
										if ( constant($upperName."_VERSION_MAJOR") >= CMS_VERSION_MAJOR && constant($upperName."_VERSION_MINOR") >= CMS_VERSION_MINOR ) {
											include $folder.$file.'/include.php';
											$modules[$name] = $folder.$file.'/include.php';
											Console::log("Loaded ($name) successfully");
										} else {
											$err = "Unable to load ($name) -- Incorrect Version (".constant($upperName."_VERSION_MAJOR").".".constant($upperName."_VERSION_MINOR")." < ".CMS_VERSION_MAJOR.".".CMS_VERSION_MINOR.")";
											Console::log($err);
											throw new Exception($err);
										}
										
									} else {
										$err = "Unable to load ($name) -- Missing versioning information";
										Console::log($err);
										throw new Exception($err);
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