<?
	/**
	 * FTPClientConnectionException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientConnectionException extends Exception {

		public function __construct($host) {
			parent::__construct();
			$this->message = "Error connecting to ".$host;
		}
		
	}

?>