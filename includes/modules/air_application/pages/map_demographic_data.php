<?php

	require_once '../app_header.php';
	
	// Grand total of activity for this application
	$total = $db->Execute("SELECT COUNT(track_id) as hits FROM tracking WHERE app_id = '".$CURRENT_APP_ID."'");
	$total = (!$total->EOF AND $total->fields['hits'] != '' ) ? $total->fields['hits'] : 0;
	
	// State specific data
	$sql = "SELECT tracking.app_id, COUNT(tracking.track_id) as hits, t2.contact_state FROM tracking JOIN ( SELECT contacts.contact_id, contacts.contact_state FROM contacts JOIN ( SELECT applications_to_contacts.contact_id FROM applications_to_contacts WHERE applications_to_contacts.app_id = '".$CURRENT_APP_ID."' ) t1 ON t1.contact_id = contacts.contact_id ) t2 ON t2.contact_id = tracking.contact_id AND tracking.app_id = '".$CURRENT_APP_ID."' GROUP BY t2.contact_state ORDER BY t2.contact_state ASC";
	for ( $rr = $db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {
		
		$sql = "SELECT COUNT(contacts.contact_id) as users FROM contacts JOIN ( SELECT * FROM applications_to_contacts WHERE app_id = '".$CURRENT_APP_ID."' ) t1 ON t1.contact_id = contacts.contact_id WHERE contact_state = '".$rr->fields['contact_state']."' AND contacts.contact_level = '".BASIC_USER_LVL."'";
		$u = $db->Execute($sql);
		if ( !$u->EOF ) {
			$s['users'] = $u->fields['users'];
		} else $s['users'] = 0;
		
		$s['hits'] = $rr->fields['hits'];
		$states[$rr->fields['contact_state']] = $s;
	}
	
	$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<root><states>'."\n";
	
	$sql = "SELECT * FROM states ORDER BY state_name ASC";
	for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
		
		$state = $states[$info->fields['short_name']];
		$percent = $total > 0 ? round($state['hits'] / $total, 2) : 0;
		
		$xml .= '<state>'."\n";
		$xml .= '	<id>'.flash_xml_data($info->fields['state_id']).'</id>'."\n";
		$xml .= '	<name>'.flash_xml_data(strtolower(str_replace(' ', '_', $info->fields['state_name']))).'</name>'."\n";
		$xml .= '	<activity>'.flash_xml_data($state['hits']).'</activity>'."\n";
		$xml .= '	<percent>'.flash_xml_data($percent).'</percent>'."\n";
		$xml .= '	<users>'.flash_xml_data($state['users']).'</users>'."\n";
		$xml .= '</state>'."\n";
		
	} $xml .= '</states></root>'."\n";
	
	header('Content-Type: text/xml');
	echo $xml;
	
?>