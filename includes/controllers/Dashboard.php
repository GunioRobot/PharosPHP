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
						
			$this->css("dashboard.css");
			$this->javascript("dashboard.php");
			
			$template = get_template("dashboard.html", "views/");
					
			$template = str_replace('{app_name}', "&quot;".$CURRENT_APP_NAME."&quot;", $template);
			$template = str_replace('{INCLUDES_DIR}', INCLUDES_SERVER, $template);

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
		
		
		public function activityData() {
		
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
		
		
		public function content($table='users-top10', $content_type_id='all', $limit="0,9") {
			
			global $CURRENT_APP_ID, $CURRENT_APP_NAME;
			Controller::loadModule("fusion_charts");
			
			
			// Top 10 table for users
			if ( $table == "users-top10" ) {
				
				$content_type = ( $content_type_id != '' && $content_type_id != 'all' ) ? " AND content_type_id = '".(int)$content_type_id."' " : '';

				// Build the sql
				$join = " JOIN( SELECT user_id, app_id, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY user_id ) t1 ON t1.user_id = users.user_id ";
				$order = " ORDER BY t1.hits DESC ";

				// Build the pagination dropdown
				$total_users = $this->db->Execute("SELECT COUNT(users.user_id) as total, t1.hits, t1.app_id FROM users".$join." GROUP BY t1.app_id  ".$order);	// don't limit!

				$ll = $limit;

				$top_whatever_dropdown = '<select id="paginate"><option value="0,9" ';
				if ( $ll == "0,9" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>Top 10</option>';

				if ( $total_users->fields['total'] > 10 ) $top_whatever_dropdown .= '<option value="10,19"';
				if ( $ll == "10,19" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>11 - 20</option>';

				if ( $total_users->fields['total'] > 20 ) $top_whatever_dropdown .= '<option value="20,29"';
				if ( $ll == "20,29" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>21 - 30</option>';

				if ( $total_users->fields['total'] > 30 ) $top_whatever_dropdown .= '<option value="30,39"';
				if ( $ll == "30,39" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>31 - 40</option>';

				if ( $total_users->fields['total'] > 40 ) $top_whatever_dropdown .= '<option value="40,49"';
				if ( $ll == "40,49" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>41 - 50</option>';

				$top_whatever_dropdown .= '</select>';			

				// Final sql for information
				$sql = "SELECT users.*, CONCAT(users.user_first_name, ' ', users.user_last_name) as full_name, t1.hits FROM users".$join.$industry.$order." LIMIT ".$limit;			


				// The USA Map for user location
				echo '<embed width="550" height="400" flashvars="" wmode="transparent" allowscriptaccess="always" quality="high" name="usa_map" id="usa_map" src="'.TEMPLATE_SERVER.'usa_map.swf" type="application/x-shockwave-flash"/>';


				// Table header
				echo '<h2>Top Users</h2>'.$top_whatever_dropdown.'&nbsp;'.content_type_dropdown($content_type_id).'<div class="clearBoth"></div>
				<table cellpadding="0" cellspacing="0">
					<tr class="dashboard-table-header-row dashboard-table-row">
						<th class="dashboard-table-header number">&nbsp;</th>
						<th class="dashboard-table-header name">Name</th>
						<th class="dashboard-table-header company">Email</th>
						<th class="dashboard-table-header industry">Phone</th>
						<th class="dashboard-table-header state-zip">State / Zip</th>
					</tr>';

				// Loop out the user information
				for ( $rr = $this->db->Execute($sql), $index = 1; !$rr->EOF; $rr->moveNext(), $index++ ) {

					$userName = format_title($rr->fields['full_name']);

					echo '<tr class="dashboard-table-row">
						<td class="number">'.$rr->fields['hits'].'</td>
						<td><div class="floatLeft"><a href="index.php?pid=5&key=user_id&user_id='.$rr->fields['user_id'].'" title="Edit &quot;'.$userName.'&quot;">'.$userName.'</a></div><br /><div class="floatLeft">';
						echo '<br /></div><div class="clearBoth"></div></td>';

						echo '<td>';
						if ( $rr->fields['user_primary_email'] != '' ) {
							echo '<a href="mailto:'.$rr->fields['user_primary_email'].'" title="Email '.$userName.'">'.$rr->fields['user_primary_email'].'</a>';

						} else echo '<a href="#" onClick="return false;" title="No Email">no email</a>';
						echo '</td>';

						echo '<td>'.format_title($rr->fields['user_phone_number']).'</td>'.
						'<td class="state-zip">'.strtoupper($rr->fields['user_state']).' - '.$rr->fields['user_zip'].'</td>
					</tr>';
				} echo '</table>';	// Close the table
			}








			// Top 10 table for content
			else if ( $table == 'content-top10' ) {			

				$content_type_id = ( !isset($content_type_id) || $content_type_id == NULL ) ? APPLICATION_TYPE_ID : $content_type_id;
				$content_type = ($content_type_id != '' && $content_type_id != 'all') ? " AND tracking.content_type_id = '".$content_type_id."' " : " AND tracking.content_type_id = '".APPLICATION_TYPE_ID."' ";

				// Build the sql
				switch ( $content_type_id ) {
					
					case SONG_TYPE_ID:
						$table = 'songs';
						$id = 'song_id';
						$TYPE = 'Song';
						$name = 'song_name';
						$notes = 'song_notes';
						$pid = '?pid=22&key=song_id&song_id=';
						break;
						
					case PHOTO_TYPE_ID:
						$table = 'photos';
						$id = 'photo_id';
						$TYPE = 'Photo';
						$name = 'photo_name';
						$notes = 'photo_notes';
						$pid = '?pid=66&key=photo_id&photo_id=';
						break;
				
					case VIDEO_TYPE_ID:
						$table = 'videos';
						$id = 'video_id';
						$TYPE = 'Video';
						$name = 'video_name';
						$notes = 'video_notes';
						$pid = '?pid=67&key=video_id&video_id=';
						break;				
						
					case DOWNLOAD_TYPE_ID:
						$table = "downloads";
						$id = "download_id";
						$TYPE = "Download";
						$name = "download_name";
						$notes = "download_notes";
						$pid = "?pid=65&key=download_id&download_id=";
						break;
						
					case APPLICATION_TYPE_ID:
					default:
						$table = "applications";
						$id = "app_id";
						$TYPE = "Application";
						$name = "app_name";
						$notes = "app_notes";
						$pid = "";						
						break;
					
				}


				$sql = "SELECT * FROM $table JOIN ( SELECT tracking.*, COUNT(tracking.track_id) as hits FROM tracking JOIN ( SELECT users.user_id FROM users JOIN ( SELECT * FROM applications_to_content WHERE app_id = '$CURRENT_APP_ID' AND content_type_id = '".USER_TYPE_ID."') t4 ON t4.table_index = users.user_id )  t1 ON t1.user_id = tracking.user_id WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY tracking.app_id,tracking.content_type_id,tracking.table_index ) t2 ON t2.table_index = $table.$id ORDER BY t2.hits DESC LIMIT $limit";

				// For selecting the right <option></option>
				$ll = $limit;


				// Build the pagination dropdown
				$total_users = $this->db->Execute("SELECT COUNT($table.$id) as total, t1.app_id FROM ".$table." JOIN (SELECT t2.* FROM users JOIN( SELECT tracking.*, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY app_id,content_type_id,table_index ) t2 ON t2.user_id = users.user_id ) t1 ON t1.table_index = ".$table.".".$id."  GROUP BY t1.app_id ORDER BY t1.hits DESC ");	// don't limit!

				$top_whatever_dropdown = '<select id="paginate"><option value="0,9" ';
				if ( $ll == "0,9" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>Top 10</option>';

				if ( $total_users->fields['total'] > 10 ) $top_whatever_dropdown .= '<option value="10,19"';
				if ( $ll == "10,19" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>11 - 20</option>';

				if ( $total_users->fields['total'] > 20 ) $top_whatever_dropdown .= '<option value="20,29"';
				if ( $ll == "20,29" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>21 - 30</option>';

				if ( $total_users->fields['total'] > 30 ) $top_whatever_dropdown .= '<option value="30,39"';
				if ( $ll == "30,39" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>31 - 40</option>';

				if ( $total_users->fields['total'] > 40 ) $top_whatever_dropdown .= '<option value="40,49"';
				if ( $ll == "40,49" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>41 - 50</option>';

				$top_whatever_dropdown .= '</select>';
				

				// The gorgeous area chart
				echo include_fusion_chart_js().renderChart(fusion_chart("FCF_Area2D.swf"), encodeDataURL(controller_link(__CLASS__,"activityData/")), "", "activity_chart", 569, 350);
				

				// Table header
				echo '<h2>Top Content</h2>'.$top_whatever_dropdown.'&nbsp;'.content_type_dropdown($content_type_id, false).'<div class="clearBoth"></div>
				<table cellpadding="0" cellspacing="0">
					<tr class="dashboard-table-header-row dashboard-table-row">
						<th class="dashboard-table-header number">&nbsp;</th>
						<th class="dashboard-table-header content-name">Name</th>';

						if ( $content_type_id != APPLICATION_TYPE_ID ) echo '<th class="dashboard-table-header size">Size</th>';

						echo '<th class="dashboard-table-header notes">Notes</th>
					</tr>';

				// Loop out the user information
				for ( $rr = $this->db->Execute($sql), $index = 1; !$rr->EOF; $rr->moveNext(), $index++ ) {
					echo '<tr class="dashboard-table-row">
						<td class="number">'.$rr->fields['hits'].'</td>';

						if ( $pid != '' ) {
							echo '<td><div class="floatLeft"><a href="index.php'.$pid.$rr->fields[$id].'" title="Edit '.$TYPE.'">'.truncate_str(format_title($rr->fields[$name]),60,'...').'</a>';
						} else echo '<td><div class="floatLeft">'.truncate_str(format_title($rr->fields[$name]), 60,'...');

						echo '</div><br /></div><div class="clearBoth"></div></td>';

						if ( $content_type_id != APPLICATION_TYPE_ID ) echo '<td>'.format_filesize($rr->fields['file_size']).'</td>';

						if ( strlen(strip_tags($rr->fields[$notes])) > 0 ) {
							$n = truncate_str(strip_tags($rr->fields[$notes]),200,'...');
						} else $n = '<em>None</em>';

						if ( $content_type_id == APPLICATION_TYPE_ID ) echo '<td style="width:315px;">'.$n.'</td>';
						else echo '<td>'.$n.'</td>';

					echo '</tr>';
				} echo '</table>';	// Close the table

			} 
			
			exit;	// Don't want system to start loading header, footer, etc as this is just an ajax response
			
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