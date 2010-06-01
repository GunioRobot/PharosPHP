<?

	function load_automatic_modules() {

		global $autoload;

		foreach($autoload as $m) {
			try {
				Controller::loadModule($m);
			} catch (Exception $e) {
				Console::log($e->getMessage());
			}
		} 
		
	}

?>