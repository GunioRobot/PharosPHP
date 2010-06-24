<?

	class InvalidKeyPathException extends Exception {
		
		protected $message = "Invalid Key Path Specified";
		
		public function __construct($message=null, $code=0, Exception $previous=null) {
			parent::__construct($message, $code, $previous);
		}
		
	}

?>