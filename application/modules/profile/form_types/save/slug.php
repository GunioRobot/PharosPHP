<?

	$_POST[$data] = String::clean_filename(post($input['varx'], ""))->lowercase()->value;

?>