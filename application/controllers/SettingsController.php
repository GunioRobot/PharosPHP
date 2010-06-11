<?php

	class SettingsController extends TableController {
		
		protected	$levels;
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Called once, in index.php (to create the controller)
		//
		//////////////////////////////////////////////////////////////////
		
		public function __construct() {
			
			parent::__construct("Setting","general_settings");
						
			$this->dataKey = "setting_id";			
			$this->tableColumns();
			
			$this->levels = user_levels_array(Settings::get( 'users.levels.super'));
						
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Builds the columns array to use for the Table output
		//
		//////////////////////////////////////////////////////////////////
		
		protected function tableColumns() {
			$this->table->columns = array();
			$this->table->columns[] =  array('name' => 'Name', 'key' => 'setting_name', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Acess', 'key' => 'setting_level', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Date Added', 'key' => 'date_added', 'class' => 'center');
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

					$row['data'][] = format_title($info->fields['setting_name']);
					$row['data'][] = $this->levels[$info->fields['setting_level']];
					$row['data'][] = format_date($info->fields['date_added'],true);
					$row['data'][] = format_date($info->fields['last_updated'],true);

					$actions = '<a href="'.Template::edit(__CLASS__,$id).'" title="Edit this '.$this->type.'">Edit</a>';

					if ( is_super() ) {
						$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
						$actions .= '<a href="'.Template::delete(__CLASS__,$id).'" title="Delete this '.$this->type.'">Delete</a>';
					}


					$row['data'][] = $actions;

					$data[] = $row;
				}

			} $this->table->data = $data;
			
		}
		
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Adding additional filtering functionality
		//
		//////////////////////////////////////////////////////////////////
		
		public function search($search) {
			
			$where = parent::search($search);
			if ( $this->table->search != '' ) {
				$where .= " AND ".$this->table->id.".setting_level <= '".(int)SECURITY_LVL."' ";
			} else $where = " WHERE ".$this->table->id.".setting_level <= '".(int)SECURITY_LVL."' ";
			
			return $where;
			
		}
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Lists out the table of settings
		//
		//////////////////////////////////////////////////////////////////
		
		public function manage($orderField='last_updated',$orderVal='desc',$page=1,$filter='') {
			
			$this->javascript('confirmDelete.php');
												
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

				if ( is_super() ) {
					$view .= '<div class="contentTabCap"></div><div class="contentTab"><a href="'.Template::create(__CLASS__).'" title="Create New '.$this->type.'" class="tabAdd">Add</a></div>';
				}

		       $view .= '</form>
		            <br clear="all" />
		          </div>'.
		          $this->table->get_html($this->table->current_page, $page_count, $start, $total);

			$this->output($view);

		}
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Shows the edit page
		//
		//////////////////////////////////////////////////////////////////
		
		public function edit($id,$repost=false) {
			
			$repost = ( $repost === "true" ) ? true : false;
			
			$this->javascript('tiny_mce_include.php');
			
			// Required by profile class and repost_mod
			@define('PROFILE_TABLE', $this->table->id);
			@define('PROFILE_TITLE', $this->type);
			@define('PROFILE_ID', $this->dataKey);
			@define('CURRENT_HTML_FILE', "edit_setting.html");

			// Template tags to pull from database and replace
			$fields = array(

				array('name' => PROFILE_ID, 'type' => 'display'),
				array('name' => '{TYPE}', 'type' => 'static', 'value' => PROFILE_TITLE),
				array('name' => '{manage}', 'type' => 'static', 'value' => Template::controller_link(__CLASS__)),
				array('name' => '{new}', 'type' => 'static', 'value' => Template::create(__CLASS__)),
				array('name' => '{form_link}', 'type' => 'static', 'value' => Template::save(__CLASS__,$id)),
				array('name' => '{data_key}', 'type' => 'static', 'value' => PROFILE_ID),
				
				
				array('name' => 'setting_value', 'type' => 'text_area', 'row' => '8', 'col' > '92', 'width' => '738px', 'height' => '100px'),
				array('name' => 'setting_notes', 'type' => 'text_area', 'row' => '8', 'col' > '92', 'width' => '738px', 'height' => '100px'),
				array('name' => 'date_added', 'type' => 'date_added'),
				array('name' => 'last_updated', 'type' => 'last_updated'),

				array('name' => 'content_type_id', 'type' => 'hidden', 'value' => SETTING_TYPE_ID),
				array('name' => 'content_type_name', 'type' => 'hidden', 'value' => strtolower(PROFILE_TITLE))

			);
			
			
			if ( is_super() ) {
				
				$fields[] = array('name' => '{new_button}', 'type' => 'static', 'value' => '<div class="contentTabCap"></div><div class="contentTab"><a href="'.Template::create(get_class($this)).'" class="tabAdd">New '.PROFILE_TITLE.'</a></div>', 'varx' => 'hide');
				$fields[] = array('name' => 'setting_name', 'type' => 'text', 'size' => '50' , 'max' => '200');
				
				$fields[] = array('name' => 'level', 'type' => 'static', 'value' => '<div class="floatLeft" style="margin-left:15px;"><strong>Setting Level:</strong><br />', 'varx' => 'hide');
				$fields[] = array('name' => 'setting_level', 'type' => 'dropdown', 'option' => user_levels_array(SECURITY_LVL), 'default' => Settings::get( 'users.levels.admin'));
				$fields[] = array('name' => '/level', 'type' => 'static', 'value' => '</div>', 'varx' => 'hide');
				
			} else {

				$fields[] = array('name' => '{new_button}', 'type' => 'static', 'value' => '', 'varx' => 'hide');
				$fields[] = array('name' => 'setting_name', 'type' => 'display');
				
				$fields[] = array('name' => 'level', 'type' => 'static', 'value' => '', 'varx' => 'hide');
				$fields[] = array('name' => 'setting_level', 'type' => 'static', 'value' => '', 'varx' => 'hide');
				$fields[] = array('name' => '/level', 'type' => 'static', 'value' => '', 'varx' => 'hide');			
				
			}
			

			// Run throught the parser and spit out the page
			$profile = new Profile($fields);
			
			if ( $id > 0 ) $this->output($profile->display($this->dataKey, $id, $repost));
			else $this->output($profile->display());
						
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Deletes an entry, used in conjunction with "confirmDelete"
		//	javascript jAlert stuff
		//
		//////////////////////////////////////////////////////////////////
		
		public function delete($id) {
			
			if ( ($confirmed = post("confirmed")) === "true" ) {

				// Delete the note itself
				$sql = "DELETE FROM ".$this->table->id." WHERE $this->dataKey = '$id' LIMIT 1";
				$this->db->Execute($sql);
				
				$obj = array('error' => false, 'redirect' => Template::manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;
				
			} else {
				
				echo "[link]".delete(__CLASS__,$id)."[/link]";
				echo "This $this->type will be permanently removed.<br /><br />";
				echo "<strong>This action cannot be undone.</strong><br />";
				exit;
				
			}
			
		}
		
	}
          

?>