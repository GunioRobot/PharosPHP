<?php

	////////////////////////////////////////////////////////////////////////////////
	//
	//	level_dropdown($tag, $currentLevel, $securityLevel)
	//
	// Returns HTML string <select></select> for choosing user levels
	//
	////////////////////////////////////////////////////////////////////////////////

	function level_dropdown($tag, $currentLevel, $securityLevel) {
	
		global $db;
	
		$drop_down = '<select class="singleText" name="'.$tag.'" id="'.$tag.'">';
		
		$sql = "SELECT * FROM user_levels WHERE user_level_id <= '".$securityLevel."'";
		for ( $levels = $db->Execute($sql); !$levels->EOF; $levels->moveNext() ) {
			$drop_down .= '<option id="level_'.$levels->fields['user_level_id'].'_option" value="'.$levels->fields['user_level_id'].'" ';
			if ( $levels->fields['user_level_id'] == $currentLevel ) $drop_down .= ' selected ';
			$drop_down .= '>'.format_title($levels->fields['user_level_name']).'</option>';	
		} 
			
		return $drop_down . '</select>';
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	user_levels_array($maxLevel)
	//
	// Returns array of user levels based on security level given
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function user_levels_array($maxLevel) {
		
		global $db;
		$ret = array();
		
		$sql = "SELECT * FROM user_levels WHERE user_level_id <= '$maxLevel' ORDER BY user_level_id ASC";
		for ( $levels = $db->Execute($sql); !$levels->EOF; $levels->moveNext() ) {
			$ret[$levels->fields['user_level_id']] = format_title($levels->fields['user_level_name']);
		} return $ret;
		
	}	
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	content_type_dropdown($sel=0, $show_all_option=true)
	//
	// Returns HTML string <select></select> for choosing a content type
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function content_type_dropdown($sel=0,$show_all_option=true) {
	
		global $db;
				
		$html = '<select id="content_types" name="content_type">';
		
		if ( !$show_all_option AND $sel == 0 ) $sel = 1;
		
		if ( $show_all_option ) $html .= '<option id="all-content_types" value="all">All Content</option>';
		
		$sql = "SELECT * FROM content_types WHERE type_name != 'user' ORDER BY type_name ASC";
		for ( $rr = $db->Execute($sql); !$rr->EOF; $rr->moveNext() ) {
			$html .= '<option id="content-'.$rr->fields['type_id'].'" value="'.$rr->fields['type_id'].'"';
			if ( $sel == $rr->fields['type_id'] ) $html .= ' selected="selected" ';
			
			$title = ( $rr->fields['type_name'] == 'view' ) ? 'Views' : format_title($rr->fields['type_table']);
			$html .= '>'.$title.'</option>';
		} $html .= '</select>';
		
		return $html;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	states_array()
	//
	// Returns array of US States
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function states_array() {
	
		$states['Alabama'] = 'AL';
		$states['Alaska'] = 'AK';
		$states['Arizona'] = 'AZ';
		$states['Arkansas'] = 'AR';
		$states['California'] = 'CA';
		$states['Colorado'] = 'CO';
		$states['Connecticut'] = 'CT';
		$states['Delaware'] = 'DE';
		$states['Florida'] = 'FL';
		$states['Georgia'] = 'GA';
		$states['Hawaii'] = 'HI';
		$states['Idaho'] = 'ID';
		$states['Illinois'] = 'IL';
		$states['Indiana'] = 'IN';
		$states['Iowa'] = 'IA';
		$states['Kansas'] = 'KS';
		$states['Kentucky'] = 'KY';
		$states['Louisiana'] = 'LA';
		$states['Maine'] = 'ME';
		$states['Maryland'] = 'MD';
		$states['Massachusetts'] = 'MA';
		$states['Michigan'] = 'MI';
		$states['Minnesota'] = 'MN';
		$states['Mississippi'] = 'MS';
		$states['Missouri'] = 'MO';
		$states['Montana'] = 'MT';
		$states['Nebraska'] = 'NE';
		$states['Nevada'] = 'NV';
		$states['New Hampshire'] = 'NH';
		$states['New Jersey'] = 'NJ';
		$states['New Mexico'] = 'NM';
		$states['New York'] = 'NY';
		$states['North Carolina'] = 'NC';
		$states['North Dakota'] = 'ND';
		$states['Ohio'] = 'OH';
		$states['Oklahoma'] = 'OK';
		$states['Oregon'] = 'OR';
		$states['Pennsylvania'] = 'PA';
		$states['Rhode Island'] = 'RI';
		$states['South Carolina'] = 'SC';
		$states['South Dakota'] = 'SD';
		$states['Tennessee'] = 'TN';
		$states['Texas'] = 'TX';
		$states['Utah'] = 'UT';
		$states['Vermont'] = 'VT';
		$states['Virginia'] = 'VA';
		$states['Washington'] = 'WA';
		$states['West Virginia'] = 'WV';
		$states['Wisconsin'] = 'WI';
		$states['Wyoming'] = 'WY';
		
		return $states;
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	state_dropdown($tag, $selection=null)
	//
	// Returns HTML string <select></select> for choosing a US State
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function state_dropdown($tag, $selection=null) {
		$drop_down = '<select class="state" name="'.$tag.'">';
		$drop_down .= '<option value=""></option>';

		$states = states_array();

		foreach($states as $long => $short) {

			if ( isset($use_long_names) AND $use_long_names == true) $name = $long;
			else $name = $short;

			$drop_down .= '<option value="'.$name.'"';
			if ( $selection == $long OR $selection == $short ) $drop_down .= 'selected';
			$drop_down .= '>'.format_title($name).'</option>';

		} $drop_down .= '</select>';

		return $drop_down;
	}
	
	

?>