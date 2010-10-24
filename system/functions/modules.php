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
	
	
	/**
	 * extend
	 * Takes at least two arrays, the first argument being your defaults
	 * Returns array with keys in later arrays overwriting default values, if provided
	 *
	 * @param array $defaults
	 * @param array $array1
	 * @param array $array2
	 * ............ $array3 etc
	 *
	 * @throws InvalidArgumentException
	 * 
	 * @return array $extended
	 * @author Matt Brewer
	 **/
	
	function extend() {
		
		if ( func_num_args() < 2 ) {
			throw new InvalidArgumentException(__FUNCTION__ . ': Expects at least two arrays');
		}
		
		$args = func_get_args();
		return call_user_func_array('array_merge_recursive', $args);
		
	}

?>