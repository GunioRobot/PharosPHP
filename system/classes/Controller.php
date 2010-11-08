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
		 * Note that this is called, even if the Route was cached
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
		
		
		/**
		 * __preRender
		 * Called after the constructer, but before the corresponding method is called (determined by the Router)
		 * Note that this is not called if the Route was cached
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public function __preRender() {
			
		}
		
		
		/**
		 * __postRender
		 * Called after the method was executed (determined by the Router)
		 * Note that this is not called if the Route was cached
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public function __postRender() {
			
		}

	}
	
?>