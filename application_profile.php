<?

	// Required by profile class and repost_mod
	define('PROFILE_TABLE', 'applications');
	define('PROFILE_TITLE', 'Application');
	define('PROFILE_ID', 'app_id');
	
	// Template tags to pull from database and replace
	$field_array = array(
		
		array('name' => 'app_id', 'type' => 'display'),
		array('name' => 'xml_version', 'type' => 'display'),
		array('name' => 'app_name', 'type' => 'text', 'size' => '50' , 'max' => '200'),
		array('name' => 'app_notes', 'type' => 'text_area', 'row' => '8', 'col' => '89'),
		array('name' => 'date_added', 'type' => 'date_added'),
		array('name' => 'last_updated', 'type' => 'last_updated')
		
	);
	
	
	if ( is_super() ) {
		
		
		// The status stuff
		$field_array[] = array('name' => 'status', 'type' => 'static', 'value' => '<div class="floatLeft" style="padding-right:15px;"><strong>Status:</strong><br />', 'varx' => 'hide');
		$field_array[] = array('name' => 'active', 'type' => 'dropdown', 'option' => array('true' => "Active", "false" => "Inactive"), 'default' => array('true' => "Active"));
		$field_array[] = array('name' => '/status', 'type' => 'static', 'value' => '</div>', 'varx' => 'hide');
		
		
		// App Version - let super edit it
		$field_array[] = array('name' => 'app_version', 'type' => 'text', 'max' => '10', 'size' => '10');		
		
	} else {
		
		// The status stuff
		$field_array[] = array('name' => 'status', 'type' => 'static', 'value' => '', 'varx' => 'hide');
		$field_array[] = array('name' => 'active', 'type' => 'static', 'value' => '', 'varx' => 'hide');
		$field_array[] = array('name' => '/status', 'type' => 'static', 'value' => '', 'varx' => 'hide');
		
		
		// App version - just display it
		$field_array[] = array('name' => 'app_version', 'type' => 'display');
		
	}
	
	
	// Run throught the parser and spit out the page
	require CLASSES_DIR.'Profile.php';
	$profile = new Profile($field_array);
	echo $profile->display();
	
?>
