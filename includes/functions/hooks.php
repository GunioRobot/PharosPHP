<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Called by the system to execute associated functions that requested to be called
	//
	////////////////////////////////////////////////////////////////////////////////

	function call_hook($name, $params=array()) {
		
		global $_application_hooks;
		if ( _valid_hook($name) ) {
			
			// Call all functions associated with this task
			$functions = $_application_hooks[$name];
			if ( !is_null($functions) && is_array($functions) && !empty($functions) ) {
				foreach($functions as $func) {
					if ( function_exists($func) ) {
						call_user_func_array($func, $params);
					} else {
						Console::log("call_hook($name): skipping function ($func) - undefined.");
					}
				}
			} else return false;
			
		} else {
			Console::log("Call to call_hook($name) failed.  Hook was undefined.");
			return false;
		}
		
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Register a function to be executed when/if the system performs the action
	//
	////////////////////////////////////////////////////////////////////////////////
		
	function add_hook($name, $function) {
	
		global $_application_hooks;
		if ( _valid_hook($name) ) {
			
			$functions =& $_application_hooks[$name];
			if ( !is_null($functions) && is_array($functions) ) {
				$functions[$function] = $function;
			} else {
				$functions = array($function);
			} return true;
			
		} else return false;
 	
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Register a new action so other modules can attach to this action in your code
	//
	//	Throws exception if you attempt to redefine a hook
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function register_new_hook_type($name) {
	
		global $_application_hooks;
		if ( _valid_hook($name) ) {
			throw new Exception("Hook ($name) already registered!");
		} else {
			$_application_hooks[$name] = null;
		}
		
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Remove a function from the specified action hook
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function remove_hook($name, $function) {
	
		global $_application_hooks;
		if ( _valid_hook($name) ) {
			
			if ( _valid_hook($name) ) {
				
				$functions = $_application_hooks[$name];
				if ( !is_null($functions) && is_array($functions) && in_array($function, $functions) ) {
					unset($functions[$function]);
				} return true;
				
			} else return false;
			
		} else return false;
	
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Remove all functions from the specified action hook
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function remove_hooks_for_name($name) {
		global $_application_hooks;
		unset($_application_hooks[$name]);	// Don't care if it's valid or not
	}
	
	
	
	
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Internal function determining if the hook action is a valid one
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function _valid_hook($name) {
		global $_application_hooks;
		return in_array($name, array_keys($_application_hooks));
	}
	
	
?>