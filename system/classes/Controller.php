<?
	
	/**
	 * Controller
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 *
	 * Parent class for all controllers you create
	 *
	 **/
	
	require_once CLASSES_DIR.'Output.php';
	require_once CLASSES_DIR.'Authentication.php';
	class Controller {
		
	
		/**
		*
		*	Protected instance vars (available to subclasses)
		*
		*/
		protected $db;
		
		
		/**
		*
		*	Public instance vars
		*
		*/
		public $name;
		public $title;
		public $keywords;
		public $description;
		public $output;
		public $auth;
		
		
		/**
		 * Constructor
		 *
		 * @return self
		 * @author Matthew Brewer
		 *
		 * NOTE: All subclasses that override this method must be sure to call 
		 * 		parent::__construct()
		 * in order for the subclass to behave properly!
		 *
		 **/
		public function __construct() {
			
			global $db;
				
			$this->db =& $db;
			$this->output = new Output();
						
			$this->name = get_class($this);
			$this->title = "";
			$this->keywords = DEFAULT_KEYWORDS;
			$this->description = DEFAULT_DESCRIPTION;
			
			$this->auth = Authentication::get();
					
		}

	}
	
?>