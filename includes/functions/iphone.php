<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	is_iPhone()
	//
	//	Returns true if accessed from iPhone/iPod Touch device, false otherwise
	//
	////////////////////////////////////////////////////////////////////////////////

	function is_iPhone() {
		return strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false;
	}



	////////////////////////////////////////////////////////////////////////////////
	//
	//	use_mobile_verison()
	//
	//	Redirects to mobile version if viewing on iPhone and not specifically set 
	//	to false.  Otherwise return true/false whether to show mobile/regular site
	//
	//	NOTE: define('USE_IPHONE_OPTIMIZED_SITE', true) must be set to use iPhone.
	//
	//	Usage:
	//
	//		if ( use_mobile_version() ) {
	//			// Show mobile templates, etc
	//		} else {
	//			// Show regular templates
	//		}
	//
	////////////////////////////////////////////////////////////////////////////////

	function use_mobile_version() {

	//	return array(true, get("ajax", "false") !== "false" ? true : false);
	
		if ( defined('USE_IPHONE_OPTIMIZED_SITE') && USE_IPHONE_OPTIMIZED_SITE ) {
	
			$useMobileVersion = get("mobile", true) == "false" ? false : true;
			$wasSet = get("mobile") !== false ? true : false;
			$ajaxVersion = get("ajax") !== false ? true : false;
	
			$useMobileVersion = ( is_iPhone() && (($wasSet && $useMobileVersion) || !$wasSet) );
			if ( $useMobileVersion && !$wasSet ) {
				redirect(HTTP_SERVER.ADMIN_DIR.'index.php?&mobile=true');
			} return array($useMobileVersion, $ajaxVersion);
		
		} else return array(false, false);
	
	}

?>