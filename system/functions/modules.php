<?

	/**
	 * @file modules.php
	 * @brief Functions for module developers
	 */

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
	 * Takes two arrays, the first argument being your defaults
	 * Returns array with keys in later array overwriting default values, if provided
	 *
	 * @param array $defaults
	 * @param array $array1
	 * 
	 * @return array $extended
	 * @author Matt Brewer
	 **/
	
	function extend(array $defaults, array $override) {
		return array_merge_recursive_distinct($defaults, $override);
	}
	
	
	/**
	 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
	 * keys to arrays rather than overwriting the value in the first array with the duplicate
	 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
	 * this happens (documented behavior):
	 *
	 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
	 *     => array('key' => array('org value', 'new value'));
	 *
	 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
	 * Matching keys' values in the second array overwrite those in the first array, as is the
	 * case with array_merge, i.e.:
	 *
	 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
	 *     => array('key' => array('new value'));
	 *
	 * Parameters are passed by reference, though only for performance reasons. They're not
	 * altered by this function.
	 *
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
	 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
	 */
	function array_merge_recursive_distinct ( array &$array1, array &$array2 ) {
	  $merged = $array1;

	  foreach ( $array2 as $key => &$value )
	  {
	    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
	    {
	      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
	    }
	    else
	    {
	      $merged [$key] = $value;
	    }
	  }

	  return $merged;
	}

?>