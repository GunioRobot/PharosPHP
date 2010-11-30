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
		 * Instance of the Loader class for loading modules, classes, config files and more
		 *
		 * @var Loader
		 */
		protected $load;
		
		
		/**
		 * Title for the controller, used in Template::title()
		 *
		 * @uses NotificationCenter::FILTER_META_TITLE
		 * @var string
		 */		
		public $title;
		
		
		/**
		 * Keywords for the controller, used in Template::keywords()
		 *
		 * @uses NotificationCenter::FILTER_META_KEYWORDS
		 * @var string
		 */
		public $keywords;
		
		
		/**
		 * Description or the controller, used in Template::description()
		 *
		 * @uses NotificationCenter::FILTER_META_DESCRIPTION
		 * @var string
		 */
		public $description;
		
		
		/**
		 * Instance of the HTTPResponse class for managing caching, views, and resources as the response to the received request
		 *
		 * @var Output
		 */
		public $output;
		
		
		/**
		 * Instance of the Authentication class for access to the built in Authentication framework
		 *
		 * @copydoc Authentication
		 * @var Authentication
		 */
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

			$this->output = new HTTPResponse();
						
			$this->title = "";
			$this->keywords = DEFAULT_KEYWORDS;
			$this->description = DEFAULT_DESCRIPTION;
			
			$this->auth = Authentication::get();
			
			$this->load = new Loader();
					
		}
		
		
		/**
		 * __missingControllerAction
		 * Error handler for an unimplemented controller action (on a per controller basis)
		 * NOTE: To use, override this method without calling parent::____missingControllerAction() so as to avoid throwing the exception
		 *
		 * @param string $class
		 * @param string $method
		 * @param array $params
		 * 
		 * @throws ControllerActionNotFoundException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function __missingControllerAction($class, $method, array $params=array()) {
			throw new ControllerActionNotFoundException($class, $method);
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