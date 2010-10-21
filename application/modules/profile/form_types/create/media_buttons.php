<?

	$app_url = ROOT_URL;
	$public_url = PUBLIC_URL; 
	$id = $form_array['id'];
	$table = $form_array['table'];
	
	$item = <<<HTML
		<div class="media-butons">
			<br />
			<table cellspacing="0" cellpadding="0" border="0" align="left" valign="middle">
				<tr>
					<td>Insert/Upload:&nbsp;</td>
					<td><a href="{$app_url}assets/add/{$table}/{$id}/" class="fancybox iframe" rel="fancybox" title="Add an Image"><img src="{$public_url}images/media-button-image.gif" alt="Add an Image" border="0" /></a></td>
				</tr>
			</table>
			<br />
		</div>	
HTML;

?>