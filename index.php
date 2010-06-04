<?
	
	// Site includes
	require_once 'includes/app_header.php';
	
	// Validate login information
	validate_login();
	
	
	// Build array of args
	for ( $i = 1; $i < 10; $i++ ) {
		if ( ($arg = get("arg$i")) !== false ) $args[] = $arg;
	}
		
		
	Hooks::call_hook(Hooks::HOOK_CONTROLLER_PRE_CREATED);	
				
	if ( $args[0] ) {
		
		
		$controllerClass = controller_name($args[0]);
		$file = CONTROLLER_DIR.$controllerClass.'.php';		
		if ( file_exists($file) ) {
			
			ob_start();

			require_once $file;
			$controller = new $controllerClass();
			Hooks::call_hook(Hooks::HOOK_CONTROLLER_POST_CREATED);	
					
			if ( $args[1] ) {
				
				$c = count($args);
				$method = controller_name($args[1]);
				$funcArgs = array_slice($args,2);	// Skip the first two arguments (class name and method) to pass along
				
				if ( method_exists($controller, $method) ) {
					call_user_func_array(array($controller, $method), $funcArgs);
				} else {
					Console::log("Unknown method (".$method.") for class($controllerClass)");
				}
							
			} else {
				
				$controller->index();
				
			}
			
			if ( $controller->output() === "" ) $controller->output(ob_get_contents());
			ob_end_clean();
			
			render_template();
				
		} else {

			Console::log("Unable to load class (".$args.")");

		}
		
	} else {
		
		$controllerClass = controller_name($DEFAULT_CONTROLLER_NAME);
		$file = CONTROLLER_DIR.$controllerClass.'.php'; 
		if ( file_exists($file) ) {
			
			ob_start();
		
			require_once $file;
			$controller = new $controllerClass();
			Hooks::call_hook(Hooks::HOOK_CONTROLLER_POST_CREATED);	
			
			$controller->index();
			
			if ( $controller->output() === "" ) $controller->output(ob_get_contents());
			ob_end_clean();
			
			render_template();
			
		} else {
			
			Console::log("Unable to load default controller class (".$DEFAULT_CONTROLLER_NAME.")");
			
		}
		
	}
	
?>