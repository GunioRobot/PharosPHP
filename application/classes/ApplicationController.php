<?

	/**
	 * ApplicationController
	 * Extend this class to provide functionality for all of your application controllers to use
	 *
	 * @package PharosPHP.Application.Classes
	 * @author Matt Brewer
	 **/
	
	require_once CLASSES_DIR.'Controller.php';
	class ApplicationController extends Controller {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function index() {}
		
	} 

?>