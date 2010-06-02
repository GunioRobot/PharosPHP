<?

	define('HOOK_APPLICATION_PUBLISH', 'application_published_hook');
	define('HOOK_APPLICATION_BOOTSTRAP', 'application_bootstrap_hook');
	define('HOOK_TEMPLATE_HEADER', 'template_header_hook');
	define('HOOK_TEMPLATE_PRE_RENDER', 'template_pre_render_hook');
	define('HOOK_TEMPLATE_POST_RENDER', 'template_post_render_hook');
	define('HOOK_USER_CREATED', 'user_created_hook');
	define('HOOK_USER_DELETED', 'user_deleted_hook');

	global $_application_hooks;
	$_application_hooks = array(
		HOOK_APPLICATION_BOOTSTRAP => null,
		HOOK_APPLICATION_PUBLISH => null,
		HOOK_USER_CREATED => null,
		HOOK_USER_DELETED => null
	);
	
	
	// Associate system actions with action hooks
	add_hook(HOOK_APPLICATION_PUBLISH, 'clean_upload_dir');
	add_hook(HOOK_APPLICATION_BOOTSTRAP, 'app_bootstrap');
	add_hook(HOOK_TEMPLATE_HEADER, 'write_css');
	add_hook(HOOK_TEMPLATE_HEADER, 'write_js');

?>