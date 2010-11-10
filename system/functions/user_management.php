<?

	/**
	 * @file user_management.php
	 * @brief Functions for managing user levels & permissions
	 */

	/**
	 * level_for_user
	 * Determines int security level for user
	 * 
	 * @param int $user_id
	 *
	 * @return boolean $is_user
	 * @author Matt Brewer
	 **/

	function level_for_user($user=null) {
		
		global $db;
				
		if ( !is_null($user) AND $user != '' ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '$user' LIMIT 1");
			return $rr->fields['user_level'];
		} else return SECURITY_LVL;
		
	}
	
	
	/**
	 * is_super
	 * Determines if the current or provided user_id is a super user
	 * 
	 * @param int $user_id
	 *
	 * @return boolean $is_user
	 * @author Matt Brewer
	 **/
	function is_super($user=null) {
		
		global $db;
		
		if ( !is_null($user) ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '".$user."' LIMIT 1");
			return $rr->fields['user_level'] >= Settings::get('application.users.levels.super');
		} else return SECURITY_LVL >= Settings::get('application.users.levels.super');
	}
	
	
	/**
	 * is_admin
	 * Determines if the current or provided user_id is an administrator
	 * 
	 * @param int $user_id
	 *
	 * @return boolean $is_user
	 * @author Matt Brewer
	 **/
	
	function is_admin($user=null) {
		
		global $db;
			
		if ( !is_null($user) ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '".$user."' LIMIT 1");
			return $rr->fields['user_level'] >= Settings::get('application.users.levels.admin');
		} else return SECURITY_LVL >= Settings::get('application.users.levels.admin');
	}
	

	/**
	 * is_user
	 * Determines if the current or provided user_id is a valid user
	 * 
	 * @param int $user_id
	 *
	 * @return boolean $is_user
	 * @author Matt Brewer
	 **/

	function is_user($user=null) {
		
		global $db;
			
		if ( !is_null($user) ) {
			$rr = $db->Execute("SELECT * FROM users WHERE user_id = '".$user."' LIMIT 1");
			return $rr->fields['user_level'] >= Settings::get('application.users.levels.basic');
		} else return SECURITY_LVL >= Settings::get('application.users.levels.basic');
	}
	
	
?>