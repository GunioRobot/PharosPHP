<?php

	/**
	 * DashboardController
	 *
	 * @package PharosPHP.Application.Controllers
	 * @author Matt Brewer
	 **/
	
	class DashboardController extends TableController {
				
		
		//////////////////////////////////////////////////////////////////
		//
		//	Called once, in index.php (to create the controller)
		//
		//////////////////////////////////////////////////////////////////
		
		public function __construct() {
			
			parent::__construct("User","users");
						
			$this->dataKey = "user_id";			
			$this->tableColumns();
			$this->load->module("fusion_charts");
															
		}
		
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Overriding default redirect to manage() function
		//
		//////////////////////////////////////////////////////////////////
		
		public function index() {
			
			global $CURRENT_APP_NAME, $CURRENT_APP_ID;
			
			$this->title = "Application Dashboard";
						
			$this->output->css("dashboard.css");
			$this->output->javascript("dashboard.js");
			include_fusion_chart_js();
						
			
			// Find the top 5 uesrs
			$html = '';
			$sql = "SELECT users.*, CONCAT(users.user_first_name, ' ', users.user_last_name) as full_name, t1.hits FROM users JOIN( SELECT user_id, app_id, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' GROUP BY user_id ) t1 ON t1.user_id = users.user_id ORDER BY t1.hits DESC LIMIT 5";			
			for ( $rr = $this->db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {

				$title = $rr->fields['full_name'];
				$link = Template::edit('Dashboard',$rr->fields['user_id']);
				$html .= '<li><a href="'.$link.'" title="Edit &quot;'.$title.'&quot;">'.truncate_str($title,15,'...').' ('.$rr->fields['hits'].')</a></li>';
			} 
			
			// Replace in the template
			$html = ( $html != '' ) ? $html : "<li><em>No Users.</em></li>";
			$this->output->set("top_users", $html);

			/*
			// Find the top 5 downloads
			$html = '';
			$sql = "SELECT products.*, t1.hits FROM products JOIN ( SELECT COUNT(track_id) as hits, table_index FROM tracking WHERE content_type_id = ".PRODUCT_TYPE_ID." AND app_id = '".$CURRENT_APP_ID."' GROUP BY content_type_id,table_index) t1 ON t1.table_index = products.id ORDER BY t1.hits DESC LIMIT 5";
			for ( $rr = $this->db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {

				$title = format_title($rr->fields['title']);
				$link = Template::edit('ApplicationEntries',$rr->fields['id']);
				$html .= '<li><a href="'.$link.'" title="Edit &quot;'.$title.'&quot;">'.truncate_str($title,15,'...').' ('.$rr->fields['hits'].')</a></li>';
			} 

			// Replace in the template
			$html = ( $html != '' ) ? $html : "<li><em>No Entries.</em></li>";
			$this->output->set("top_items", $html);
			*/
			
			$this->output->set("title", $this->title);
			$this->output->view("dashboard.php");
			
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
		
		
		public function content($table='users-top10', $content_type_id='all', $limit="0,10") {
			
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

				$top_whatever_dropdown = '<select id="paginate"><option value="0-10" ';
				if ( $ll == "0,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>Top 10</option>';

				if ( $total_users->fields['total'] > 10 ) $top_whatever_dropdown .= '<option value="10-10"';
				if ( $ll == "10,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>11 - 20</option>';

				if ( $total_users->fields['total'] > 20 ) $top_whatever_dropdown .= '<option value="20-10"';
				if ( $ll == "20,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>21 - 30</option>';

				if ( $total_users->fields['total'] > 30 ) $top_whatever_dropdown .= '<option value="30-10"';
				if ( $ll == "30,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>31 - 40</option>';

				if ( $total_users->fields['total'] > 40 ) $top_whatever_dropdown .= '<option value="40-10"';
				if ( $ll == "40,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>41 - 50</option>';

				$top_whatever_dropdown .= '</select>';		

				// Final sql for information
				$sql = "SELECT users.*, CONCAT(users.user_first_name, ' ', users.user_last_name) as full_name, t1.hits FROM users".$join.$industry.$order." LIMIT ".$limit;			


				// The USA Map for user location
				echo '<embed width="550" height="400" flashvars="xml='.urlencode(Template::controller_link(__CLASS__, 'map_demographic_data/')).'" wmode="transparent" allowscriptaccess="always" quality="high" name="usa_map" id="usa_map" src="'.PUBLIC_URL.'swf/usa_map.swf" type="application/x-shockwave-flash"/>';


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
						<td><div class="floatLeft"><a href="'.Template::edit('Dashboard',$rr->fields['user_id']).'" title="Edit &quot;'.$userName.'&quot;">'.$userName.'</a></div><br /><div class="floatLeft">';
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


				// $sql = "SELECT * FROM $table JOIN ( SELECT tracking.*, COUNT(tracking.track_id) as hits FROM tracking JOIN ( SELECT users.user_id FROM users JOIN ( SELECT * FROM applications_to_content WHERE app_id = '$CURRENT_APP_ID' AND content_type_id = '".USER_TYPE_ID."') t4 ON t4.table_index = users.user_id )  t1 ON t1.user_id = tracking.user_id WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY tracking.app_id,tracking.content_type_id,tracking.table_index ) t2 ON t2.table_index = $table.$id ORDER BY t2.hits DESC LIMIT $limit";
				$sql = "SELECT * FROM $table JOIN ( SELECT tracking.*, COUNT(tracking.track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY tracking.app_id,tracking.content_type_id,tracking.table_index ) t2 ON t2.table_index = $table.$id ORDER BY t2.hits DESC LIMIT $limit";
				// die($sql);

				// For selecting the right <option></option>
				$ll = $limit;


				// Build the pagination dropdown
				// $pagination_sql = "SELECT COUNT($table.$id) as total, t1.app_id FROM ".$table." JOIN (SELECT t2.* FROM users JOIN( SELECT tracking.*, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY app_id,content_type_id,table_index ) t2 ON t2.user_id = users.user_id ) t1 ON t1.table_index = ".$table.".".$id."  GROUP BY t1.app_id ORDER BY t1.hits DESC";/
				$pagination_sql = "SELECT COUNT($table.$id) as total, t1.app_id, t1.hits FROM ".$table." JOIN ( SELECT tracking.*, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY app_id,content_type_id,table_index ) t1 ON t1.table_index = ".$table.".".$id."  GROUP BY t1.app_id ORDER BY t1.hits DESC";
				// die($pagination_sql);
				$total_users = $this->db->Execute($pagination_sql);	// don't limit!
		
				$top_whatever_dropdown = '<select id="paginate"><option value="0-10" ';
				if ( $ll == "0,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>Top 10</option>';

				if ( $total_users->fields['total'] > 10 ) $top_whatever_dropdown .= '<option value="10-10"';
				if ( $ll == "10,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>11 - 20</option>';

				if ( $total_users->fields['total'] > 20 ) $top_whatever_dropdown .= '<option value="20-10"';
				if ( $ll == "20,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>21 - 30</option>';

				if ( $total_users->fields['total'] > 30 ) $top_whatever_dropdown .= '<option value="30-10"';
				if ( $ll == "30,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>31 - 40</option>';

				if ( $total_users->fields['total'] > 40 ) $top_whatever_dropdown .= '<option value="40-10"';
				if ( $ll == "40,10" ) $top_whatever_dropdown .= ' selected="selected"';
				$top_whatever_dropdown .= '>41 - 50</option>';

				$top_whatever_dropdown .= '</select>';			

				// The gorgeous area chart
				echo renderChart(fusion_chart("FCF_Area2D.swf"), encodeDataURL(Template::controller_link(__CLASS__,"activity-data/10/$content_type_id/")), "", "activity_chart", 569, 350);
				

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
			$this->table->columns[] =  array('name' => 'Company', 'key' => 'user_company', 'class' => 'center');
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
					
					$row['data'][] = $info->fields['user_company'] == "" ? "<em>None Provided</em>" : truncate_str($info->fields['user_company'], 35);
					
					$active = $info->fields['user_is_active'] === "true";
					// $row['data'][] = '<a class="confirm-with-popup" href="'.Template::controller_link(__CLASS__, sprintf('%s/%d/', ($active?'deactivate':'activate'), $id)).'" title="'.($active?'Disable':'Enable').' this user"><img src="'.PUBLIC_URL.'images/'. ($active ? 'icon_checkWT.gif' : 'icon_blankWT.gif') .'" /></a>';
					$row['data'][] = '<img src="'.PUBLIC_URL.'images/'. ($active ? 'icon_checkWT.gif' : 'icon_blankWT.gif') .'" />';
					$row['data'][] = format_date($info->fields['last_updated'],true);

					$actions = '<a href="'.Template::edit(__CLASS__,$id).'" title="Edit this '.$this->type.'">Edit</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					
					if ( $info->fields['user_is_active'] === "true" ) {
						$actions .= '<a class="confirm-with-popup" href="'.Template::controller_link(__CLASS__,"deactivate/$id/").'" title="Deactivate this '.$this->type.'">Deactivate</a>';
					} else {
						$actions .= '<a class="confirm-with-popup" href="'.Template::controller_link(__CLASS__,"activate/$id/").'" title="Activate this '.$this->type.'">&nbsp;Activate&nbsp;</a>';
					}
					
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a href="'.sprintf(Template::controller_link(__CLASS__,"track/%d/"),$id).'" title="View tracking information for this '.$this->type.'">Trends</a>';
					$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
					$actions .= '<a class="confirm-with-popup" href="'.Template::delete(__CLASS__,$id).'" title="Delete this '.$this->type.'">Delete</a>';
					

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
				$where .= " AND ".$this->table->id.".user_level <= '".(int)Settings::get('application.users.levels.basic')."'";
			} else {
				$where = " WHERE ".$this->table->id.".user_level <= '".(int)Settings::get('application.users.levels.basic')."'";
			} return $where;
			
		}
	
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Lists out the table of settings
		//
		//////////////////////////////////////////////////////////////////

		public function manage($orderField='last_updated',$orderVal='desc',$page=1,$filter='') {
			
			$this->output->javascript('export_users.php');
												
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
		            <h1 id="page_title"><strong>User Tracking</strong></h1>
		            <form id="'.$this->table->id.'_form" action="'.$this->table->basic_a."$orderField/$orderVal/$page/".'" method="post">
		            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$this->table->search.'"/><a href="#" onClick="$('."'".'#'.$this->table->id.'_form'."'".').submit();" class="inputPress">Search</a></div>';

			$view .= '<div class="contentTabCap"></div><div class="contentTab"><a id="export-users" href="'.Template::controller_link(__CLASS__,'export-users/').'" title="Export Users" class="tabArrow">Export</a></div>';
				

		    $view .= '</form>
		            <br clear="all" />
		          </div>'.
		          $this->table->get_html($this->table->current_page, $page_count, $start, $total);

			$this->output->view($view);

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
				array('name' => '{Tracking Link}', 'type' => 'static', 'value' => Template::controller_link(__CLASS__,sprintf('track/%d/', $id))),
				array('name' => '{form_link}', 'type' => 'static', 'value' => Template::save(__CLASS__,$id)),
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
				// array('name' => 'user_industry', 'type' => 'dropdown', 'option' => $this->industries),
				
				array('name' => 'user_is_active', 'type' => 'checkbox', 'checkvalue' => 'true'),
				
				array('name' => 'date_added', 'type' => 'date_added'),
				array('name' => 'last_updated', 'type' => 'last_updated'),
				array('name' => 'content_type_id', 'type' => 'hidden', 'value' => USER_TYPE_ID),
				array('name' => 'content_type_name', 'type' => 'hidden', 'value' => strtolower(PROFILE_TITLE))

			);
			

			// Run throught the parser and spit out the page
			$profile = new Profile($fields);
			
			if ( $id > 0 ) echo $profile->display($this->dataKey, $id, $repost);
			else $profile->display();
						
		}
		
		
		
		public function track($id) {
			
			global $CURRENT_APP_ID;
			
			$this->output->css("dashboard.css");
						
			// User information
			$sql = sprintf("SELECT * FROM users WHERE user_id = '%d' LIMIT 1", $id);
			$info = $this->db->Execute($sql);
			$info->fields['full_name'] = $info->fields['user_first_name'].' '.$info->fields['user_last_name'];
			$user = clean_object($info->fields);
			
			$sql = sprintf("SELECT COUNT(t.track_id) as launches FROM tracking t WHERE t.user_id = '%d' AND t.app_id = '%d' AND t.content_type_id = '%d' GROUP BY t.user_id", $id, $CURRENT_APP_ID, APPLICATION_TYPE_ID);
			$info = $this->db->Execute($sql);
			$launches = $info->fields['launches'] > 0 ? $info->fields['launches'] : 0;
			
			// Tracking infromation
			$sql = sprintf("SELECT * FROM tracking t, views v WHERE t.content_type_id = '%d' AND t.user_id = '%d' AND t.app_id = '%d' AND v.id = t.table_index ORDER BY t.timestamp DESC LIMIT 75", VIEW_TYPE_ID, $id, $CURRENT_APP_ID);
			for ( $track = array(), $info = $this->db->Execute($sql); !$info->EOF; $info->moveNext() ) {				
				$track[] = clean_object($info->fields);
			}
			
			$this->output->set("track", $track);
			$this->output->set("launches", $launches);
			$this->output->set("user", $user);
			
			$this->output->view("user-tracking-view.php");
			
		}
		
		
		public function setStatus($id, $status='true') {
			
			if ( !in_array($status, array('true','false')) ) {
				$status = 'true';
			}
			
			$sql = sprintf("UPDATE `users` SET user_is_active = '%s' WHERE user_id = %d LIMIT 1", $status, $id);
			$this->db->Execute($sql);
			
			redirect(Template::manage(__CLASS__));
			
		}
		
		

		public function exportUsers() {
			
			if ( ($month = Input::post("date")) !== false && $month !== "all-data" ) {
				$date = new DateTime($month);
				$where = "AND MONTH(date_added) = '".$date->format("m")."' AND YEAR(date_added) = '".$date->format("Y")."'";
			} 
			
			$filename = Settings::get('application.system.site.name').' User Export '.date('m-d-Y H:i a');
			
			$fields = array(
				"user_last_name" => "Last Name",
				"user_first_name" => "First Name",
				"user_address_line_1" => "Address",
				"user_state" => "State",
				"user_zip" => "Zip Code",
				"user_company" => "Company",
				"user_phone_number" => "Phone",
				"user_primary_email" => "Email",
				"registered_ip_address" => "Registered IP",
				"date_added" => "Date Registered"
			);
			
			$output = implode(', ', array_values($fields))."\n";

			$sql = "SELECT * FROM users WHERE user_level = '".(int)Settings::get('application.users.levels.basic')."' $where ORDER BY user_last_name,user_first_name";
			for ( $info = $this->db->Execute($sql); !$info->EOF; $info->moveNext() ) {
				
				$values = array();
				foreach($fields as $key => $display) {
				
					$val = $info->fields[$key];
					
					$values[] = csv_data($val);
										
				} 
				
				$output .= implode(",", $values) . "\n";
			
			}
			
			force_download($filename, $output);
			exit;
			
		}
		
		
		//////////////////////////////////////////////////////////////////
		//
		//	Deletes an entry, used in conjunction with "confirmDelete"
		//	javascript jAlert stuff
		//
		//////////////////////////////////////////////////////////////////
		
		public function delete($id) {
			
			if ( ($confirmed = Input::post("confirmed")) === "true" ) {
				
				// Delete tracking information
				$sql = "DELETE FROM tracking WHERE content_type_id = '".(int)USER_TYPE_ID."' AND table_index = '".(int)$id."'";
				$this->db->Execute($sql);
				
				// Delete application to content links
				$sql = "DELETE FROM applications_to_content WHERE content_type_id = '".(int)USER_TYPE_ID."' AND table_index = '".(int)$id."'";
				$this->db->Execute($sql);

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
		
		
		
		public function deactivate($id) {
			
			
			if ( ($confirmed = Input::post("confirmed")) === "true" ) {
				
				$sql = "UPDATE users SET user_is_active = 'false', last_updated = NOW() WHERE user_id = '".(int)$id."' LIMIT 1";
				$this->db->Execute($sql);
				
				$obj = array('error' => false, 'redirect' => Template::manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;

			} else {
				
				echo "[link]".Template::controller_link(__CLASS__,"/deactivate/$id/")."[/link]";
				echo "<strong>This user will be deactivated.</strong><br /><br />";
				echo "They will no longer be able to receive updates to the application.<br />";
				exit;
				
			}

			
		}
		
		
		public function activate($id) {


			if ( ($confirmed = Input::post("confirmed")) === "true" ) {

				$sql = "UPDATE users SET user_is_active = 'true', last_updated = NOW() WHERE user_id = '".(int)$id."' LIMIT 1";
				$this->db->Execute($sql);

				$obj = array('error' => false, 'redirect' => Template::manage(__CLASS__));
				echo json_encode((object)$obj);
				exit;

			} else {

				echo "[link]".Template::controller_link(__CLASS__,"/activate/$id/")."[/link]";
				echo "<strong>This user will be re-activated.</strong><br /><br />";
				echo "You are enabling application updates for this user.<br />";
				exit;

			}


		}
		
		public function demographicData() {
			
		
			global $CURRENT_APP_ID;
		
			// Grand total of activity for this application
			$total = $this->db->Execute("SELECT COUNT(track_id) as hits FROM tracking WHERE app_id = '".$CURRENT_APP_ID."'");
			$total = (!$total->EOF AND $total->fields['hits'] != '' ) ? intval($total->fields['hits']) : 0;
			
			$states = array();

			// State specific data
			// $sql = "SELECT tracking.app_id, COUNT(tracking.track_id) as hits, t2.user_state FROM tracking JOIN ( SELECT users.user_id, users.user_state FROM users JOIN ( SELECT table_index FROM applications_to_content WHERE content_type_id = '".(int)USER_TYPE_ID."' AND app_id = '".$CURRENT_APP_ID."' ) t1 ON t1.table_index = users.user_id ) t2 ON t2.user_id = tracking.user_id AND tracking.app_id = '".$CURRENT_APP_ID."' GROUP BY t2.user_state ORDER BY t2.user_state ASC";
			$sql = "SELECT tracking.app_id, COUNT(tracking.track_id) as hits, t2.user_state FROM tracking JOIN ( SELECT users.user_id, users.user_state FROM users WHERE users.user_level = '".(int)Settings::get('application.users.levels.basic')."' ) t2 ON t2.user_id = tracking.user_id AND tracking.app_id = '".$CURRENT_APP_ID."' GROUP BY t2.user_state ORDER BY t2.user_state ASC";
			for ( $rr = $this->db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {

				// $sql = "SELECT DISTINCT users.user_id FROM users JOIN ( SELECT * FROM applications_to_content WHERE content_type_id = '".(int)USER_TYPE_ID."' AND app_id = '".$CURRENT_APP_ID."' ) t1 ON t1.table_index = users.user_id WHERE user_state = '".$rr->fields['user_state']."' AND users.user_level = '".Settings::get('application.users.levels.basic')."'";
				$sql = "SELECT DISTINCT users.user_id FROM users WHERE user_state = '".$rr->fields['user_state']."' AND users.user_level = '".Settings::get('application.users.levels.basic')."'";
				
				$u = $this->db->Execute($sql);
				$s['users'] = $u->RecordCount();

				$s['hits'] = $rr->fields['hits'];
				$states[$rr->fields['user_state']] = $s;
			}

			responseXML("false", "", $dom, $root);
			$stateNames = array_flip(states_array());

			foreach($stateNames as $short => $long) {

				$percent = $total > 0 ? number_format(round($states[$short]['hits'] / $total, 2),2) : 0;
				// var_dump($percent, round($states[$short]['hits'] / $total, 2));

				$el = $dom->createElement("state");
				$name = $dom->createElement("name");
				$activity = $dom->createElement("activity");
				$percentEl = $dom->createElement("percent");
				$users = $dom->createElement("users");

				$name->appendChild($dom->createCDATASection(strtolower(str_replace(' ','_',$long))));
				$activity->appendChild($dom->createCDATASection($states[$short]['hits']));
				$percentEl->appendChild($dom->createCDATASection($percent));
				$users->appendChild($dom->createCDATASection($states[$short]['users']));

				$el->appendChild($name);
				$el->appendChild($activity);
				$el->appendChild($percentEl);
				$el->appendChild($users);

				$root->appendChild($el);

			} 
			// exit;

			printXML($dom->saveXML());
		}
		
	}
          

?>