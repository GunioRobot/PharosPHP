<?

	/**
	 * __()
	 * Alias for "Language::get($keypath)"
	 *
	 * @param (Keypath|string) $keypath
	 * @param string $default_text (optional - used if value is not defined)
	 *
	 * @throws InvalidKeyPathException
	 *
	 * @return mixed $value
	 * @author Matt Brewer
	 **/
	
	function __($keypath, $default="") {
		return Language::get($keypath, $default);
	}

?>