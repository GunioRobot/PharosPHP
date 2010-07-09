<?

	/**
	*
	*	CRON Processor
	*
	* 	This page processes all internal PharosPHP scheduled tasks.
	* 	Do not invoke yourself - this page needs to be called via a CRON 
	*	job setup on your server.  Then PharosPHP will be able to schedule
	* 	tasks internally.
	*
	*/

	require_once 'system/init.php';	
	if ( ($key = get("secret")) === APPLICATION_SECRET_KEY ) {
		Cron::process();
	}

?>