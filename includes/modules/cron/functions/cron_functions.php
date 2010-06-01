<?

	////////////////////////////////////////////////////////////////////////////////
	//
	//	install_cron()
	//
	//	Verifies that the needed table is in the database
	//
	////////////////////////////////////////////////////////////////////////////////

	function install_cron() {
	
		global $db;
		$sql = sprintf("CREATE TABLE IF NOT EXISTS `%s` (
		  `id` int(11) NOT NULL auto_increment,
		  `task` text NOT NULL,
		  `type` VARCHAR(200) NOT NULL,
		  `repeats` VARCHAR(7) NOT NULL default 'false',
		  `interval` int(11) NOT NULL default 0,
		  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
		  `in_progress` VARCHAR(7) NOT NULL default 'false',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;", TABLE_CRON_JOBS);

		$db->Execute($sql);
	
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	register_cron_job($task, $type, $repeats, $interval)
	//
	//	Registers a CRON task in the database
	//		Optionally, the task can repeat at an interval (specified in integer minutes)
	//		The CRON job which processes these jobs only runs once a minute
	//
	////////////////////////////////////////////////////////////////////////////////
		
	function register_cron_job($task, $type=CRON_TYPE_FUNCTION, $delay=0, $repeats=false, $interval=1) {
	
		global $db;
		if ( $task != "" ) {
			$sql = sprintf("INSERT INTO %s (`task`,`type`,`repeats`,`interval`,`timestamp`,`in_progress`) VALUES('%s','%s','%s','%d',NOW() + INTERVAL %d MINUTE,'false')", TABLE_CRON_JOBS, $db->prepare_input($task), $db->prepare_input($type), ($repeats?"true":"false"), $interval, $delay);
			$db->Execute($sql);
		}
	
	}
	
	
	
	
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	_run_cron_task($cron_obj)
	//
	//	This function is called when the parent CRON page looks through the database
	// 	and finds tasks that are waiting to be ran
	//
	////////////////////////////////////////////////////////////////////////////////	
	
	function _run_cron_task($obj) {
		
		global $db;
		
		$sql = sprintf("UPDATE `%s` SET `in_progress` = 'true' WHERE `id` = '%d' LIMIT 1", TABLE_CRON_JOBS, $obj->id);
		$db->Execute($sql);
	
		switch($obj->type) {
			case CRON_TYPE_CURL: 
				_mark_cron_as_completed($obj);
				redirect($obj->task);
			break;

			case CRON_TYPE_FUNCTION:
			default:
				if ( function_exists($obj->task) ) {
					call_user_func($obj->task);
					_mark_cron_as_completed($obj);
				}
			break;
			
		}
	
	}
	

	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	_next_cron_job()
	//
	//	Chooses the next non-processing cron task to be performed by the system
	//
	////////////////////////////////////////////////////////////////////////////////

	function _next_cron_job() {
		
		global $db;
		
		$sql = sprintf("SELECT * FROM `%s` WHERE `repeats` = 'false' AND `in_progress` = 'false' AND `timestamp` <= NOW() ORDER BY `timestamp` ASC LIMIT 1", TABLE_CRON_JOBS);
		$info = $db->Execute($sql);
		if ( $info->EOF ) {
			
			$sql = sprintf("SELECT * FROM `%s` WHERE `repeats` = 'true' AND `in_progress` = 'false' AND `timestamp` + INTERVAL `interval` MINUTE <= NOW() AND `timestamp` <= NOW()  ORDER BY `timestamp` ASC LIMIT 1", TABLE_CRON_JOBS);
			$info = $db->Execute($sql);
			if ( !$info->EOF ) {
				return clean_object($info->fields);
			} else return false;
			
		} else return clean_object($info->fields);
		
	}
	
	
	
	////////////////////////////////////////////////////////////////////////////////
	//
	//	Mark a cron job as completed
	//
	////////////////////////////////////////////////////////////////////////////////
	
	function _mark_cron_as_completed($job) {
		
		global $db;

		// Update the job
		if ( $job->repeats == "true" ) {
			$sql = sprintf("UPDATE `%s` SET `in_progress` = 'false', `timestamp` = NOW() WHERE `id` = '%d' LIMIT 1", TABLE_CRON_JOBS, $job->id);
			$db->Execute($sql);
		} else {
				
			// Remove from queue if the cron wasn't supposed to repeat
			$sql = sprintf("DELETE FROM `%s` WHERE `id` = '%d' LIMIT 1", TABLE_CRON_JOBS, $job->id);
			$db->Execute($sql);
			
		}
		
	}
	


?>