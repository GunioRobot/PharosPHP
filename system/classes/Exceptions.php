<?

	/**
	 * PharosBaseException
	 * Provides basic exception additions that all built-in exceptions make use of
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class PharosBaseException extends Exception {
		public function __construct($message="") {
			parent::__construct();
			$this->message = $message;
		}
	}
	

	/**
	 * CachedFileExpiredException
	 * Raised when using the Cache and the resource has expired
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class CachedFileExpiredException extends PharosBaseException {
		protected $message = "Cached has expired";
	}
	
	
	
	/**
	 * CacheNotEnabledException
	 * Raised when using the Cache and it is not enabled
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class CacheNotEnabledException extends PharosBaseException {
		protected $message = "Cache is not enabled";
	}
	
	
	
	/**
	 * CacheNotWritableException
	 * Raised when using the Cache and it is not writable
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class CacheNotWritableException extends PharosBaseException {
		protected $message = "Cache is not writable";
	}
	
	
	
	/**
	 * ClassNotFoundException
	 * Raised when attempting to load a class, and it cannot be found
	 * IE, Loader::load_class("BOB");
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class ClassNotFoundException extends PharosBaseException {}
	
	
	
	/**
	 * ControllerActionNotFoundException
	 * Raised in the default implementation of the Controller->__missingControllerAction() so the system
	 * has a chance to display a 404 page. Overriding Controller->__missingControllerAction() so that it does
	 * not throw a ControllerActionNotFoundException will eliminate the automatic 404 page generation
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class ControllerActionNotFoundException extends PharosBaseException {
		public function __construct($controller, $method) {
			parent::__construct();
			$this->message = sprintf('%s->%s() is not implemented.', $controller, $method);
		}
	}  
	
	
	/**
	 * DatabaseConnectionException
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class DatabaseConnectionException extends PharosBaseException {} 
	
	
	/**
	 * FTPClientConnectionException
	 * Raised when the FTPClient encounters an unknown error establishing a connection
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientConnectionException extends PharosBaseException {

		public $host;
		public function __construct($host) {
			parent::__construct(sprintf("Error connecting to [%s]", $host));
			$this->host = $host;
		}
		
	}
	
	
	
	/**
	 * FTPClientInvalidConnectionSettingsException
	 * Raised when invalid connection settings are provided to the FTPClient
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientInvalidConnectionSettingsException extends PharosBaseException {
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
	 * Raised when there is a failure logging in with the FTPClient
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientLoginFailureException extends PharosBaseException {

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
	 * Raised when asking the FTPClient to make a connection, when the client has already connected to the specified host
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientMultipleConnectCallsException extends PharosBaseException {
		public $host = "UKNOWN HOST";
		public function __construct($host="UKNOWN HOST") {
			parent::__construct();
			$this->host = $host;
			$this->message = sprintf("Client is already connected to [%s]. Cannot initiate another connection while connected.", $host);
		}
	}
	
	
	/**
	 * FTPClientNotConnectedException
	 * Raised when using the FTPClient class and the client has not established a connection
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class FTPClientNotConnectedException extends PharosBaseException {
		protected $message = "Client is not connected.";
	}
	
	
	/**
	 * InvalidFileSystemPathException
	 * 
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/

	class InvalidFileSystemPathException extends PharosBaseException {
		
		/**
		 * The path which was invalid
		 *
		 * @var string
		 **/

		public $path = "";
		public function __construct($path="") {
			parent::__construct(sprintf("Path not found: [%s]", $path));
			$this->path = $path;
		}
	
	}
	
	
	/**
	 * InvalidNotificationException
	 * Raised when using the NotificationCenter and providing an invalid hook name (one that has not been registered)
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class InvalidNotificationException extends PharosBaseException {
		protected $message = "Invalid Notification Specified";
	}
	
	
	/**
	 * InvalidKeyPathException
	 * Raised when attempting to create a key path in an invalid format
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class InvalidKeyPathException extends PharosBaseException {
		protected $message = "Invalid Key Path Specified";
	}
	
	
	/**
	 * ModuleNotFoundException
	 * Raised when loading a module failed
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class ModuleNotFoundException extends PharosBaseException {
		protected $message = "Module not found";
	}
	
	
	/**
	 * PharosModelInvalidLoadException
	 * Raised when a model cannot be loaded correctly
	 *
	 * @package default
	 * @author Matt Brewer
	 **/

	class PharosModelInvalidLoadException extends PharosBaseException {
		protected $message = "Model could not be loaded";
	}
	
	
	/**
	 * ReadOnlyPropertyException
	 * Raised when code attempts to access protected or private instance vars not protected by the language
	 * IE, when the class implements magic __set() & __get() methods giving the user exposure to internal workings.
	 *
	 * @package PharosPHP.Core.Classes.Exceptions
	 * @author Matt Brewer
	 **/
	
	class ReadOnlyPropertyException extends PharosBaseException {
		public $property = "";
		public function __construct($property) {
			parent::__construct(sprintf("The property [%s] is read-only.", $property));
			$this->property = $property;
		}
	} 
	

?>