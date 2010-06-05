<?

	function load_automatic_modules() {
		Hooks::call_hook(Hooks::HOOK_MODULES_PRE_LOADED);
		Modules::init();
		Hooks::call_hook(Hooks::HOOK_MODULES_POST_LOADED);
	}
	
?>