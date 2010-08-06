<?

	
	
	
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
			return $rr->fields['user_level'] >= Settings::get('application.users.levels.super');
		} else return SECURITY_LVL >= Settings::get('application.users.levels.super');
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
			return $rr->fields['user_level'] >= Settings::get('application.users.levels.admin');
		} else return SECURITY_LVL >= Settings::get('application.users.levels.admin');
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
			return $rr->fields['user_level'] >= Settings::get('application.users.levels.basic');
		} else return SECURITY_LVL >= Settings::get('application.users.levels.basic');
	}
	
	


	
?>