<?

	class GenericPageController extends ApplicationController {
		
		protected $text = "";
		protected $page = null;
	
		public function __construct($title="") {
			parent::__construct();
			$this->title = $title;
		}
		
		protected function images() {
			$view = '';
			if ( count($this->images) > 0 ) {
				foreach($this->images as $i) {
			    	$view .= '<img src="'.UPLOAD_SERVER.$i->thumb.'" alt="'.$i->alt.'" border="0" />';
				} 
			} return $view;
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
				
				// Build the images array to pass off to generic template
				for ( $fields = get_object_vars($page), $i = 1; $i <= NUMBER_IMAGES_PER_ITEM; $i++ ) {
					if ( ($thumb = $fields['image'.$i.'_thumb']) !== "" ) {
						$this->images[] = (object)array("path" => "", "rel" => $this->title, "alt" => $fields['image'.$i.'_alt'], "thumb" => $thumb);
					} 
				}
				
				
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