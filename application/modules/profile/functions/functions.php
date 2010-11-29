<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	choose_form_type($form_array, $id, $value, $field_id, $link_value='null')
	//
	//	Most of the params are passed through to the require() form_type
	//	Returns a string of html to insert into the template
	//
	////////////////////////////////////////////////////////////////////////////////

	function choose_form_type($form_array,$id,$value,$field_id,$link_value = 'null') {

		global $db;
			
		$file = FORM_TYPE_DIR.'create/'.$form_array['type'].'.php';
	
		if ( file_exists($file) )  require($file);
		else $item = 'Form Function Not Set ('.$form_array['type'].')';
	
		return $item;
	}



	////////////////////////////////////////////////////////////////////////////////
	//
	//	process_form_type($string)
	//
	//	Runs the corresponding code to process the original "form_type" after form
	//	submitatl.
	//
	////////////////////////////////////////////////////////////////////////////////	

	function process_form_type($type) {
	
		$file = FORM_TYPE_DIR.'save/'.$type.'.php';
		if ( file_exists($file) ) return $file;
		else throw new Exception("form_type did not exist: ($file)");
	
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Simply processes the form
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function process_profile($id) {
		global $db, $CURRENT_APP_ID, $CURRENT_APP_NAME;
		require_once FORM_TYPE_DIR.'/process_profile.php';
		return $id;
	}


	////////////////////////////////////////////////////////////////////////////////
	//
	//	Returns array of options
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function get_options($input) {
		$options = explode(':', trim($input['varx']));
		foreach($options as $op) {
			$_1 = explode('@',trim($op));
			$temp[trim($_1[0])] = trim($_1[1]);
		} return $temp;
	}
	
	
	/**
	 * define_profile_module_hooks
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function define_profile_module_hooks() {
		
		define('EXTERNAL_HOOK_PROFILE_MODULE_PRE_PROCESSED', 'profile_module_pre_processed_external_hook');
		define('EXTERNAL_HOOK_PROFILE_MODULE_POST_PROCESSED', 'profile_module_post_processed_external_hook');
		
		NotificationCenter::define(EXTERNAL_HOOK_PROFILE_MODULE_PRE_PROCESSED);		// function ($fields) {}
		NotificationCenter::define(EXTERNAL_HOOK_PROFILE_MODULE_POST_PROCESSED);	// function ($id, $fields) {}
		
	}
	
?>