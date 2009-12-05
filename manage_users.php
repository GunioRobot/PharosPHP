<?php

	define('TABLE', 'users');
	define('ID', 'user_id');
	
	if ( !isset($_GET['table_id']) OR (isset($_GET['table_id']) AND $_GET['table_id'] == TABLE) ) {

		$PROFILE_PID = 5;
		$TYPE = 'User'; 
		
		$TRACKING = $_GET['user_tracking'] == 'true' ? true : false;
//		$INDUSTRY = ($_GET['indus'] != '' AND $_GET['indus'] != 'all') ? '&indus='.$_GET['indus'] : '';
			
		$rows_per_page = 25;
	
		$table_info['table_id'] = TABLE;
		$table_info['table_class'] = 'list';
		$table_info['head_class'] = 'contentTitleBar';
		$table_info['pid'] = $_GET['pid'];
		$table_info['extra_href'] = '';
		$table_info['columns'] = array();
		
		if ( $TRACKING ) {
//			$table_info['extra_href'] .= $INDUSTRY;
		}
		
		$userLevels = user_levels_array(SUPER_LVL);
		
		$table_info['columns'][] = array('name' => 'ID', 'key' => ID,  'class' => 'listCheckBox center');
		$table_info['columns'][] = array('name' => 'Name', 'key' => 'full_name');
		$table_info['columns'][] = array('name' => 'Email',  'key' => 'user_primary_email', 'class' => 'center');
		$table_info['columns'][] = array('name' => 'Level',  'key' => 'user_level', 'class' => 'center');
									
		if ( $TRACKING ) {
//			$table_info['columns'][] = array('name' => 'Industry',  'key' => 'user_industry', 'class' => 'center');
		} else {
			$table_info['columns'][] = array('name' => 'Last Login', 'key' => 'user_last_login', 'class' => 'center');
		}
		
		$table_info['columns'][] = array('name' => 'Action', 'class' => 'actions');
	
		$table_info['data'] = array();
		
		
		// Check what page we're viewing
		if ( isset($_GET[$table_info['table_id'].'_page']) AND $_GET[$table_info['table_id'].'_page'] != '' ) 
			$page = intval($_GET[$table_info['table_id'].'_page']);
		else $page = 1;
		
		
		
		// Build the WHERE part of the query
		if ( ($search = get("search")) ) {
			$where = basic_where($search, TABLE) . ' AND '.TABLE.'.user_level <= "'.SECURITY_LVL.'"';
			$table_info['extra_href'] .= "&search=$search";
		} else $where = " WHERE ".TABLE.".user_level <= '".SECURITY_LVL."'";
				
		

		$where .= " AND ".TABLE.".user_level >= '".ADMIN_LVL."' ";
		$join = "";
		$group = '';
		$count_group = '';
		$order = '';
		$appID = '';
		
		
		// Check to see if we're ordering
		$order = '';
		$defaultField = $TRACKING ? "full_name" : "user_last_login";
		$defaultOrder = $TRACKING ? "asc" : "desc";
		$table_info['ordered_row'] = get("order_field", $defaultField);
		$table_info['order'] = get("order", $defaultOrder);
		foreach($table_info['columns'] as $c){
			if ( $c['key'] == 'full_name' ) {
				$order = ' ORDER BY '.$table_info['ordered_row'].' '.$table_info['order'];
				break;
			} else if ( $c['key'] == $_GET['order_field'] ) {
				$order = ' ORDER BY '.TABLE.'.'.$table_info['ordered_row'].' '.$table_info['order'];
				break;
			}
		}	
				
		// Stuff for pagination later
		$sql = "SELECT COUNT(".TABLE.'.'.ID.") as total ".$appID." FROM ".TABLE." ".$join.$where.$count_group;
		$total = $db->Execute($sql);		
		$total = $total->fields['total'] != '' ? $total->fields['total'] : '0';
		$page_count = intval(ceil($total/$rows_per_page));
		$page_count = $page_count > 0 ? $page_count : 1;
		$start = ($page-1) * $rows_per_page;


		// Actually pull the information.  Will be ordered and/or filtered, or just straight results if nothing used
		$sql = "SELECT *, CONCAT(".TABLE.".user_first_name, ' ', ".TABLE.".user_last_name) as full_name FROM ".TABLE." ".$join.$where.$group.$order." LIMIT ".$start.",".$rows_per_page;
		$info = $db->Execute($sql);
		
	
		
		
		// Build the data array to pass to the table
		for ( $i = 1; !$info->EOF; $i++ ) {
		
			$key = '&key='.ID.'&'.ID.'='.$info->fields[ID];
		
			$class = ( $i % 2 ) ? 'listTier1' : 'listTier2';
			$row = array('class' => $class, 'data' => array());
				
				
			$row['data'][] = $info->fields[ID];
			$row['data'][] = format_title($info->fields['full_name']);
			$row['data'][] = '<a href="mailto:'.$info->fields['user_primary_email'].'" title="Email &quot;'.htmlentities($info->fields['full_name']).'&quot;">'.$info->fields['user_primary_email'].'</a>';
			
			
			$row['data'][] = $userLevels[$info->fields['user_level']];
	
			$lastLogin = format_date($info->fields['user_last_login'],true);
			$lastLogin = $lastLogin == '' ? '&nbsp;' : $lastLogin;
			$row['data'][] = $lastLogin;
			
			
			$actions = '<a href="index.php?pid='.$PROFILE_PID.$key.'" title="Edit this Account">Edit</a>';
			$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
			$actions .= '<a href="repost.php?pid='.$_GET['pid'].'&action=delete&type=user'.$key.'" title="Delete this Account">Delete</a>';
			$row['data'][] = $actions;
		
			$table_info['data'][] = $row;
			$info->moveNext();
		}
		
		
		
		// Build our table
		require CLASSES_DIR.'Table.php';
		$table = new Table($table_info);
					
		echo '<div class="titleTabs">
	            <h1 id="page_title">Manage Administrators</h1>
	            <form id="'.$table_info['table_id'].'_form" action="index.php" method="get">
	            <input type="hidden" name="pid" value="'.$_GET['pid'].'"/>
	            <input type="hidden" name="table_id" value="'.$table_info['table_id'].'"/>
	            <input type="hidden" name="user_tracking" value="'.$_GET['user_tracking'].'"/>
	            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$_GET['search'].'"/><a href="#" onClick="$('."'".'#'.$table_info['table_id'].'_form'."'".').submit();" class="inputPress">Search</a></div>';
	
	
			if ( $TRACKING ) {
				$search = $_GET['search'] != '' ? '&search='.$_GET['search'] : '';
				echo '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid=32" title="Email Users" class="tabArrow">Send Email</a></div>';
			} else {
				echo '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid='.$PROFILE_PID.'" title="Create New '.$TYPE.'" class="tabAdd">Add</a></div>';
			}
	
	        echo '</form>
	            <br clear="all" />
	          </div>'.
	          $table->get_html($page,$page_count, $start, $total);
	}	          


?>