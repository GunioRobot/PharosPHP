<?

	$template = get_template(basename(__FILE__), 'views/iPhone/');
	
	$listing = '';
	$sql = "SELECT * FROM admin_nav WHERE parent_id = 0 AND display != 'hidden' AND device_type != 'pc' AND ".SECURITY_LVL." >= lvl ORDER BY order_num ASC";		
	for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
				
		$listing .= '<h2>'.format_title($info->fields['iPhone_title']).'</h2><ul>';
		$sql = "SELECT * FROM admin_nav WHERE parent_id = '".$info->fields['id']."' AND display != 'hidden' AND device_type != 'pc' AND ".SECURITY_LVL." >= lvl ORDER BY order_num ASC";
		for ( $kids = $db->Execute($sql); !$kids->EOF; $kids->moveNext() ) {
			$listing .= '<li class="arrow"><a href="'.HTTP_SERVER.ADMIN_DIR.'index.php?pid='.$kids->fields['id'].'&ajax=true" id="'.make_id($kids->fields['iPhone_title'].'-'.$kids->fields['id']).'">'.format_title($kids->fields['iPhone_title']).'</a></li>';
		} $listing .= '</ul>';
		
	}

	$template = str_replace("[nav_listing]", $listing, $template);
	
	echo $template;

?>