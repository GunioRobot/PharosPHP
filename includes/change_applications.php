<?php

	require_once 'app_header.php';

	select_app(request("app_id",DEFAULT_APP_ID));
	redirect(urldecode(request('location', 'index.php?')));

?>