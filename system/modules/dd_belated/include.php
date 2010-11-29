<?

	require_once dirname(__FILE__).'/class.php';
	NotificationCenter::register_callback(NotificationCenter::TEMPLATE_HEADER_NOTIFICATION, 'DD_Belated::write');
		

?>