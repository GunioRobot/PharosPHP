<?

	if ( ($pass = Input::post($data)) ) {
		$_POST[$data] = Authentication::hash_password($pass);
	}
	
?>