<?

	$sub_temp_array = explode('::',$_POST[$data.'_array']);
	$sub_item ='';
	for($ii=0; $ii<count($sub_temp_array); $ii++){
		if($_POST[$data.'_'.$sub_temp_array[$ii]] != 'true'){
			$sub_item .= 'false::';
		}else{
			$sub_item .= 'true::';
		}
		
	}
	
	$_POST[$data] = substr($sub_item,0,-2);
	
?>