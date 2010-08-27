<?

	class CronController extends ApplicationController {
		
		public function index() {
			Cron::process();
		}
		
	}

?>