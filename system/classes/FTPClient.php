<?

	/**
	 * FTP Client
	 * 
	 * Comprehensive class wrapper for all PHP provided FTP functions and operations.
	 * Provides more intuitive and cohesive API than the one provided with PHP.
	 * 
	 * USAGE:
	 *
	 * 		$ftp = new FTPClient("localhost", $username, $password);
	 *		$contents = $ftp->ls();
	 *		foreach($contents as $f) {
	 *			echo $f;
	 *		}
	 *		
	 * 		$ftp->disconnect();
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 **/
	
	class FTPClient	extends Object {
		
		const FILE_HANDLE = "handle";
		const FILE_PATH = "path";
		
		const SITE_EXEC = 1;
		const EXEC = 2;
		const RAW = 3;
		
		protected $connection;
		protected $debug = false;
		
		public $host;
		public $username;
		public $password;
		public $port = 21;
		public $timeout = 90;
		public $ssl = false;
		
		
		/**
		 * Constructor
		 *
		 * @param string $host
		 * @param string $username
		 * @param string $password
		 * @param string $port
		 * @param string $timeout
		 * @param string $ssl
		 *
		 * @return FTPClient - Initialized object
		 * @author Matt Brewer
		 **/

		public function __construct($host=null,$username=null,$password=null,$port=21,$timeout=90,$ssl=false) {
			$this->settings($host,$username,$password,$port,$timeout);
		}
		
		
		/**
		 * settings
		 *
		 * @param string $host
		 * @param string $username
		 * @param string $password
		 * @param string $port
		 * @param string $timeout
		 * @param string $ssl
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function settings($host=null,$username=null,$password=null,$port=21,$timeout=90,$ssl=false) {
			$this->host = $host;
			$this->username = $username;
			$this->password = $password;
			$this->port = $port;
			$this->timeout = $timeout;
			$this->ssl  =$ssl;
		}
			
			
		/**
		 * connect
		 *
		 * @param string $host
		 * @param string $username
		 * @param string $password
		 * @param string $port
		 * @param string $timeout
		 * @param string $ssl
		 *
		 * @throws FTPClientMultipleConnectCallsException
		 * @throws FTPClientConnectionException
		 * @throws FTPClientLoginFailureException
		 * @throws FTPClientInvalidConnectionSettingsException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function connect($host=null,$username=null,$password=null,$port=21,$timeout=90,$ssl=false) {
			
			if ( $this->connection ) {
				throw new FTPClientMultipleConnectCallsException();
			}
			
			if ( $this->validate_settings() ) {
				
				// Attempt to connect using SSL if preferred and available
				if ( $this->ssl && function_exists('ftp_ssl_connect') ) {
					if ( !($this->connection = @ftp_ssl_connect($this->host, $this->port, $this->timeout)) ) {
						throw new FTPClientConnectionException();
					}
				} else {
					if ( !($this->connection = @ftp_connect($this->host, $this->port, $this->timeout)) ) {
						throw new FTPClientConnectionException();
					}
				}
				
				// Perform LOGIN
				if ( !@ftp_login($this->connection, $this->username, $this->password) ) {
					$this->disconnect();
					throw new FTPClientLoginFailureException();
				}
				
			} else throw new FTPClientInvalidConnectionSettingsException();
			
		}
		
		
		/**
		 * cd
		 * 
		 * @param $dir (optional) - leave blank to go up a directory level
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function cd($dir="") {
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("cd %s\n", $dir);
				
				if ( $dir == "" ) {
					return @ftp_cdup($this->connection);
				} else {
					return @ftp_chdir($this->connection, $dir);
				}
				
			} else throw new FTPClientNotConnectedException();
		}
		
		
		/**
		 * chmod
		 *
		 * @param string $mode
		 * @param string $filename
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function chmod($mode, $filename) {
			
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("chmod %s %s\n", $mode, $filename);
				return @ftp_chmod($this->connection, $mode, $filename);
				
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * size
		 *
		 * @param string $path
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return int $size
		 * @author Matt Brewer
		 **/

		public function size($path) {
			
			if ( $this->connection ) {
				return $this->_size($path, true);
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * rm
		 * 
		 * @param string $path
		 * @param bool $recursive
		 *
		 * @throws InvalidArgumentException
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function rm($path, $recursive=true) {
			
			if ( $this->connection ) {
				$this->delete($path, $recursive, true);
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * exec
		 *
		 * @param string $command
		 * @param string $type (FTPClient::EXEC | FTPClient::SITE_EXEC | FTPClient::RAW )
		 *
		 * @throws InvalidArgumentException
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/
		
		public function exec($cmd, $type=self::EXEC) {
			
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("exec `%s`\n", $cmd);
				switch($type) {
					
					case self::EXEC:
						return @ftp_exec($this->connection, $cmd);
						break;
						
					case self::SITE_EXEC:
						return @ftp_site($this->connection, $cmd);
						break;
						
					case self::RAW:
						return @ftp_raw($this->connection, $cmd);
						break;
						
					default:
						throw new InvalidArgumentException("Unrecognized type: ($type)");
						break;
					
				}
					
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * download
		 * 
		 * @param array $fileinfo (FTPClient::FILE_HANDLE | FTPClient::FILE_PATH must be provided)
		 * @param string $remote_path
		 * @param bool $asynchronous (optional) - defaults to false
		 * @param string $mode (optional) - defaults to FTP_BINARY (FTP_BINARY | FTP_ASCII )
		 * @param int $resumepos (optional) - default to 0
		 * 
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function download($file_info, $remote_path, $asynchronous=false, $mode=FTP_BINARY, $resumepos=0) {
			
			if ( $this->connection ) {
				
				if ( !isset($file_info[self::FILE_HANDLE]) && !isset($file_info[self::FILE_PATH]) ) throw new InvalidArgumentException();
								
				if ( $this->debug ) echo sprintf("download [%s] to [%s] (mode=%s, position=%s)\n", $remote_path, (!isset($file_info[self::FILE_PATH]) ? "FILE_HANDLE" : $file_info[self::FILE_PATH]), $mode, $resumepos);
				if ( !isset($file_info[self::FILE_PATH]) ) {
					
					if ( $asynchronous ) {
						return @ftp_nb_fget($this->connection, $file_info[self::FILE_HANDLE], $remote_path, $mode, $resumepos);
					} else {
						return @ftp_fget($this->connection, $file_info[self::FILE_HANDLE], $remote_path, $mode, $resumepos);
					}
					
				} else {
					
					if ( $asynchronous ) {
						return @ftp_nb_get($this->connection, $file_info[self::FILE_PATH], $remote_path, $mode, $resumepos);
					} else {
						return @ftp_get($this->connection, $file_info[self::FILE_PATH], $remote_path, $mode, $resumepos);
					}
					
				}
				
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * upload
		 *
		 * @param array $fileinfo (FTPClient::FILE_HANDLE | FTPClient::FILE_PATH must be provided)
		 * @param string $remote_path
		 * @param bool $asynchronous (optional) - defaults to false
		 * @param string $mode (optional) - defaults to FTP_BINARY (FTP_BINARY | FTP_ASCII )
		 * @param int $resumepos (optional) - default to 0
		 * 
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/
				
		public function upload($file_info, $remote_path, $asynchronous=false, $mode=FTP_BINARY, $resumepos=0) {
			
			if ( $this->connection ) {
				
				if ( !isset($file_info[self::FILE_HANDLE]) || !isset($file_info[self::FILE_PATH]) ) throw new InvalidArgumentException();

				if ( $this->debug ) echo sprintf("upload [%s] to [%s] (mode=%s, position=%s)\n", (!isset($file_info[self::FILE_PATH]) ? "FILE_HANDLE" : $file_info[self::FILE_PATH]), $remote_path, $mode, $resumepos);
				
				if ( !isset($file_info[self::FILE_PATH]) ) {
					
					if ( $asynchronous ) {
						return @ftp_nb_fput($this->connection, $remote_path, $file_info[self::FILE_HANDLE], $mode, $resumepos);
					} else {
						return @ftp_fput($this->connection, $remote_path, $file_info[self::FILE_HANDLE], $mode, $resumepos);
					}
					
				} else {
					
					if ( $asynchronous ) {
						return @ftp_nb_put($this->connection, $remote_path, $file_info[self::FILE_PATH], $mode, $resumepos);
					} else {
						return @ftp_put($this->connection, $remote_path, $file_info[self::FILE_PATH], $mode, $resumepos);
					}
				}
				
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * ls
		 * Lists a directory's contents
		 *
		 * @param string $dir (optional) - defaults to current working directory
		 * @param bool $raw_listing (optional) - defaults to false
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return mixed $listing
		 * @author Matt Brewer
		 **/

		public function ls($dir=".", $raw=false) {
			
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("ls %s\n", $dir);
				return $raw ? @ftp_rawlist($this->connection, $dir) : @ftp_nlist($this->connection, $dir);
				
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * mv
		 *
		 * @param string $old
		 * @param string $new
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function mv($old, $new) {
			
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("move %s %s\n", $old, $new);
				return @ftp_rename($this->connection, $old, $new);
				
			} else throw new FTPClientNotConnectedException();
			
		}
			
			
		/**
		 * set_option
		 *
		 * @param string $option
		 * @param string $value
		 *
		 * @throws InvalidArgumentException
		 * @throws FTPClientNotConnectedException
		 *	
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function set_option($option, $value) {
			if ( $this->connection ) {
				if ( $this->debug ) echo sprintf("get_option %s\n", $option);
				$retval = @ftp_set_option($this->connection, $option, $value);
				if ( $retval === false ) throw new InvalidArgumentException("ftp_set_option($option, $value) is not supported.");
				return $retval;
			} else throw new FTPClientNotConnectedException();
		
		}
		
		
		/**
		 * get_option
		 *
		 * @param string $option
		 *
		 * @throws InvalidArgumentException
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function get_option($option) {
			
			if ( $this->connection ) {
				$retval = @ftp_get_option($this->connection, $option);
				if ( $retval === false ) throw new InvalidArgumentException("ftp_get_option($option) is not supported.");
				return $retval;
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * passive
		 *
		 * @param bool $passive
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function passive($bool) {
			
			if ( $this->connection ) {
			
				if ( $this->debug ) echo sprintf("ftp_passive(%s)\n", ($bool?"true":"false"));
				return @ftp_pasv($this->connection, (bool)$bool);
				
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * pwd
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return mixed - $path on success, false on error
		 * @author Matt Brewer
		 **/
		
		public function pwd() {
		
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("pwd\n");
				return @ftp_pwd($this->connection);
				
			} else throw new FTPClientNotConnectedException();
		
		}
		
		
		/**
		 * mkdir
		 *
		 * @param string $dir
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/
		
		public function mkdir($dir) {
			
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("mkdir [%s]\n", $dir);
				return @ftp_mkdir($this->connection, $dir) !== false;
				
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * nb_continue
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return int (FTP_FAILED | FTP_FINISHED | FTP_MOREDATA)
		 * @author Matt Brewer
		 **/

		public function nb_continue() {
			
			if ( $this->connection ) {
				
				if ( $this->debug ) echo sprintf("ftp_nb_continue(%s)\n", $this->host);
				return @ftp_nb_continue($this->connection);
				
			} else throw new FTPClientNotConnectedException();
			
		}
		
		
		/**
		 * debug
		 *
		 * @param bool $bool (optional) - if not provided, returns debug status
		 *
		 * @return bool $debug
		 * @author Matt Brewer
		 **/

		public function debug($bool=null) {
			if ( $bool == null ) {
				return $this->debug;
			} else {
				return $this->debug = (bool)$bool;
			}
		}
		
		
		/**
		 * disconnect
		 *
		 * @throws FTPClientNotConnectedException
		 *
		 * @return bool $success
		 * @author Matt Brewer
		 **/

		public function disconnect() {
			if ( $this->connection ) {
				if ( $this->debug ) echo sprintf("Disconnecting from [%s]\n", $this->host);
				$success = @ftp_close($this->connection);
				$this->connection = null;
				return $success;
			} else throw new FTPClientNotConnectedException();
		}
		
		
		/**
		 * Destructor
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function __destruct() {
			try {
				$this->disconnect();
			} catch (Exception $e) {}
		}
		
		
		
		
		
		
		/**
		 * validate_settings
		 *
		 * @return bool $valid
		 * @author Matt Brewer
		 **/
		
		protected function validate_settings() {
			return $this->host != "" && $this->username != "" && $this->password;
		}
		
		
		/**
		 * delete
		 *
		 * @param string $path
		 * @param bool $recursive
		 * @param bool $echo (optional)
		 *
		 * @throws InvalidArgumentException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		protected function delete($path, $recursive, $echo=false) {
			
			$full_path = sprintf("ftp://%s:%s@%s%s", $this->username, $this->password, $this->host, $this->pwd()."/".$path);
			if ( @is_dir($full_path) ) {
				
				if ( $recursive ) {
					
					if ( $this->debug && $echo ) echo sprintf("rm -rf %s\n", $path);
										
					$dir = opendir($full_path);
					while ( ($entry = readdir($dir)) !== false) { 
													
						if ( $entry != "." && $entry != ".." ) {
					
							if ( @is_dir($full_path.$entry) ) {
								$this->delete($path.$entry."/", $recursive);
							} else {
								@ftp_delete($this->connection, $path.$entry);
							}
							
						}
					
					}
					
					closedir($dir);
					@ftp_rmdir($this->connection, $path);
					
					return true;
					
				} else throw new InvalidArgumentException("Path was a directory, yet recursive was false");
				
			} else {
				if ( $this->debug && $echo ) echo sprintf("rm %s\n", $path);
				return @ftp_delete($this->connection, $path);
			}
			
		}
		
		
		/**
		 * _size
		 *
		 * @param string $path
		 * @param bool $echo (optional)
		 *
		 * @throws InvalidArgumentException
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		protected function _size($path, $echo=false) {
			
			$size = 0;

			$full_path = sprintf("ftp://%s:%s@%s%s", $this->username, $this->password, $this->host, $this->pwd()."/".$path);
			if ( @is_dir($full_path) ) {

				if ( $this->debug && $echo ) echo sprintf("size -rf `%s`\n", $path);

				$dir = opendir($full_path);
				while ( ($entry = readdir($dir)) !== false) { 

					if ( $entry != "." && $entry != ".." ) {

						if ( @is_dir($full_path.$entry) ) {
							$size += $this->_size($path.$entry."/");
						} else {
							$size += (int)@ftp_size($this->connection, $path.$entry);
						}

					}

				}

				closedir($dir);
				return $size;

			} else {
				if ( $this->debug && $echo ) echo sprintf("size `%s`\n", $path);
				return (int)@ftp_size($this->connection, $path);
			}

		}
		
	} 

?>