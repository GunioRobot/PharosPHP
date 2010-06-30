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
		date_default_timezone_set(Settings::get("system.timezone"));
		
		load_content_types();
		Settings::load_dynamic_system_settings();
		load_automatic_modules();
						
		$CURRENT_APP_ID = session("app_id", 1);
				
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
	
		$upload_dir = Settings::get("filesystem.upload_directory");
		if ( $upload_dir[0] == "/" ) {
			define('UPLOAD_DIR', $upload_dir);
		} else {
			define('UPLOAD_DIR', APPLICATION_DIR.$upload_dir);
		}
		
		define('XML_DIR', APPLICATION_DIR.Settings::get("filesystem.xml_directory"));
	
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Server Path Information
		//
		////////////////////////////////////////////////////////////////////////////////

		define('APPLICATION_SERVER', HTTP_SERVER.'application/');
		define('SYSTEM_SERVER', HTTP_SERVER.'system/');

		define('PUBLIC_SERVER', HTTP_SERVER.'public/');		
				
		if ( $upload_dir[0] == "/" ) {
			define('UPLOAD_SERVER', HTTP_SERVER.substr($upload_dir, strpos($upload_dir, APP_PATH) + strlen(APP_PATH)));
		} else {
			define('UPLOAD_SERVER', APPLICATION_SERVER.$upload_dir);
		}
		
		define('XML_SERVER', APPLICATION_SERVER.Settings::get("filesystem.xml_directory"));
		define('CACHE_SERVER', SYSTEM_SERVER.'cache/');
		define('MODULES_SERVER', APPLICATION_SERVER.'modules/');


		////////////////////////////////////////////////////////////////////////////////
		//
		//	System software settings
		//
		////////////////////////////////////////////////////////////////////////////////

		define('SECURE_KEYWORD',md5(Settings::get('system.site.name')));
		define('APPLICATION_SECRET_KEY', md5(Settings::get('system.site.name')));
		
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