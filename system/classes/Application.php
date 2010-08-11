<?

	class Application {
		
		protected $controller = null;
		
		public static function controller() {
			return self::$controller;
		}
		
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
				if ( ($language = Settings::get("application.system.language")) !== Keypath::VALUE_UNDEFINED ) {
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
		
		
		/**
		 * run
		 * Performs the main work of the site - loads in the appropriate controller and executes it
		 *
		 * @throws Exception
		 * @throws InvalidFileSystemPathException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function run() {
			
			global $db;
			
			// The Router has parsed for the class to load, attempt to load it				
			$controllerClass = Router::controller();
			$file = CONTROLLER_DIR.$controllerClass.'.php';	

			try {

				ob_start();

				// Find the page to include, takes care of page_slug = '' for the homepage automatically...
				$page = $db->Execute("SELECT * FROM pages WHERE LOWER(slug) = '".strtolower(Template::controller_slug($controllerClass))."' LIMIT 1");
				if ( !$page->EOF ) {

					$page = clean_object($page->fields);

					// If there is a controller class declared, let's use it
					if ( file_exists($file) ) {

						require_once $file;
						if ( is_subclass_of($controllerClass, "ApplicationGenericPageController") ) {
							self::$controller = new $controllerClass($page->title);
						} else {
							self::$controller = new $controllerClass();
						}

						if ( method_exists(self::$controller, "page") ) {
							self::$controller->page($page);
						}

						if ( method_exists(self::$controller, "pageText") ) {
							self::$controller->pageText($page->text);
						}

						self::_execute();

					} else {

						// Just use the text from the database with generic controller class
						require_once APPLICATION_CLASSES_DIR.'ApplicationGenericPageController.php';
						self::$controller = new ApplicationGenericPageController($page->title);

						if ( method_exists(self::$controller, "page") ) {
							self::$controller->page($page);
						}

						if ( method_exists(self::$controller, "pageText") ) {
							self::$controller->pageText($page->text);
						}

						echo self::$controller->index();

					}

					// Grab the contents of the buffer & give to controller to use. Turn off buffering as well
					self::$controller->output->finalize(ob_get_clean());				

					// Render the template to the browser
					Template::render();

				} else {

					if ( file_exists($file) ) {

						require_once $file;
						self::$controller = new $controllerClass();
						Hooks::call_hook(Hooks::HOOK_CONTROLLER_POST_CREATED, array($controllerClass));	

						// Determine if should process login information or not
						if ( self::$controller->auth->login_required() && !self::$controller->auth->logged_in() ) {
							redirect(Template::controller_link('Session','login/'));
						}

						// Simply return cached information it's available
						if ( server("REQUEST_METHOD") === "GET" && ($cache = Output::cached_content()) !== false ) {
							die($cache);
						}

						self::_execute();

						// Grab the contents of the buffer & give to controller to use. Turn off buffering as well
						self::$controller->output->finalize(ob_get_clean());				

						// Render the template to the browser
						Template::render();

					} else throw new InvalidFileSystemPathException(sprintf("Could not locate file: (%s)", $file));

				}

			} catch (Exception $e) {
				redirect(Template::controller_link("PageNotFound"));
			}
			
		}
		
		
		/**
		 * _execute()
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		protected static function _execute() {
						
			// Call a method on the class (determined by the Router) & capture the output		
			$method = Router::method();	
			if ( method_exists(self::$controller, $method) ) {

				if ( Router::using_named_params() ) {
					call_user_func(array(self::$controller, $method), Router::params());
				} else {
					call_user_func_array(array(self::$controller, $method), Router::params());
				}

			} else throw new Exception("Unknown method (".$method.") for class($controllerClass)");
			
		}
		
	}

?>