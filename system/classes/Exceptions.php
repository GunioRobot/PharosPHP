<?

	/**
	 * CachedFileExpiredException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class CachedFileExpiredException extends Exception {
		protected $message = "Cached has expired";
	}
	
	
	
	/**
	 * CacheNotEnabledException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class CacheNotEnabledException extends Exception {
		protected $message = "Cache is not enabled";
	}
	
	
	
	/**
	 * CacheNotWritableException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class CacheNotWritableException extends Exception {
		protected $message = "Cache is not writable";
	}
	
	
	
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
	
	
	
	/**
	 * ControllerActionNotFoundException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	class ControllerActionNotFoundException extends Exception {
		public function __construct($controller, $method) {
			parent::__construct();
			$this->message = sprintf('%s->%s() is not implemented.', $controller, $method);
		}
	}  
	
	
	
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
	
	
	
	/**
	 * FTPClientInvalidConnectionSettingsException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientInvalidConnectionSettingsException extends Exception {
		public function __construct(array $settings) {
			parent::__construct();

			$this->message = "Invalid Connection Settings: {";
			foreach($settings as $key => $value) {
				$this->message .= $key." => ".$value."\n";
			}
			$this->message .= "}";
			
		}
	}
	
	
	
	/**
	 * FTPClientLoginFailureException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientLoginFailureException extends Exception {

		public function __construct($settings) {
			parent::__construct();
			
			$this->message = "Login Failure with settings: {";
			foreach($settings as $key => $value) {
				$this->message .= $key." => ".$value."\n";
			}
			$this->message .= "}";
			
		}
		
	}
	
	
	
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
	
	
	
	/**
	 * FTPClientNotConnectedException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientNotConnectedException extends Exception {
		protected $message = "Client is not connected.";
	}
	
	
	
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
	
	
	
	/**
	 * InvalidHookException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class InvalidHookException extends Exception {
		protected $message = "Invalid Hook Specified";
	}
	
	
	
	/**
	 * InvalidKeyPathException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class InvalidKeyPathException extends Exception {
		protected $message = "Invalid Key Path Specified";
	}
	
	
	
	

?>