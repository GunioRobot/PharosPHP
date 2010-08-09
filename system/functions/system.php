<?php
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	app_bootstrap()
	//
	//	Loads the current application into the system
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function bootstrap_system() {
		
		global $db, $CURRENT_APP_ID, $CURRENT_APP_NAME;
		
		Hooks::call_hook(Hooks::HOOK_SYSTEM_PRE_BOOTSTRAP);
		
		// Set the system timezone
		date_default_timezone_set(Settings::get("application.system.timezone"));
		
		load_content_types();
		Settings::load_dynamic_system_settings();
		load_automatic_modules();
						
		$CURRENT_APP_ID = session("app_id", 1);
				
		$title = $db->Execute("SELECT app_name FROM applications WHERE app_id = '$CURRENT_APP_ID' LIMIT 1");
		$CURRENT_APP_NAME = format_title($title->fields['app_name']);
		
		Hooks::call_hook(Hooks::HOOK_SYSTEM_POST_BOOTSTRAP);
				
	}	
	

	
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