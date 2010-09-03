<?

	$_POST[$data] = String::clean_filename(Input::post($input['varx'], ""))->lowercase()->value;

?>