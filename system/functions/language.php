<?

	/**
	 * __()
	 * Alias for "Language::get($keypath)"
	 *
	 * @param (Keypath|string) $keypath
	 *
	 * @throws InvalidKeyPathException
	 *
	 * @return mixed $value
	 * @author Matt Brewer
	 **/
	
	function __($keypath) {
		Language::get($keypath);
	}

?>