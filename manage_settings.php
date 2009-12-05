<?php

	define('TABLE', 'general_settings');
	define('ID', 'setting_id');

	if ( !isset($_GET['table_id']) OR (isset($_GET['table_id']) AND $_GET['table_id'] == TABLE) ) {

		$PROFILE_PID = 59;
		$TYPE = 'Setting'; 
	
		$rows_per_page = 25;
	
		$table_info['table_id'] = TABLE;
		$table_info['table_class'] = 'list';
		$table_info['head_class'] = 'contentTitleBar';
		$table_info['pid'] = $_GET['pid'];
		$table_info['extra_href'] = '';
		
		$table_info['columns'] = array();
		$table_info['columns'][] = array('name' => 'Name', 'key' => 'setting_name', 'class' => 'center');
		$table_info['columns'][] = array('name' => 'Date Added', 'key' => 'date_added', 'class' => 'center');
		$table_info['columns'][] = array('name' => 'Last Updated', 'key' => 'last_updated', 'class' => 'center');
		$table_info['columns'][] = array('name' => 'Action', 'class' => 'actions');
		$table_info['data'] = array();
		
		
		// Check what page we're viewing
		if ( isset($_GET[$table_info['table_id'].'_page']) AND $_GET[$table_info['table_id'].'_page'] != '' ) 
			$page = intval($_GET[$table_info['table_id'].'_page']);
		else $page = 1;
		
		
		
	
		// Build the search
		if ( ($search = get("search")) ) {
			$where = generic_search(TABLE, $search);
			$table_info['extra_href'] .= '&search='.$_GET['search'];
		} else $where = " ";

		
		
						
		// Check to see if we're ordering
		$order = '';
		$table_info['ordered_row'] = get("order_field", "last_updated");
		$table_info['order'] = get("order", "desc");
		foreach($table_info['columns'] as $c) {
			if ( $c['key'] == $table_info['ordered_row'] ) {
				$order = ' ORDER BY '.TABLE.'.'.$table_info['ordered_row'].' '.$table_info['order'];
			}
		}
	

		// Stuff for pagination later
		$total = $db->Execute("SELECT COUNT(".TABLE.'.'.ID.") as total FROM ".TABLE." ".$where.$order);
		$total = $total->fields['total'] != '' ? $total->fields['total'] : '0';
		$page_count = intval(ceil($total/$rows_per_page));
		$page_count = $page_count > 0 ? $page_count : 1;
		$start = ($page-1) * $rows_per_page;

	

		// Actually pull the information.  Will be ordered and/or filtered, or just straight results if nothing used
		$sql = "SELECT * FROM ".TABLE." ".$where.$order." LIMIT ".$start.",".$rows_per_page;
		$info = $db->Execute($sql);
		
		// Build the data array to pass to the table
		for ( $i = 1; !$info->EOF; $i++ ) {
		
			$key = '&key='.ID.'&'.ID.'='.$info->fields[ID];
		
			$class = ( $i % 2 ) ? 'listTier1' : 'listTier2';
			$row = array('class' => $class, 'data' => array());
				
			$row['data'][] = format_title($info->fields['setting_name']);
			$row['data'][] = format_date($info->fields['date_added'],true);
			$row['data'][] = format_date($info->fields['last_updated'],true);
						
						
			$actions = '<a href="index.php?pid='.$PROFILE_PID.$key.'" title="Edit this '.$TYPE.'">Edit</a>';
			
			if ( is_super() ) {
				$actions .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
				$actions .= '<a href="repost.php?pid='.$_GET['pid'].'&action=delete&type='.strtolower($TYPE).$key.'" title="Delete this '.$TYPE.'">Delete</a>';
			}
			
			
			$row['data'][] = $actions;
		
			$table_info['data'][] = $row;
			$info->moveNext();
		}
		
		
		
		// Build our table
		require CLASSES_DIR.'Table.php';
		$table = new Table($table_info);
		
		echo '<div class="titleTabs">
	            <h1 id="page_title">Manage '.$TYPE.'s</strong></h1>
	            <form id="'.$table_info['table_id'].'_form" action="index.php" method="get">
	            <input type="hidden" name="pid" value="'.$_GET['pid'].'"/>
	            <input type="hidden" name="table_id" value="'.$table_info['table_id'].'"/>
	            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$_GET['search'].'"/><a href="#" onClick="$('."'".'#'.$table_info['table_id'].'_form'."'".').submit();" class="inputPress">Search</a></div>';
	
			if ( is_super() ) {
				echo '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid='.$PROFILE_PID.'" title="Create New '.$TYPE.'" class="tabAdd">Add</a></div>';
			}
	            
	       echo '</form>
	            <br clear="all" />
	          </div>'.
	          $table->get_html($page,$page_count, $start, $total);
	}	          

?>