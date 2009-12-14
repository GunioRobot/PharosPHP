<?php

	$target = $form_array['target'] != '' ? $form_array['target'] : '_self';			// Defaults to "_self"
	$title = $form_array['title'] != '' ? $form_array['title'] : 'View';				// Defaults to "View"
	$prefix = $form_array['prefix'] != '' ? $form_array['prefix'] : '';					// Defaults to ""
	$value = $form_array['full'] != '' ? $form_array['full'] : $prefix.$value;			// Defaults to prefix.value from db, or a full href passed in
	$class = $form_array['class'] != '' ? 'class="'.$form_array['class'].'"' : '';		// For styling
	$text = $form_array['text'] != '' ? $form_array['text'] : 'Some Link';				// Text of anchor
	
	// Only site out if given either a full or a value (don't want empty links)
	if ( $value != $prefix ) $item = '<a href="'.$value.'" '.$class.' title="'.$title.'" target="'.$target.'">'.$text.'</a>';
	else $item = '';
	
?>