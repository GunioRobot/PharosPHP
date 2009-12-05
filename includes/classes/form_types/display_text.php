<?
if(isset($form_array['value'])){
	$item = $form_array['value'].'<input type="Hidden" name="'.$form_array['name'].'" value="'.$form_array['value'].'">';
}else{
	$item = $value;
}
?>