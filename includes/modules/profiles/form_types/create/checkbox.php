<?
if($form_array['checkvalue'] == $value){
	$item = '<input type="checkbox" name="'.$form_array['name'].'" value="'.$form_array['checkvalue'].'" checked>';
}else{
	$item = '<input type="checkbox" name="'.$form_array['name'].'" value="'.$form_array['checkvalue'].'">';
}
?>