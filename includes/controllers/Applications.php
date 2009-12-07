<?php

	require_once CLASSES_DIR.'Table.php';

	class Applications extends Controller {
		
		protected $table;
		protected $type = 'Application';
		
		public function __construct() {
			
			parent::__construct();
			$this->title = "Manage Applications";
			
			$this->table = new Table();
			$this->table->rows_per_page = 25;
			$this->table->display_pages = 5;
			
			define('ID', 'app_id');

			$this->table->id = 'applications';
			$this->table->class = 'list';
			$this->table->head_class = 'contentTitleBar';
			$this->table->extra_href = '';
			
			$this->table->columns[] =  array('name' => 'ID', 'key' => ID,  'class' => 'listCheckBox center');
			$this->table->columns[] =  array('name' => 'Name', 'key' => 'app_name');
			$this->table->columns[] =  array('name' => 'Date Added', 'key' => 'date_added', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Last Updated', 'key' => 'last_updated', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Action', 'class' => 'actions');
			
			
		}
		
		private function search() {
			
			if ( ($search = get("search")) ) {
				$where = basic_where($search, $this->table->id);
				$this->table->extra_href .= '&search='.$search;
			} else {
				$where = " ";
			}
			
			// Tack on the active part, if filtering for a regular admin
			if ( !is_super() ) {
				if ( $where != ' ' ) {
					$where .= " AND ".$this->table->id.".active = 'true' ";
				} else $where = " WHERE ".$this->table->id.".active = 'true' ";
			}
			
			return $where;
			
		}
		
		private function order($orderField='',$order='') {
			
			if ( $orderField != '' ) {
				$order = ' ORDER BY '.$this->table->id.'.'.$orderField.' '.$order;
			} else $order = ' ORDER BY '.$this->table->id.'.last_updated DESC';
			
			return $order;
			
		}
		
		private function buildData($sql) {
			
			if ( $sql ) {
			
				for ( $info = $this->db->Execute($sql), $i = 1; !$info->EOF; $info->moveNext(), $i++ ) {

					$key = '&key='.ID.'&'.ID.'='.$info->fields[ID];

					$class = ( $i % 2 ) ? 'listTier1' : 'listTier2';
					$row = array('class' => $class, 'data' => array());

					$row['data'][] = $info->fields[ID];
					$row['data'][] = format_title($info->fields['app_name']);
					$row['data'][] = format_date($info->fields['date_added'],true);
					$row['data'][] = format_date($info->fields['last_updated'],true);


					$actions = '<a href="repost.php?pid='.$_GET['pid'].$key.'&action=publish_app" title="Publish this '.$TYPE.'">Publish</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a href="index.php?pid='.$PROFILE_PID.$key.'" title="Edit this '.$TYPE.'">Edit</a>';

					if ( is_super() ) {
						$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
						$actions .= '<a href="repost.php?pid='.$_GET['pid'].'&action=delete&type='.strtolower($TYPE).$key.'" title="Delete this '.$TYPE.'">Delete</a>';
					}


					$row['data'][] = $actions;

					$data[] = $row;
				}

				$this->table->data = $data;

			}
			
		}
		
		public function index() {
		
			$data = array();
	
			$page = $this->table->get_current_page();
			$where = $this->search();
			$order = $this->order();
			
			$sql = "SELECT COUNT(".$this->table->id.'.'.ID.") as total FROM ".$this->table->id." ".$where.$order;
			list($total, $page_count, $start) = $this->table->paginate($sql);

			// Build the data array to pass to the table
			$sql = "SELECT * FROM ".$this->table->id." ".$where.$order." LIMIT ".$start.",".$this->table->rows_per_page;
			$this->buildData($sql);


			$view =  '<div class="titleTabs">
		            <h1 id="page_title">Application Library</strong></h1>
		            <form id="'.$this->table->id.'_form" action="index.php" method="get">
		            <input type="hidden" name="table_id" value="'.$this->table->id.'"/>
		            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$_GET['search'].'"/><a href="#" onClick="$('."'".'#'.$this->table->id.'_form'."'".').submit();" class="inputPress">Search</a></div>';

				if ( is_super() ) {
					$view .= '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid='.$PROFILE_PID.'" title="Create New '.$TYPE.'" class="tabAdd">Add</a></div>';
				}

		       $view .= '</form>
		            <br clear="all" />
		          </div>'.
		          $this->table->get_html($page,$page_count, $start, $total);
		
			$this->view($view);
			
		}
		
		public function manage($orderField,$order) {
						
			$data = array();

			$page = $this->table->get_current_page();
			$where = $this->search();
			$order = $this->order($orderField,$order);

			$sql = "SELECT COUNT(".$this->table->id.'.'.ID.") as total FROM ".$this->table->id." ".$where.$order;
			list($total, $page_count, $start) = $this->table->paginate($sql);

			// Build the data array to pass to the table
			$sql = "SELECT * FROM ".$this->table->id." ".$where.$order." LIMIT ".$start.",".$this->table->rows_per_page;
			echo $sql;
			$this->buildData($sql);


			$view =  '<div class="titleTabs">
		            <h1 id="page_title">Application Library</strong></h1>
		            <form id="'.$this->table->id.'_form" action="index.php" method="get">
		            <input type="hidden" name="pid" value="'.$_GET['pid'].'"/>
		            <input type="hidden" name="table_id" value="'.$this->table->id.'"/>
		            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$_GET['search'].'"/><a href="#" onClick="$('."'".'#'.$this->table->id.'_form'."'".').submit();" class="inputPress">Search</a></div>';

				if ( is_super() ) {
					$view .= '<div class="contentTabCap"></div><div class="contentTab"><a href="'.controller_link(__CLASS__).'/create/" title="Create New '.$TYPE.'" class="tabAdd">Add</a></div>';
				}

		       $view .= '</form>
		            <br clear="all" />
		          </div>'.
		          $this->table->get_html($page,$page_count, $start, $total);

			$this->view($view);
			

		}
		
	}
          

?>