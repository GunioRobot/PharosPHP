<?

	require_once CLASSES_DIR.'Table.php';
	
	abstract class TableController extends Controller {
	
	
		protected $table;
		protected $type = "";
		protected $dataKey = "";
		
		//////////////////////////////////////////////////////////////////
		//
		//	Following 3 functions must be included in subclass!
		//
		//////////////////////////////////////////////////////////////////
		
		abstract protected function tableColumns();
		abstract protected function buildData($sql);
		abstract protected function edit($id,$repost=false);
		abstract protected function delete($id);
		abstract public function manage($orderField='last_updated',$orderVal='desc',$page=1,$filter='');
		
		
	
		//////////////////////////////////////////////////////////////////
		//
		//	Optionally pass a type and id when initializing
		//
		//////////////////////////////////////////////////////////////////
	
		public function __construct($type="", $tableID="") {

			parent::__construct();
			$this->title = $type ? "Manage ".$this->type : get_class($this);
			$this->type = $type;

			$this->table = new Table();
			$this->table->rows_per_page = DEFAULT_ROWS_PER_TABLE_PAGE;
			$this->table->display_pages = DEFAULT_PAGES_PER_PAGINATION;

			$this->table->id = $tableID;
			$this->table->class = 'list';
			$this->table->head_class = 'contentTitleBar';
			
			Controller::loadModule("profile");
						
		}
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Get/Set the title of the controller
		//
		//////////////////////////////////////////////////////////////////
		
		public function title($string="") {
			if ( $string != "" ) {
				$this->title = $string;
			} else return $this->title;
		}
		
	
		//////////////////////////////////////////////////////////////////
		//
		//	Basic search functionality
		//
		//////////////////////////////////////////////////////////////////

		protected function search($search) {

			// Pull from a new post, or just grab the existing get one passed in
			$search = ( ($s = post("search")) ) ? $s : $search;

			if ( $search != '' ) {
				$where = basic_where($search, $this->table->id);
				$this->table->search = $search;
			} else {
				$where = " ";
				$this->table->search = "";
			}

			return $where;

		}


		//////////////////////////////////////////////////////////////////
		//
		//	Basic ordering functionality
		//
		//////////////////////////////////////////////////////////////////

		protected function order($orderField='last_updated',$orderVal='desc') {

			$order = ' ORDER BY '.$this->table->id.'.'.$orderField.' '.$orderVal;
			$this->table->ordered_row = $orderField;
			$this->table->order = $orderVal;

			return $order;

		}


		//////////////////////////////////////////////////////////////////
		//
		//	Default controller operation
		//
		//////////////////////////////////////////////////////////////////

		public function index() {
			redirect(manage(get_class($this)));
		}
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	For creating new entry, but just calls the edit code
		//
		//////////////////////////////////////////////////////////////////

		public function create() {
			$this->edit(0);
		}


		//////////////////////////////////////////////////////////////////
		//
		//	Performs save operations (whether new or old entry)
		//
		//////////////////////////////////////////////////////////////////

		public function save($id=0) {
			$id = process_profile($id);
			redirect(edit(get_class($this),$id)."true/");
		}

	
	}

?>