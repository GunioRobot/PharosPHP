<?
	
	// The CSV string of unique checkbox names
	$csv_string = $form_array['varx'];
	$checkbox_names = $form_array['checkbox_names'];
	$check_selection = $form_array['check_selection'];
			
	// An array from the csv_string
	$csv_array = explode('::', $csv_string);
	$check_array = explode('::', $check_selection);
	
	// Build the HTML itself, 5 checkboxes per row
	$item = '<ul>';
	$ii = 0;
	foreach ( $checkbox_names as $name => $value ) {
		
		$item .= '<li>';
		$ischecked .= array_search($name,$csv_array).'::';
		$item .= $form_array['left_html'] . '<input type="checkbox" name="'.$form_array['name'].'_'.array_search($name,$csv_array).'" value="true"';
		if ( $check_array[$ii] == 'true' ) $item .= ' checked'; 	// If it's checked
		$item .= '>' . $value . $form_array['right_html'] ;			// Ending HTML (may be '')
		$item .= '</li>';
		$ii++;
	} $item .= '</ul>';
		
	$item .= '<input type="hidden" name="'.$form_array['name'].'_array" value="'.substr($ischecked,0,-2).'">' ;
	
?>