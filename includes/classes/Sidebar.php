<?

	class Sidebar {
		
		protected $db;
		protected $pages;
		
		public function __construct() {

			global $db;
			$this->db =& $db;
			
			$this->pages = array();
			$this->pages[0] = (object)array("id" => 0, "name" => "Root", "children" => array());
			
		}
		
		public function build(&$parent=null) {
			
			if ( is_null($parent) ) {
				$parent =& $this->pages[0];
			}
			
			// Grab the information for all the kids
			$sql = "SELECT * FROM admin_nav WHERE parent_id = '".(int)$parent->id."' AND display != 'hidden' AND ".SECURITY_LVL." >= min_lvl AND ".SECURITY_LVL." <= max_lvl ORDER BY order_num ASC";
			for ( $info = $this->db->Execute($sql); !$info->EOF; $info->moveNext() ) {
				
				// Pull the info, let the name be dynamic and figure that out
				$page = (object)$info->fields;
				$page->name = substr($page->name,0,2) == '%%' ? eval(substr($page->name,2)) : $page->name;
				$page->page = html_entity_decode(preg_replace('/<[^>]*>/', '', $page->page));
						
				// Let each arg be dynamic, calculate that here
				$vars = explode('/',$page->page);
				foreach($vars as $i => &$v) {
					if ( substr($v,0,2) == "%%" ) {
						$v = eval(substr($v,2));
					}
				} $page->page = implode('/',$vars);
				
				// Find the children, and finally store the new information
				$parent->children[$page->id] = $page;			
				$this->build($page);
				
			}
			
		}
	
		public function pages() {
			return $this->pages[0]->children;
		}
		
		public function page($id) {
			if ( is_int($id) && $id > 0 ) {
				if ( in_array($id, $this->pages[0]->children) ) {
					return $this->pages[0]->children[$id];
				} else {
					foreach($this->pages[0]->children as $p) {
						if ( in_array($id, $p) ) return $p->children[$id];
					}
				}
			} return false;
		}
	
	}
	
?>