<?

	/**
	 * PageNotFoundController
	 *
	 * @package PharosPHP.Application.Controllers
	 * @author Matt Brewer
	 **/
	
	class PageNotFoundController extends ApplicationGenericPageController {
		
		public function __construct() {
			parent::__construct("Page Not Found");						
		}

	
		public function index() {
			
			$view = '<h3>May We Suggest</h3>';
			$view .= '<ul>';
			
			for ( $pages = $this->db->Execute("SELECT * FROM pages ORDER BY title ASC"); !$pages->EOF; $pages->moveNext() ) {
				$view .= '<li><a href="'.internal_external_link($pages->fields['slug'].'/').'" title="'.$pages->fields['title'].'">'.$pages->fields['title'].'</a></li>';
			} $view .= '</ul>';
						
			$this->output->view($view);
			$this->output->cache(60);
		
		}
		
	
	}
	
?>