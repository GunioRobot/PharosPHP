<?php

	/*
	
		Creates a new user or returns id of existing based upon 'email', required fields are:
			name
			email
			address
			state
			zip
			phone
			app_id
			time => defaults to NOW() if not provided
			
		prints XML, look for user.created(true/false) and if false, user.error for the reason.  
		user.id is valid only on success
	
	*/

	require_once '../app_header.php';
	
	$name			= 	$db->prepare_input($_REQUEST['name']);
	$email 			= 	$db->prepare_input($_REQUEST['email']);
	$address 		= 	$db->prepare_input($_REQUEST['address']);
	$state			= 	$db->prepare_input($_REQUEST['state']);
	$zip 			= 	$db->prepare_input($_REQUEST['zip']);
	$phone 			= 	$db->prepare_input($_REQUEST['phone']);
	$birthdate		= 	$db->prepare_input($_REQUEST['dob']);
	$app 			=	$_REQUEST['app_id'];
	$time 			= 	$_REQUEST['time'] != '' ? "'".$_REQUEST['time']."'" : 'NOW()';
	
	if ( $name AND $email AND $address AND $state AND $zip AND $phone AND $birthdate AND $app ) {		
		
		$sql = "SELECT * FROM contacts WHERE contact_username =  '".$email."'";
		$existing = $db->Execute($sql);
		if ( !$existing->EOF AND $existing->fields['contact_id'] != '' ) {
		
			$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
			$xml .=		'	<user>'."\n";
			$xml .= 	'		<created>true</created>';
			$xml .= 	'		<error>'.xml_data("User exists with that username").'</error>';
			$xml .=		'		<id>'.xml_data($existing->fields['contact_id']).'</id>'."\n";
			$xml .=		'		<email>'.xml_data($email).'</email>'."\n";
			$xml .=		'	</user>'."\n";
			$xml .=		'</root>'."\n";	
		
			header('Content-Type: text/xml');
			echo $xml;	
			exit;
		}
	
		// Create the contact itself
		$sql = "INSERT INTO contacts (contact_first_name,contact_phone_number,contact_primary_email,contact_address_line_1,contact_zip,contact_state,contact_date_added,contact_last_updated,contact_level,contact_username,contact_birthday) VALUES('".$name."', '".$phone."', '".$email."', '".$address."', '".$zip."', '".$state."', ".$time.", ".$time.", '".BASIC_USER_LVL."', '".$email."', '".$birthdate."')";
		$db->Execute($sql);
		
		$contactID = $db->insert_ID();
		$sql = "INSERT INTO applications_to_contacts (app_id,contact_id) VALUES ('".$app."', '".$contactID."')";
		$db->Execute($sql);
		
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
		$xml .=		'	<user>'."\n";
		$xml .= 	'		<created>true</created>';
		$xml .= 	'		<error>'.xml_data("Created a new user").'</error>';
		$xml .=		'		<id>'.xml_data($contactID).'</id>'."\n";
		$xml .=		'		<email>'.xml_data($email).'</email>'."\n";
		$xml .=		'	</user>'."\n";
		$xml .=		'</root>'."\n";
		
		header('Content-Type: text/xml');
		echo $xml;
		exit;
	
	}
	
	
	else {
		
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
		$xml .=		'	<user>'."\n";
		$xml .= 	'		<created>false</created>';
		$xml .= 	'		<error>'.xml_data("Missing a required field").'</error>';
		$xml .=		'		<id>0</id>'."\n";
		$xml .=		'		<email>'.xml_data("").'</email>'."\n";
		$xml .=		'	</user>'."\n";
		$xml .=		'</root>'."\n";	
	
		header('Content-Type: text/xml');
		echo $xml;	
		exit;
		
	}


?>