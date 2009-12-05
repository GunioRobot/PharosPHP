
	<? 
	
		$pid = get("pid");

		$sql = "SELECT * FROM admin_nav WHERE id = '$pid' LIMIT 1";
		$page = $db->Execute($sql);
		
		if ( !$page->EOF ) {
			
			if ( ($function = get("function")) ) {
				 require INCLUDES_DIR.$function.".php";
			} else { 
				if ( file_exists($page->fields['page']) ) require $page->fields['page'] ; 
			}
			
		}
		
	?>