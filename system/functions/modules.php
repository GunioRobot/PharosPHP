<?

	/**
	 * is_shared_module
	 * Checks to see if the module is installed in the SYSTEM_DIR directory
	 *
	 * @param string $path - complete path to the module
	 *
	 * @return boolean $status
	 * @author Matt Brewer
	 **/
	
	function is_shared_module($path) {
		return stripos($path, SYSTEM_PATH) !== false;
	}
	
	
	/**
	 * module_dir
	 * Path to the module, relative to the install root
	 * 
	 * @param string $path - complete path to the module
	 *
	 * @return string $dir
	 * @author Matt Brewer
	 **/
	
	function module_dir($path) {
		return (is_shared_module($path) ? basename(SYSTEM_PATH) : APP_DIR) . DS . 'modules' . DS . basename($path) . DS;
	}
	
	
	/**
	 * module_url
	 * Complete URL to the module directory
	 *
	 * @param string $path - complete path to the module
	 *
	 * @return string $url
	 * @author Matt Brewer
	 **/
	
	function module_url($path) {
		return (is_shared_module($path) ? SYSTEM_URL : APP_URL ) . 'modules' . DS . basename($path) . DS;
	}

?>