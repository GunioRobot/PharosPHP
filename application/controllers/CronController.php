<?

	/**
	 * CronController
	 *
	 * @package PharosPHP.Application.Controllers
	 * @author Matt Brewer
	 **/
	
	class CronController extends ApplicationController {
		
		public function __construct() {
			parent::__construct();
			$this->auth->login_required(false);
		}
		
		public function index() {
			Cron::process();
		}
		
	}

?>