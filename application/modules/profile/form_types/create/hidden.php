<?
if(isset($form_array['value'])){
	$item = '<input type="Hidden" name="'.$form_array['name'].'" value="'.$form_array['value'].'" id="'.$form_array['name'].'">';
}else{
	$item = '<input type="Hidden" name="'.$form_array['name'].'" value="'.$value.'" id="'.$form_array['name'].'">';
}
?>