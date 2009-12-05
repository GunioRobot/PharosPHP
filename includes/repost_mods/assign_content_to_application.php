<?php

	if ( ($content_type_id = get("content_type_id")) AND ($content_type_name = get("content_type_name")) AND ($table_index = get("table_index")) AND ($app_id = get("app_id")) ) {
		
		$sql = "INSERT INTO applications_to_content (content_type_id,content_type_name,table_index,app_id) VALUES ('$content_type_id','$content_type_name','$table_index','$app_id')";
		$db->Execute($sql);		
		
	} else die("required fields not met");

?>