<?

	$ID = $form_array['id'] != '' ? $form_array['id'] : $form_array['name'];

	$item = '<select name="'.$form_array['name'].'" id="'.$ID.'" class="'.$form_array['class'].'" style="'.$form_array['style'].'">';
	if ( isset($_GET[$form_array['name']]) ) {
		$value = $_GET[$form_array['name']];
	} else {
		if ( $form_array['default'] AND !$value ) {
			$value = $form_array['default'];
		} else if ( !$value ) {
			$item .= '<option value="">Select a Option</option>';
		}
	} 

	foreach ( $form_array['option'] as $option => $displayName) {
		$item .= '<option value="'.$option.'"';
		if ( $value == $option ) $item .= ' selected ';
		$item .= '>'.format_title($displayName).'</option>';
	} $item .= '</select>';


?>