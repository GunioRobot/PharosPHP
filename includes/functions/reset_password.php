<?php
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	reset_password($username)
	//
	//	Sends an email to the user containing a temporary password
	//
	////////////////////////////////////////////////////////////////////////////////

	function reset_password($username) {
		
		global $db;
		Controller::loadModule("rmail");
				
		$info = $db->Execute("SELECT * FROM users WHERE user_username = '$username' LIMIT 1");
		if ( $info->fields['user_primary_email'] != '' ) {
			
			$new_password = substr(rand(0,100).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).'_'.chr(rand(65,117)).rand(50,100).'_'.RESET_PASSWORD_RANDOM_WORD, 0, 49);	// Limit the password to 50 max characters (all the database holds)
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
	
?>