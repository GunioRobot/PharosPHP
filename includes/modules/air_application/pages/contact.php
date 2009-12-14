<?php

	/*
	
		Sends an email and stores message in the database from the user.
			
		prints XML, look for email.sent(true/false) and if false, email.error for the reason.  
	
	*/

	require_once '../app_header.php';
	
	Controller::loadModule("rmail");
	
	$fullName		= 	$db->prepare_input($_REQUEST['name']);
	$company 		= 	$db->prepare_input($_REQUEST['company']);
	$email 			= 	$db->prepare_input($_REQUEST['email']);
	$phone 			= 	$db->prepare_input($_REQUEST['phone']);
	$comments 		= 	$db->prepare_input($_REQUEST['comments']);
	$app 			=	$_REQUEST['app_id'];
	$time 			= 	$_REQUEST['time'] != '' ? "'".$_REQUEST['time']."'" : 'NOW()';
	
	if ( $fullName AND $company AND $email AND $phone AND $comments AND $app ) {
		
		$html = '<html><body>';
		$html .= '<h2>Message From: <strong>'.htmlentities($fullName).'</strong></h2>';
		$html .= '<table>';
		$html .= '	<tr><td><strong>Name:</strong></td><td>'.htmlentities($fullName).'</td></tr>';
		$html .= '	<tr><td><strong>Company:</strong></td><td>'.htmlentities($company).'</td></tr>';
		$html .= '	<tr><td><strong>Email:</strong></td><td>'.htmlentities($email).'</td></tr>';
		$html .= '	<tr><td><strong>Phone:</strong></td><td>'.htmlentities($phone).'</td></tr>';
		$html .= '	<tr><td><strong>Comments:</strong></td><td>'.htmlentities($comments).'</td></tr>';		
		$html .= '</table>';
		$html .= '</body></html>';
		
		$mail = new Rmail();
		$mail->setFrom($email);
		$mail->setSubject(SITE_NAME.": AIR App Message");
		$mail->setHtml($html);
	
		if ( $mail->send(array(SYS_ADMIN_EMAIL)) ) {
			
			// Store message in db
			$sql = "INSERT INTO messages VALUES (NULL,'".$fullName."','".$company."','".$email."','".$phone."','".$comments."','".$app."', $time)";
			$db->Execute($sql);
		
			// True xml
			$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
			$xml .=		'	<email>'."\n";
			$xml .= 	'		<sent>true</sent>';
			$xml .= 	'		<error>'.xml_data("").'</error>';
			$xml .=		'	</email>'."\n";
			$xml .=		'</root>'."\n";	
		
			header('Content-Type: text/xml');
			echo $xml;	
			exit;
			
		}
	
		else {
	
			// Error sending the email itself
			$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
			$xml .=		'	<email>'."\n";
			$xml .= 	'		<sent>false</sent>';
			$xml .=		'		<error>'.xml_data("There was an error sending the email").'</error>'."\n";
			$xml .=		'	</email>'."\n";
			$xml .=		'</root>'."\n";
		
			header('Content-Type: text/xml');
			echo $xml;
			exit;
			
		}
	
	}
	
	
	else {
		
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
		$xml .=		'	<email>'."\n";
		$xml .= 	'		<sent>false</sent>';
		$xml .= 	'		<error>'.xml_data("Missing a required field").'</error>';
		$xml .=		'	</email>'."\n";
		$xml .=		'</root>'."\n";	
	
		header('Content-Type: text/xml');
		echo $xml;	
		exit;
		
	}


?>