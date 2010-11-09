<?

	/**
	 * Language API
	 * 
	 * Most users will use the class statically, ie:
	 * 		Language::setLanguage(Language::DUTCH);
	 * 		echo Language::lookup("actions.save", "Save")
	 * 
	 * However, you may initiate an instance of the class to easily use different languages at once, ie:
	 * 		$lang = new Language(Language::ENGLISH);
	 *  	$lang->language = Language::FRENCH;
	 *		$lang->language = "bob";	// Throws exception, not a valid language
	 *		echo $lang->language;		// prints "fr"
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
		
	final class Language extends Object {
		
		const DUTCH = "nl";
		const ENGLISH = "en";
		const FRENCH = "fr";
		const GERMAN = "de";
		const ITALIAN = "it";
		const JAPENESE = "jp";
		const KOREAN = "ko";
		const PORTUGUESE = "pt";
		const RUSSIAN = "ru";
		const SPANISH = "es";
		
		protected static $available_languages = array(
			self::DUTCH, 
			self::ENGLISH, 
			self::FRENCH,
			self::GERMAN,
			self::ITALIAN,
			self::JAPENESE,
			self::KOREAN,
			self::PORTUGUESE,
			self::RUSSIAN,
			self::SPANISH
		);
		
		protected static $languages = array();
		protected static $current_language = self::ENGLISH;
		
		protected $language = self::ENGLISH;
		
		
		/**
		 * __construct
		 * Constructor
		 * 
		 * @param string $language_or_path
		 * 
		 * @throws InvalidArgumentException
		 * @throws InvalidFileSystemPathException
		 *
		 * @return Language $obj
		 * @author Matt Brewer
		 **/
		
		public function __construct($lang=self::ENGLISH) {
			parent::__construct();
			$this->language = $lang;
		}
		
		
		/**
		 * __set
		 * Dynamic mutator method. 
		 * Calling $lang->language = "en"; verifies the assignment is valid, and loads the associated language file
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function __set($key, $lang) {
			if ( $key == "language" ) {
				if ( !in_array($lang, self::$available_languages) ) {
					throw new InvalidArgumentException(sprintf("[Language]: Invalid language provided to setLanguage() => %s", $lang));
				} 

				$this->language = $lang;
				self::load($lang);
			}
		}
		
		
		/**
		 * load
		 * Attempts to load the requested file path, using the basename of the file as the language identifier (ie, "en.yml" for English or "fr.yml" for French)
		 * This method first checks in application/languages, then in system/languages if the path is relative. If a full path is provided, the resource is loaded from that location.
		 *
		 * @param string $path|$language (all paths must be {i18n abbrev}.yml)
		 *
		 * @throws InvalidArgumentException
		 * @throws InvalidFileSystemPathException
		 *
		 * @return mixed $language_dictionary
		 * @author Matt Brewer
		 **/

		public static function load($path) {
			
			$info = pathinfo($path);
			$ext = (isset($info['extension']) && $info['extension']) != "" ? strtolower($info['extension']) : "yml";
			$file = $info['filename'];
			$filename = $file.".".$ext;
						
			if ( $ext != "yml" ) {
				throw new InvalidArgumentException(sprintf("Expected YML - received (%s)", $filename));
			}
						
			if ( $info['dirname'] == "." ) {	// If a directory wasn't provided, infer one
			
				if ( !file_exists(APPLICATION_LANGUAGES_PATH.$filename) && !file_exists(LANGUAGES_PATH.$filename) ) {
					throw new InvalidFileSystemPathException(sprintf("Language file does not exist: (%s)", $filename));
				}
							
				// Load the system language file first, so that if an application language file is found, the values defined there will override
				if ( file_exists(LANGUAGES_PATH.$filename) ) {
					$config = sfYaml::load(LANGUAGES_PATH.$filename);					
					self::$languages[$file] = (isset(self::$languages[$file]) && is_array(self::$languages[$file])) ? array_merge(self::$languages[$file], $config) : $config;
					
				}
			
				// Any values defined in /application/languages/{lang}.yml will override values defined for the system version
				if ( file_exists(APPLICATION_LANGUAGES_PATH.$filename) ) {
					$config = sfYaml::load(APPLICATION_LANGUAGES_PATH.$filename);
					self::$languages[$file] = (isset(self::$languages[$file]) && is_array(self::$languages[$file])) ? array_merge(self::$languages[$file], $config) : $config;
				}
							
				return self::$languages[$file];
							
			} else {	// Directory was provided, use it
				
				if ( !file_exists($path) ) {
					throw new InvalidFileSystemPathException(sprintf("Language file does not exist: (%s)", $path));
				}
				
				$config = sfYaml::load($path);
				return self::$languages[$file] = is_array(self::$languages[$file]) ? array_merge(self::$languages[$file], $config) : $config;
				
			}
			
		}
		
		
		/**
		 * lookup
		 * Performs a lookup operation with the given keypath from the current (or provided) language. 
		 * If the value is not set, the $default parameter is returned.
		 *
		 * @param (Keypath|string) $keypath
		 * @param (string|String) $default_text (optional - used if value is not defined)
		 * @param (string|String) $lang (optional - uses default_language already defined)
		 *
		 * @throws UnexpectedValueException
		 * @throws InvalidKeyPathException
		 *
		 * @return mixed $value
		 * @author Matt Brewer
		 **/
		
		public static function lookup($path, $default="", $lang=null) {
			
			if ( is_null($lang) ) {
				$lang = self::$current_language;
			} else $lang = strval($lang);
			
			if ( !isset(self::$languages[$lang]) ) {
				throw new UnexpectedValueException(sprintf("Unexpected language: (%s)", $lang));
			}
			
			if ( !($path instanceof Keypath) ) {
				$path = new Keypath($path);
			}		
			
			$value = $path->retrieve(self::$languages[$lang]);
			return $value === Keypath::VALUE_UNDEFINED ? $default : $value;
			
		}
		
		
		
		/**
		 * retrieve
		 * Performs a lookup of the keypath with the current language, returning the $default if not set
		 * NOTE: Currently the only non-static method in the class, requires an instance to use this method
		 *
		 * @param (Keypath|string) $keypath
		 * @param string $default_text (optional - used if value is not defined)
		 *
		 * @return mixed $value
		 * @author Matt Brewer
		 **/
		
		public function retrieve($path, $default="") {
			return self::lookup($path, $default, $this->language);
		}
		
		
		/**
		 * setLanguage
		 * Changes the current language
		 *
		 * @uses Language::load($lang)
		 * @throws InvalidArgumentException
		 *
		 * @param string $language
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function setLanguage($lang) {
			if ( !in_array($lang, self::$available_languages) ) {
				throw new InvalidArgumentException(sprintf("[Language]: Invalid language provided to setLanguage() => %s", $lang));
			} 
			 
			self::$current_language = $lang;
			self::load($lang);
		}
		
		
		/**
		 * currentLanguage
		 * Returns the current language
		 *
		 * @return string $current_language
		 * @author Matt Brewer
		 **/

		public static function currentLanguage() {
			return self::$current_langage;
		}
		
	} 

?>