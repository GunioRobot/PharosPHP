<?

	/**
	 * CronController
	 *
	 * @package PharosPHP.Application.Controllers
	 * @author Matt Brewer
	 **/
	
	class CronController extends ApplicationController {
		
		/**
		 * Controller Initializer
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function __construct() {
			parent::__construct();
			$this->auth->login_required(false);
		}
		
		/**
		 * Web handler for default method
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function index() {
			Cron::process();
		}
		
	}

?>