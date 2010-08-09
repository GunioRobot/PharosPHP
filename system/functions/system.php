<?php
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	load_content_types() 
	//
	//	defines "USER_TYPE_ID" etc from the entries in the content_types_table
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function load_content_types() {
		
		global $db;
		
		$sql = "SELECT * FROM content_types ORDER BY type_id DESC";
		for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
			@define(strtoupper(str_replace(" ", "_", $info->fields['type_name'])).'_TYPE_ID', $info->fields['type_id']);
		}
		
	}
	


	////////////////////////////////////////////////////////////////////////////////
	//
	//	select_app($id)
	//
	//	Sets the id for the current application.  Must refresh page to call 
	//	app_bootstrap() after this
	//
	////////////////////////////////////////////////////////////////////////////////
	function select_app($id) {
				
		$CURRENT_APP_ID = $id;
		$_SESSION['app_id'] = $CURRENT_APP_ID;
						
	}
	
	
	


?>