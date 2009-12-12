<?

//========
$add_where ='';
if(isset($form_array['special']) && $form_array['special'] !=''){
	$sql = 'select * from groups where id="'.$_GET[$_GET['key']].'" limit 1';
	$db_info = $db->Execute($sql);
	$display = 	$db_info->fields['display_value'];
	
	
	$db_html = $db->Execute('select * from groups');
	
	while(!$db_html->EOF){
		$loopme = explode(',',$db_html->fields['group_list']);
		for($i=0; $i<count($loopme); $i++){	
			if(isset($loopme[$i]) && $loopme[$i] !=''){
				$add_where .=' and id!= "'.$loopme[$i].'"';
			}
		}
		$db_html->MoveNext();
	}
	if(strlen($add_where) > 1){
		$add_where = ' where '.substr($add_where,4,strlen($add_where));
	}
}
// ========================= (db call) ==============================
	$add='';
	if($form_array['display_sub']){
		if($display){
			$add = $display.' as display_sub, ';
		}else{
			$add = $form_array['display_sub'].' as display_sub, ';
		}
	}
	$sql = 'select '.$add.$form_array['display'].' as display ,'.$form_array['value'].' as value from '.$form_array['table'].$add_where;
	$db_dropdown = $db->Execute($sql);
// ==================================================================

$item = '<select name="'.$form_array['name'].'">';

// =================== (Choose Default Selection) ===================
	if(isset($_GET[$form_array['name']])){
		$value = $_GET[$form_array['name']];
	}else{
		if($form_array['default'] && !$value){
			$value = $form_array['default'];
		}else{
			$item .= '<option value="">Select a Option</option>';
		}
	} 
// ==================================================================
// ==================== (Loop it out) ===============================
	while(!$db_dropdown->EOF){
		$add='';
		if($form_array['display_sub']){
			$add = ' - '.$db_dropdown->fields['display_sub'];
		}
		if($db_dropdown->fields['value'] == $value){
			$item .= '<option value="'.$db_dropdown->fields['value'].'" selected>'.format_title($db_dropdown->fields['display']).$add.'</option>';
		}else{
			$item .= '<option value="'.$db_dropdown->fields['value'].'">'.format_title($db_dropdown->fields['display']).$add.'</option>';
		}
		$db_dropdown->MoveNext();
	}
// ==================================================================

$item .= '</select>';

?>