<?

	require_once dirname(__FILE__).'/class.php';
	Hooks::register_callback(Hooks::HOOK_TEMPLATE_HEADER, 'DD_Belated::write');
		

?>