<?
	
	$chr_array = array(' PM', ' AM');
	$_POST[$data] = str_replace($chr_array, '', $_POST[$data.'_time']).':00';
		
?>