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

	class Hooks {
	
		protected static $hooks = array();
		
		const HOOK_APPLICATION_DELETED = 'application_deleted_hook';						// function($app_id) {}
		const HOOK_APPLICATION_PUBLISH = 'application_published_hook';						// function($app_id) {}
		
		const HOOK_CONTROLLER_PRE_CREATED = "controller_pre_created_hook";					// function() {}
		const HOOK_CONTROLLER_POST_CREATED = "controller_post_created_hook";				// function($class) {}
		
		const HOOK_CORE_CLASSES_LOADED = "core_classes_loaded_hook";						// function() {}

		const HOOK_MODULES_PRE_LOADED = 'modules_pre_loaded_hook';							// function() {}
		const HOOK_MODULE_LOADED = 'module_loaded_hook';									// function($module_name) {}
		const HOOK_MODULES_POST_LOADED = 'modules_post_loaded_hook';						// function() {}
		
		const HOOK_PROFILE_MODULE_PRE_PROCESSED = 'profile_module_pre_processed_hook';		// function($fields) {}
		const HOOK_PROFILE_MODULE_POST_PROCESSED = 'profile_module_post_processed_hook';	// function($id, $fields) {}

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
			
			self::$hooks = array(
								
				self::HOOK_APPLICATION_DELETED => null,
				self::HOOK_APPLICATION_PUBLISH => null,
				
				self::HOOK_CORE_CLASSES_LOADED => null,
				
				self::HOOK_CONTROLLER_PRE_CREATED => null,
				self::HOOK_CONTROLLER_POST_CREATED => null,

				self::HOOK_MODULES_PRE_LOADED => null,
				self::HOOK_MODULE_LOADED => null,
				self::HOOK_MODULES_POST_LOADED => null,
				
				self::HOOK_PROFILE_MODULE_PRE_PROCESSED => null,
				self::HOOK_PROFILE_MODULE_POST_PROCESSED => null,
				
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
		 * @return boolean $success
		 * @author Matt Brewer
		 **/

		public static function register_callback($name, $function, $params=array()) {
			
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
					$functions[$f] = (object)array("function" => $f, "params" => $params);
				} return true;
				
			} else return false;

		}
		
		
		
		/**
		 * execute($name, $params=array())
		 * Called by the system to execute associated functions that requested to be called
		 *
		 * @throws InvalidHookException
		 *
		 * @param string $name
		 * @param array $params
		 * @return boolean $success
		 * @author Matt Brewer
		 **/
		
		public static function execute($name, $params=array()) {

			if ( self::_valid_hook($name) ) {

				// Call all functions associated with this task
				$functions = self::$hooks[$name];
				if ( !is_null($functions) && is_array($functions) && !empty($functions) ) {
				
					foreach($functions as $obj) {
																		
						if ( strpos($obj->function, "::") !== false ) {
							
							list($class, $method) = explode("::", $obj->function);
							call_user_func_array(array($class, $method), array_merge($params, $obj->params));
							
						} else {
						
							if ( function_exists($obj->function) ) {
								call_user_func_array($obj->function, $params + $obj->params);
							} else throw new InvalidHookException(sprintf("Hooks::execute(%s): skipping function (%s) - undefined.", $name, $obj->function));
							
						}
						
					}
					
				} else return false;

			} else throw new InvalidHookException(sprintf("Hooks::execute(%s). Hook was undefined.", $name));
			 
			return true;	// Successfully called all hooks if made it to this line

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
			
		}
	
	}
	
?>