<?

	require_once dirname(__FILE__).'/defines/autoload.php';
	require_once dirname(__FILE__).'/functions/autoload.php';
	
	if ( !defined('APPLICATION_SECRET_KEY') ) define('APPLICATION_SECRET_KEY', md5(Settings::get('system.site.name')));		// Backwards comptabile with older CMSLite installs
	
	install_cron();	// Makes sure that the database contains the correct table

?>