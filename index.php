<?
	
	// Site includes
	require_once 'includes/app_header.php';
	
	// Validate login information
	validate_login();
	
	// Build array of args
	for ( $i = 1; $i < 10; $i++ ) {
		if ( ($arg = get("arg$i")) !== false ) $args[] = $arg;
	}
	
				
	if ( $args[0] ) {
		
		
		$controllerClass = controller_name($args[0]);
		$file = CONTROLLER_DIR.$controllerClass.'.php';		
		if ( file_exists($file) ) {

			require $file;
			$controller = new $controllerClass();
					
			if ( $args[1] ) {
				
				$c = count($args);
				$funcArgs = array_slice($args,2);	// Skip the first two arguments (class name and method) to pass along
				
				if ( method_exists($controller, $args[1]) ) {
					call_user_func_array(array($controller, $args[1]), $funcArgs);
				} else {
					Console::log("Unknown method (".$args[1].") for class($controllerClass)");
				}
							
			} else {
				
				$controller->index();
				
			}
			
			require_once TEMPLATE_DIR.'tpl_HTML_header.php';
			require_once TEMPLATE_DIR.'tpl_header.php';
			require_once TEMPLATE_DIR.'tpl_body.php';
			require_once TEMPLATE_DIR.'tpl_footer.php';
				
		} else {

			Console::log("Unable to load class (".$args.")");

		}
		
	} else {
		
		$controllerClass = controller_name(DEFAULT_CONTROLLER_NAME);
		$file = CONTROLLER_DIR.$controllerClass.'.php'; 
		if ( file_exists($file) ) {
		
			require $file;
			$controller = new $controllerClass();
			
			$controller->index();
			
			require_once TEMPLATE_DIR.'tpl_HTML_header.php';
			require_once TEMPLATE_DIR.'tpl_header.php';
			require_once TEMPLATE_DIR.'tpl_body.php';
			require_once TEMPLATE_DIR.'tpl_footer.php';
			
		} else {
			
			Console::log("Unable to load default controller class (".DEFAULT_CONTROLLER_NAME.")");
			
		}
		
	}
	
?>