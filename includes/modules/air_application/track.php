<?php

	/*
	
		Throws tracking info in the database for you Jay.
		
		All are required field except for 'time', which will default to NOW().
	
	*/

	require_once '../../app_header.php';
	
	$user = 	request('contact_id');
	$app = 		request('app_id');
	$content = 	request('content_type_id');
	$index = 	request('table_index');
	$time = 	request('time') ? "'".request('time')."'" : 'NOW()';
	
	if ( $user AND $app AND $content AND $index ) {
	
		$sql = "INSERT INTO tracking (contact_id,app_id,content_type_id,table_index,timestamp) VALUES('".$user."','".$app."','".$content."','".$index."',".$time.")";
		$db->Execute($sql);
		
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
		$xml .=		'	<log>'."\n";
		$xml .= 	'		<logged>true</logged>';
		$xml .=		'		<error>'.xml_data("").'</error>'."\n";
		$xml .=		'	</log>'."\n";
		$xml .=		'</root>'."\n";

		header('Content-Type: text/xml');
		echo $xml;
		exit;
	
	}
	
	else {
		
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root>'."\n";
		$xml .=		'	<log>'."\n";
		$xml .= 	'		<logged>false</logged>';
		$xml .=		'		<error>'.xml_data("Missing required fields").'</error>'."\n";
		$xml .=		'	</log>'."\n";
		$xml .=		'</root>'."\n";

		header('Content-Type: text/xml');
		echo $xml;
		exit;
		
	}
	
	


?>