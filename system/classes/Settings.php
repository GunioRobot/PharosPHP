<?
	/**
	 * Settings API
	 * 
	 * 	The settings API allows for the application to read settings set in the 
	 *	"application/configuration/application.yml" file at its basic usage.
	 *
	 *	The application can register new settings during runtime by providing a 
	 *	keypath that does not exist, as well as the value to store.  The application
	 *	can update an existing keypath by providing a new value.  This value will
	 *	be valid during the length of the application lifetime, but WILL NOT be saved
	 *	to disk.
	 *
	 *	Key-Paths:
	 *		A keypath is a string for traversing dictionary contents and retrieving
	 *		a value.  An example would be "system.site.name" which performs two 
	 *		dictionary lookups and returns the value of the last string piece
	 *
	 *	Usage:
	 *
	 *		$value = self::get($keypath)
	 *		The return value will be an array or scalar.
	 *
	 *		self::set($keypath, $newValue)
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	
	final class Settings {
			
		private static $config = array();
		
		/**
		 * load
		 *
		 * @param string $filename
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function load($filename='application.yml') {
			
			if ( strrpos($filename, ".yml") === false ) {
				$filename .= ".yml";
			}
			
			if ( !file_exists(CONFIGURATION_PATH.$filename) ) {
				throw new InvalidFileSystemPathException(sprintf("File does not exist: (%s)", CONFIGURATION_PATH.$filename));
			}
						
			self::$config[self::key_for_filename($filename)] = sfYaml::load(CONFIGURATION_PATH.$filename);
			
		}
		
		
		/**
		 * get($path, $default=false, $stripTags=false)
		 * Retrieves a value by recursive lookup using the keypath
		 * 
		 * NOTE: To retrieve a setting not stored in the settings YAML but in the database, use a keypath
		 * with a prefix of "dynamic", such as "dynamic.My Setting"
		 * 
		 * @throws InvalidKeyPathException - if the keypath is invalid or if the setting is undefined
		 *
		 * @param (Keypath|string) $keypath
		 * @param mixed (optional) $default
		 * @param mixed (optional) $stripTags - if true, strip all tags, if false, strip none, if strip, those are the allowable tags (pass to striptags())
		 * @return mixed $value
		 * @author Matt Brewer
		 **/

		public static function get($path, $default=false, $stripTags=false) {
			
			if ( !($path instanceof Keypath) ) {
				$path = new Keypath($path);
			}
			
			if ( $path->length() == 2 && $path->item(0) == "dynamic" ) {
				
				global $db;
				static $_application_settings = array();
				
				$components = $path->components();
				$key = $components[1];
				$hash = md5($key);
				if ( in_array($hash, array_keys($_application_settings)) ) {
					return $_application_settings[$hash] !== false ? $_application_settings[$hash] : $default;
				} else {

					$setting = $db->Execute("SELECT * FROM general_settings WHERE setting_name RLIKE '$key' LIMIT 1");
					if ( !$setting->EOF ) {

						$value = $setting->fields['setting_value'];
						if ( $stripTags === true ) {
							$value = strip_tags(html_entity_decode(stripslashes($value)));
						} else if ( is_string($stripTags) ) {
							$value = strip_tags(html_entity_decode(stripslashes($value)), $stripTags);
						} 
						
						$_application_settings[$hash] = $value;
						return $value;

					} else {

						$_application_settings[$hash] = false;
						return $default;

					}

				}
				
			} else {
				return $path->retrieve(self::$config);
			}
						
		}		
		
		
		/**
		 * set($keypath, $value)
		 * Sets a given keypath to the provided value
		 * 
		 * @throws InvalidKeyPathException - if keypath was invalid
		 *
		 * @param (Keypath|string) $keypath
		 * @param mixed $value
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function set($keypath, $value) {
			
			if ( !($path instanceof Keypath) ) {
				$path = new Keypath($path);
			}
			
			if ( $path->length() == 1 ) {
				self::$config[$path->item(0)] = $value;
			} else if ( $this->length() == 2 ) {
				self::$config[$path->item(0)][$path->item(1)] = $value;
			} else {
				
				$arr =& self::$config[$path->item(0)][$path->item(1)];
				$components = array_slice($path->components,2);
				
				if ( !empty($components) ) {
					
					$set = false;
					foreach($components as $c) {
						if ( isset($arr[$c]) ) {
							$arr =& $arr[$c];
						} else {
							$arr = $value;
							$set = true;
						}
					} 
					
					if ( !$set ) $arr = $value;
					
				} else $arr = $value;
				
			}
						
		}
		
		
		

		/**
		 * load_dynamic_system_settings()
		 * PharosPHP uses several constants in code that are in fact dynamic settings from the database.
		 * This method loads those dynamic settings into the system as constants
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function load_dynamic_system_settings() {

			$default_email = new String('server@%s', Input::server("SERVER_NAME"));

			define('SYS_ADMIN_EMAIL', self::get('dynamic.Admin Email', $default_email, true));
			define('SERVER_MAILER', self::get('dynamic.Server Email', $default_email, true));
			define('SITE_TAGLINE', self::get('dynamic.Site Tagline', 'CMS Framework for Developers', true));
			define('TITLE_SEPARATOR', self::get('dynamic.Title Separator', ' | ', true));
			define('DEFAULT_KEYWORDS', self::get('dynamic.Default Keywords', 'CMS, Content Management System, CMS-Lite, Matt Brewer, PHP', true));
			define('DEFAULT_DESCRIPTION', self::get('dynamic.Default Description', SITE_TAGLINE, true));
			define('DEFAULT_ROWS_PER_TABLE_PAGE', self::get('dynamic.Default Rows per Table Page', 25, true));
			define('DEFAULT_PAGES_PER_PAGINATION', self::get('dynamic.Default Pages per Pagination', 5, true));
			define('SHOW_PROFILER_RESULTS', self::get('dynamic.Show Profiler Results', false, true)==="true"?true:false);	
			define('DELETE_OLD_WHEN_UPLOADING_NEW', self::get('dynamic.Delete Old When Uploading New',"true",true)==="true"?true:false);
			define('RESET_PASSWORD_RANDOM_WORD', self::get('dynamic.Reset Password Random Word', '_cmslite',true));

		}
		
		
		/**
		 * load_static_system_settings
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function load_static_system_settings() {
			
			$pos = strrpos(APP_PATH, APP_DIR);
			$root = substr($_SERVER['DOCUMENT_ROOT'], 0, $pos);
			$root = substr($_SERVER['SCRIPT_FILENAME'], strlen($root));
			$pos = strrpos($root, APP_DIR);
			$root_dir = trim(substr($root, 0, $pos), "/");
						
			$host = ( isset($_SERVER['REDIRECT_HTTPS']) && $_SERVER['REDIRECT_HTTPS'] == "on" ) ? "https://" : "http://";
			define('ROOT_URL', $host.$_SERVER['HTTP_HOST'].'/'.$root_dir.'/');
						
			if ( !defined("UPLOAD_PATH") ) {
				define("UPLOAD_PATH", APP_PATH."uploads/");
			}			
					
			if ( !defined("APP_URL") ) {
				define("APP_URL", ROOT_URL.APP_DIR.'/');
			}			
		
			if ( !defined("PUBLIC_URL") ) {
				define("PUBLIC_URL", APP_URL.PUBLIC_DIR.'/');
			}
			
			if ( !defined("UPLOAD_URL") ) {
				define("UPLOAD_URL", APP_URL."uploads/");
			}
			
			if ( !defined("MODULES_URL") ) {
				define("MODULES_URL", APP_URL."modules/");
			}
						
			define('SECURE_KEYWORD',md5(self::get('application.system.site.name')));
			define('APPLICATION_SECRET_KEY', md5(self::get('application.system.site.name')));
			define('SALT', self::get("application.salt"));
			
		}
		
		
		/**
		 * key_for_filename
		 *
		 * @param string $filename
		 *
		 * @return string $key
		 * @author Matt Brewer
		 **/

		protected static function key_for_filename($filename) {
			$info = pathinfo($filename);
			return $info['filename'];
		}
		
			
	}

?>