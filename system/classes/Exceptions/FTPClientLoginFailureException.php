<?
	/**
	 * FTPClientLoginFailureException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientLoginFailureException extends Exception {

		public function __construct($settings) {
			$this->message = "Login Failure with settings: {";
			foreach($settings as $key => $value) {
				$this->message .= $key." => ".$value."\n";
			}
			$this->message .= "}";
		}
		
	}

?>