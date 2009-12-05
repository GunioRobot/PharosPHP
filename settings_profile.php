<?

	// Required by profile class and repost_mod
	define('PROFILE_TABLE', 'general_settings');
	define('PROFILE_TITLE', 'Setting');
	define('PROFILE_ID', 'setting_id');
	
	// Template tags to pull from database and replace
	$field_array = array(
		
		array('name' => PROFILE_ID, 'type' => 'display'),
		array('name' => 'setting_value', 'type' => 'text_area', 'row' => '8', 'col' > '92', 'width' => '738px', 'height' => '100px'),
		array('name' => 'setting_notes', 'type' => 'text_area', 'row' => '8', 'col' > '92', 'width' => '738px', 'height' => '100px'),
		array('name' => 'date_added', 'type' => 'date_added'),
		array('name' => 'last_updated', 'type' => 'last_updated'),
		
		array('name' => 'content_type_id', 'type' => 'hidden', 'value' => SETTINGS_TYPE_ID),
		array('name' => 'content_type_name', 'type' => 'hidden', 'value' => strtolower(PROFILE_TITLE))
		
	);
	
	if ( is_super() ) {
		$field_array[] = array('name' => 'setting_name', 'type' => 'text', 'size' => '50' , 'max' => '200');
	} else if ( is_admin() ) {
		$field_array[] = array('name' => 'setting_name', 'type' => 'display');
	}

	
	// Run throught the parser and spit out the page
	require CLASSES_DIR.'Profile.php';
	$profile = new Profile($field_array);
	echo $profile->display();

?>
