<?

	/**
	 * FTPClientInvalidConnectionSettingsException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientInvalidConnectionSettingsException extends Exception {
		public function __construct($settings) {
			$this->message = "Invalid Connection Settings: {";
			foreach($settings as $key => $value) {
				$this->message .= $key." => ".$value."\n";
			}
			$this->message .= "}";
		}
	}

?>