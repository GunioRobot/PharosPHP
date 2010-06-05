<?

	$time = $value != '' ? $value : ( isset($form_array['default']) ? $form_array['default'] : '18:00:00' );	
	$tag = $form_array['name'];

	$hour = intval(substr($time,0,2));
	$minute = intval(substr($time,3,5));

	$item = '<select id="'.$tag.'" name="'.$tag.'_time">';
	for ( $i=0; $i < 24; $i++ ) {
		
		// On the hour option
		$tstamp = mktime($i,0,0,0,0);
		$item .= '<option value="'.date('H:i', $tstamp).'"';
		if ( $hour == $i && $minute == 0 ) $item .= " selected";
		$item .= '>'.date('h:i A', $tstamp).'</option>';
		
		// On the 1/2 hour option
		$tstamp = mktime($i,30,0,0,0);
		$item .= '<option value="'.date('H:i', $tstamp).'"';
		if ( $hour == $i && $minute == 30 ) $item .= " selected";
		$item .= '>'.date('h:i A', $tstamp).'</option>';

	} $item .= '</select>';
	
?>