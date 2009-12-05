<?php

	define("TABLE", 'admin_nav');

	if ( !isset($_GET['table_id']) OR (isset($_GET['table_id']) AND $_GET['table_id'] == TABLE) ) {


		$PROFILE_PID = 3;
		$TYPE = 'Page'; 
	
		$rows_per_page = 25;
	
		$table_info['table_id'] = TABLE;
		$table_info['table_class'] = 'list';
		$table_info['head_class'] = 'contentTitleBar';
		$table_info['pid'] = $_GET['pid'];
		$table_info['extra_href'] = '';
		$table_info['columns'] = 
								array(
									array('name' => 'ID', 'key' =>'id',  'class' => 'listCheckBox center'),
									array('name' => 'Name', 'key' => 'name'),
									array('name' => 'Min Level', 'key' => 'min_lvl', 'class' => 'listCheckbox center'),
									array('name' => 'Max Level', 'key' => 'max_lvl', 'class' => 'listCheckbox center'),
									array('name' => 'Action', 'class' => 'actions')
								);
	
		$table_info['data'] = array();
		
		
		// Check what page we're viewing
		if ( isset($_GET[$table_info['table_id'].'_page']) AND $_GET[$table_info['table_id'].'_page'] != '' ) 
			$page = intval($_GET[$table_info['table_id'].'_page']);
		else $page = 1;
		
		
		
		// Build the search
		$table_info['extra_href'] .= '&search='.get("search", "");
		$where = ( $search = get("search") ) ? basic_where($search, TABLE) : " ";

		
		// Check to see if we're ordering
		$order = '';
		$table_info['ordered_row'] = get("order_field", "name");
		$table_info['order'] = get("order", "asc");
		foreach($table_info['columns'] as $c) {
			if ( $c['key'] == $table_info['ordered_row'] ) {
				$order = ' ORDER BY '.TABLE.'.'.$table_info['ordered_row'].' '.$table_info['order'];
			}
		}
			
		
				
		// Stuff for pagination later
		$total = $db->Execute("SELECT COUNT(id) as total FROM admin_nav ".$where.$order);
		$total = $total->fields['total'] != '' ? $total->fields['total'] : '0';
		$page_count = intval(ceil($total/$rows_per_page));
		$page_count = $page_count > 0 ? $page_count : 1;
		$start = ($page-1) * $rows_per_page;



		// Actually pull the information.  Will be ordered and/or filtered, or just straight results if nothing used
		$info = $db->Execute("SELECT * FROM admin_nav ".$where.$order." LIMIT ".$start.",".$rows_per_page);


		// Pull in the names of the security levels from table
		$sql = "SELECT * FROM user_levels";
		for ( $sLevels = $db->Execute($sql); !$sLevels->EOF; $sLevels->moveNext() ) {
			$securityLevels[$sLevels->fields['user_level_id']] = format_title($sLevels->fields['user_level_name']);
		}

		
		// Build the data array to pass to the table
		for ( $i = 1; !$info->EOF; $i++ ) {
		
			$key = '&key=id&id='.$info->fields['id'];

			$class = ( $i % 2 ) ? 'listTier1' : 'listTier2';
			$row = array('class' => $class, 'data' => array(
														$info->fields['id'],
														'<a href="index.php?pid='.$PROFILE_PID.$key.'">'.$info->fields['name'].'</a>',
														$securityLevels[$info->fields['min_lvl']],
														$securityLevels[$info->fields['max_lvl']],
														'<a href="index.php?pid='.$PROFILE_PID.$key.'">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;'.'<a href="repost.php?pid='.$_GET['pid'].'&action=delete&type=nav-item'.$key.'" title="Delete this Page">Delete</a>'
													));
		
			array_push($table_info['data'], $row);
			$info->moveNext();
		}
		
		
		
		// Build our table
		require CLASSES_DIR.'Table.php';
		$table = new Table($table_info);
	
				
		echo '<div class="titleTabs">
	            <h1 id="page_title">Manage Navigation</h1>
	            <form id="'.$table_info['table_id'].'_form" action="index.php" method="get">
	            <input type="hidden" name="pid" value="'.$_GET['pid'].'"/>
	            <input type="hidden" name="table_id" value="'.$table_info['table_id'].'"/>
	            <div class="contentTabCap"></div><div class="contentTab"><input id="search" name="search" value="'.$_GET['search'].'"/><a href="#" onClick="$('."'".'#'.$table_info['table_id'].'_form'."'".').submit();" class="inputPress">Search</a></div>
	            <div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid='.$PROFILE_PID.'" title="Create New '.$TYPE.'" class="tabAdd">Add</a></div>
	            </form>
	            <br clear="all" />
	          </div>'.
	          $table->get_html($page,$page_count, $start, $total);
	}	          

?>