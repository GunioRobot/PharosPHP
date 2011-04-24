<?

	/**
	 * ApplicationGenericPageController
	 * Override this class to provide customized behavior for generic text pages pulling from the database
	 *
	 * @package PharosPHP.Application.Classes
	 * @author Matt Brewer
	 **/
	
	class ApplicationGenericPageController extends ApplicationController {
		
		protected $text = "";
		protected $page = null;

		public function __construct($title="") {
			parent::__construct();
			$this->title = $title;
		}

		public function headerView() {
			return '';
		}

		protected function footerView() {
			return '';
		}


		public function index() {
			echo $this->text();
		}

		public function page($page=null) {
			if ( !is_null($page) ) {

				$this->page = $page;

				$this->text($page->text);
			}
		}

		public function text($text="") {
			if ( $text != "" ) { 
				$this->text = $text;
			} else return $this->text;
		}


		public function output($string="") {			
			if ( $string != '' ) {
				$this->output = $string;
			} else {

				if ( $this->output === "" ) {

					return "";

				} else {

					$s = "";
					if ( method_exists($this, "headerView") ) {
						$s .= $this->headerView();
					}

					$s .= $this->output;

					if ( method_exists($this, "footerView") ) {
						$s .= $this->footerView();
					}
					return $s;

				}

			}
		}
		
	}

?>