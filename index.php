<?
		
	// Site includes
	require_once 'includes/app_header.php';
	
	// Validate login information
	validate_login();
			
	Hooks::call_hook(Hooks::HOOK_CONTROLLER_PRE_CREATED);	
					
	$controllerClass = Router::controller();
	$file = CONTROLLER_DIR.$controllerClass.'.php';		
	if ( file_exists($file) ) {
		
		ob_start();

		require_once $file;
		$controller = new $controllerClass();
		Hooks::call_hook(Hooks::HOOK_CONTROLLER_POST_CREATED, array($controllerClass));	
				
		$method = Router::method();	
		if ( method_exists($controller, $method) ) {
			call_user_func_array(array($controller, $method), Router::params());
		} else {
			Console::log("Unknown method (".$method.") for class($controllerClass)");
		}	
		
		if ( $controller->output() === "" ) $controller->output(ob_get_contents());
		ob_end_clean();
		
		render_template();
			
	} else {

		Console::log("Unable to load class (".$args.")");

	}
		
	
?>