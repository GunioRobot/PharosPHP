<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	System Actions, aka "Hooks"
	//	Hooks provide the foundation for a flexible framework.
	//
	//	The file contains the core system actions defined in this version of 
	//	CMSLite.  The Hooks API allows for developers to easily register custom
	//	callback functions to be executed whenever the specified system action 
	//	occurs.
	//
	//	For even more flexibilty, CMSLite allows developers to register their own
	//	"system action" so that other developers can register callback functions,
	//	creating a rich collobarative framework architecture.
	//
	////////////////////////////////////////////////////////////////////////////////

	define('HOOK_APPLICATION_PUBLISH', 'application_published_hook');		// function($app_id) {}
	define('HOOK_APPLICATION_BOOTSTRAP', 'application_bootstrap_hook');		// function() {}
	
	define('HOOK_MODULE_LOADED', 'module_loaded_hook');						// function($module_name) {}
	
	define('HOOK_TEMPLATE_HEADER', 'template_header_hook');					// function() {}
	define('HOOK_TEMPLATE_PRE_RENDER', 'template_pre_render_hook');			// function() {}
	define('HOOK_TEMPLATE_POST_RENDER', 'template_post_render_hook');		// function() {}
	
	define('HOOK_USER_CREATED', 'user_created_hook');						// function($user_id) {}
	define('HOOK_USER_DELETED', 'user_deleted_hook');						// function($user_id) {}

?>