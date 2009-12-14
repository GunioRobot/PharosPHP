<?php

	require_once CLASSES_DIR.'TableController.php';

	class Dashboard extends TableController {
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Called once, in index.php (to create the controller)
		//
		//////////////////////////////////////////////////////////////////
		
		public function __construct() {
			
			parent::__construct("User","users");
						
			$this->dataKey = "user_id";			
			$this->tableColumns();
						
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Overriding default redirect to manage() function
		//
		//////////////////////////////////////////////////////////////////
		
		public function index() {
			
			global $CURRENT_APP_NAME, $CURRENT_APP_ID;
			
			Controller::loadModule("fusion_charts");
			
			$this->css("dashboard.css");
			
			$template = get_template("dashboard.html", "views/");
					
			$template = str_replace('{app_name}', "&quot;".$CURRENT_APP_NAME."&quot;", $template);
			$template = str_replace('{INCLUDES_DIR}', INCLUDES_SERVER, $template);

			$template = str_replace('{ACTIVITY_CHART}', include_fusion_chart_js().renderChart(fusion_chart("FCF_Area2D.swf"), $strURL, controller_link(__CLASS__,"data/"), "activity_chart", 569, 350), $template);

/*

			// Find the top 5 downloads
			$html = '';
			$sql = "SELECT downloads.*, t1.hits FROM downloads JOIN ( SELECT COUNT(track_id) as hits, table_index FROM tracking WHERE content_type_id = ".DOWNLOAD_TYPE_ID." AND app_id = '".$CURRENT_APP_ID."' GROUP BY content_type_id,table_index) t1 ON t1.table_index = downloads.download_id ORDER BY t1.hits DESC LIMIT 5";
			for ( $rr = $this->db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {

				$title = format_title($rr->fields['download_name']);
				$key = '&key=download_id&download_id='.$rr->fields['download_id'];
				$html .= '<li><a href="index.php?pid=65'.$key.'" title="Edit &quot;'.$title.'&quot;">'.truncate_str($title,15,'...').' ('.$rr->fields['hits'].')</a></li>';
			} 

			// Replace in the template
			$html = ( $html != '' ) ? $html : "<li><em>No Downloads.</em></li>";
			$template = str_replace('[top_downloads]', $html, $template);

*/

	
			
			$this->output($template);
			
		}
		
		
		public function data() {
		
			$timePeriod = 10;
			$previous = new DateTime();
			$previous->modify('-'.$timePeriod.' days');

			$xml = "<graph caption='Application Activity' subcaption='(".$previous->format('m/d/Y')." to ".date("m/d/Y").")' hovercapbg='FFECAA' hovercapborder='F47E00' formatNumberScale='0' decimalPrecision='0' showvalues='0' numdivlines='3' numVdivlines='0' yaxisminvalue='1000' yaxismaxvalue='1800'  rotateNames='1'>";


			for ( $i = 0; $i <= $timePeriod; $i++, $previous->modify('+1 day') ) {
				$xml .= "<set name='".$previous->format('m/d/Y')."' value='".rand(1000,5000)."' />";
			}

			$xml .= "</graph>";

			printXML($xml);
			
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

					$row['data'][] = $info->fields['user_first_name'] . ' ' .$info->fields['user_last_name'];
					$row['data'][] = '<a href="mailto:'.$info->fields['user_primary_email'].'">'.$info->fields['user_primary_email'].'</a>';
					$row['data'][] = format_date($info->fields['date_added'],true);
					$row['data'][] = format_date($info->fields['last_updated'],true);

					$actions = '<a href="'.edit(__CLASS__,$id).'" title="Edit this '.$this->type.'">Edit</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a href="'.delete(__CLASS__,$id).'" title="Delete this '.$this->type.'">Delete</a>';

					$row['data'][] = $actions;

					$data[] = $row;
				}

			} $this->table->data = $data;
			
		}
		
		
	
		//////////////////////////////////////////////////////////////////
		//
		//	Take default searching, but make sure user is only basic level
		//
		//////////////////////////////////////////////////////////////////
		
		public function search($search) {
			
			$where = parent::search($search);
			
			if ( $this->table->search != "" ) {
				$where .= " AND ".$this->table->id.".user_level <= '".(int)BASIC_USER_LVL."'";
			} else {
				$where = " WHERE ".$this->table->id.".user_level <= '".(int)BASIC_USER_LVL."'";
			} return $where;
			
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
		            <h1 id="page_title">User Tracking</strong></h1>
		            <form id="'.$this->table->id.'_form" action="'.$this->table->basic_a."$orderField/$orderVal/$page/".'" method="post">
		            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$this->table->search.'"/><a href="#" onClick="$('."'".'#'.$this->table->id.'_form'."'".').submit();" class="inputPress">Search</a></div>';

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
				
				array('name' => 'user_level', 'type' => 'dropdown', 'option' => user_levels_array($id>0?level_for_user($id):SUPER_LVL), 'default' => ADMIN_LVL),
	
				array('name' => 'date_added', 'type' => 'date_added'),
				array('name' => 'last_updated', 'type' => 'last_updated'),
				array('name' => 'content_type_id', 'type' => 'hidden', 'value' => USER_TYPE_ID),
				array('name' => 'content_type_name', 'type' => 'hidden', 'value' => strtolower(PROFILE_TITLE))

			);
			

			// Run throught the parser and spit out the page
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