<?

	/**
	 * Cron API
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	final class Cron {
		
		const TYPE_CURL = 'curl -L -s';
		const TYPE_FUNCTION = 'function';
		const TYPE_CLASS = 'static class';
		const TABLE = 'cron_jobs';
		
		private function __construct() {}
		
		
		/**
		 * process
		 * Processes the next task, if one is available
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function process() {
			if ( ($job = self::_next_job()) !== false ) {
				self::_run_task($job);
			}
		}
				
		
		/**
		 * register
		 * Register a task to be performed later
		 *
		 * @param string $task
		 * @param array $params
		 * @param string (optional) $cron_task_type (TYPE_CURL | TYPE_FUNCTION | TYPE_CLASS )
		 * @param int (optional) $delay - in minutes
		 * @param boolean (optional) $repeats
		 * @param int (optional) $repeat_interval
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public static function register($task, $params=array(), $type=self::TYPE_FUNCTION, $delay=0, $repeats=false, $interval=1) {

			global $db;
			if ( $task != "" ) {
				$sql = sprintf("INSERT INTO %s (`task`,`params`,`type`,`repeats`,`interval`,`timestamp`,`in_progress`) VALUES('%s','%s','%s','%s','%d',NOW() + INTERVAL %d MINUTE,'false')", self::TABLE, $db->prepare_input($task), $db->prepare_input(serialize($params)), $db->prepare_input($type), ($repeats?"true":"false"), $interval, $delay);
				$db->Execute($sql);
			}

		}
		
		
		/**
		 * _run_task
		 * Runs a specified task
		 *
		 * @param object $obj
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		private static function _run_task($obj) {
			
			global $db;

			$sql = sprintf("UPDATE `%s` SET `in_progress` = 'true' WHERE `id` = '%d' LIMIT 1", self::TABLE, $obj->id);
			$db->Execute($sql);

			switch($obj->type) {
			
				case self::TYPE_CURL: 
					self::_mark_cron_as_completed($obj);
					redirect($obj->task);
				break;
				
				case self::TYPE_CLASS:

					$components = explode("::", $obj->task);
					$class = $components[0];
					$method = $components[1];

					if ( !class_exists() ) {

						$controller_file = CONTROLLER_PATH.$class.'.php';
						$class_file = CLASSES_PATH.$class.'.php';

						if ( file_exists($controller_file) ) require_once $controller_file;
						else if ( file_exists($class_file) ) require_once $class_file;

					}

					call_user_func_array(array($class, $method), unserialize($obj->params));
					self::_mark_cron_as_completed($obj);

				break;

				case self::TYPE_FUNCTION:
				default:
					if ( function_exists($obj->task) ) {
						call_user_func_array($obj->task, unserialize($obj->params));
						self::_mark_cron_as_completed($obj);
					}
				break;
				
			}
			
		}
		
		
		/**
		 * _next_job
		 * Retrieves the next task from the queue, returning false if one is not available
		 *
		 * @return (object|false) $task
		 * @author Matt Brewer
		 **/

		private static function _next_job() {

			global $db;

			$sql = sprintf("SELECT * FROM `%s` WHERE `repeats` = 'false' AND `in_progress` = 'false' AND `timestamp` <= NOW() ORDER BY `timestamp` ASC LIMIT 1", self::TABLE);
			$info = $db->Execute($sql);
			if ( $info->EOF ) {

				$sql = sprintf("SELECT * FROM `%s` WHERE `repeats` = 'true' AND `in_progress` = 'false' AND `timestamp` + INTERVAL `interval` MINUTE <= NOW() AND `timestamp` <= NOW()  ORDER BY `timestamp` ASC LIMIT 1", self::TABLE);
				$info = $db->Execute($sql);
				if ( !$info->EOF ) {
					return clean_object($info->fields);
				} else return false;

			} else return clean_object($info->fields);

		}
		
		
		/**
		 * _mark_cron_as_completed
		 * Marks a task as completed, removing it from the queue
		 *
		 * @param object $job
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		private static function _mark_cron_as_completed($job) {

			global $db;

			// Update the job
			if ( $job->repeats == "true" ) {
				$sql = sprintf("UPDATE `%s` SET `in_progress` = 'false', `timestamp` = NOW() WHERE `id` = '%d' LIMIT 1", self::TABLE, $job->id);
				$db->Execute($sql);
			} else {

				// Remove from queue if the cron wasn't supposed to repeat
				$sql = sprintf("DELETE FROM `%s` WHERE `id` = '%d' LIMIT 1", self::TABLE, $job->id);
				$db->Execute($sql);

			}

		}		
		
		
	} 

?>