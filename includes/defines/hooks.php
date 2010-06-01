<?

	define('HOOK_APPLICATION_PUBLISH', 'application_published_hook');
	define('HOOK_APPLICATION_BOOTSTRAP', 'application_bootstrap_hook');
	define('HOOK_USER_CREATED', 'user_created_hook');
	define('HOOK_USER_DELETED', 'user_deleted_hook');

	global $_application_hooks;
	$_application_hooks = array(
		HOOK_APPLICATION_BOOTSTRAP => null,
		HOOK_APPLICATION_PUBLISH => null,
		HOOK_USER_CREATED => null,
		HOOK_USER_DELETED => null
	);

?>