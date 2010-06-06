<?
		
	// Begin loading the system
	require_once 'system/init.php';
	
	// Validate login information
	validate_login();
			
	// System action to allow post system-init, pre controller created actions to execute		
	Hooks::call_hook(Hooks::HOOK_CONTROLLER_PRE_CREATED);	
	
	// The Router has parsed for the class to load, attempt to load it				
	$controllerClass = Router::controller();
	$file = CONTROLLER_DIR.$controllerClass.'.php';		
	if ( file_exists($file) ) {
		
		ob_start();

		require_once $file;
		$controller = new $controllerClass();
		Hooks::call_hook(Hooks::HOOK_CONTROLLER_POST_CREATED, array($controllerClass));	
		
		// Call a method on the class (determined by the Router) & capture the output		
		$method = Router::method();	
		if ( method_exists($controller, $method) ) {
			call_user_func_array(array($controller, $method), Router::params());
		} else {
			Console::log("Unknown method (".$method.") for class($controllerClass)");
		}	

		// Grab the contents of the buffer & give to controller to use. Turn off buffering as well
		$controller->finalize(ob_get_flush());
		
		// Render the template to the browser
		render_template();
			
	} else {

		Console::log("Unable to load class (".$args.")");

	}
		
	
?>