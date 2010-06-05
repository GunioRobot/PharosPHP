<?php

	class UsersController extends TableController {
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Called once, in index.php (to create the controller)
		//
		//////////////////////////////////////////////////////////////////
		
		public function __construct() {
			
			parent::__construct("User","users");
						
			$this->dataKey = "user_id";			
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
			$this->table->columns[] =  array('name' => 'Name', 'key' => 'user_first_name', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Email', 'key' => 'user_primary_email', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Level', 'key' => 'user_level', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Date Added', 'key' => 'date_added', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Last Login', 'key' => 'user_last_login', 'class' => 'center');
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

					$row['data'][] = $info->fields['user_first_name'] . ' ' .$info->fields['user_last_name'];
					$row['data'][] = '<a href="mailto:'.$info->fields['user_primary_email'].'">'.$info->fields['user_primary_email'].'</a>';
					$row['data'][] = $this->levels[$info->fields['user_level']];
					$row['data'][] = format_date($info->fields['date_added'],true);
					
					$loginDate = format_date($info->fields['user_last_login'],true);
					if ( $loginDate == "" ) $loginDate = "<em>Never</em>";
					$row['data'][] = $loginDate;

					$actions = '<a href="'.edit(__CLASS__,$id).'" title="Edit this '.$this->type.'">Edit</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a class="confirm-with-popup" href="'.delete(__CLASS__,$id).'" title="Delete this '.$this->type.'">Delete</a>';

					$row['data'][] = $actions;

					$data[] = $row;
				}

			} $this->table->data = $data;
			
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Adding a little more filtering to basic searching and display
		//
		//////////////////////////////////////////////////////////////////
		
		public function search($search) {
			
			$where = parent::search($search);
			if ( $this->table->search != '' ) {
				$where .= " AND ".$this->table->id.".user_level <= '".(int)SECURITY_LVL."' ";
			} else $where  = " WHERE ".$this->table->id.".user_level <= '".(int)SECURITY_LVL."' ";
						
			return $where;
			
		}
		
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Lists out the table of settings
		//
		//////////////////////////////////////////////////////////////////
		
		public function manage($orderField='user_last_login',$orderVal='desc',$page=1,$filter='') {
															
			$this->table->current_page = intval($page);

			$where = $this->search($filter);
			$order = $this->order($orderField,$orderVal);
			
			$this->table->basic_a = controller_link(__CLASS__,"/".__FUNCTION__."/");
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

			$view .= '<div class="contentTabCap"></div><div class="contentTab"><a href="'.create(__CLASS__).'" title="Create New '.$this->type.'" class="tabAdd">Add</a></div>';

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
						
			// Required by profile class and repost_mod
			@define('PROFILE_TABLE', $this->table->id);
			@define('PROFILE_TITLE', $this->type);
			@define('PROFILE_ID', $this->dataKey);
			@define('CURRENT_HTML_FILE', "edit_user.html");

			// Template tags to pull from database and replace
			$fields = array(

				array('name' => PROFILE_ID, 'type' => 'display'),
				array('name' => '{TYPE}', 'type' => 'static', 'value' => PROFILE_TITLE),
				array('name' => '{manage}', 'type' => 'static', 'value' => controller_link(__CLASS__)),
				array('name' => '{new}', 'type' => 'static', 'value' => create(__CLASS__)),
				array('name' => '{form_link}', 'type' => 'static', 'value' => save(__CLASS__,$id)),
				array('name' => '{data_key}', 'type' => 'static', 'value' => PROFILE_ID),
				
				array('name' => 'user_first_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'fname'),
				array('name' => 'user_middle_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'mname'),
				array('name' => 'user_last_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_phone_number', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_fax_number', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_address_line_1', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_address_line_2', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_city', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'city'),
				array('name' => 'user_state', 'type' => 'dropdown', 'option' => array_values(states_array()), 'class' => 'state'),
				array('name' => 'user_zip', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'zip'),
				array('name' => 'user_username', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_password', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_primary_email', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_secondary_email', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_birthday', 'type' => 'dob'),
				array('name' => 'user_notes', 'type' => 'text_area', 'class' => 'notes'),
				
				array('name' => 'user_level', 'type' => 'dropdown', 'option' => user_levels_array(SECURITY_LVL), 'default' => Settings::get( 'users.levels.admin')),
	
				array('name' => 'date_added', 'type' => 'date_added'),
				array('name' => 'last_updated', 'type' => 'last_updated'),
				array('name' => 'content_type_id', 'type' => 'hidden', 'value' => USER_TYPE_ID),
				array('name' => 'content_type_name', 'type' => 'hidden', 'value' => strtolower(PROFILE_TITLE))

			);
			

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
				
				// Delete tracking information
				$sql = "DELETE FROM tracking WHERE content_type_id = '".(int)USER_TYPE_ID."' AND table_index = '".(int)$id."'";
				$this->db->Execute($sql);
				
				// Delete application to content links
				$sql = "DELETE FROM applications_to_content WHERE content_type_id = '".(int)USER_TYPE_ID."' AND table_index = '".(int)$id."'";
				$this->db->Execute($sql);

				// Delete the note itself
				$sql = "DELETE FROM ".$this->table->id." WHERE $this->dataKey = '$id' LIMIT 1";
				$this->db->Execute($sql);
				
				$obj = array('error' => false, 'redirect' => manage(__CLASS__));
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