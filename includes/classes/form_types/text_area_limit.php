<?
$item = '<textarea  
		onKeyDown="textCounter(this,\''.$field_id.'\','.$form_array['max'].')" 
		onKeyUp="textCounter(this,\''.$field_id.'\','.$form_array['max'].')" 
		onFocus="textCounter(this,\''.$field_id.'\','.$form_array['max'].')"
		wrap="soft"
		name="'.$form_array['name'].'"
		cols="'.$form_array['col'].'"
		rows="'.$form_array['row'].'"
		id="'.$form_array['name'].'">'.$value.'</textarea>
	<div id="'.$field_id.'" class="progress"></div>
	<script>textCounter(document.getElementById("'.$form_array['name'].'"),"'.$field_id.'",'.$form_array['max'].')</script>
';
?>