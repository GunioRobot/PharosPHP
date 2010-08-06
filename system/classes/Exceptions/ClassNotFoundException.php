<?

	/**
	 * ClassNotFoundException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class ClassNotFoundException extends Exception {
		public function __construct($message) {
			parent::__construct();
			$this->message = $message;
		}
	}

?>