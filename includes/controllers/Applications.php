<?php

	require_once CLASSES_DIR.'TableController.php';

	class Applications extends TableController {
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Called once, in index.php (to create the controller)
		//
		//////////////////////////////////////////////////////////////////
		
		public function __construct() {
			
			parent::__construct("Application","applications");
						
			$this->dataKey = "app_id";			
			$this->tableColumns();
						
		}
		

		//////////////////////////////////////////////////////////////////
		//
		//	Builds the columns array to use for the Table output
		//
		//////////////////////////////////////////////////////////////////
		
		protected function tableColumns() {
			$this->table->columns = array();
			$this->table->columns[] =  array('name' => 'Name', 'key' => 'app_name', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Date Added', 'key' => 'date_added', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Last Updated', 'key' => 'last_updated', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Action', 'class' => 'actions');
		}
		

		
		//////////////////////////////////////////////////////////////////
		//
		//	Providing additional search functionality by subclassing
		//
		//////////////////////////////////////////////////////////////////
		
		protected function search($search) {
			
			$where = parent::search($search);
			
			// Tack on the active part, if filtering for a regular admin
			if ( !is_super() ) {
				if ( $where != ' ' ) {
					$where .= " AND ".$this->table->id.".active = 'true' ";
				} else $where = " WHERE ".$this->table->id.".active = 'true' ";
			}
			
			return $where;
			
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

					$row['data'][] = format_title($info->fields['app_name']);
					$row['data'][] = format_date($info->fields['date_added'],true);
					$row['data'][] = format_date($info->fields['last_updated'],true);

					$actions = '<a href="'.controller_link(__CLASS__,"/publish/$id/").'" title="Publish this '.$this->type.'">Publish</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a href="'.edit(__CLASS__,$id).'" title="Edit this '.$this->type.'">Edit</a>';


					if ( is_super() ) {
						$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
						$actions .= '<a href="'.delete(__CLASS__,$id).'" title="Delete this '.$this->type.'">Delete</a>';
					}


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
		
		public function manage($orderField='last_updated',$orderVal='desc',$page=1,$filter='') {
			
			$this->javascript('confirmDelete.php');
												
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

				if ( is_super() ) {
					$view .= '<div class="contentTabCap"></div><div class="contentTab"><a href="'.create(__CLASS__).'" title="Create New '.$this->type.'" class="tabAdd">Add</a></div>';
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
		
		public function edit($id) {
			
			$this->javascript('tiny_mce_include.php');
			
			// Required by profile class and repost_mod
			@define('PROFILE_TABLE', $this->table->id);
			@define('PROFILE_TITLE', $this->type);
			@define('PROFILE_ID', $this->dataKey);
			@define('CURRENT_HTML_FILE', "edit_application.html");

			// Template tags to pull from database and replace
			$fields = array(

				array('name' => PROFILE_ID, 'type' => 'display'),
				array('name' => '{TYPE}', 'type' => 'static', 'value' => PROFILE_TITLE),
				array('name' => '{manage}', 'type' => 'static', 'value' => controller_link(__CLASS__)),
				array('name' => '{new}', 'type' => 'static', 'value' => create(__CLASS__)),
				array('name' => '{form_link}', 'type' => 'static', 'value' => save(__CLASS__,$id)),
				array('name' => '{data_key}', 'type' => 'static', 'value' => PROFILE_ID),
				
				array('name' => 'xml_version', 'type' => 'display'),
				array('name' => 'app_name', 'type' => 'text', 'size' => '50' , 'max' => '200'),
				array('name' => 'app_notes', 'type' => 'text_area', 'row' => '8', 'col' => '89', 'width' => '738px', 'height' => '100px'),
				array('name' => 'date_added', 'type' => 'date_added'),
				array('name' => 'last_updated', 'type' => 'last_updated')

			);


			if ( is_super() ) {


				// The status stuff
				$fields[] = array('name' => 'status', 'type' => 'static', 'value' => '<div class="floatLeft" style="padding-right:15px;"><strong>Status:</strong><br />', 'varx' => 'hide');
				$fields[] = array('name' => 'active', 'type' => 'dropdown', 'option' => array('true' => "Active", "false" => "Inactive"), 'default' => array('true' => "Active"));
				$fields[] = array('name' => '/status', 'type' => 'static', 'value' => '</div>', 'varx' => 'hide');


				// App Version - let super edit it
				$fields[] = array('name' => 'app_version', 'type' => 'text', 'max' => '10', 'size' => '10');		

			} else {

				// The status stuff
				$fields[] = array('name' => 'status', 'type' => 'static', 'value' => '', 'varx' => 'hide');
				$fields[] = array('name' => 'active', 'type' => 'static', 'value' => '', 'varx' => 'hide');
				$fields[] = array('name' => '/status', 'type' => 'static', 'value' => '', 'varx' => 'hide');


				// App version - just display it
				$fields[] = array('name' => 'app_version', 'type' => 'display');

			}
			

			// Run throught the parser and spit out the page
			require_once CLASSES_DIR.'Profile.php';
			$profile = new Profile($fields);
			
			if ( $id > 0 ) $this->output($profile->display($this->dataKey, $id));
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
				
				// Delete the tracking info for this app
				$sql = "DELETE FROM tracking WHERE app_id = '$id'";
				$db->Execute($sql);

				// Delete the application to content links
				$sql = "DELETE FROM applications_to_content WHERE app_id = '$id'";
				$db->Execute($sql);

				// Delete the application itself
				$sql = "DELETE FROM ".$this->table->id." WHERE $this->dataKey = '$id' LIMIT 1";
				$this->db->Execute($sql);
				
				select_app(DEFAULT_APP_ID);
				
				$obj = array('error' => false, 'redirect' => manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;
				
			} else {
				
				echo "[link]".delete(__CLASS__,$id)."[/link]";
				echo "This will remove the application and all it's tracking information from the system.<br /><br />";
				echo "<strong>This action cannot be undone.</strong><br />";
				exit;
				
			}
			
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Publishes an application's XML feeds
		//
		//////////////////////////////////////////////////////////////////
		
		public function publish($id) {
		
			// App info
			$sql = "SELECT * FROM applications WHERE app_id = '".(int)$id."' LIMIT 1";
			$app = $this->db->Execute($sql);

			// Just as a precaution, ignore if the app_id doesn't match anything in the system
			if ( !$app->EOF ) {

				// Update the xml version and write a new xml file
				$newVersion = floatval($app->fields['xml_version']) + 0.1;
				$app->fields['xml_version'] = $newVersion;

				$status = write_xml($app->fields);
				if ( !$status->error ) {

					// Place new version in the db
					$sql = "UPDATE applications SET xml_version = '$newVersion' WHERE app_id = '$appID' LIMIT 1";
					$this->db->Execute($sql);
					
					// SHOW SUCCESS PAGE
					
				} else {
					
					// SHOW ERROR PAGE
					
				}
				
			}
			
		}
		
	}
          

?>