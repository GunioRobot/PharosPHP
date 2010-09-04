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
	
	class Controller extends Object {
		
	
		/**
		*
		*	Protected instance vars (available to subclasses)
		*
		*/
		protected $load;
		
		
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

			parent::__construct();

			$this->output = new Output();
						
			$this->name = get_class($this);
			$this->title = "";
			$this->keywords = DEFAULT_KEYWORDS;
			$this->description = DEFAULT_DESCRIPTION;
			
			$this->auth = Authentication::get();
			
			$this->load = new Loader();
					
		}

	}
	
?>