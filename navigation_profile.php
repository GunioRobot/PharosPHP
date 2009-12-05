<?

	// Required by profile class and repost_mod
	define('PROFILE_TABLE', 'admin_nav');
	define('PROFILE_TITLE', 'Navigation');
	define('PROFILE_ID', 'id');
	
	
	// Template tags to pull from database and replace
	$field_array = array(
		array('name' => 'parent_id' ,'type' => 'text', 'size' => '3' , 'max' => '3'),
		array('name' => 'name' ,'type' => 'text', 'size' => '50' , 'max' => '200'),
		array('name' => 'iPhone_title' ,'type' => 'text', 'size' => '50' , 'max' => '200'),
		array('name' => 'page' ,'type' => 'text_area', 'row' => 8, 'col' => 43),
		array('name' => 'order_num' ,'type' => 'text', 'size' => '2' , 'max' => '2'),
		array('name' => 'display' ,'type' => 'dropdown', 'option' => array('visible' => 'visible','hidden' => 'hidden'), 'default' =>'visible'),
		array('name' => 'device_type' ,'type' => 'dropdown', 'option' => array('iphone' => 'iPhone', 'pc' => 'PC/Mac', 'all' => 'All'), 'default' =>'all')
	);
	
	for ( $rr = $db->Execute("SELECT * FROM user_levels ORDER BY user_level_id ASC"); !$rr->EOF; $rr->moveNext() ) {
		$levels[$rr->fields['user_level_id']] = format_title($rr->fields['user_level_name']);
	}
	
	$field_array[] = array('name' => 'min_lvl', 'type' => 'dropdown', 'option' => $levels, 'default' => ADMIN_LVL);
	$field_array[] = array('name' => 'max_lvl', 'type' => 'dropdown', 'option' => $levels, 'default' => ADMIN_LVL);
	
	
	
	
	// Run throught the parser and spit out the page
	require CLASSES_DIR.'Profile.php';
	$profile = new Profile($field_array);
	echo $profile->display();
	
?>
