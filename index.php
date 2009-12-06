<?
	
	// Site includes
	require_once 'includes/app_header.php';
	
	// Validate login information
	validate_login();
		
	$args[] = get('arg1');		// Page we're looking for...
	$args[] = get('arg2');		// Var for the page itself to use
	$args[] = get('arg3');		// Var for the page itself to use
	$args[] = get('arg4');		// Var for the page itself to use
	$args[] = get('arg5');		// Var for the page itself to use
	$args[] = get('arg6');		// Var for the page itself to use
	
	if ( $args[0] ) {
		
		$file = CONTROLLER_DIR.controller_name($args[0]).'.php';		
		if ( file_exists($file) ) {
			
		
			require $file;
			$controller = new $controllerClass();
					
			if ( $args[1] ) {
				
				$c = count($args);
				for ( $i = 2; $i < $c; $i++ ) {
					if ( $args[$i] !== false ) {
						$funcArgs[] = $args[$i];
					}
				}
				
				call_user_func_array(array($controller, $args[1]), $funcArgs);
							
			} else {
				
				$controller->index();
				
			}
			
			
			if ( method_exists($controller, "_willLoadView") ) {
				$controller->_willLoadView();
			}
			
			require_once TEMPLATE_DIR.'tpl_HTML_header.php';
			require_once TEMPLATE_DIR.'tpl_header.php';
			require_once TEMPLATE_DIR.'tpl_body.php';
			require_once TEMPLATE_DIR.'tpl_footer.php';
				
		}
		
	} else {
		
		Console::log("Unable to load class (".$args.")");
		
	}		
	
?>