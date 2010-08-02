<?

	/**
	 * FTPClientMultipleConnectCallsException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientMultipleConnectCallsException extends Exception {
		public function __construct($host="UKNOWN HOST") {
			parent::__construct();
			$this->message = sprintf("Client is already connected to [%s]. Cannot initiate another connection while connected.", $host);
		}
	}

?>