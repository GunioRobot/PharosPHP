<?
	
	$_POST[$data] = str_replace(array(' ','_'), '-', preg_replace('/[^[a-z\s_]|^[0-9]]+/', '', strtolower(stripslashes(post($input['varx'],"")))));

?>