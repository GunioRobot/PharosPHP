<?php

	$name = $form_array['name'];
	$id = $form_array['id'] != '' ? $form_array['id'] : $name;
	
	$item = '<input type="file" name="'.$name.'" id="'.$id.'"/>';
		
?>