<?

	/**
	 * Application
	 *
	 * Used by the system to initialize and serve up pages 
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	final class Application extends Object {
		
		protected static $controller = null;
		
		
		/**
		 * controller
		 *
		 * @return ApplicationController (subclass of)
		 * @author Matt Brewer
		 **/

		public static function controller() {
			return self::$controller;
		}
		
		
		/**
		 * Loads the minimal amount of classes needed to run, without i18n support
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function pre_bootstrap() {
			
			Loader::load_class('Input');
			Loader::load_class('Output');
			Loader::load_class('Cookie');
			
			Loader::load_class('Authentication');
			Hooks::execute(Hooks::HOOK_AUTHENTICATION_LOADED);
			
			Loader::load_class('Cache');
			Cache::init();
			Hooks::execute(Hooks::HOOK_CACHE_LOADED);
			
			Loader::load_class('Cron');
			Loader::load_class('Browser');
			
			Browser::reset();
			Cron::install();
			Router::parse();		
			
			Hooks::execute(Hooks::HOOK_SYSTEM_SHORT_INIT_COMPLETE);	
			
		}
		
		
		/**
		 * load_modules
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function load_modules() {
			Hooks::execute(Hooks::HOOK_MODULES_PRE_LOADED);
			Modules::init();
			Hooks::execute(Hooks::HOOK_MODULES_POST_LOADED);
		}
		
		
		/**
		 * load_application_files
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function load_application_files() {
			
			// Conditionally include support for ActiveRecord
			if ( version_compare(phpversion(), "5.3.0") >= 0 ) {
				require_once CLASSES_PATH.'ActiveRecord/init.php';
			}


			// Load in all the application defined functions
			foreach(glob(APPLICATION_FUNCTIONS_PATH.'*.php') as $filename) {
				require_once $filename;
			}
			
			// Bootstrap the system
			Hooks::execute(Hooks::HOOK_APPLICATION_CORE_LOADED);
			
		}
		
		
		/**
		 * Performs all the initializing needed before a Controller is created and run
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function bootstrap() {
			
			global $db, $CURRENT_APP_ID, $CURRENT_APP_NAME;

			Hooks::execute(Hooks::HOOK_SYSTEM_PRE_BOOTSTRAP);

			// Set the system timezone
			date_default_timezone_set(Settings::get("application.system.timezone"));
			
			// Load in the default language
			try {
				if ( ($language = Settings::get("application.system.language")) !== Keypath::VALUE_UNDEFINED ) {
					Language::setLanguage($language);
					Language::load($language);
				} 
				Hooks::execute(Hooks::HOOK_LANGUAGE_API_LOADED);
			} catch (InvalidKeyPathException $e) {}

			load_content_types();
			Settings::load_dynamic_system_settings();

			$CURRENT_APP_ID = Input::session("app_id", 1);

			$title = $db->Execute("SELECT app_name FROM applications WHERE app_id = '$CURRENT_APP_ID' LIMIT 1");
			$CURRENT_APP_NAME = format_title($title->fields['app_name']);

			Hooks::execute(Hooks::HOOK_SYSTEM_POST_BOOTSTRAP);
			
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
			$file = CONTROLLER_PATH.$controllerClass.'.php';	

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
						
						Hooks::execute(Hooks::HOOK_APPLICATION_CONTROLLER_LOADED, array($controllerClass));	

						if ( method_exists(self::$controller, "page") ) {
							self::$controller->page($page);
						}

						if ( method_exists(self::$controller, "pageText") ) {
							self::$controller->pageText($page->text);
						}

						self::_execute();

					} else {

						// Just use the text from the database with generic controller class
						require_once APPLICATION_CLASSES_PATH.'ApplicationGenericPageController.php';
						self::$controller = new ApplicationGenericPageController($page->title);
						
						Hooks::execute(Hooks::HOOK_APPLICATION_CONTROLLER_LOADED, array($controllerClass));	

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
						Hooks::execute(Hooks::HOOK_APPLICATION_CONTROLLER_LOADED, array($controllerClass));	

						// Determine if should process login information or not
						if ( self::$controller->auth->login_required() && !self::$controller->auth->logged_in() ) {
							redirect(Template::controller_link('Session','login/'));
						}

						// Simply return cached information it's available
						if ( Input::server("REQUEST_METHOD") === "GET" && ($cache = Output::cached_content()) !== false ) {
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
		 * @throws Exception
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