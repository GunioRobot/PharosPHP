<?

	/**
	 * @file language.php
	 * @brief Functions for the i18n "localization" framework
	 */

	/**
	 * i18n()
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
	
	function i18n($keypath, $default="", $lang=null) {
		return Language::lookup($keypath, $default, $lang);
	}

?>