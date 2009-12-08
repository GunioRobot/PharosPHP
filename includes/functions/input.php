<?


	////////////////////////////////////////////////////////////////////////////////
	//
	//	get($key, $default=false)
	//
	//	Returns the value from $_GET or the default value, if $_GET wasn't set
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function get($key, $default=false) {
		return ( isset($_GET[$key]) AND $_GET[$key] != '' ) ? $_GET[$key] : $default;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	post($key, $default=false)
	//
	//	Returns the value from $_POST or the default value, if $_POST wasn't set
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function post($key, $default=false) {
		return ( isset($_POST[$key]) AND $_POST[$key] != '' ) ? $_POST[$key] : $default;
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	request($key, $default=false)
	//
	//	Returns the value from $_REQUEST or the default value, if $_REQUEST wasn't set
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function request($key, $default=false) {
		return ( isset($_REQUEST[$key]) AND $_REQUEST[$key] != '' ) ? $_REQUEST[$key] : $default;
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	session($key, $default=false)
	//
	//	Returns the value from $_SESSION or the default value, if $_SESSION wasn't set
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function session($key, $default=false) {
		return ( isset($_SESSION[$key]) AND $_SESSION[$key] != '' ) ? $_SESSION[$key] : $default;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	server($key, $default=false)
	//
	//	Returns the value from $_SERVER or the default value, if $_SERVER wasn't set
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function server($key, $default=false) {
		return ( isset($_SERVER[$key]) AND $_SERVER[$key] != '' ) ? $_SERVER[$key] : $default;
	}
	

?>