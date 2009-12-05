<?php

	// Required by Profile class and repost_mod system
	define('PROFILE_TABLE', 'users');
	define('PROFILE_TITLE', 'User');
	define('PROFILE_ID', 'user_id');
				
	$companyID = get("company_id");
					
	$title = get(PROFILE_ID) ? 'Edit ' : 'Create ';
	$title .= $companyID ? 'User' : 'Administrator';			
					
	$fields = array(
		
		array('name' => 'user_id', 'type' => 'display'),
		array('name' => 'user_first_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'fname'),
		array('name' => 'user_middle_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'mname'),
		array('name' => 'user_last_name', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_phone_number', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_fax_number', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_address_line_1', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_address_line_2', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_city', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'city'),
		array('name' => 'user_state', 'type' => 'dropdown', 'option' => array_values(states_array()), 'class' => 'state'),
		array('name' => 'user_zip', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'zip'),
		array('name' => 'user_username', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_password', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_primary_email', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_secondary_email', 'type' => 'text', 'size' => 32, 'max' => 200, 'class' => 'singleText'),
		array('name' => 'user_birthday', 'type' => 'dob'),
		array('name' => 'user_level', 'type' => 'dropdown', 'option' => user_levels_array($companyID?BASIC_USER_LVL:level_for_user(get("user_id", ""))), 'default' => ((is_admin()&&$companyID)?BASIC_USER_LVL:ADMIN_LVL)),
		array('name' => 'date_added', 'type' => 'date_added'),
		array('name' => 'last_updated', 'type' => 'last_updated'),
		array('name' => 'user_notes', 'type' => 'text_area', 'class' => 'notes'),
		
		array('name' => 'company_id', 'type' => 'hidden', 'value' => $companyID),
		array('name' => '{company_id_show}', 'type' => 'static', 'value' => $companyID),
		array('name' => '{company_id}', 'type' => 'static', 'value' => '&company_id='.$companyID, 'varx' => 'hide'),
		
		array('name' => 'title', 'type' => 'static', 'value' => $title, 'varx' => 'hide'),
		
		
		array('name' => 'content_type_id', 'type' => 'hidden', 'value' => USER_TYPE_ID),
		array('name' => 'content_type_name', 'type' => 'hidden', 'value' => strtolower(PROFILE_TITLE))
	 
	);
	
	if ( is_admin() ) {
		if ( $companyID ) {
			$fields[] = array('name' => 'manage_company', 'type' => 'static', 'varx' => 'hide', 'value' => '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid=44&key=company_id&company_id='.$companyID.'" title="Manage Company" class="tabText">Manage Company</a></div>');
			$fields[] = array('name' => 'new_user', 'type' => 'static', 'varx' => 'hide', 'value' => '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid=5&company_id='.$companyID.'" class="tabAdd">New User</a></div>');
		} else {
			$fields[] = array('name' => 'manage_company', 'type' => 'static', 'varx' => 'hide', 'value' => '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid=2" title="Manage Administrator Accounts" class="tabText">Manage Admin Accounts</a></div>');
			$fields[] = array('name' => 'new_user', 'type' => 'static', 'varx' => 'hide', 'value' => '<div class="contentTabCap"></div><div class="contentTab"><a href="index.php?pid=5&company_id='.$companyID.'" class="tabAdd">New Admin</a></div>');
		}
	} else {
		$fields[] = array('name' => 'manage_company', 'type' => 'static', 'varx' => 'hide', 'value' => '');
		$fields[] = array('name' => 'new_user', 'type' => 'static', 'varx' => 'hide', 'value' => '');
	}

	// Run throught the parser and spit out the page
	require CLASSES_DIR.'Profile.php';
	$profile = new Profile($fields);
	echo $profile->display();		

?>
