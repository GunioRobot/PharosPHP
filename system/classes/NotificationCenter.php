<?

	/**
	 * 	NotificationCenter
	 *
	 * 	@package PharosPHP.Core.Classes
	 *
	 * 	Notification system for application messaging, providing the foundation for a flexible framework.
	 *
	 *	The file contains the core system actions defined in this version of 
	 *	PharosPHP.  The NotificationCenter API allows for developers to easily register custom
	 *	callback functions to be executed whenever the specified system action 
	 *	occurs.
	 *
	 *	For even more flexibility, PharosPHP allows developers to register their own
	 *	"system action" so that other developers can register callback functions,
	 *	creating a rich collaborative framework architecture.
	 *
	 * 	@author Matt Brewer
	 *
	 **/

	final class NotificationCenter extends Object {
	
		protected static $hooks = array();
		protected static $initialized = false;
		
		const FILTER_PASSWORD_RESET_EMAIL_HTML = "password_reset_email_html_hook";					// function($html, $password) {}
		const FILTER_PASSWORD_RESET_EMAIL_SUBJECT = "password_reset_email_subject_hook";			// function($subject) {}
		const FILTER_PASSWORD_RANDOM_GENERATE = "generate_random_password_hook";					// function($password) {}

		const FILTER_META_DESCRIPTION = "filter_meta_description_hook";								// (string) function ($description) {}
		const FILTER_META_KEYWORDS = "filter_meta_keywords_hook";									// (string) function ($keywords) {}
		const FILTER_META_TITLE = "filter_site_title_hook";											// (string) function($title) {}

		const FILTER_XML_FLASH_CDATA = 'xml_flash_cdata_hook';										// (string) function($string, $anchor_color) {}
		const FILTER_XML_FLASH_TLF_FORMAT = 'xml_flash_tlf_format';									// (string) function($string, $anchor_color) {}
	
		const APPLICATION_CONTROLLER_LOADED_NOTIFICATION = "application_controller_loaded_hook";	// function($class) {}
		const APPLICATION_CORE_LOADED_NOTIFICATION = 'application_core_loaded_hook';				// function() {}
		const APPLICATION_CREATE_XML_NOTIFICATION = "application_create_xml_hook";					// function ($app) {}
		const APPLICATION_DELETED_NOTIFICATION = 'application_deleted_hook';						// function($app_id) {}
		const APPLICATION_PUBLISH_NOTIFICATION = 'application_published_hook';						// function($app_id) {}
		
		const AUTHENTICATION_LOADED_NOTIFICATION = 'authentication_loaded_hook';					// function() {}
		
		const CACHE_LOADED_NOTIFICATION = 'cache_loaded_hook';										// function() {}
				
		const CORE_CLASSES_LOADED_NOTIFICATION = "core_classes_loaded_hook";						// function() {}

		const LANGUAGE_API_LOADED_NOTIFICATION = 'language_api_loaded_hook';						// function() {}

		const MODULES_PRE_LOADED_NOTIFICATION = 'modules_pre_loaded_hook';							// function() {}
		const MODULE_LOADED_NOTIFICATION = 'module_loaded_hook';									// function($module_name) {}
		const MODULES_POST_LOADED_NOTIFICATION = 'modules_post_loaded_hook';						// function() {}
		
		const SYSTEM_SHORT_INIT_COMPLETE_NOTIFICATION = 'system_short_init_complete_hook';			// function() {}
		const SYSTEM_PRE_BOOTSTRAP_COMPLETE_NOTIFICATION = 'system_pre_bootstrap_hook';				// function() {}
		const SYSTEM_POST_BOOTSTRAP_NOTIFICATION = 'system_post_bootstrap_hook';					// function() {}
	
		const TEMPLATE_HEADER_NOTIFICATION = 'template_header_hook';								// function() {}
		const TEMPLATE_FOOTER_NOTIFICATION = 'template_footer_hook';								// function() {}
		const TEMPLATE_PRE_RENDER_NOTIFICATION = 'template_pre_render_hook';						// function() {}
		const TEMPLATE_POST_RENDER_NOTIFICATION = 'template_post_render_hook';						// function() {}			
		
	
			
		/**
		 * init
		 * Constructs object & registers default set of hook actions
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function init() {
			
			// Don't allow more than once
			if ( self::$initialized ) {
				return;
			} 
			
			self::$initialized = true;
			self::$hooks = array(
				
				self::FILTER_META_DESCRIPTION => null,
				self::FILTER_META_KEYWORDS => null,
				self::FILTER_META_TITLE => null,

				self::FILTER_PASSWORD_RESET_EMAIL_HTML => null,
				self::FILTER_PASSWORD_RESET_EMAIL_SUBJECT => null,
				self::FILTER_PASSWORD_RANDOM_GENERATE => null,

				self::FILTER_XML_FLASH_CDATA => null,
				self::FILTER_XML_FLASH_TLF_FORMAT => null,
							
				self::APPLICATION_CONTROLLER_LOADED_NOTIFICATION => null,
				self::APPLICATION_CORE_LOADED_NOTIFICATION => null,			
				self::APPLICATION_CREATE_XML_NOTIFICATION => null,				
				self::APPLICATION_DELETED_NOTIFICATION => null,
				self::APPLICATION_PUBLISH_NOTIFICATION => null,
				
				self::AUTHENTICATION_LOADED_NOTIFICATION => null,
				
				self::CACHE_LOADED_NOTIFICATION => null,
				
				self::CORE_CLASSES_LOADED_NOTIFICATION => null,
										
				self::LANGUAGE_API_LOADED_NOTIFICATION => null,

				self::MODULES_PRE_LOADED_NOTIFICATION => null,
				self::MODULE_LOADED_NOTIFICATION => null,
				self::MODULES_POST_LOADED_NOTIFICATION => null,
							
				self::SYSTEM_SHORT_INIT_COMPLETE_NOTIFICATION => null,
				self::SYSTEM_PRE_BOOTSTRAP_COMPLETE_NOTIFICATION => null,
				self::SYSTEM_POST_BOOTSTRAP_NOTIFICATION => null,

				self::TEMPLATE_HEADER_NOTIFICATION => null,
				self::TEMPLATE_FOOTER_NOTIFICATION => null,
				self::TEMPLATE_PRE_RENDER_NOTIFICATION => null,
				self::TEMPLATE_POST_RENDER_NOTIFICATION => null
				
			);
			
			self::_register_default_hooks();
			
		}
		
		
		/**
		 * register_callback
		 * Register a function to be executed when/if the system performs the action
		 *
		 * @param string $notification
		 * @param callback $callback
		 * @param int $priority
		 * @param ... va_args
		 *
		 * @return boolean $success
		 * @author Matt Brewer
		 **/

		public static function register_callback($notification, $callback, $priority=1) {
			
			if ( (is_array($callback) && count($callback) != 2) || $callback == "" ) return false;
			if ( self::_valid_hook($notification) ) {
				
				// Attach multiple functions to one system action
				$functions =& self::$hooks[$notification];
				if ( !is_array($functions) ) {
					$functions = array();
				}
				
				$params = array_slice(func_get_args(), 2);
				$functions[$priority][self::_serialize($callback)] = (object)compact("callback", "params");
				
				return true;
				
			} else return false;

		}
		
		
		/**
		 * execute
		 * Called by the system to execute associated functions that requested to be called
		 * The return value is the result of the called functions. For a chain of functions attached to one hook, 
		 * the result returned by the first function is passed in to the next function, etc - the hook returns the last
		 * value, allowing developers to "refine" the return value. 
		 *
		 * @throws InvalidHookException
		 *
		 * @param string $notification
		 * @param ... va_args
		 *
		 * @return mixed $function_return_value
		 * @author Matt Brewer
		 **/
		
		public static function execute($notification) {

			$params = array_slice(func_get_args(), 1);
			$value = "__ignore__";
			
			if ( self::_valid_hook($notification) ) {

				// Call all functions associated with this task
				$priorities = self::$hooks[$notification];
				if ( is_array($priorities) ) {
																				
					foreach($priorities as $priority => $functions) {		
						
						if ( is_array($functions) ) {

							foreach($functions as $obj) {
								
								if ( $value !== "__ignore__" ) {
									$args = array_merge(array($value), $obj->params, $params);
								} else {
									$args = array_merge($obj->params, $params);
								}
								
								if ( count($obj->callback) == 2 ) {			// Static or instance method on a class/object
									$value = call_user_func_array($obj->callback, $args);
								} else if ( count($obj->callback) == 1 ) {	// Function
									if ( function_exists($obj->callback) ) {
										$value = call_user_func_array($obj->callback, $args);
									} else throw new InvalidHookException(sprintf("NotificationCenter::execute(%s): skipping function (%s) - undefined.", $notification, $obj->callback));
								}

							}

						}
						
					}
					
				}
				
				return $value !== "__ignore__" ? $value : reset($params);

			} else throw new InvalidHookException(sprintf("NotificationCenter::execute(%s). Hook was undefined.", $notification));

		}
		
		
		/**
		 * define
		 * Register a new action so other modules can attach to this action in your code
		 * 
		 * @throws InvalidHookException - when attempting to redefine a hook
		 * 
		 * @param string $notification
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function define($notification) {

			if ( self::_valid_hook($notification) ) {
				throw new InvalidHookException("Hook ($notification) already registered!");
			} else {
				self::$hooks[$notification] = null;
			}

		}
		
		
		
		/**
		 * remove_callback
		 * Remove a function from the specified action hook
		 *
		 * @param string $notification
		 * @param mixed $callback
		 *
		 * @return boolean $success
		 * @author Matt Brewer
		 **/
		
		public static function remove_callback($notification, $callback) {

			if ( self::_valid_hook($notification) ) {

				$functions =& self::$hooks[$notification];				
				if ( !is_null($functions) && is_array($functions) && in_array(self::_serialize($callback), array_keys($functions)) ) {
					unset($functions[$function]);
				} return true;

			} else return false;

		}
		
		
		/**
		 * unset_hook
		 * Remove all functions from the specified action hook
		 * 
		 * @param string $notification
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function unset_hook($notification) {
			unset(self::$hooks[$notification]);	// Don't care if it's valid or not
		}
				
		
		/**
		 * _valid_hook
		 * Internal function determining if the hook action is a valid one
		 * 
		 * @param string $notification
		 *
		 * @return boolean $exists
		 * @author Matt Brewer
		 **/

		private static function _valid_hook($notification) {			
			return in_array($notification, array_keys(self::$hooks));
		}
		
		
		
		
		/**
		 * _register_default_hooks
		 * Internal function registering default set of action hooks
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		private static function _register_default_hooks() {
									
			self::register_callback(self::TEMPLATE_HEADER_NOTIFICATION, array("Template", "write_header_meta"));
			self::register_callback(self::TEMPLATE_HEADER_NOTIFICATION, array("Template", "write_css"));
			self::register_callback(self::TEMPLATE_HEADER_NOTIFICATION, array("Template", "write_js"));
			
			self::register_callback(self::CORE_CLASSES_LOADED_NOTIFICATION, array("Application", "pre_bootstrap"));			
			
			self::register_callback(self::SYSTEM_SHORT_INIT_COMPLETE_NOTIFICATION, array("Application", "load_modules"));
			
			self::register_callback(self::MODULES_POST_LOADED_NOTIFICATION, array("Application", "load_application_files"));
			
			self::register_callback(self::APPLICATION_CORE_LOADED_NOTIFICATION, array("Application", "bootstrap"));
			
		}
		
		
		/**
		 * _serialize
		 * Internal function to store an array as a key in the functions array
		 *
		 * @return string $hash
		 * @author Matt Brewer
		 **/
		
		private static function _serialize($callback) {
			if ( is_array($callback) ) {
				$callback = implode(":", $callback);
			} else {
				$callback = strval($callback);
			} return md5($callback);
		}
	
	}
	
?>