<?

	require_once CLASSES_DIR.'Table.php';
	
	abstract class TableController extends Controller {
	
		protected $table;
		protected $type = "";
		protected $dataKey = "";
		
		abstract protected function tableColumns();
		abstract protected function buildData($sql);
		abstract public function manage($orderField='last_updated',$orderVal='asc',$page=1,$filter='');
	
		public function __construct($type="", $tableID="") {

			parent::__construct();
			$this->title = $type ? "Manage ".$this->type : __CLASS__;
			$this->type = $type;

			$this->table = new Table();
			$this->table->rows_per_page = DEFAULT_ROWS_PER_TABLE_PAGE;
			$this->table->display_pages = 5;

			$this->table->id = $tableID;
			$this->table->class = 'list';
			$this->table->head_class = 'contentTitleBar';

		}
		
		public function title($string="") {
			if ( $string != "" ) {
				$this->title = $string;
			} else return $this->title;
		}
		
		
	

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

		protected function order($orderField='last_updated',$orderVal='asc') {

			$order = ' ORDER BY '.$this->table->id.'.'.$orderField.' '.$orderVal;
			$this->table->ordered_row = $orderField;
			$this->table->order = $orderVal;

			return $order;

		}

		public function index() {
			redirect(controller_link(__CLASS__).'/manage/');
		}

	
	}

?>