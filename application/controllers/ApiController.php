<?

	/**
	 * ApiController
	 *
	 * @package PharosPHP.Application.Controllers
	 * @author Matt Brewer
	 **/
		
	class ApiController extends ApplicationController {
		
		// Authentication Operations
		protected static $SUCCESSFUL_LOGIN	= array("code" => 1, "message" => "Successfully logged in");
		protected static $ERROR_USER_IS_INACTIVE = array("code" => 2, "message" => "This user account has been deactivated by the administrator.  If you have questions please contact support at <a href=\"mailto:%s\">%s</a>");
		protected static $ERROR_USER_WRONG_PASSWORD = array("code" => 3, "message" => "Incorrect password provided");
		protected static $ERROR_USER_INVALID_USERNAME = array("code" => 4, "message" => "Cannot find a user with that username");

		// Registration Operations
		protected static $SUCCESSFUL_REGISTER	= array("code" => 5, "message" => "Successfully registered");
		protected static $ERROR_REGISTER_INVALID_USERNAME = array("code" => 6, "message" => "Username is already registered");
		
		// Locking Account
		protected static $SUCCESSFUL_LOCK_ACCOUNT = array("code" => 7, "message" => "Your account has been locked after three invalid login attempts.  Please contact the administrator at <a href=\"mailto:innvideohelp@cooperindustries.com\">innvideohelp@cooperindustries.com</a> to re-activate this account. Thank you.");
		
		// Password Reset
		protected static $SUCCESSFUL_PASSWORD_RESET = array("code" => 8, "message" => "Your password has been reset and emailed to the email address you registered");
		protected static $FAILED_PASSWORD_RESET = array("code" => 9, "message" => "Unable to reset your password. Please see an administrator for help.");
		protected static $FAILED_PASSWORD_RESET_USER_NOT_FOUND = array("code" => 10, "message" => "Unable to reset your password. Username was not found.");
		
		protected $level;
		protected $states;
		protected $password_reset_email;
		protected $fields = array('user_username', 'user_primary_email', 'user_dob', 'user_address', 'user_state', 'user_zip', 'user_phone');
		
		
		public function __construct() {
			parent::__construct();
			
			$this->level = Settings::get('application.users.levels.basic');
			$this->states = states_array();
			
			$this->title = "Adobe AIR Integration";
			$this->auth->login_required(false);
			
			$this->password_reset_email = Settings::get("application.email.password_reset");
						
		}
		
		
		public function index() {
			throwErrorXML("Direct Access Not Allowed");
		}
		
		/**
		 * register
		 *
		 * @param string $sample ("true"|"false")
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function register($sample="false") {
			
			if ( $sample == "true" ) {

				responseXML(false,"",$dom,$root);
				
				$holder = $dom->createElement("possible_messages");
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$SUCCESSFUL_REGISTER['code']);		
				$el->appendChild($dom->createCDATASection(self::$SUCCESSFUL_REGISTER['message']));
				$holder->appendChild($el);
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$ERROR_REGISTER_INVALID_USERNAME['code']);		
				$el->appendChild($dom->createCDATASection(self::$ERROR_REGISTER_INVALID_USERNAME['message']));
				$holder->appendChild($el);
								
				$root->appendChild($holder);
				
						
				$user = $dom->createElement("user");
				foreach($this->fields as $f) {
					$el = $dom->createElement($f);
					if ( $f == "user_state" ) {
						$f = "two letter abbrev!";
					}
					$el->appendChild($dom->createCDATASection($f));
					$user->appendChild($el);
				}
				
				$root->appendChild($user);
				printXML($dom->saveXML());
				
			}	
			
			if ( ($inputXML = sanitize_incoming_xml()) != "" ) {

				if ( ($XML = DOMDocument::loadXML($inputXML)) !== false ) {

					$user = $XML->getElementsByTagName("user")->item(0);
						
					responseXML(false,"",$dom,$root);
					$message = $dom->createElement("status");
					$status = self::$SUCCESSFUL_REGISTER;
					
					$fields = array();
					foreach($this->fields as $f) {
						$fields[$f] = $user->getElementsByTagName($f)->item(0)->nodeValue;
					}
										
					$this->fields[] = "user_username";
					$fields['user_username'] = $fields['user_primary_email'];
					
					$sql = sprintf("SELECT * FROM `users` WHERE (`user_username` = '%s' OR `user_primary_email` = '%s') AND `user_level` = '%d' LIMIT 1", $this->db->prepare_input($fields['user_primary_email']), $this->db->prepare_input($fields['user_primary_email']), $this->level);
					$info = $this->db->Execute($sql);
					if ( !$info->EOF ) {
					 	$status = self::$ERROR_REGISTER_INVALID_USERNAME;
					} else {
						
						$new_password = Authentication::random_password();
						$sql = sprintf("INSERT INTO `users` (`%s`,`user_password`,`registered_ip_address`,`date_added`,`last_updated`) VALUES('%s','%s','%s',NOW(),NOW())", implode("`,`", $this->fields), implode("','", $fields), Authentication::hash_password($new_password), Input::server("REMOTE_ADDR",""));
						$this->db->Execute($sql);
						$id = $this->db->insert_ID();
						
						$el = $dom->createElement("user");
						$el->setAttribute("id", $id);
						
						$username = $dom->createElement("username");
						$password = $dom->createElement("password");
						
						$username->appendChild($dom->createCDATASection($fields['user_username']));
						$password->appendChild($dom->createCDATASection($new_password));
						
						$el->appendChild($username);
						$el->appendChild($password);
						$root->appendChild($el);
						
					}
										
					$message->setAttribute("code", $status['code']);
					$message->appendChild($dom->createCDATASection($status['message']));
					$root->appendChild($message);
					printXML($dom->saveXML());
						
				} else throwErrorXML("Error parsing XML!");

			} else throwErrorXML("XML was blank!");
			
		}
		
		
		/**
		 * authenticate
		 *
		 * @param string $sample ("true"|"false")
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function authenticate($sample="false") {
			
			if ( $sample == "true" ) {
				responseXML(false,"",$dom,$root);
				
				$holder = $dom->createElement("possible_messages");
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$SUCCESSFUL_LOGIN['code']);		
				$el->appendChild($dom->createCDATASection(self::$SUCCESSFUL_LOGIN['message']));
				$holder->appendChild($el);
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$ERROR_USER_IS_INACTIVE['code']);		
				$el->appendChild($dom->createCDATASection(sprintf(self::$ERROR_USER_IS_INACTIVE['message'], $this->password_reset_email, $this->password_reset_email)));
				$holder->appendChild($el);
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$ERROR_USER_WRONG_PASSWORD['code']);		
				$el->appendChild($dom->createCDATASection(self::$ERROR_USER_WRONG_PASSWORD['message']));
				$holder->appendChild($el);
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$ERROR_USER_INVALID_USERNAME['code']);		
				$el->appendChild($dom->createCDATASection(self::$ERROR_USER_INVALID_USERNAME['message']));
				$holder->appendChild($el);
				
				$root->appendChild($holder);
				
						
				$el = $dom->createElement("user");
				$el->setAttribute("id", 1);
				$el->setAttribute("name", "Matt Brewer");
				
				$name = $dom->createElement("username");
				$pass = $dom->createElement("password");
				
				$name->appendChild($dom->createCDATASection("matt@dmgx.com"));
				$pass->appendChild($dom->createCDATASection("password"));
				
				$el->appendChild($name);
				// $el->appendChild($pass);
				$root->appendChild($el);
				
				printXML($dom->saveXML());
			}

			if ( ($inputXML = sanitize_incoming_xml()) != "" ) {

				if ( ($XML = DOMDocument::loadXML($inputXML)) !== false ) {

					$user = $XML->getElementsByTagName("user")->item(0);
						
					responseXML(false,"",$dom,$root);
					$message = $dom->createElement("status");
					$status = self::$SUCCESSFUL_LOGIN;
					
					$username = $user->getElementsByTagName('username')->item(0)->nodeValue;
					$password = Authentication::hash_password($user->getElementsByTagName('password')->item(0)->nodeValue);

					$sql = sprintf("SELECT * FROM `users` WHERE (`user_username` = '%s' OR `user_primary_email` = '%s') AND `user_level` = '%d' LIMIT 1", $this->db->prepare_input($username), $this->db->prepare_input($username), $this->level);
					$info = $this->db->Execute($sql);
					if ( !$info->EOF ) {
						
						if ( $info->fields['user_is_active'] !== 'true' ) {
							$status['code'] = self::$ERROR_USER_IS_INACTIVE['code'];
							$status['message'] = sprintf(self::$ERROR_USER_IS_INACTIVE['message'], $this->password_reset_email, $this->password_reset_email);								
						} //else $status = self::$ERROR_USER_WRONG_PASSWORD;								
						
						$el = $dom->createElement("user");
						$el->setAttribute("id", $info->fields['user_id']);
						$el->setAttribute("name", $info->fields['user_first_name']. ' ' . $info->fields['user_last_name']);
							
						$name = $dom->createElement("username");
						$name->appendChild($dom->createCDATASection($info->fields['user_primary_email']));
						$el->appendChild($name);
						
						$root->appendChild($el);
						
					} else $status = self::$ERROR_USER_INVALID_USERNAME;
					
					
					$message->setAttribute("code", $status['code']);
					$message->appendChild($dom->createCDATASection($status['message']));
					$root->appendChild($message);
										
					printXML($dom->saveXML());
					
				} else throwErrorXML("Error parsing XML!");

			} else throwErrorXML("XML was blank!");
			
		}
		
		
		/**
		 * lockAccount
		 *
		 * @param string $sample ("true"|"false")
		 *
		 * @return void
		 * @author Matt Brewer
		 **/
		
		public function lockAccount($sample="false") {
			
			if ( $sample == "true" ) {
				responseXML(false,"",$dom,$root);
				
				$holder = $dom->createElement("possible_messages");
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$SUCCESSFUL_LOCK_ACCOUNT['code']);		
				$el->appendChild($dom->createCDATASection(self::$SUCCESSFUL_LOCK_ACCOUNT['message']));
				$holder->appendChild($el);
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$ERROR_USER_INVALID_USERNAME['code']);		
				$el->appendChild($dom->createCDATASection(self::$ERROR_USER_INVALID_USERNAME['message']));
				$holder->appendChild($el);
				
				$root->appendChild($holder);
				
						
				$el = $dom->createElement("user");
				$el->setAttribute("id", 1);
				
				$name = $dom->createElement("username");
				
				$name->appendChild($dom->createCDATASection("matt@dmgx.com"));
				
				$el->appendChild($name);
				$root->appendChild($el);
				
				printXML($dom->saveXML());
			}

			if ( ($inputXML = sanitize_incoming_xml()) != "" ) {

				if ( ($XML = DOMDocument::loadXML($inputXML)) !== false ) {

					$user = $XML->getElementsByTagName("user")->item(0);
						
					responseXML(false,"",$dom,$root);
					$message = $dom->createElement("status");
					$status = self::$SUCCESSFUL_LOCK_ACCOUNT;
					
					$username = $user->getElementsByTagName('username')->item(0)->nodeValue;

					$sql = sprintf("SELECT * FROM `users` WHERE (`user_username` = '%s' OR `user_primary_email` = '%s') AND `user_level` = '%d' LIMIT 1", $this->db->prepare_input($username), $this->db->prepare_input($username), $this->level);
					$info = $this->db->Execute($sql);
					if ( $info->EOF ) {
						$status = self::$ERROR_USER_INVALID_USERNAME;
					} else {
						$sql = sprintf("UPDATE `users` SET `user_is_active` = 'false' WHERE (`user_username` = '%s' OR `user_primary_email` = '%s') AND `user_level` = '%d' LIMIT 1", $this->db->prepare_input($username), $this->db->prepare_input($username), $this->level);
						$this->db->Execute($sql);
					}
					
					$message->setAttribute("code", $status['code']);
					$message->appendChild($dom->createCDATASection($status['message']));
					$root->appendChild($message);
					printXML($dom->saveXML());
					
				} else throwErrorXML("Error parsing XML!");

			} else throwErrorXML("XML was blank!");
			
		}
		
		
		/**
		 * track
		 *
		 * @param string $sample ("true"|"false")
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function track($sample="false") {
			
			$this->load->klass("TrackingEntry");
			
			if ( $sample == "true" ) {
				printXML(TrackingEntry::sample_xml());
			}
			
			// Create an xml parser, printing error if we can't
			if ( !($xmlparser = xml_parser_create()) ) {
				throwErrorXML("Unable to create xml parser!");
			}

			if ( ($inputXML = sanitize_incoming_xml()) != "" ) {

				if ( ($XML = DOMDocument::loadXML($inputXML)) !== false ) {

					$track = $XML->getElementsByTagName("track");
					foreach($track as $t) {
						$entry = new TrackingEntry($t);
						$entry->insert();
						unset($entry);
					}

					responseXML(false,"",$dom,$root);
					printXML($dom->saveXML());

				} else throwErrorXML("Error parsing XML!");

			} else throwErrorXML("XML was blank!");		
			
		}
		
		
		/**
		 * passwordReset
		 *
		 * @param string $sample ("true"|"false")
		 *
		 * @return void
		 * @author Matt Brewer
		 **/

		public function resetPassword($sample="false") {
			
			if ( $sample == "true" ) {
				responseXML(false,"",$dom,$root);

				$holder = $dom->createElement("possible_messages");

				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$SUCCESSFUL_PASSWORD_RESET['code']);		
				$el->appendChild($dom->createCDATASection(self::$SUCCESSFUL_PASSWORD_RESET['message']));
				$holder->appendChild($el);

				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$FAILED_PASSWORD_RESET['code']);		
				$el->appendChild($dom->createCDATASection(self::$FAILED_PASSWORD_RESET['message']));
				$holder->appendChild($el);
				
				$el = $dom->createElement("status");
				$el->setAttribute("code", self::$FAILED_PASSWORD_RESET_USER_NOT_FOUND['code']);		
				$el->appendChild($dom->createCDATASection(self::$FAILED_PASSWORD_RESET_USER_NOT_FOUND['message']));
				$holder->appendChild($el);

				$root->appendChild($holder);

				$el = $dom->createElement("user");
				$el->setAttribute("id", 1);

				$name = $dom->createElement("username");
				$name->appendChild($dom->createCDATASection("matt@dmgx.com"));
				$el->appendChild($name);
				
				$root->appendChild($el);

				printXML($dom->saveXML());
			}

			if ( ($inputXML = sanitize_incoming_xml()) != "" ) {

				if ( ($XML = DOMDocument::loadXML($inputXML)) !== false ) {

					$user = $XML->getElementsByTagName("user")->item(0);

					responseXML(false,"",$dom,$root);
					$message = $dom->createElement("status");
					$status = self::$SUCCESSFUL_PASSWORD_RESET;

					$username = $user->getElementsByTagName('username')->item(0)->nodeValue;
					
					$this->load->module("rmail");

					$info = $this->db->Execute("SELECT * FROM users WHERE (user_username = '$username' OR user_primary_email = '$username') AND user_level = '".Settings::get("application.users.levels.basic")."' LIMIT 1");
					if ( $info->fields['user_primary_email'] != '' ) {
						if ( !Authentication::get()->reset_password($username) ) { 
							$status = self::$FAILED_PASSWORD_RESET;
						} 
					} else {
						$status = self::$FAILED_PASSWORD_RESET_USER_NOT_FOUND;
					}
								
					$message->setAttribute("code", $status['code']);
					$message->appendChild($dom->createCDATASection($status['message']));
					$root->appendChild($message);
					
					$pass = $dom->createElement("password");
					$pass->appendChild($dom->createCDATASection($new_password));
					$root->appendChild($pass);
					
					printXML($dom->saveXML());

				} else throwErrorXML("Error parsing XML!");

			} else throwErrorXML("XML was blank!");
			
		}
		
		

		public function update() {
			$this->output->layout = "empty";
			$this->output->view(file_get_contents(UPLOAD_URL.'xml/update.xml'));
			$this->output->header("Content-Type: text/xml");
			$this->output->cache(1 * Cache::WEEKS);	
		}
		
		public function meta() {			
			$this->output->layout = "empty";
			$this->output->view(self::meta_xml());
			$this->output->header("Content-Type: text/xml");
			$this->output->cache(1 * Cache::WEEKS);
		}
		
		public function version() {
			$this->output->layout = "empty";
			$this->output->view(file_get_contents(UPLOAD_URL.'xml/version.xml'));
			$this->output->header("Content-Type: text/xml");
			$this->output->cache(1 * Cache::WEEKS);
		}
	
		public function content() {
			$this->output->layout = "empty";
			$this->output->view(file_get_contents(UPLOAD_URL.'xml/content.xml'));
			$this->output->header("Content-Type: text/xml");
			$this->output->cache(1 * Cache::WEEKS);
		}
		
		protected static function meta_xml() {
			
			global $db;
			responseXML(false,'',$dom,$root);
			
			// All the states in the system
			$states = array_flip(states_array());
			foreach($states as $short => $long) {
				$el = $dom->createElement("state");
				$el->setAttribute("id", $short);
				$el->appendChild($dom->createCDATASection($long));
				$root->appendChild($el);
			}
			
			// All the content types in the system
			$sql = sprintf("SELECT * FROM content_types ORDER BY type_id");
			for ( $info = $db->Execute($sql); !$info->EOF; $info->moveNext() ) {
				$el = $dom->createElement("content_type");
				$el->setAttribute("id", $info->fields['type_id']);
				$el->appendChild($dom->createCDATASection($info->fields['type_name']));
				$root->appendChild($el);
			}	
			
			return $dom->saveXML();
			
		}
		
	}

?>