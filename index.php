<?

	// Begin loading the system
	require_once 'system/init.php';

	// System action to allow post system-init, pre controller created actions to execute		
	Hooks::call_hook(Hooks::HOOK_CONTROLLER_PRE_CREATED);	
				
	// The Router has parsed for the class to load, attempt to load it				
	$controllerClass = Router::controller();
	$file = CONTROLLER_DIR.$controllerClass.'.php';	
	
	try {
	
		// Find the page to include, takes care of page_slug = '' for the homepage automatically...
		$page = $db->Execute("SELECT * FROM pages WHERE LOWER(slug) = '".strtolower(Template::controller_slug($controllerClass))."' LIMIT 1");
		if ( !$page->EOF ) {
		
			$page = clean_object($page->fields);
		
			// If there is a controller class declared, let's use it
			if ( file_exists($file) ) {
			
				require_once $file;
				$controller = new $controllerClass($page->title);
				
				if ( method_exists($controller, "page") ) {
					$controller->page($page);
				}
						
				if ( method_exists($controller, "pageText") ) {
					$controller->pageText($page->text);
				}
						
				// Call a method on the class (determined by the Router) & capture the output		
				$method = Router::method();	
				if ( method_exists($controller, $method) ) {
			
					if ( Router::using_named_params() ) {
						call_user_func(array($controller, $method), Router::params());
					} else {
						call_user_func_array(array($controller, $method), Router::params());
					}
			
				} else throw new Exception("Unknown method (".$method.") for class($controllerClass)");
			
			} else {
						
				// Just use the text from the database with generic controller class
				require_once APPLICATION_CLASSES_DIR.'ApplicationGenericPageController.php';
				$controller = new ApplicationGenericPageController($page->title);
		
				if ( method_exists($controller, "page") ) {
					$controller->page($page);
				}
			
				if ( method_exists($controller, "pageText") ) {
					$controller->pageText($page->text);
				}
			
				echo $controller->index();
									
			}
		
			// Grab the contents of the buffer & give to controller to use. Turn off buffering as well
			$controller->output->finalize(ob_get_clean());				
	
			// Render the template to the browser
			Template::render();
		
		} else {
	
			if ( file_exists($file) ) {
		
				ob_start();

				require_once $file;
				$controller = new $controllerClass();
				Hooks::call_hook(Hooks::HOOK_CONTROLLER_POST_CREATED, array($controllerClass));	
		
				// Determine if should process login information or not
				if ( $controller->auth->login_required() && !$controller->auth->logged_in() ) {
					redirect(Template::controller_link('Session','login/', true));
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
		
				// Render the template to the browser
				Template::render();
			
			} else throw new Exception("FileNotFoundException: ".$file);
		
		}
		
	} catch (Exception $e) {
		redirect(Template::controller_link("PageNotFound"));
	}
	
?>