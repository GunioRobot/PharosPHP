<?
	
	if ( ($key = get("key")) && ($id = get($key)) ) {
		$data = false;
	} else {
		$_POST[$data] = 'NOW()';
		$quoteValue = false;
	}
	
?>