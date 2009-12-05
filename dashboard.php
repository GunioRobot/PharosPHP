<?php

	// Standard page load
	if ( request('json', 'false') === "false" ) {
	
		$template = get_template('dashboard.html', '/views/');
		$template = str_replace('{app_name}', "&quot;".$CURRENT_APP_NAME."&quot;", $template);
		$template = str_replace('{INCLUDES_DIR}', INCLUDES_SERVER, $template);
		
		/*
		// Find the top 5 photos
		$html = '';
		$sql = "SELECT photos.*, t1.hits FROM photos JOIN ( SELECT COUNT(track_id) as hits, table_index FROM tracking WHERE content_type_id = ".PHOTO_TYPE_ID." AND app_id = '".$CURRENT_APP_ID."' GROUP BY content_type_id,table_index) t1 ON t1.table_index = photos.photo_id ORDER BY t1.hits DESC LIMIT 5";
		for ( $rr = $db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {

			$title = format_title($rr->fields['photo_name']);
			$key = '&key=photo_id&photo_id='.$rr->fields['photo_id'];
			$html .= '<li><a href="index.php?pid=25'.$key.'" title="Edit &quot;'.$title.'&quot;">'.truncate_str($title,15,'...').' ('.$rr->fields['hits'].')</a></li>';
		} 

		// Replace in the template
		$html = ( $html != '' ) ? $html : "<li><em>No Photos.</em></li>";
		$template = str_replace('[top_photos]', $html, $template);
		*/
		

		echo $template;
		
	} 
	
	// Ajax request
	else {
		
		require_once 'includes/app_header.php';
		
		/*

		// Top 10 table for users
		if ( !isset($_GET['table']) OR (isset($_GET['table']) AND $_GET['table'] == 'users-top10') ) {
			
			// Get an array of industry names for use later
			$industries = array('0' => 'None Specified');
			for ( $rr = $db->Execute("SELECT * FROM user_industries ORDER BY industry_name ASC"); !$rr->EOF; $rr->moveNext() ) {
				$industries[$rr->fields['industry_id']] = format_title($rr->fields['industry_name']);
			}
			

			// Optional filters on the user information
//			$industry = ($_GET['industry'] != '' AND $_GET['industry'] != 'all') ? " WHERE contacts.contact_industry = '".$_GET['industry']."' " : '';
			$industry = '';

			$content_type = ($_GET['content_type_id'] != '' AND $_GET['content_type_id'] != 'all') ? " AND content_type_id = '".$_GET['content_type_id']."' " : '';

			// Build the sql
			$join = " JOIN( SELECT contact_id, app_id, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY contact_id ) t1 ON t1.contact_id = contacts.contact_id ";
			$order = " ORDER BY t1.hits DESC ";

			// For selecting the right <option></option>
			$ll = get("limit", "0,9");

			// Build the pagination dropdown
			$total_users = $db->Execute("SELECT COUNT(contacts.contact_id) as total, t1.hits, t1.app_id FROM contacts".$join.$industry." GROUP BY t1.app_id  ".$order);	// don't limit!
		
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

			$limit = "LIMIT ".get("limit", 10);

			// Final sql for information
			$sql = "SELECT contacts.*, CONCAT(contacts.contact_first_name, ' ', contacts.contact_last_name) as full_name, t1.hits FROM contacts".$join.$industry.$order.$limit;			
						
			// Table header
			echo '<h2>Top Users</h2>'.$top_whatever_dropdown.'&nbsp;'.content_type_dropdown($_GET['content_type_id']).'<div class="clearBoth"></div>
			<table cellpadding="0" cellspacing="0">
				<tr class="dashboard-table-header-row dashboard-table-row">
					<th class="dashboard-table-header number">&nbsp;</th>
					<th class="dashboard-table-header name">Name</th>
					<th class="dashboard-table-header company">Email</th>
					<th class="dashboard-table-header industry">Phone</th>
					<th class="dashboard-table-header state-zip">State / Zip</th>
				</tr>';
			
			// Loop out the user information
			for ( $rr = $db->Execute($sql), $index = 1; !$rr->EOF; $rr->moveNext(), $index++ ) {
				
				$contactName = format_title($rr->fields['full_name']);
				
				echo '<tr class="dashboard-table-row">
					<td class="number">'.$rr->fields['hits'].'</td>
					<td><div class="floatLeft"><a href="index.php?pid=5&key=contact_id&contact_id='.$rr->fields['contact_id'].'" title="Edit &quot;'.$contactName.'&quot;">'.$contactName.'</a></div><br /><div class="floatLeft">';
					echo '<br /></div><div class="clearBoth"></div></td>';
				
					echo '<td>';
					if ( $rr->fields['contact_primary_email'] != '' ) {
						echo '<a href="mailto:'.$rr->fields['contact_primary_email'].'" title="Email '.$contactName.'">'.$rr->fields['contact_primary_email'].'</a>';

					} else echo '<a href="#" onClick="return false;" title="No Email">no email</a>';
					echo '</td>';
				
					echo '<td>'.format_title($rr->fields['contact_phone_number']).'</td>'.
					'<td class="state-zip">'.strtoupper($rr->fields['contact_state']).' - '.$rr->fields['contact_zip'].'</td>
				</tr>';
			} echo '</table>';	// Close the table
		}
		
		
		
		
		
		
		
		
		// Top 10 table for content
		else if ( isset($_GET['table']) AND $_GET['table'] == 'content-top10' ) {			

			// Optional filters on the user information
//			$industry = ($_GET['industry'] != '' AND $_GET['industry'] != 'all') ? " WHERE contacts.contact_industry = '".$_GET['industry']."' " : '';
			$industry = '';


			$_GET['content_type_id'] = ( !isset($_GET['content_type_id']) OR $_GET['content_type_id'] == NULL ) ? APPLICATION_TYPE_ID : $_GET['content_type_id'];
			$content_type = ($_GET['content_type_id'] != '' AND $_GET['content_type_id'] != 'all') ? " AND tracking.content_type_id = '".$_GET['content_type_id']."' " : " AND tracking.content_type_id = '".APPLICATION_TYPE_ID."' ";

			// Build the sql
			$limit = "LIMIT ".get("limit", 10);
						
			
			if ( $_GET['content_type_id'] == SONG_TYPE_ID ) {
				$table = 'songs';
				$id = 'song_id';
				$TYPE = 'Song';
				$name = 'song_name';
				$notes = 'song_notes';
				$pid = '?pid=22&key=song_id&song_id=';
			} else if ( $_GET['content_type_id'] == PHOTO_TYPE_ID ) {
				$table = 'photos';
				$id = 'photo_id';
				$TYPE = 'Photo';
				$name = 'photo_name';
				$notes = 'photo_notes';
				$pid = '?pid=25&key=photo_id&photo_id=';
			} else if ( $_GET['content_type_id'] == VIDEO_TYPE_ID ) {
				$table = 'videos';
				$id = 'video_id';
				$TYPE = 'Video';
				$name = 'video_name';
				$notes = 'video_notes';
				$pid = '?pid=36&key=video_id&video_id=';
			} else if ( $_GET['content_type_id'] == APPLICATION_TYPE_ID ) {
				$table = "applications";
				$id = "app_id";
				$TYPE = "Application";
				$name = "app_name";
				$notes = "app_notes";
				$pid = "";
			} else if ( $_GET['content_type_id'] == EVENT_TYPE_ID ) {
				$table = "events";
				$id = "event_id";
				$TYPE = "Event";
				$name = "event_name";
				$notes = "event_notes";
				$pid = "?pid=40&key=event_id&event_id=";
			}
			
			$sql = "SELECT * FROM $table JOIN ( SELECT tracking.*, COUNT(tracking.track_id) as hits FROM tracking JOIN ( SELECT contacts.contact_id FROM contacts JOIN ( SELECT * FROM applications_to_contacts WHERE app_id = '$CURRENT_APP_ID') t4 ON t4.contact_id = contacts.contact_id $industry)  t1 ON t1.contact_id = tracking.contact_id WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY tracking.app_id,tracking.content_type_id,tracking.table_index ) t2 ON t2.table_index = $table.$id ORDER BY t2.hits DESC $limit";
			
			// For selecting the right <option></option>
			$ll = get("limit", "0,9");

			// Build the pagination dropdown
			$total_users = $db->Execute("SELECT COUNT($table.$id) as total, t1.app_id FROM ".$table." JOIN (SELECT * FROM contacts JOIN( SELECT tracking.*, COUNT(track_id) as hits FROM tracking WHERE tracking.app_id = '$CURRENT_APP_ID' $content_type GROUP BY app_id,content_type_id,table_index ) t2 ON t2.contact_id = contacts.contact_id $industry ) t1 ON t1.table_index = ".$table.".".$id."  GROUP BY t1.app_id ORDER BY t1.hits DESC ");	// don't limit!
					
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
			

			// Table header
			echo '<h2>Top Content</h2>'.$top_whatever_dropdown.'&nbsp;'.content_type_dropdown($_GET['content_type_id'], false).'<div class="clearBoth"></div>
			<table cellpadding="0" cellspacing="0">
				<tr class="dashboard-table-header-row dashboard-table-row">
					<th class="dashboard-table-header number">&nbsp;</th>
					<th class="dashboard-table-header content-name">Name</th>';
					
					if ( $_GET['content_type_id'] != APPLICATION_TYPE_ID ) echo '<th class="dashboard-table-header size">Size</th>';
					
					echo '<th class="dashboard-table-header notes">Notes</th>
				</tr>';
			
			// Loop out the user information
			for ( $rr = $db->Execute($sql), $index = 1; !$rr->EOF; $rr->moveNext(), $index++ ) {
				echo '<tr class="dashboard-table-row">
					<td class="number">'.$rr->fields['hits'].'</td>';
					
					if ( $pid != '' ) {
						echo '<td><div class="floatLeft"><a href="index.php'.$pid.$rr->fields[$id].'" title="Edit '.$TYPE.'">'.truncate_str(format_title($rr->fields[$name]),60,'...').'</a>';
					} else echo '<td><div class="floatLeft">'.truncate_str(format_title($rr->fields[$name]), 60,'...');
					
					echo '</div><br /></div><div class="clearBoth"></div></td>';
					
					if ( $_GET['content_type_id'] != APPLICATION_TYPE_ID ) echo '<td>'.format_filesize($rr->fields['file_size']).'</td>';
					
					if ( strlen(strip_tags($rr->fields[$notes])) > 0 ) {
						$n = truncate_str(strip_tags($rr->fields[$notes]),200,'...');
					} else $n = '<em>None</em>';
					
					if ( $_GET['content_type_id'] == APPLICATION_TYPE_ID ) echo '<td style="width:315px;">'.$n.'</td>';
					else echo '<td>'.$n.'</td>';
					
				echo '</tr>';
			} echo '</table>';	// Close the table
						
		}
		
		*/
		
	}
		
?>