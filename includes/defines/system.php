<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Server Path Information
	//
	////////////////////////////////////////////////////////////////////////////////
	
	define('INCLUDES_SERVER', HTTP_SERVER.ADMIN_DIR.'includes/');
	define('CLASSES_SERVER', INCLUDES_SERVER.'classes/');
	define('FUNCTIONS_SERVER', INCLUDES_SERVER.'functions/');
	define('TEMPLATE_SERVER', INCLUDES_SERVER.'templates/'.TEMPLATE_NAME.'/');
	define('UPLOAD_SERVER', HTTP_SERVER.ADMIN_DIR.UPLOAD_TO);
	define('XML_SERVER', HTTP_SERVER.ADMIN_DIR.XML_TO);
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	File System Information
	//
	////////////////////////////////////////////////////////////////////////////////
	

	define('TEMPLATE_DIR', INCLUDES_DIR.'templates/'.TEMPLATE_NAME.'/');
	define('CONTROLLER_DIR', INCLUDES_DIR.'controllers/');
	define('MODULES_DIR', INCLUDES_DIR.'modules/');
	
	
	
		
	////////////////////////////////////////////////////////////////////////////////
	//
	//	System software settings
	//
	////////////////////////////////////////////////////////////////////////////////

	define('SECURE_KEYWORD',md5(SITE_NAME));
	define('CMS_VERSION_MAJOR', 1);
	define('CMS_VERSION_MINOR', 0);
		
?>