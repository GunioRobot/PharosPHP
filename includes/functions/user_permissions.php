<?

	define('BASIC_USER_LVL', '1');
	define('ADMIN_LVL', '4');
	define('SUPER_LVL', '5');
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	validate_login()
	//
	// 	Will redirect to login page if user isn't logged in
	//
	////////////////////////////////////////////////////////////////////////////////

	function validate_login() {
	
		global $db;
	
		list($useMobileVersion, $loadViaAjax) = use_mobile_version();
	
		// Page we're looking for (won't be set if it's just index.php - just show welcome.php to everyone)
		$_GET['pid'] = get("pid", $useMobileVersion ? IPHONE_WELCOME_PID : WELCOME_PID);
	
		// Check that user is logging on to right domain
		if ( session('domain_id') != DOMAIN_ID ) $failed = true;
	
		if ( !session("uid") ) {
		
			// Pass on the GET vars and log will pass on to index.php so log takes you back to where you wanted to go
			$get_string = '';
			foreach ( $_GET as $key => $value ) {
				$get_string .= '&'.$key.'='.$value;
			}
					
			redirect(HTTP_SERVER.ADMIN_DIR.'log.php?'.$get_string);

		}
		
		// If good to go
		define('SECURITY_LVL', $_SESSION['user_level']);	
	
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	level_for_user($user=null)
	//
	// Returns integer security level for supplied user, of if null, current user
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function level_for_user($user=null) {
		
		global $db;
				
		if ( !is_null($user) AND $user != '' ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '$user' LIMIT 1");
			return $rr->fields['user_level'];
		} else return SECURITY_LVL;
		
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	level_for_user($user=null)
	//
	// 	true/false for supplied user, of if null, current user
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function is_super($user=null) {
		
		global $db;
		
		if ( !is_null($user) ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '".$user."' LIMIT 1");
			return $rr->fields['user_level'] >= SUPER_LVL;
		} else return SECURITY_LVL >= SUPER_LVL;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	is_admin($user=null)
	//
	// 	true/false for supplied user, of if null, current user
	//
	////////////////////////////////////////////////////////////////////////////////
	function is_admin($user=null) {
		
		global $db;
			
		if ( !is_null($user) ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '".$user."' LIMIT 1");
			return $rr->fields['user_level'] >= ADMIN_LVL;
		} else return SECURITY_LVL >= ADMIN_LVL;
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	is_user($user=null)
	//
	// 	true/false for supplied user, of if null, current user
	//
	////////////////////////////////////////////////////////////////////////////////
	function is_user($user=null) {
		
		global $db;
			
		if ( !is_null($user) ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '".$user."' LIMIT 1");
			return $rr->fields['user_level'] >= BASIC_USER_LVL;
		} else return SECURITY_LVL >= BASIC_USER_LVL;
	}

	
?>