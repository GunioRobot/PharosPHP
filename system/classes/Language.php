<?

	/**
	 * Language API
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	class Language {
		
		const ENGLISH = "en";
		const FRENCH = "fr";
		
		protected static $languages = array();
		protected static $current_language = self::ENGLISH;
		
		public $language = self::ENGLISH;
		
		
		/**
		 * load
		 *
		 * @param string $filename
		 *
		 * @throws InvalidArgumentException
		 * @throws InvalidFileSystemPathException
		 *
		 * @return mixed $language_dictionary
		 * @author Matt Brewer
		 **/

		public static function load($filename) {
			
			$info = pathinfo($filename);
			if ( strtolower($info['extension']) != "yml" ) {
				throw new InvalidArgumentException(sprintf("Expected YML - received (%s)", $info['basename']));
			}
			
			$key = $info['filename'];
			if ( $info['dirname'] == "." ) {	// If a directory wasn't provided, infer one
			
				if ( !file_exists(APPLICATION_LANGUAGES_DIR.$filename) && !file_exists(LANGUAGES_DIR.$filename) ) {
					throw new InvalidFileSystemPathException(sprintf("Language file does not exist: (%s)", $filename));
				}
			
				// Load the system language file first, so that if an application language file is found, the values defined there will override
				if ( file_exists(LANGUAGES_DIR.$filename) ) {
					$config = sfYaml::load(LANGUAGES_DIR.$filename);
					self::$languages[$key] = is_array(self::$languages[$key]) ? array_merge(self::$languages[$key], $config) : $config;
				}
			
				// Any values defined in /application/languages/{lang}.yml will override values defined for the system version
				if ( file_exists(APPLICATION_LANGUAGES_DIR.$filename) ) {
					$config = sfYaml::load(APPLICATION_LANGUAGES_DIR.$filename);
					self::$languages[$key] = is_array(self::$languages[$key]) ? array_merge(self::$languages[$key], $config) : $config;
				}
							
				return self::$languages[$key];
							
			} else {	// Directory was provided, use it
				
				if ( !file_exists($filename) ) {
					throw new InvalidFileSystemPathException(sprintf("Language file does not exist: (%s)", $filename));
				}
				
				$config = sfYaml::load($filename);
				return self::$languages[$key] = is_array(self::$languages[$key]) ? array_merge(self::$languages[$key], $config) : $config;
				
			}
			
		}
		
		
		/**
		 * get
		 *
		 * @param (Keypath|string) $keypath
		 * @param string $default_text (optional - used if value is not defined)
		 *
		 * @throws InvalidKeyPathException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function get($path, $default="") {
			
			if ( !($path instanceof Keypath) ) {
				$path = new Keypath($path);
			}		
			
			$value = $path->retrieve(self::$languages[self::$current_language]);
			return $value === Keypath::VALUE_UNDEFINED ? $default : $value;
			
		}
		
		
		/**
		 * setLanguage
		 *
		 * @param string $language
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function setLanguage($lang) {
			self::$current_language = $lang;
		}
		
		
		/**
		 * get_by_lang
		 *
		 * @param string $language
		 * @param (Keypath|string) $keypath
		 * @param string $default_text (optional - used if value is not defined)
		 *
		 * @throws UnexpectedValueException
		 * @throws InvalidKeyPathException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function get_by_lang($lang, $path, $default="") {
			
			if ( !isset(self::$languages[$lang]) ) {
				throw new UnexpectedValueException(sprintf("Unexpected language: (%s)", $lang));
			}
			
			if ( !($path instanceof Keypath) ) {
				$path = new Keypath($path);
			}		

			$value = $path->retrieve(self::$languages[$lang]);
			return $value === Keypath::VALUE_UNDEFINED ? $default : $value;
		
		}
		
	} 

?>