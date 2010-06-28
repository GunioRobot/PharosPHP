<?
	
	/**
	 * InvalidHookException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class InvalidHookException extends Exception {
		
		protected $message = "Invalid Hook Specified";
		
		public function __construct($message=null, $code=0, Exception $previous=null) {
			parent::__construct($message, $code, $previous);
		}
		
	}

?>