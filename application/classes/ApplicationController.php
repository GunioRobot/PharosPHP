<?

	/**
	 * ApplicationController
	 * Extend this class to provide functionality for all of your application controllers to use
	 *
	 * @package PharosPHP.Application.Classes
	 * @author Matt Brewer
	 **/
	
	class ApplicationController extends Controller {
		
		public function __construct() {
			parent::__construct();
			$this->auth->login_required(true);
		}
		
		public function index() {}
		
	} 

?>