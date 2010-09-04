<?

	/**
	 * Hooks API
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 *
	 * 	System Actions, aka "Hooks"
	 *	Hooks provide the foundation for a flexible framework.
	 *
	 *	The file contains the core system actions defined in this version of 
	 *	PharosPHP.  The Hooks API allows for developers to easily register custom
	 *	callback functions to be executed whenever the specified system action 
	 *	occurs.
	 *
	 *	For even more flexibility, PharosPHP allows developers to register their own
	 *	"system action" so that other developers can register callback functions,
	 *	creating a rich collaborative framework architecture.
	 *
	 **/

	final class Hooks extends Object {
	
		protected static $hooks = array();
		protected static $initialized = false;
		
		const FILTER_PASSWORD_RESET_EMAIL_HTML = "password_reset_email_html_hook";			// function($html, $password) {}
		const FILTER_PASSWORD_RESET_EMAIL_SUBJECT = "password_reset_email_subject_hook";	// function($subject) {}
		const FILTER_PASSWORD_RANDOM_GENERATE = "generate_random_password_hook";			// function($password) {}

		const FILTER_META_DESCRIPTION = "filter_meta_description_hook";						// (string) function ($description) {}
		const FILTER_META_KEYWORDS = "filter_meta_keywords_hook";							// (string) function ($keywords) {}
		const FILTER_META_TITLE = "filter_site_title_hook";									// (string) function($title) {}

		const FILTER_XML_FLASH_CDATA = 'xml_flash_cdata_hook';								// (string) function($string, $anchor_color) {}
		const FILTER_XML_FLASH_TLF_FORMAT = 'xml_flash_tlf_format';							// (string) function($string, $anchor_color) {}
	
		
		const HOOK_APPLICATION_CONTROLLER_LOADED = "application_controller_loaded_hook";	// function($class) {}
		const HOOK_APPLICATION_CORE_LOADED = 'application_core_loaded_hook';				// function() {}
		const HOOK_APPLICATION_CREATE_XML = "application_create_xml_hook";					// function ($app) {}
		const HOOK_APPLICATION_DELETED = 'application_deleted_hook';						// function($app_id) {}
		const HOOK_APPLICATION_PUBLISH = 'application_published_hook';						// function($app_id) {}
		
		const HOOK_AUTHENTICATION_LOADED = 'authentication_loaded_hook';					// function() {}
		
		const HOOK_CACHE_LOADED = 'cache_loaded_hook';										// function() {}
				
		const HOOK_CORE_CLASSES_LOADED = "core_classes_loaded_hook";						// function() {}

		const HOOK_LANGUAGE_API_LOADED = 'language_api_loaded_hook';						// function() {}

		const HOOK_MODULES_PRE_LOADED = 'modules_pre_loaded_hook';							// function() {}
		const HOOK_MODULE_LOADED = 'module_loaded_hook';									// function($module_name) {}
		const HOOK_MODULES_POST_LOADED = 'modules_post_loaded_hook';						// function() {}
		
		const HOOK_SYSTEM_SHORT_INIT_COMPLETE = 'system_short_init_complete_hook';			// function() {}
		const HOOK_SYSTEM_PRE_BOOTSTRAP = 'system_pre_bootstrap_hook';						// function() {}
		const HOOK_SYSTEM_POST_BOOTSTRAP = 'system_post_bootstrap_hook';					// function() {}
	
		const HOOK_TEMPLATE_HEADER = 'template_header_hook';								// function() {}
		const HOOK_TEMPLATE_FOOTER = 'template_footer_hook';								// function() {}
		const HOOK_TEMPLATE_PRE_RENDER = 'template_pre_render_hook';						// function() {}
		const HOOK_TEMPLATE_POST_RENDER = 'template_post_render_hook';						// function() {}

		const HOOK_USER_CREATED = 'user_created_hook';										// function($user_id) {}
		const HOOK_USER_DELETED = 'user_deleted_hook';										// function($user_id) {}
			
		
	
			
		/**
		 * init()
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
							
				self::HOOK_APPLICATION_CONTROLLER_LOADED => null,
				self::HOOK_APPLICATION_CORE_LOADED => null,			
				self::HOOK_APPLICATION_CREATE_XML => null,				
				self::HOOK_APPLICATION_DELETED => null,
				self::HOOK_APPLICATION_PUBLISH => null,
				
				self::HOOK_AUTHENTICATION_LOADED => null,
				
				self::HOOK_CACHE_LOADED => null,
				
				self::HOOK_CORE_CLASSES_LOADED => null,
										
				self::HOOK_LANGUAGE_API_LOADED => null,

				self::HOOK_MODULES_PRE_LOADED => null,
				self::HOOK_MODULE_LOADED => null,
				self::HOOK_MODULES_POST_LOADED => null,
							
				self::HOOK_SYSTEM_SHORT_INIT_COMPLETE => null,
				self::HOOK_SYSTEM_PRE_BOOTSTRAP => null,
				self::HOOK_SYSTEM_POST_BOOTSTRAP => null,

				self::HOOK_TEMPLATE_HEADER => null,
				self::HOOK_TEMPLATE_FOOTER => null,
				self::HOOK_TEMPLATE_PRE_RENDER => null,
				self::HOOK_TEMPLATE_POST_RENDER => null,

				self::HOOK_USER_CREATED => null,
				self::HOOK_USER_DELETED => null
				
			);
			
			self::_register_default_hooks();
			
		}
		

		
		
		/**
		 * register_callback($name, $function, $params)
		 * Register a function to be executed when/if the system performs the action
		 *
		 * @param string $name
		 * @param string $function_name
		 * @param array $extra_parameters
		 * @param Object $obj
		 *
		 * @return boolean $success
		 * @author Matt Brewer
		 **/

		public static function register_callback($name, $function, $params=array(), $obj=null) {
			
			if ( $function != "" && self::_valid_hook($name) ) {
			
				// Convert string to one element array so we can easily iterate
				if ( is_string($function) ) {
					$function = array($function);
				} else if ( !is_array($function) ) {
					$function = array(strval($function));
				}
				
				// Attach multiple functions to one system action
				$functions =& self::$hooks[$name];
				if ( !is_array($functions) ) {
					$functions = array();
				}
				
				foreach($function as $f) {
					$functions[$f] = (object)array("object" => $obj, "function" => $f, "params" => $params);
				} return true;
				
			} else return false;

		}
		
		
		
		/**
		 * execute($name, $params=array())
		 * Called by the system to execute associated functions that requested to be called
		 * The return value is the result of the called functions. For a chain of functions attached to one hook, 
		 * the result returned by the first function is passed in to the next function, etc - the hook returns the last
		 * value, allowing developers to "refine" the return value. 
		 *
		 * @throws InvalidHookException
		 *
		 * @param string $name
		 * @param array $params
		 *
		 * @return mixed $function_return_value
		 * @author Matt Brewer
		 **/
		
		public static function execute($name, $params=array()) {

			if ( self::_valid_hook($name) ) {

				// Call all functions associated with this task
				$functions = self::$hooks[$name];
				if ( is_array($functions) && !empty($functions) ) {
					
					foreach($functions as $obj) {
																		
						if ( strpos($obj->function, "::") !== false ) {
							
							list($class, $method) = explode("::", $obj->function);
							$params['value'] = call_user_func_array(array($class, $method), $params + $obj->params);
							
						} else if ( $obj->object !== null ) {
						
							$params['value'] = call_user_func_array(array($obj->object, $obj->function), $params + $obj->params);
						
						} else {
						
							if ( function_exists($obj->function) ) {
								$params['value'] = call_user_func_array($obj->function, $params + $obj->params);
							} else throw new InvalidHookException(sprintf("Hooks::execute(%s): skipping function (%s) - undefined.", $name, $obj->function));
							
						}
						
					}
										
				} 
				
				return $params['value'];

			} else throw new InvalidHookException(sprintf("Hooks::execute(%s). Hook was undefined.", $name));

		}
		
		
		
		/**
		 * define($name)
		 * Register a new action so other modules can attach to this action in your code
		 * 
		 * @throws InvalidHookException - when attempting to redefine a hook
		 * 
		 * @param string $name
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public static function define($name) {

			if ( self::_valid_hook($name) ) {
				throw new InvalidHookException("Hook ($name) already registered!");
			} else {
				self::$hooks[$name] = null;
			}

		}
		
		
		
		/**
		 * remove_callback($name, $function)
		 * Remove a function from the specified action hook
		 *
		 * @param string $name
		 * @param string $function_name
		 * @return boolean $success
		 * @author Matt Brewer
		 **/
		
		public static function remove_callback($name, $function) {

			if ( self::_valid_hook($name) ) {

				$functions =& self::$hooks[$name];
				if ( !is_null($functions) && is_array($functions) && in_array($function, array_keys($functions)) ) {
					unset($functions[$function]);
				} return true;

			} else return false;

		}
		
		
		
		/**
		 * unset_hook($name)
		 * Remove all functions from the specified action hook
		 * 
		 * @param string $name
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function unset_hook($name) {
			unset(self::$hooks[$name]);	// Don't care if it's valid or not
		}
		
		
		
		
		
		
		
		
		
		
		/**
		 * _valid_hook($name)
		 * Internal function determining if the hook action is a valid one
		 * 
		 * @param string $name
		 * @return boolean $exists
		 * @author Matt Brewer
		 **/

		private static function _valid_hook($name) {			
			return in_array($name, array_keys(self::$hooks));
		}
		
		
		
		
		/**
		 * _register_default_hooks()
		 * Internal function registering default set of action hooks
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		private static function _register_default_hooks() {
			
			self::register_callback(self::HOOK_APPLICATION_PUBLISH, 'clean_upload_dir');
						
			self::register_callback(self::HOOK_TEMPLATE_HEADER, 'Template::write_header_meta');
			self::register_callback(self::HOOK_TEMPLATE_HEADER, 'Template::write_css');
			self::register_callback(self::HOOK_TEMPLATE_HEADER, 'Template::write_js');
			
			self::register_callback(self::HOOK_CORE_CLASSES_LOADED, 'Application::pre_bootstrap');			
			
			self::register_callback(self::HOOK_SYSTEM_SHORT_INIT_COMPLETE, 'Application::load_modules');
			
			self::register_callback(self::HOOK_MODULES_POST_LOADED, 'Application::load_application_files');
			
			self::register_callback(self::HOOK_APPLICATION_CORE_LOADED, 'Application::bootstrap');
			
		}
	
	}
	
?>