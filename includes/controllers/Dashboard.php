<?php

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
			
			$this->industries = user_industries_array();
												
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Overriding default redirect to manage() function
		//
		//////////////////////////////////////////////////////////////////
		
		public function index() {
			
			global $CURRENT_APP_NAME, $CURRENT_APP_ID;
						
			$this->css("dashboard.css");
			$this->javascript("dashboard.js");
			
			$template = get_template("dashboard.html", "views/");
					
			$template = str_replace('{app_name}', "&quot;".$CURRENT_APP_NAME."&quot;", $template);
			$template = str_replace('{INCLUDES_DIR}', INCLUDES_SERVER, $template);
			
			
			// Find the top 5 uesrs
			$html = '';
			$sql = "SELECT users.*, CONCAT(users.user_first_name, ' ', users.user_last_name) as full_name, t1.hits FROM users JOIN( SELECT user_id, app_id, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' GROUP BY user_id ) t1 ON t1.user_id = users.user_id ORDER BY t1.hits DESC LIMIT 5";			
			for ( $rr = $this->db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {

				$title = $rr->fields['full_name'];
				$link = edit('Dashboard',$rr->fields['user_id']);
				$html .= '<li><a href="'.$link.'" title="Edit &quot;'.$title.'&quot;">'.truncate_str($title,15,'...').' ('.$rr->fields['hits'].')</a></li>';
			} 
			
			// Replace in the template
			$html = ( $html != '' ) ? $html : "<li><em>No Users.</em></li>";
			$template = str_replace('[top_users]', $html, $template);


			// Find the top 5 downloads
			$html = '';
			$sql = "SELECT products.*, t1.hits FROM products JOIN ( SELECT COUNT(track_id) as hits, table_index FROM tracking WHERE content_type_id = ".PRODUCT_TYPE_ID." AND app_id = '".$CURRENT_APP_ID."' GROUP BY content_type_id,table_index) t1 ON t1.table_index = products.id ORDER BY t1.hits DESC LIMIT 5";
			for ( $rr = $this->db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {

				$title = format_title($rr->fields['title']);
				$link = edit('ApplicationEntries',$rr->fields['id']);
				$html .= '<li><a href="'.$link.'" title="Edit &quot;'.$title.'&quot;">'.truncate_str($title,15,'...').' ('.$rr->fields['hits'].')</a></li>';
			} 

			// Replace in the template
			$html = ( $html != '' ) ? $html : "<li><em>No Entries.</em></li>";
			$template = str_replace('[top_items]', $html, $template);
			
			
			$this->output($template);
			
		}
		
		
		public function activityData($timePeriod=10,$content_type_id=APPLICATION_TYPE_ID) {
			
			global $CURRENT_APP_ID;
		
			$timePeriod = intval($timePeriod);
			$timePeriod = ($timePeriod > 5 && $timePeriod <= 50) ? $timePeriod : 10;
			
			if ( $content_type_id == APPLICATION_TYPE_ID ) {
				$title = "Application Launches";
				$where = " AND content_type_id = '".(int)$content_type_id."' ";
			} else if ( $content_type_id == PRODUCT_TYPE_ID ) {
				$title = "Products Viewed/Downloaded";
				$where = " AND content_type_id = '".(int)$content_type_id."' ";
			} else {
				$title = "All Application Activity";
				$where = "";
			}
			
			$previous = new DateTime();
			$previous->modify('-'.$timePeriod.' days');

			$data = array();
			$maxValue = 1;
			
			$sql = "SELECT COUNT(track_id) as hits, DATE(timestamp) as date FROM tracking WHERE CURDATE() >= DATE(DATE_SUB(timestamp, INTERVAL ".(int)$timePeriod." DAY)) AND app_id = '".(int)$CURRENT_APP_ID."' $where GROUP BY DATE(timestamp) ORDER BY timestamp ASC";
			for ( $info = $this->db->Execute($sql); !$info->EOF; $info->moveNext() ) {
				$data[$info->fields['date']] = $info->fields['hits'];
				if ( $info->fields['hits'] > $mavValue ) $maxValue = $info->fields['hits'];
			}
			
			$maxValue = ($maxValue % 5 > 0) ? $maxValue += (5-($maxValue % 5)) : $maxValue;
			$xml = "<graph caption='$title in Last ".$timePeriod." Days' subcaption='(".$previous->format('m/d/Y')." to ".date("m/d/Y").")' hovercapbg='99CCFF' hovercapborder='0033FF' formatNumberScale='0' decimalPrecision='0' showvalues='0' numdivlines='3' numVdivlines='0' yAxisMaxValue='".$maxValue."' rotateNames='1' areaBorderColor='0033FF'>";
			
			for ( $i = 0; $i <= $timePeriod; $previous->modify('+1 day'), $i++ ) {
				$value = isset($data[$previous->format('Y-m-d')]) ? $data[$previous->format('Y-m-d')] : 0;
				$xml .= "<set name='".$previous->format('m/d/Y')."' value='".$value."' color='6699FF'/>";
			} $xml .= "</graph>";

			printXML($xml);
			
		}
		
		
		public function content($table='users-top10', $content_type_id='all', $limit="0,9") {
			
			$limit = str_replace('-',',',$limit);	// Can't use comma in URL
			
			global $CURRENT_APP_ID, $CURRENT_APP_NAME;
			Modules::load("fusion_charts");
			
			// Top 10 table for users
			if ( $table == "users-top10" ) {
				
				$content_type = ( $content_type_id != '' && $content_type_id != 'all' ) ? " AND content_type_id = '".(int)$content_type_id."' " : '';

				// Build the sql
				$join = " JOIN( SELECT user_id, app_id, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY user_id ) t1 ON t1.user_id = users.user_id ";
				$order = " ORDER BY t1.hits DESC ";

				// Build the pagination dropdown
				$total_users = $this->db->Execute("SELECT COUNT(users.user_id) as total, t1.hits, t1.app_id FROM users".$join." GROUP BY t1.app_id  ".$order);	// don't limit!

				$ll = $limit;

				$top_whatever_dropdown = '<select id="paginate"><option value="0-9" ';
				if ( $ll == "0,9" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>Top 10</option>';

				if ( $total_users->fields['total'] > 10 ) $top_whatever_dropdown .= '<option value="10-19"';
				if ( $ll == "10,19" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>11 - 20</option>';

				if ( $total_users->fields['total'] > 20 ) $top_whatever_dropdown .= '<option value="20-29"';
				if ( $ll == "20,29" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>21 - 30</option>';

				if ( $total_users->fields['total'] > 30 ) $top_whatever_dropdown .= '<option value="30-39"';
				if ( $ll == "30,39" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>31 - 40</option>';

				if ( $total_users->fields['total'] > 40 ) $top_whatever_dropdown .= '<option value="40-49"';
				if ( $ll == "40,49" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>41 - 50</option>';

				$top_whatever_dropdown .= '</select>';			

				// Final sql for information
				$sql = "SELECT users.*, CONCAT(users.user_first_name, ' ', users.user_last_name) as full_name, t1.hits FROM users".$join.$industry.$order." LIMIT ".$limit;			


				// The USA Map for user location
				echo '<embed width="550" height="400" flashvars="xml='.urlencode(MODULES_SERVER.'corelabs/pages/map_demographic_data.php').'" wmode="transparent" allowscriptaccess="always" quality="high" name="usa_map" id="usa_map" src="'.TEMPLATE_SERVER.'usa_map.swf" type="application/x-shockwave-flash"/>';


				// Table header
				echo '<h2>Top Users</h2>'.$top_whatever_dropdown.'&nbsp;'.content_type_dropdown($content_type_id).'<div class="clearBoth"></div>
				<table cellpadding="0" cellspacing="0">
					<tr class="dashboard-table-header-row dashboard-table-row">
						<th class="dashboard-table-header number">&nbsp;</th>
						<th class="dashboard-table-header name">Name</th>
						<th class="dashboard-table-header company">Email</th>
						<th class="dashboard-table-header industry">Core Business</th>
						<th class="dashboard-table-header state-zip">State / Zip</th>
					</tr>';

				// Loop out the user information
				for ( $rr = $this->db->Execute($sql), $index = 1; !$rr->EOF; $rr->moveNext(), $index++ ) {

					$userName = format_title($rr->fields['full_name']);

					echo '<tr class="dashboard-table-row">
						<td class="number">'.$rr->fields['hits'].'</td>
						<td><div class="floatLeft"><a href="'.edit('Dashboard',$rr->fields['user_id']).'" title="Edit &quot;'.$userName.'&quot;">'.$userName.'</a></div><br /><div class="floatLeft">';
						echo '<br /></div><div class="clearBoth"></div></td>';

						echo '<td>';
						if ( $rr->fields['user_primary_email'] != '' ) {
							echo '<a href="mailto:'.$rr->fields['user_primary_email'].'" title="Email '.$userName.'">'.$rr->fields['user_primary_email'].'</a>';

						} else echo '<a href="#" onClick="return false;" title="No Email">no email</a>';
						echo '</td>';
						
						echo '<td>'.$this->industries[$rr->fields['user_industry']].'</td>';

						echo '<td class="state-zip">'.strtoupper($rr->fields['user_state']).' - '.$rr->fields['user_zip'].'</td>
					</tr>';
				} echo '</table>';	// Close the table
			}








			// Top 10 table for content
			else if ( $table == 'content-top10' ) {			


				$content_type_id = ( !isset($content_type_id) || $content_type_id == NULL ) ? APPLICATION_TYPE_ID : $content_type_id;
				$content_type = ($content_type_id != '' && $content_type_id != 'all') ? " AND tracking.content_type_id = '".$content_type_id."' " : " AND tracking.content_type_id = '".APPLICATION_TYPE_ID."' ";

				// Build the sql
				switch ( $content_type_id ) {
				
					case CATEGORY_TYPE_ID:
						$table = 'categories';
						$id = 'id';
						$TYPE = 'Category';
						$name = 'title';
						$notes = 'notes';
						break;				
						
					case PRODUCT_TYPE_ID:
						$table = "products";
						$id = "id";
						$TYPE = "Product";
						$name = "title";
						$notes = "path";
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

				$top_whatever_dropdown = '<select id="paginate"><option value="0-9" ';
				if ( $ll == "0,9" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>Top 10</option>';

				if ( $total_users->fields['total'] > 10 ) $top_whatever_dropdown .= '<option value="10-19"';
				if ( $ll == "10,19" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>11 - 20</option>';

				if ( $total_users->fields['total'] > 20 ) $top_whatever_dropdown .= '<option value="20-29"';
				if ( $ll == "20,29" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>21 - 30</option>';

				if ( $total_users->fields['total'] > 30 ) $top_whatever_dropdown .= '<option value="30-39"';
				if ( $ll == "30,39" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>31 - 40</option>';

				if ( $total_users->fields['total'] > 40 ) $top_whatever_dropdown .= '<option value="40-49"';
				if ( $ll == "40,49" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>41 - 50</option>';

				$top_whatever_dropdown .= '</select>';
				

				// The gorgeous area chart
				echo include_fusion_chart_js().renderChart(fusion_chart("FCF_Area2D.swf"), encodeDataURL(controller_link(__CLASS__,"activity-data/10/$content_type_id/")), "", "activity_chart", 569, 350);
				

				// Table header
				echo '<h2>Top Content</h2>'.$top_whatever_dropdown.'&nbsp;'.content_type_dropdown($content_type_id).'<div class="clearBoth"></div>';
				
				if ( $content_type_id != 'all' ) {
					
					echo 	'<table cellpadding="0" cellspacing="0">
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

						//	if ( $content_type_id != APPLICATION_TYPE_ID ) {
						//		echo '<td><div class="floatLeft"><a href="'.edit($table,$rr->fields[$id]).'" title="Edit '.$TYPE.'">'.truncate_str(format_title($rr->fields[$name]),60,'...').'</a>';
						//	} else 
							echo '<td><div class="floatLeft">'.truncate_str(format_title($rr->fields[$name]), 60,'...');

							echo '</div><br /></div><div class="clearBoth"></div></td>';

							if ( $content_type_id != APPLICATION_TYPE_ID ) echo '<td>'.format_filesize($rr->fields['download_path_file_size']).'</td>';

							if ( strlen(strip_tags($rr->fields[$notes])) > 0 ) {
								$n = truncate_str(strip_tags($rr->fields[$notes]),200,'...');
							} else $n = '<em>None</em>';

							if ( $content_type_id == APPLICATION_TYPE_ID ) echo '<td style="width:315px;">'.$n.'</td>';
							else echo '<td>'.$n.'</td>';

						echo '</tr>';
					} echo '</table>';	// Close the table
					
				}

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
			$this->table->columns[] =  array('name' => 'Industry', 'key' => 'user_industry', 'class' => 'center');
			$this->table->columns[] =  array('name' => 'Active', 'key' => 'user_is_active', 'class' => 'center');
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
					
					$industry = $this->industries[$info->fields['user_industry']];
					$industry = $industry == "" ? "<em>None Provided</em>" : $industry;
					$row['data'][] = $industry;
					
					
					$row['data'][] = '<img src="'.TEMPLATE_SERVER.'images/'. ($info->fields['user_is_active'] === "true" ? 'icon_checkWT.gif' : 'icon_blankWT.gif') .'" />';
					$row['data'][] = format_date($info->fields['last_updated'],true);

					$actions = '<a href="'.edit(__CLASS__,$id).'" title="Edit this '.$this->type.'">Edit</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					
					if ( $info->fields['user_is_active'] === "true" ) {
						$actions .= '<a class="confirm-with-popup" href="'.controller_link(__CLASS__,"deactivate/$id/").'" title="Deactivate this '.$this->type.'">Deactivate</a>';
					} else {
						$actions .= '<a class="confirm-with-popup" href="'.controller_link(__CLASS__,"activate/$id/").'" title="Activate this '.$this->type.'">&nbsp;Activate&nbsp;</a>';
					}
					
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a href="'.sprintf(controller_link(__CLASS__,"track/%d/"),$id).'" title="View tracking information for this '.$this->type.'">Trends</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a class="confirm-with-popup" href="'.delete(__CLASS__,$id).'" title="Delete this '.$this->type.'">Delete</a>';
					

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
			
			$this->javascript('export_users.php');
												
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

			$view .= '<div class="contentTabCap"></div><div class="contentTab"><a id="export-users" href="'.controller_link(__CLASS__,'export-users/').'" title="Export Users" class="tabArrow">Export</a></div>';
				

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
		
		public function edit($id, $repost=false) {
						
			$repost = ( $repost === "true" ) ? true : false;
			
			// Required by profile class and repost_mod
			@define('PROFILE_TABLE', $this->table->id);
			@define('PROFILE_TITLE', $this->type);
			@define('PROFILE_ID', $this->dataKey);
			@define('CURRENT_HTML_FILE', "edit_basic_user.html");
			
			$states = array_values(states_array());
			$states = array_combine($states, $states);

			// Template tags to pull from database and replace
			$fields = array(

				array('name' => PROFILE_ID, 'type' => 'display'),
				array('name' => '{TYPE}', 'type' => 'static', 'value' => PROFILE_TITLE),
				array('name' => '{Tracking Link}', 'type' => 'static', 'value' => controller_link(__CLASS__,'manage/')),
				array('name' => '{form_link}', 'type' => 'static', 'value' => save(__CLASS__,$id)),
				array('name' => '{data_key}', 'type' => 'static', 'value' => PROFILE_ID),
				
				array('name' => 'user_first_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				// array('name' => 'user_middle_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'mname'),
				array('name' => 'user_last_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_company', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_company_website', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_phone_number', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_fax_number', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_address_line_1', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_position', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_city', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'city'),
				array('name' => 'user_state', 'type' => 'dropdown', 'option' => $states, 'class' => 'state'),
				array('name' => 'user_zip', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'fname'),
				array('name' => 'user_username', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_password', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_primary_email', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_secondary_email', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
				array('name' => 'user_birthday', 'type' => 'dob'),
				array('name' => 'user_notes', 'type' => 'text_area', 'class' => 'notes'),
				array('name' => 'user_industry', 'type' => 'dropdown', 'option' => $this->industries),
				
				array('name' => 'user_is_active', 'type' => 'checkbox', 'checkvalue' => 'true'),
				
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
		
		
		
		public function track($id) {
			
			global $CURRENT_APP_ID;
			
			$this->css("dashboard.css");
			
			build_categories();
			
			// User information
			$sql = sprintf("SELECT * FROM users WHERE user_id = '%d' LIMIT 1", $id);
			$info = $this->db->Execute($sql);
			$info->fields['full_name'] = $info->fields['user_first_name'].' '.$info->fields['user_last_name'];
			$user = clean_object($info->fields);
			
			$sql = sprintf("SELECT COUNT(t.track_id) as launches FROM tracking t WHERE t.user_id = '%d' AND t.app_id = '%d' AND t.content_type_id = '%d' GROUP BY t.user_id", $id, $CURRENT_APP_ID, APPLICATION_TYPE_ID);
			$info = $this->db->Execute($sql);
			$launches = $info->fields['launches'] > 0 ? $info->fields['launches'] : 0;
			
			// Tracking infromation
			$sql = sprintf("SELECT * FROM tracking t, products p WHERE t.content_type_id = '%d' AND t.user_id = '%d' AND t.app_id = '%d' AND p.id = t.table_index ORDER BY t.timestamp DESC LIMIT 75", PRODUCT_TYPE_ID, $id, $CURRENT_APP_ID);
			for ( $track = array(), $info = $this->db->Execute($sql); !$info->EOF; $info->moveNext() ) {

				$product = clean_object($info->fields);
				
				$path = array_reverse(recursive_path($product));
				$path[] = $product->title;
				$product->path = implode("&nbsp;&raquo;&nbsp;", $path);
				
				$track[] = $product;
				
			}
			
			// Show the view
			require_once TEMPLATE_DIR.'views/user-tracking-view.php';
			
		}
		
		

		public function exportUsers() {
			
			if ( ($month = post("date")) !== false && $month !== "all-data" ) {
				$date = new DateTime($month);
				$where = "AND MONTH(date_added) = '".$date->format("m")."' AND YEAR(date_added) = '".$date->format("Y")."'";
			} 
			
			$filename = SITE_NAME.' User Export '.date('m-d-Y H:i a');
			
			$fields = array(
				"user_last_name" => "Last Name",
				"user_first_name" => "First Name",
				"user_address_line_1" => "Address",
				"user_state" => "State",
				"user_zip" => "Zip Code",
				"user_position" => "Title",
				"user_company" => "Company",
				"user_company_website" => "Company Website",
				"user_industry" => "Industry",
				"user_primary_email" => "Email",
				"user_type" => "Type",
				"date_added" => "Date Registered"
			);
			
			$output = implode(', ', array_values($fields))."\n";

			$sql = "SELECT * FROM users WHERE user_level = '".(int)BASIC_USER_LVL."' $where ORDER BY user_last_name,user_first_name";
			for ( $info = $this->db->Execute($sql); !$info->EOF; $info->moveNext() ) {
				
				$values = array();
				foreach($fields as $key => $display) {
				
					$val = $info->fields[$key];
					
					if ( $key == "user_industry" ) {
						$val = $this->industries[$val];
					} 
					
					$values[] = csv_data($val);
										
				} 
				
				$output .= implode(",", $values) . "\n";
			
			}
			
			csv_download($filename, $output);
			exit;
			
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
		
		
		
		public function deactivate($id) {
			
			
			if ( ($confirmed = post("confirmed")) === "true" ) {
				
				$sql = "UPDATE users SET user_is_active = 'false', last_updated = NOW() WHERE user_id = '".(int)$id."' LIMIT 1";
				$this->db->Execute($sql);
				
				$obj = array('error' => false, 'redirect' => manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;

			} else {
				
				echo "[link]".controller_link(__CLASS__,"/deactivate/$id/")."[/link]";
				echo "<strong>This user will be deactivated.</strong><br /><br />";
				echo "They will no longer be able to receive updates to the application.<br />";
				exit;
				
			}

			
		}
		
		
		public function activate($id) {


			if ( ($confirmed = post("confirmed")) === "true" ) {

				$sql = "UPDATE users SET user_is_active = 'true', last_updated = NOW() WHERE user_id = '".(int)$id."' LIMIT 1";
				$this->db->Execute($sql);

				$obj = array('error' => false, 'redirect' => manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;

			} else {

				echo "[link]".controller_link(__CLASS__,"/activate/$id/")."[/link]";
				echo "<strong>This user will be re-activated.</strong><br /><br />";
				echo "You are enabling application updates for this user.<br />";
				exit;

			}


		}
		
	}
          

?>