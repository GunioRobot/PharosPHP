<?
$item = '<input type="file" name="'.$form_array['name'].'">';
if($value)
{
	$delete_link='index.php?fuction=check_delete&pid='.$_GET['pid'].'&table='.PROFILE_TABLE.'&field='.$form_array['name'].'&key='.$_GET['key'].'&'.$_GET['key'].'='.$_GET[$_GET['key']].'&file='.urlencode($value);
	$item ='
		<table>
			<tr>
				<td><input type="file" name="'.$form_array['name'].'"></td>
				<td>&nbsp;&nbsp;<a target="_blank" href="image.php?img='.urlencode($value).'"><img src="includes/templates/'.TEMPLET_STYLE.'/images/view_image.gif" border="0" alt="View '.$value.'"></a></td>
				<td>&nbsp;&nbsp;<a href="'.$delete_link.'"><img src="includes/templates/'.TEMPLET_STYLE.'/images/delete.gif" border="0" alt="Delete '.$value.'"></a></td>
			</tr>
		</table>
	';
}
?>