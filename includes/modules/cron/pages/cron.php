<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	Load this page every minute via a cron job
	//
	//	The cron module will take care of specifially scheduling and executing tasks
	//	created by CMSLite from there
	//
	////////////////////////////////////////////////////////////////////////////////
	

	require_once '../../../app_header.php';

	if ( ($key = get("secret")) === APPLICATION_SECRET_KEY ) {
		
		if ( ($job = _next_cron_job()) !== false ) {
			_run_cron_task($job);
		}
		
	} 
	

?>