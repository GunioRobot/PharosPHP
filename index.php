<?

	// Begin loading the system
	require_once 'system/init.php';

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
		
		// Determine if should process login information or not
		if ( $controller->auth->login_required() && !$controller->auth->logged_in() ) {
			redirect(Template::controller_link('Session','login/'));
		}
		
		// Simply return cached information it's available
		if ( server("REQUEST_METHOD") === "GET" && ($cache = Output::cached_content()) !== false ) {
			die($cache);
		}
		
		// Call a method on the class (determined by the Router) & capture the output		
		$method = Router::method();	
		if ( method_exists($controller, $method) ) {
			
			if ( Router::using_named_params() ) {
				call_user_func(array($controller, $method), Router::params());
			} else {
				call_user_func_array(array($controller, $method), Router::params());
			}
			
		} else {
			if ( class_exists("Console") ) Console::log("Unknown method (".$method.") for class($controllerClass)");
		}	

		// Grab the contents of the buffer & give to controller to use. Turn off buffering as well
		$controller->output->finalize(ob_get_clean());		
		
		// Send HTTP headers to the browser if requested
		foreach($controller->output->header() as $header) {
			header($header);
		}
		
		// Render the template to the browser
		Template::render();
			
	} else throw new Exception("FileNotFoundException: ".$file);
	
?>