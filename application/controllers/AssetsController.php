<?php

	class AssetsController extends TableController {
		
		protected $brands = array();
		
		public function __construct() {
			
			parent::__construct("Asset","assets");
						
			$this->dataKey = "id";			
			$this->tableColumns();
									
		}
		
		
		public function add($table, $id, $tab="default") {
			
			$this->load->module("swfupload");
			
			$this->output->javascript("swfupload/swfupload.js");
			$this->output->javascript("swfupload/file_upload_ajax.php", array("allowed_file_types" => "*.jpg;*.jpeg;*.png;*.gif", "table" => $table, "id" => $id));
			$this->output->javascript("embedded-media-upload.js");
			
			$this->output->layout = "ajax-slim";
			
			for ( $data = array(), $info = $this->db->Execute(sprintf("SELECT * FROM `assets` WHERE `assoc_table` = '%s' AND `assoc_id` = '%d'", $table, $id)); !$info->EOF; $info->moveNext() ) {
				$data[] = clean_object($info->fields);
			}
			
			for ( $all = array(), $info = $this->db->Execute(sprintf("SELECT * FROM `assets`")); !$info->EOF; $info->moveNext() ) {
				$all[] = clean_object($info->fields);
			}
			
			$this->output->set("data", $data);
			$this->output->set("all", $all);
			$this->output->set("tab", $tab);
			$this->output->view("add-media-asset.php");
			
		}
		
		
		public function upload() {
			
			$this->output->layout = "empty";
			
			$sent = false;
			foreach($_FILES as $name => $file) {
				
				$filename = save_uploaded_file($name);
				$info = pathinfo($filename);
				$size = filesize(UPLOAD_PATH.$filename);
				$extension = strtolower($info['extension']);
				
				if ( in_array($extension, array("png", "jpg", "gif", "jpeg")) ) {
					list($width, $height) = getimagesize(UPLOAD_PATH.$filename);
				} else {
					$width = 0;
					$height = 0;
				}
				
				$sql = sprintf("INSERT INTO `assets` (`title`,`filename`,`filename_file_size`,`filename_file_type`,`width`,`height`,`assoc_table`,`assoc_id`,`date_added`,`last_updated`) VALUES('%s','%s','%s','%s','%d','%d','%s','%s',NOW(),NOW())", format_title($info['filename']), $info['basename'], $size, $extension, $width, $height, $this->db->prepare_input(Input::request("assoc_table")), $this->db->prepare_input(Input::request("assoc_id")));
				$this->db->Execute($sql);
				
				$sent = true;
				echo UPLOAD_URL.$filename;
			}
						
			if ( !$sent ) echo 'false';
			
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Builds the columns array to use for the Table output
		//
		//////////////////////////////////////////////////////////////////
		
		protected function tableColumns() {
			$this->table->columns = array();
			$this->table->columns[] =  array('name' => 'Title', 'key' => 'title', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Type', 'key' => 'filename_file_type', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Size', 'key' => 'filename_file_size', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Last Updated', 'key' => 'last_updated', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Action', 'class' => 'actions');
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Builds the data array to pass to the Table class
		//
		//////////////////////////////////////////////////////////////////
		
		protected function buildData($sql) {
			
			$data = array();
			if ( $sql ) {
							
				for ( $info = $this->db->Execute($sql), $i = 1; !$info->EOF; $info->moveNext(), $i++ ) {

					$id = $info->fields[$this->dataKey];

					$class = ( $i % 2 ) ? 'listTier1' : 'listTier2';
					$row = array('class' => $class, 'data' => array());

					$row['data'][] = $info->fields['title'];
					$row['data'][] = String::create($info->fields['filename_file_type'])->uppercase();
					$row['data'][] = String::create($info->fields['filename_file_size'])->filesize();
													
					$row['data'][] = format_date($info->fields['last_updated'],true);;

					$actions = '<a href="'.Template::edit(__CLASS__,$id).'" title="Edit this '.$this->type.'">Edit</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a class="confirm-with-popup" href="'.Template::delete(__CLASS__,$id).'" title="Delete this '.$this->type.'">Delete</a>';

					$row['data'][] = $actions;

					$data[] = $row;
				}

			} $this->table->data = $data;
			
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Lists out the table of settings
		//
		//////////////////////////////////////////////////////////////////
		
		public function manage($orderField='title',$orderVal='asc',$page=1,$filter='') {
															
			$this->table->current_page = intval($page);

			$where = $this->search($filter);
			$order = $this->order($orderField,$orderVal);
			
			$this->table->basic_a = Template::controller_link(__CLASS__,"/".__FUNCTION__."/");
			$this->table->get_links = "$orderField/$orderVal/";

			$sql = "SELECT COUNT(".$this->table->id.'.'.$this->dataKey.") as total FROM ".$this->table->id." ".$where.$order;
			list($total, $page_count, $start) = $this->table->paginate($sql);
			
			// Build the data array to pass to the table
			$sql = "SELECT * FROM ".$this->table->id." ".$where.$order." LIMIT ".$start.",".$this->table->rows_per_page;
			$this->buildData($sql);

			$view =  '<div class="titleTabs">
		            <h1 id="page_title">'.$this->type.' Library</strong></h1>
		            <form id="'.$this->table->id.'_form" action="'.$this->table->basic_a."$orderField/$orderVal/$page/".'" method="post">
		            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$this->table->search.'"/><a href="#" onClick="$('."'".'#'.$this->table->id.'_form'."'".').submit();" class="inputPress">Search</a></div>';

			$view .= '<div class="contentTabCap"></div><div class="contentTab"><a href="'.Template::create(__CLASS__).'" title="Create New '.$this->type.'" class="tabAdd">Add</a></div>';

		    $view .= '</form>
		            <br clear="all" />
		          </div>'.
		          $this->table->get_html($this->table->current_page, $page_count, $start, $total);

			echo $view;

		}
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Shows the edit page
		//
		//////////////////////////////////////////////////////////////////
		
		public function edit($id,$repost=false) {
						
			$repost = ( $repost === "true" ) ? true : false;
						
			// Required by profile class and repost_mod
			@define('PROFILE_TABLE', $this->table->id);
			@define('PROFILE_TITLE', $this->type);
			@define('PROFILE_ID', $this->dataKey);
			@define('CURRENT_HTML_FILE', "edit_asset.html");
			
			// Template tags to pull from database and replace
			$fields = array(

				array('name' => PROFILE_ID, 'type' => 'display'),
				array('name' => '{TYPE}', 'type' => 'static', 'value' => PROFILE_TITLE),
				array('name' => '{manage}', 'type' => 'static', 'value' => Template::controller_link(__CLASS__)),
				array('name' => '{new}', 'type' => 'static', 'value' => Template::create(__CLASS__)),
				array('name' => '{form_link}', 'type' => 'static', 'value' => Template::save(__CLASS__,$id)),
				array('name' => '{data_key}', 'type' => 'static', 'value' => PROFILE_ID),
				
				array('name' => 'title', 'type' => 'text', 'style' => 'width:910px;'),			
				array('name' => 'filename', 'type' => 'file', 'varx' => 'store_filesize@true:store_file_type@true:save_as_image@false'),
				array('name' => '{filename}', 'type' => 'link', 'prefix' => UPLOAD_URL.'push.php?f=', 'title' => 'Download File', 'text' => 'Download'),
				array('name' => '{remove_filename}', 'type' => 'remove_image'),
			
				array('name' => 'notes', 'type' => 'text_area', 'row' => '8', 'col' => '89', 'width' => '910px', 'height' => '100px'),
				array('name' => 'date_added', 'type' => 'date_added'),
				array('name' => 'last_updated', 'type' => 'last_updated')

			);
			

			// Run throught the parser and spit out the page
			$profile = new Profile($fields);
			
			if ( $id > 0 ) echo $profile->display($this->dataKey, $id, $repost);
			else echo $profile->display();
						
		}
		
		

		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Deletes an entry, used in conjunction with "confirmDelete"
		//	javascript jAlert stuff
		//
		//////////////////////////////////////////////////////////////////
		
		public function delete($id) {
			
			if ( ($confirmed = Input::post("confirmed")) === "true" ) {
				
				$info = $this->db->Execute(sprintf("SELECT * FROM ".$this->table->id." WHERE $this->dataKey = %d", $id));
				if ( !$info->EOF && $info->fields['filename'] != "" && file_exists(UPLOAD_PATH.$info->fields['filename']) ) {
					unlink(UPLOAD_PATH.$info->fields['filename']);
				}
				
				// Delete the note itself
				$sql = "DELETE FROM ".$this->table->id." WHERE $this->dataKey = '$id' LIMIT 1";
				$this->db->Execute($sql);
				
				$obj = array('error' => false, 'redirect' => Template::manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;
				
			} else {
				
				echo "[link]".Template::delete(__CLASS__,$id)."[/link]";
				echo "This $this->type will be permanently removed.<br /><br />";
				echo "<strong>This action cannot be undone.</strong><br />";
				exit;
				
			}
			
		}
		
	}
          

?>