<?php

	define('DEFAULT_APP_ID', 1);
	$CURRENT_APP_ID = DEFAULT_APP_ID;
	$CURRENT_APP_NAME = "Some App";
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	app_bootstrap()
	//
	//	Loads the current application into the system
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function bootstrap_system() {
		
		global $db, $CURRENT_APP_ID, $CURRENT_APP_NAME;
		load_static_settings();
		
		Hooks::call_hook(Hooks::HOOK_SYSTEM_PRE_BOOTSTRAP);
		
		load_content_types();
		load_dynamic_system_settings();
		load_defines();
		load_automatic_modules();
						
		$CURRENT_APP_ID = session("app_id", DEFAULT_APP_ID);
				
		$title = $db->Execute("SELECT app_name FROM applications WHERE app_id = '$CURRENT_APP_ID' LIMIT 1");
		$CURRENT_APP_NAME = format_title($title->fields['app_name']);
		
		Hooks::call_hook(Hooks::HOOK_SYSTEM_POST_BOOTSTRAP);
				
	}	
	
	
	function load_static_settings() {
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Main Site Information
		//	
		////////////////////////////////////////////////////////////////////////////////
		$host = ( isset($_SERVER['REDIRECT_HTTPS']) && $_SERVER['REDIRECT_HTTPS'] == "on" ) ? "https://" : "http://";
		define('HTTP_SERVER', $host.$_SERVER['HTTP_HOST'].'/'.APP_PATH);



		////////////////////////////////////////////////////////////////////////////////
		//
		//	Basic File System Stuff
		//
		////////////////////////////////////////////////////////////////////////////////

		define('INCLUDES_DIR', SERVER_DIR.ADMIN_DIR.'includes/');
		define('CLASSES_DIR', INCLUDES_DIR.'classes/');
		define('FUNCTIONS_DIR', INCLUDES_DIR.'functions/');
		define('UPLOAD_DIR', SERVER_DIR.ADMIN_DIR.Settings::get("upload_directory"););
		define('XML_DIR', SERVER_DIR.ADMIN_DIR.Settings::get("xml_directory"););
		define('DEFINES_DIR', INCLUDES_DIR.'defines/');
		
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