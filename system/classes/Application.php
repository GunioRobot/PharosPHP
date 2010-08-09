<?

	class Application {
		
		public static function pre_bootstrap() {
			
			Loader::load_class('Output');
			Loader::load_class('Cookie');
			Loader::load_class('Authentication');
			Loader::load_class('Cache');
			Loader::load_class('Cron');
			Loader::load_class('Browser');
			
			Browser::reset();
			Cache::init();
			Cron::install();
			Router::parse();			
			
		}
		
		public static function bootstrap() {
			
			global $db, $CURRENT_APP_ID, $CURRENT_APP_NAME;

			Hooks::call_hook(Hooks::HOOK_SYSTEM_PRE_BOOTSTRAP);

			// Set the system timezone
			date_default_timezone_set(Settings::get("application.system.timezone"));
			
			// Load in the default language
			try {
				if ( ($language = Settings::get("system.language")) !== Keypath::VALUE_UNDEFINED ) {
					Language::setLanguage($language);
					Language::load($language);
				} 
			} catch (InvalidKeyPathException $e) {}

			load_content_types();
			Settings::load_dynamic_system_settings();

			$CURRENT_APP_ID = session("app_id", 1);

			$title = $db->Execute("SELECT app_name FROM applications WHERE app_id = '$CURRENT_APP_ID' LIMIT 1");
			$CURRENT_APP_NAME = format_title($title->fields['app_name']);

			Hooks::call_hook(Hooks::HOOK_SYSTEM_POST_BOOTSTRAP);
			
		}
		
	}

?>