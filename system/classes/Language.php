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
			$ext = $info['extension'] != "" ? strtolower($info['extension']) : "yml";
			$file = $info['filename'];
			$filename = $file.".".$ext;
						
			if ( $ext != "yml" ) {
				throw new InvalidArgumentException(sprintf("Expected YML - received (%s)", $filename));
			}
						
			if ( $info['dirname'] == "." ) {	// If a directory wasn't provided, infer one
			
				if ( !file_exists(APPLICATION_LANGUAGES_DIR.$filename) && !file_exists(LANGUAGES_DIR.$filename) ) {
					throw new InvalidFileSystemPathException(sprintf("Language file does not exist: (%s)", $filename));
				}
							
				// Load the system language file first, so that if an application language file is found, the values defined there will override
				if ( file_exists(LANGUAGES_DIR.$filename) ) {
					$config = sfYaml::load(LANGUAGES_DIR.$filename);
					self::$languages[$file] = is_array(self::$languages[$file]) ? array_merge(self::$languages[$file], $config) : $config;
				}
			
				// Any values defined in /application/languages/{lang}.yml will override values defined for the system version
				if ( file_exists(APPLICATION_LANGUAGES_DIR.$filename) ) {
					$config = sfYaml::load(APPLICATION_LANGUAGES_DIR.$filename);
					self::$languages[$file] = is_array(self::$languages[$file]) ? array_merge(self::$languages[$file], $config) : $config;
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
		 *
		 * @param (Keypath|string) $keypath
		 * @param string $default_text (optional - used if value is not defined)
		 * @param string $lang (optional - uses default_language already defined)
		 *
		 * @throws UnexpectedValueException
		 * @throws InvalidKeyPathException
		 *
		 * @return mixed $value
		 * @author Matt Brewer
		 **/
		
		public static function lookup($path, $default="", $lang=null) {
			
			if ( !is_string($lang) ) {
				$lang = self::$current_language;
			}
			
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
		 * currentLanguage
		 *
		 * @return string $current_language
		 * @author Matt Brewer
		 **/

		public static function currentLanguage() {
			return self::$current_langage;
		}
		
	} 

?>