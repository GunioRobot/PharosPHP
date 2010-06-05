<?

	$chr_array = array(' PM',' AM');
	$_POST[$data] = $_POST[$data.'_year'].'-'.$_POST[$data.'_month'].'-'.$_POST[$data.'_day'].' '.str_replace($chr_array,'',$_POST[$data.'_time']).':00';

?>