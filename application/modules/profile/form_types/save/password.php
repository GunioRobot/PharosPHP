<?

	if ( ($pass = Input::post($data)) ) {
		$_POST[$data] = Authentication::hashed_password($pass);
	}
	
?>