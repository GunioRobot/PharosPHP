<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	is_valid_phone_number($string)
	//
	//	Returns true/false
	//		Attempts to match pattern: "(123)-456-7890" after stripping all spaces
	//
	////////////////////////////////////////////////////////////////////////////////

	function is_valid_phone_number($PhoneNumber) {
		return preg_match("/^\(?[0-9]{3}\)?(-|.)?[0-9]{3}(-|.)?[0-9]{4}/", str_replace(" ", "", $PhoneNumber));
	}
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	is_valid_email_address($email)
	//
	//	Returns true if email is technically valid (not necessarily existing)
	//		
	////////////////////////////////////////////////////////////////////////////////
	
	function is_valid_email_address($email) {
		
		require_once MODULES_DIR.'forms/classes/EmailAddressValidator.php';
		
		$validator = new EmailAddressValidator;
        return $validator->check_email_address($email);
		
	}
	
?>