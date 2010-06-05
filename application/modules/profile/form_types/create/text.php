<?
	$id = $form_array['name'];
	
	if ( $form_array['default'] && !$value ) {
		$item = '<input id="'.$id.'" type="text" name="'.$form_array['name'].'" value="'.$form_array['default'].'" size="'.$form_array['size'].'" maxlength="'.$form_array['max'].'" class="'.$form_array['class'].'" style="'.$form_array['style'].'">';
	} else {
		$item = '<input id="'.$id.'" type="text" name="'.$form_array['name'].'" value="'.htmlentities(stripslashes($value)).'" size="'.$form_array['size'].'" maxlength="'.$form_array['max'].'" class="'.$form_array['class'].'" style="'.$form_array['style'].'">';
	}
	
?>

