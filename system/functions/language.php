<?

	/**
	 * __()
	 * Alias for "Language::lookup()"
	 *
	 * @param (Keypath|string) $keypath
	 * @param string $default_text (optional - used if value is not defined)
	 * @param string $language
	 *
	 * @throws InvalidKeyPathException
	 *
	 * @return mixed $value
	 * @author Matt Brewer
	 **/
	
	function __($keypath, $default="", $lang=null) {
		return Language::lookup($keypath, $default, $lang);
	}

?>