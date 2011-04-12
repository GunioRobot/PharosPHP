<?

	if ( ($pass = Input::post($data)) !== false ) {
		$_POST[$data] = Authentication::hashed_password($pass);
	} else {
		$data = null;	// Don't Update
	}
		
?>