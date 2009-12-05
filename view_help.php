<?php

	if ( is_admin() ) {
		$template = get_template('admin.html', '/help/');
	} else if ( is_user() ) {
		$template = get_template('restricted_user.html', '/help/');
	}
	
	$template = str_replace('{HTTP_SERVER}', HTTP_SERVER, $template);
	
	echo $template;

?>