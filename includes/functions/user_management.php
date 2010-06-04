<?

	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	validate_login()
	//
	// 	Will redirect to login page if user isn't logged in
	//
	////////////////////////////////////////////////////////////////////////////////

	function validate_login() {
	
		global $db;
		
		if ( get("arg1") === "session" && get("arg2") === "login" ) {
			return;
		}
		
		if ( !is_logged_in() ) {
			redirect(controller_link('Session','login/'));
		}
		
		// If good to go
		define('SECURITY_LVL', $_SESSION['user_level']);	
	
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	is_logged_in()
	//
	// 	Validates rear login different than front login
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function is_logged_in() {
		return session("uid") !== false && session("domain_id") === SECURE_KEYWORD;
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
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	reset_password($username)
	//
	//	Sends an email to the user containing a temporary password
	//
	////////////////////////////////////////////////////////////////////////////////

	function reset_password($username) {
		
		global $db;
		Modules::load("rmail");
				
		$info = $db->Execute("SELECT * FROM users WHERE user_username = '$username' LIMIT 1");
		if ( $info->fields['user_primary_email'] != '' ) {
			
			$new_password = random_password();
			$db->Execute("UPDATE users SET user_password = '".$new_password."', last_updated = NOW() WHERE user_id = '".$info->fields['user_id']."' LIMIT 1");
		
			$html = '<html><body>';
			$html .= '<h2>Password Reset</h2>';
			$html .= 'You requested to have your password reset.  If you did not make the request, please user the system administrator at: <a href="mailto:'.SYS_ADMIN_EMAIL.'?subject=Bad Password Reset">'.SYS_ADMIN_EMAIL.'</a>';
			$html .= '<br /><br /><hr>';
			$html .= 'Your new password is: <strong>'.$new_password.'</strong><br />';
			$html .= 'Login at <a href="'.site_link().'">'.site_link().'</a> with your username and new password.';
			$html .= '</body></html>';
		
			$mail = new Rmail();
			$mail->setFrom(SERVER_MAILER);
			$mail->setSubject(SITE_NAME.': Password Reset');
			$mail->setPriority('high');
			$mail->setHTML($html);
						
			if ( !$mail->send(array($info->fields['user_primary_email'])) ) redirect(site_link('password_reset.php?success=false'));
			else redirect(site_link('password_reset.php?success=true'));

		} else redirect(site_link('password_reset.php?success=false&bad=true'));
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	random_password()
	//
	//	Generates a completely random password for any user
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function random_password() {
		return str_replace('_', '', substr(rand(0,100).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).rand(50,100).RESET_PASSWORD_RANDOM_WORD, 0, 49));	// Limit the password to 50 max characters (all the database holds)
	}

	
?>