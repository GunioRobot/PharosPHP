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
	 *		A keypath is a string for traversing dictionary contents and retreving
	 *		a value.  An example would be "system.site.name" which performs two 
	 *		dictionary lookups and returns the value of the last string piece
	 *
	 *	Usage:
	 *
	 *		$value = Settings::get($keypath)
	 *		The return value will be an array or scalar.
	 *
	 *		Settings::set($keypath, $newValue)
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	Settings::load();
	class Settings {
			
		private static $config = array();
		
		/**
		 * load()
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function load() {
			self::$config = sfYaml::load(CONFIGURATION_DIR.'application.yml');
			load_static_settings();
		}
		
		
		/**
		 * get($path, $default=false, $stripTags=false)
		 * Retrieves a value by recursive lookup using the keypath
		 * 
		 * NOTE: To retrieve a setting not stored in the settings YAML but in the database, use a keypath
		 * with a prefix of "dynamic", such as "dynamic.My Setting"
		 * 
		 * @throws Exception - if the keypath is invalid or if the setting is undefined
		 *
		 * @param string $keypath
		 * @param mixed (optional) $default
		 * @param boolean (optional) $stripTags
		 * @return mixed $value
		 * @author Matt Brewer
		 **/

		public static function get($path, $default=false, $stripTags=false) {
			
			$components = explode(".", trim($path,". "));
			if ( empty($components) ) throw new Exception("Invalid key path ($path)");
			
			
			if ( count($components) == 1 ) {
				
				return self::$config[$components[0]];
				
			} else if ( count($components) == 2 ) {
				
				if ( $components[0] == "dynamic" ) {
				
					global $db;
					static $_application_settings = array();

					$key = $components[0];
					$hash = md5($key);
					if ( in_array($hash, array_keys($_application_settings)) ) {
						return $_application_settings[$hash] !== false ? $_application_settings[$hash] : $default;
					} else {

						$setting = $db->Execute("SELECT * FROM general_settings WHERE setting_name RLIKE '$key' LIMIT 1");
						if ( !$setting->EOF ) {

							$value = $stripTags ? strip_tags(html_entity_decode(stripslashes($setting->fields['setting_value']))) : $setting->fields['setting_value'];
							$_application_settings[$hash] = $value;
							return $value;

						} else {

							$_application_settings[$hash] = false;
							return $default;

						}

					}
				
				} else return self::$config[$components[0]][$components[1]];
				
			} else {
			
				$arr = self::$config[$components[0]][$components[1]];
				$components = array_slice($components,2);
				
				$count = 0;
				if ( !empty($components) ) {
					foreach($components as $c) {
						if ( isset($arr[$c]) ) {
							$arr = $arr[$c];
							$count++;
						} else {
							if ( $count == count($components) ) return $arr;
							else throw new Exception("Setting not defined! (".$path.")");
						}
					} return $arr;
				} else return $arr;
				
			}
			
		}		
		
		
		/**
		 * set($keypath, $value)
		 * Sets a given keypath to the provided value
		 * 
		 * @throws Exception - if keypath was invalid
		 *
		 * @param string $keypath
		 * @param mixed $value
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function set($keypath, $value) {
			
			$components = explode(".", trim($path,". "));
			if ( empty($components) ) {
				throw new Exception("Invalid key path ($path)");
			} else if ( count($components) == 1 ) {
				self::$config[$components[0]] = $value;
			} else if ( count($components) == 2 ) {
				self::$config[$components[0]][$components[1]] = $value;
			} else {

				$arr =& self::$config[$components[0]][$components[1]];
				$components = array_slice($components,2);

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
			
			define('SYS_ADMIN_EMAIL', Settings::get('dynamic.Admin Email', 'matt@dmgx.com', true));
			define('SERVER_MAILER', Settings::get('dynamic.Server Email', 'matt@dmgx.com', true));
			define('SITE_TAGLINE', Settings::get('dynamic.Site Tagline', 'CMS Framework for Developers', true));
			define('TITLE_SEPARATOR', Settings::get('dynamic.Title Separator', ' | ', true));
			define('DEFAULT_KEYWORDS', Settings::get('dynamic.Default Keywords', 'CMS, Content Management System, CMS-Lite, Matt Brewer, PHP', true));
			define('DEFAULT_DESCRIPTION', Settings::get('dynamic.Default Description', SITE_TAGLINE, true));
			define('DEFAULT_ROWS_PER_TABLE_PAGE', Settings::get('dynamic.Default Rows per Table Page', 25, true));
			define('DEFAULT_PAGES_PER_PAGINATION', Settings::get('dynamic.Default Pages per Pagination', 5, true));
			define('SHOW_PROFILER_RESULTS', Settings::get('dynamic.Show Profiler Results', false, true)==="true"?true:false);	
			define('DELETE_OLD_WHEN_UPLOADING_NEW', Settings::get('dynamic.Delete Old When Uploading New',"true",true)==="true"?true:false);
			define('RESET_PASSWORD_RANDOM_WORD', Settings::get('dynamic.Reset Password Random Word', '_cmslite',true));
				
		}
		
			
	}

?>