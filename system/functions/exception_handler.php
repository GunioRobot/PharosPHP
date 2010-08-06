<?

	/**
	 * pharos_exception_handler 
	 * DO NOT CALL THIS METHOD DIRECTLY - used for catching uncaught Exceptions in PharosPHP
	 *
	 * @param Exception $exception
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function pharos_exception_handler($exception) {
		
		// these are our templates
	    $traceline = "#%s %s(%s): %s(%s)";
	    $msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

	    // alter your trace as you please, here
	    $trace = $exception->getTrace();
	    foreach ($trace as $key => $stackPoint) {
	        // I'm converting arguments to their type
	        // (prevents passwords from ever getting logged as anything other than 'string')
	        $trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
	    }

	    // build your tracelines
	    $result = array();
	    foreach ($trace as $key => $stackPoint) {
	        $result[] = sprintf(
	            $traceline,
	            $key,
	            $stackPoint['file'],
	            $stackPoint['line'],
	            $stackPoint['function'],
	            implode(', ', $stackPoint['args'])
	        );
	    }
	    // trace always ends with {main}
	    $result[] = '#' . ++$key . ' {main}';

	    // write tracelines into main template
	    $msg = sprintf(
	        $msg,
	        get_class($exception),
	        $exception->getMessage(),
	        $exception->getFile(),
	        $exception->getLine(),
	        implode("\n", $result),
	        $exception->getFile(),
	        $exception->getLine()
	    );

	    // log or echo as you please
	    error_log($msg);
		
	}
	
	
	
	/**
	 * pharos_error_handler
	 *
	 * @return void
	 * @author Matt Brewer
	 **/

	function pharos_error_handler($errno, $errstr, $errfile="", $errline=1) {

		// This error code is not included in error_reporting
		if ( !(error_reporting() & $errno) ) {
			return;
		}
		
		switch ($errno) {
			
			case E_ERROR:
		    case E_USER_ERROR:
		        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
		        echo "  Fatal error on line $errline in file $errfile";
		        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		        echo "Aborting...<br />\n";
		        exit(1);
		        break;

			case E_WARNING:
		    case E_USER_WARNING:
		        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
		        break;

			case E_NOTICE:
		    case E_USER_NOTICE:
		        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
		        break;

		    default:
		        echo "Unknown error type: [$errno] $errstr<br />\n";
		        break;
	    }

	    /* Don't execute PHP internal error handler */
	    return true;
		
	}
	
	// Set the two custom handlers for PHP
	set_exception_handler('pharos_exception_handler');
	set_error_handler('pharos_error_handler');


?>