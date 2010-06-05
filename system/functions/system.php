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
		load_static_settings();
		
		Hooks::call_hook(Hooks::HOOK_SYSTEM_PRE_BOOTSTRAP);
		
		load_content_types();
		load_dynamic_system_settings();
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

		define('SYSTEM_DIR', SERVER_DIR.APP_PATH.'system/');
		define('APPLICATION_DIR', SERVER_DIR.APP_PATH.'application/');
		
		define('CLASSES_DIR', SYSTEM_DIR.'classes/');
		define('FUNCTIONS_DIR', SYSTEM_DIR.'functions/');
		define('UPLOAD_DIR', APPLICATION_DIR.Settings::get("filesystem.upload_directory"));
		define('XML_DIR', APPLICATION_DIR.Settings::get("filesystem.xml_directory"));
		define('CACHE_DIR', SYSTEM_DIR.'cache/');	
		
		define('VIEWS_DIR', APPLICATION_DIR.'views/');	
		define('LAYOUTS_DIR', APPLICATION_DIR.'layouts/');
		define('MODELS_DIR', APPLICATION_DIR.'models/');
		define('CONFIGURE_DIR', APPLICATION_DIR.'configure/');
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Server Path Information
		//
		////////////////////////////////////////////////////////////////////////////////

		define('APPLICATION_SERVER', HTTP_SERVER.'application/');
		define('SYSTEM_SERVER', HTTP_SERVER.'system/');

		define('PUBLIC_SERVER', HTTP_SERVER.'public/');
		define('UPLOAD_SERVER', APPLICATION_SERVER.Settings::get("filesystem.upload_directory"));
		define('XML_SERVER', APPLICATION_SERVER.Settings::get("filesystem.xml_directory"));
		define('CACHE_SERVER', SYSTEM_SERVER.'cache/');



		////////////////////////////////////////////////////////////////////////////////
		//
		//	File System Information
		//
		////////////////////////////////////////////////////////////////////////////////

		define('CONTROLLER_DIR', APPLICATION_DIR.'controllers/');
		define('MODULES_DIR', APPLICATION_DIR.'modules/');
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