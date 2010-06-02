<?

	class Hooks {
	
		protected $hooks;
	
	
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Constructs object & registers default set of hook actions
		//
		////////////////////////////////////////////////////////////////////////////////

		public function __construct() {
			
			$this->hooks = array(
				HOOK_APPLICATION_BOOTSTRAP => null,
				HOOK_APPLICATION_PUBLISH => null,

				HOOK_MODULE_LOADED => null,

				HOOK_TEMPLATE_HEADER => null,
				HOOK_TEMPLATE_PRE_RENDER => null,
				HOOK_TEMPLATE_POST_RENDER => null,

				HOOK_USER_CREATED => null,
				HOOK_USER_DELETED => null
			);
			
			$this->_register_default_hooks();
			
		}
		
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Register a function to be executed when/if the system performs the action
		//
		////////////////////////////////////////////////////////////////////////////////

		public function add_hook($name, $function) {
			
			if ( $function != "" && $this->_valid_hook($name) ) {
			
				// Convert string to one element array so we can easily iterate
				if ( is_string($function) ) {
					$function = array($function);
				} else if ( !is_array($function) ) {
					$function = array(strval($function));
				}
				
				// Attach multiple functions to one system action
				$functions =& $this->hooks[$name];
				foreach($function as $f) {
					if ( !is_null($functions) && is_array($functions) ) {
						$functions[$function] = $function;
					} else {
						$functions = array($function);
					} 
				} return true;
				
			} else return false;

		}
		
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Called by the system to execute associated functions that requested to be called
		//
		////////////////////////////////////////////////////////////////////////////////
		
		public function call_hook($name, $params=array()) {

			if ( $this->_valid_hook($name) ) {

				// Call all functions associated with this task
				$functions = $this->hooks[$name];
				if ( !is_null($functions) && is_array($functions) && !empty($functions) ) {
					foreach($functions as $func) {
						if ( function_exists($func) ) {
							call_user_func_array($func, $params);
						} else {
							Console::log("Hooks::call_hook($name): skipping function ($func) - undefined.");
						}
					}
				} else return false;

			} else {
				Console::log("Call to Hooks::call_hook($name) failed.  Hook was undefined.");
				return false;
			}

		}
		
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Register a new action so other modules can attach to this action in your code
		//
		//	Throws exception if you attempt to redefine a hook
		//
		////////////////////////////////////////////////////////////////////////////////

		public function register_new_hook_action($name) {

			if ( $this->_valid_hook($name) ) {
				throw new Exception("Hook ($name) already registered!");
			} else {
				$this->hooks[$name] = null;
			}

		}
		
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Remove a function from the specified action hook
		//
		////////////////////////////////////////////////////////////////////////////////

		public function remove_hook($name, $function) {

			if ( $this->_valid_hook($name) ) {

				$functions = $this->hooks[$name];
				if ( !is_null($functions) && is_array($functions) && in_array($function, $functions) ) {
					unset($functions[$function]);
				} return true;

			} else return false;

		}
		
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Remove all functions from the specified action hook
		//
		////////////////////////////////////////////////////////////////////////////////

		public function remove_hooks_for_name($name) {
			unset($this->hooks[$name]);	// Don't care if it's valid or not
		}
		
		
		
		
		
		
		
		
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Internal function determining if the hook action is a valid one
		//
		////////////////////////////////////////////////////////////////////////////////

		private function _valid_hook($name) {
			return in_array($name, array_keys($this->hooks));
		}
		
		
		
		
		////////////////////////////////////////////////////////////////////////////////
		//
		//	Internal function registering default set of action hooks
		//
		////////////////////////////////////////////////////////////////////////////////
		
		private function _register_default_hooks() {
			
			$this->add_hook(HOOK_APPLICATION_PUBLISH, 'clean_upload_dir');
			
			$this->add_hook(HOOK_APPLICATION_BOOTSTRAP, array(
					'load_content_types', 
					'load_dynamic_system_settings',
					'load_defines',
					'load_automatic_modules',
					'app_bootstrap')
			);
			
			$this->add_hook(HOOK_TEMPLATE_HEADER, 'write_css');
			$this->add_hook(HOOK_TEMPLATE_HEADER, 'write_js');
			
		}
	
	}
	
?>