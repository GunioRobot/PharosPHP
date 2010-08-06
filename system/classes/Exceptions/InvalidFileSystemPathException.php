<?

	/**
	 * InvalidFileSystemPathException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class InvalidFileSystemPathException extends Exception {
		public function __construct($message) {
			parent::__construct();
			$this->message = $message;
		}
	}

?>