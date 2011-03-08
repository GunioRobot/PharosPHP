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
		
		const PAGE_LOOKUP_SQL_FILTER = "page_lookup_sql_notification";
		
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
		 * environment
		 * Retrieves the settings for the active environment
		 *
		 * @return stdClass $settings
		 * @author Matt Brewer
		 **/
		
		public static function environment() {
			static $environment = null;
			if ( is_null($environment) ) {
				foreach(array("development", "testing", "production") as $env) {
					if ( ($settings = Settings::get(sprintf("application.environment.%s.enabled", $env))) === true ) {
						$environment = new stdClass;
						$environment->env = $env;
						$environment->settings = clean_object(Settings::get(sprintf("application.environment.%s", $env)));
						break;
					}
				}
			} 
			return $environment;
		}
		
		
		/**
		 * pre_bootstrap
		 * Loads the minimal amount of classes needed to run, without i18n support
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function pre_bootstrap() {
			
			NotificationCenter::define(Application::PAGE_LOOKUP_SQL_FILTER);
			
			Loader::load_class('Input');
			Loader::load_class('HTTPResponse');
			Loader::load_class('Cookie');
			
			Loader::load_class('Authentication');
			NotificationCenter::execute(NotificationCenter::AUTHENTICATION_LOADED_NOTIFICATION);
			
			Loader::load_class('Cache');			
			Cache::init();
			NotificationCenter::execute(NotificationCenter::CACHE_LOADED_NOTIFICATION);
			
			Loader::load_class('Cron');
			Loader::load_class('Browser');
			
			Browser::reset();
			Router::parse();		
						
			NotificationCenter::execute(NotificationCenter::SYSTEM_SHORT_INIT_COMPLETE_NOTIFICATION);	
			
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
			$files = glob(APPLICATION_FUNCTIONS_PATH.'*.php');
			foreach(($files !== false ? $files : array()) as $filename) {
				require_once $filename;
			}
			
			// Bootstrap the system
			NotificationCenter::execute(NotificationCenter::APPLICATION_CORE_LOADED_NOTIFICATION);
			
		}
		
		
		/**
		 * bootstrap
		 * Performs all the initializing needed before a Controller is created and run
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function bootstrap() {
			
			global $db;

			NotificationCenter::execute(NotificationCenter::SYSTEM_PRE_BOOTSTRAP_COMPLETE_NOTIFICATION);

			// Set the system timezone
			date_default_timezone_set(Settings::get("application.system.timezone"));
			
			// Load in the default language
			try {
				if ( ($language = Settings::get("application.system.language")) !== Keypath::VALUE_UNDEFINED ) {
					Language::setLanguage($language);
					Language::load($language);
				} 
				NotificationCenter::execute(NotificationCenter::LANGUAGE_API_LOADED_NOTIFICATION, $language);
			} catch (InvalidKeyPathException $e) {}

			Settings::load_dynamic_system_settings();
		
			NotificationCenter::execute(NotificationCenter::SYSTEM_POST_BOOTSTRAP_NOTIFICATION);
			
		}
		
		
		/**
		 * run
		 * Performs the main work of the site - loads in the appropriate controller and executes it
		 *
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
				
				$slug = strtolower(Template::controller_slug($controllerClass));
				$sql = "SELECT * FROM `pages` WHERE LOWER(`slug`) = '" . $slug . "' LIMIT 1";
				$page = $db->Execute(NotificationCenter::execute(Application::PAGE_LOOKUP_SQL_FILTER, $sql, $slug));
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
						
						NotificationCenter::execute(NotificationCenter::APPLICATION_CONTROLLER_LOADED_NOTIFICATION, $controllerClass);	

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
						
						NotificationCenter::execute(NotificationCenter::APPLICATION_CONTROLLER_LOADED_NOTIFICATION, $controllerClass);	

						if ( method_exists(self::$controller, "page") ) {
							self::$controller->page($page);
						}

						if ( method_exists(self::$controller, "pageText") ) {
							self::$controller->pageText($page->text);
						}

						self::_execute();

					}

					// Grab the contents of the buffer & give to controller to use. Turn off buffering as well
					self::$controller->output->finalize(ob_get_clean());				

					// Render the template to the browser
					Template::render();

				} else {

					if ( file_exists($file) ) {

						require_once $file;
						self::$controller = new $controllerClass();
						NotificationCenter::execute(NotificationCenter::APPLICATION_CONTROLLER_LOADED_NOTIFICATION, $controllerClass);	

						// Determine if should process login information or not
						if ( self::$controller->auth->login_required() && !self::$controller->auth->logged_in() ) {
							redirect(Template::controller_link('Session','login/'));
						}

						// Simply return cached information it's available
						if ( Input::server("REQUEST_METHOD") === "GET" && ($cache = HTTPResponse::cached_content()) !== false ) {
							if ( !empty($cache->headers) ) {
								foreach($cache->headers as $h) {
									header($h);
								}
							}
							die($cache->content);
						}

						self::_execute();

						// Grab the contents of the buffer & give to controller to use. Turn off buffering as well
						self::$controller->output->finalize(ob_get_clean());				

						// Render the template to the browser
						Template::render();

					} else {
						throw new InvalidFileSystemPathException(sprintf("Could not locate file: (%s)", $file));
					}

				}

			} catch (ControllerActionNotFoundException $e) {
				redirect(Template::controller_link("error/404"));
			} catch (InvalidFileSystemPathException $e) {
				redirect(Template::controller_link("error/404"));
			}
			
		}
		
		
		/**
		 * exception_handler
		 * Handles all uncaught exceptions in the application
		 * NOTE: Script execution stops after this method completes execution
		 *
		 * @param Exception $exception
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/
		public static function exception_handler($exception) {
			require_once VIEWS_PATH . 'errors' . DS . 'exception.php';
		}
		
		
		/**
		 * error_handler
		 * Handles errors that occur in the script
		 *
		 * @param int $errno
		 * @param string $errstr
		 * @param string $errfile
		 * @param int $errline
		 * @param array $errcontext
		 * 
		 * @return boolean $halt_execution
		 * @author Matt Brewer
		 **/
		public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {

			// This error code is not included in error_reporting
			if ( !(error_reporting() & $errno) ) {
		        return;
		    }
		
			$log = function_exists('NSLog');

			$message = "";
		    switch ($errno) {
		   	 	case E_USER_ERROR:
					if ( $log ) NSLog("ERROR: [%d]: '%s' on line %s in file %s.", $errno, $errstr, $errline, $errfile);
			        $message .= "<b>FATAL ERROR</b> [$errno] $errstr<br />\n";
			        $message .= "  Fatal error on line $errline in file $errfile";
			        $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			        $message .= "Aborting...<br />\n";
					require VIEWS_PATH . 'errors' . DS . 'error.php';
			        exit(1);
			        break;

			    case E_USER_WARNING:
					if ( $log ) NSLog("WARNING: [%d]: %s", $errno, $errstr);
			        break;

			    case E_USER_NOTICE:
					if ( $log ) NSLog("NOTICE: [%d]: %s", $errno, $errstr);
			        break;

			    default:
			       	if ( $log ) NSLog("UNKNOWN: [%d]: %s", $errno, $errstr);
			        break;
		    }

		    // Don't execute PHP internal error handler
		    return $log;
			
		}
		
		
		/**
		 * _execute()
		 * The most important method in the entire framework
		 * Executes the correct method on the ApplicationController subclass
		 *
		 * @throws ControllerActionNotFoundException
		 * 
		 * @return void
		 * @author Matt Brewer
		 **/

		protected static function _execute() {
						
			// Call a method on the class (determined by the Router) & capture the output		
			$method = Router::method();	
			if ( method_exists(self::$controller, $method) ) {
				
				self::$controller->__preRender();

				if ( Router::using_named_params() ) {
					call_user_func(array(self::$controller, $method), Router::params());
				} else {
					call_user_func_array(array(self::$controller, $method), Router::params());
				}
				
				self::$controller->__postRender();

			} else {
				self::$controller->__missingControllerAction(get_class(self::$controller), $method, Router::params());			
			}
			
		}
		
	}

?>