<?

	/**
	 * 
	 * Authentication framework
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 *
	 **/

	final class Authentication {
				
		protected $logged_in = false;
		protected $login_required = false;
		protected $user;
		protected $db;
		
		static protected $instance;

		/**
		 * global()
		 * 
		 * Static accessor to the session Authentication object
		 *
		 * @return Authentication obj 
		 * @author Matthew
		 **/
		public static function get() {		
			if ( !self::$instance ) {
				$t = new Authentication();
				$t->lookup();
				self::$instance = $t;
			} return self::$instance;
		}


		protected function __construct() {
						
			global $db;
			$this->db =& $db;
			
		}


		/**
		 * user($u)
		 *
		 * Will return the stored user object if called with no parameters.
		 * Otherwise takes the specified parameter and stores internally
		 *
		 * @param object user (optional) - expects a clean_object($database_info->fields) param
		 * @return void
		 * @author Matthew
		 **/
		public function user($u=false) {
			
			if ( $u !== false ) {
				$this->user = $u;
				$this->logged_in = true;
			} else {
				return $this->user;
			}
			
		}
		
		
		/**
		 * login()
		 *
		 * Takes the three provided params and validates against the database 
		 *
		 * @param string $username
		 * @param string $password
		 * @param int $level
		 * @param string $comparison_operator - (>, <, <=, >=)
		 * 
		 * @return bool - true if the login was successful
		 * @author Matthew
		 **/
		public function login($username, $password, $level, $comparison_operator) {
			
			$sql = sprintf("SELECT * FROM users WHERE user_username = '%s' AND user_password = '%s' AND user_level %s '%d' LIMIT 1", $this->db->prepare_input($username), $this->db->prepare_input($password), $comparison_operator, $level);
			$info = $this->db->Execute($sql);
						
			if ( !$info->EOF && $info->fields['user_id'] > 0 ) {
				
				$this->logged_in = true;
				unset($info->fields['user_password']);
				$this->user(clean_object($info->fields));
				
				$this->db->Execute(sprintf("UPDATE users SET user_last_login = NOW(), logged_in = 'true' WHERE user_id = '%d' LIMIT 1", $this->user->user_id));
				
				$duration = Settings::get("application.users.login_interval") * 60 + time();
				Cookie::set("pharos_authentication[uid]", $this->user->user_id, $duration);				
				Cookie::set("pharos_authentication[name]", $this->user->user_first_name." ".$this->user->user_last_name, $duration);
				define('SECURITY_LVL', $this->user->user_level);

				return true;
				
			} else return false;
			
		}
		
		public function logout() {
			$this->db->Execute(sprintf("UPDATE users SET last_logout = NOW(), logged_in = 'false' WHERE user_id = '%d' LIMIT 1", $this->user->user_id));
			Cookie::delete("pharos_authentication");
			$this->logged_in = false;
		}
		
		
		/**
		 * logged_in
		 *
		 * If the user is currently logged in
		 * 
		 * @return bool
		 * @author Matthew
		 **/		
		public function logged_in() {
			return $this->logged_in;
		}
		
		
		/**
		 * login_required
		 *
		 * If this :controller & :action require login
		 *
		 * @return bool
		 * @param bool - set if the login is required or not
		 * @author Matthew
		 **/
		public function login_required($bool=null) {
			if ( is_bool($bool) ) $this->login_required = $bool;
			else return $this->login_required;
		}
		
		
		/**
		 * reset_password($username)
		 *
		 * @param string $username
		 * @param bool $send_email
		 * 
		 * @return void
		 * @author Matthew
		 **/
		public function reset_password($username, $send_email=true) {

			Modules::load("rmail");

			$info = $this->db->Execute("SELECT * FROM users WHERE user_username = '$username' LIMIT 1");
			if ( $info->fields['user_primary_email'] != '' ) {

				$new_password = self::random_password();
				$this->db->Execute("UPDATE users SET user_password = '".$new_password."', last_updated = NOW() WHERE user_id = '".$info->fields['user_id']."' LIMIT 1");

				if ( $send_email ) {
			
					// Create our default HTML
					try {
						$from = Settings::get("application.email.password_reset");
					} catch(Exception $e) {
						$from = SYS_ADMIN_EMAIL;
					}

					$login_page = Template::site_link();
					$html = <<<HTML
					<html>
						<body>
							<h2>Password Reset</h2>
							<p>You requested to have your password reset. If you did not make this request, please email the system administrator at: <a href="mailto:$from?subject=Invalid Password Reset Request">$from</a></p>
							<br /><hr /><br />
							<p>Your new password is: <strong>$new_password</strong><br /></p>
							<p>Login at <a href="$login_page">$login_page</a> with your username and new password.</p>
						</body>
					</html>
HTML;
				
					// Run the HTML through a filter
					$html = Hooks::execute(Hooks::FILTER_PASSWORD_RESET_EMAIL_HTML, array("value" => $html, $new_password));
					$subject = Hooks::execute(Hooks::FILTER_PASSWORD_RESET_EMAIL_SUBJECT, array("value" => Settings::get('application.system.site.name').': Password Reset'));

					$mail = new Rmail();
					$mail->setFrom($from);
					$mail->setSubject($subject);
					$mail->setPriority('high');
					$mail->setHTML($html);
					
				}
				
				if ( !$mail->send(array($info->fields['user_primary_email'])) ) return 0;
				else return true;

			} else return 1;
			
		}
		
		
		/**
		 * random_password()
		 *
		 * @return string new_password
		 * @author Matthew
		 **/
		public static function random_password() {
			$value = substr(md5(time()),0,15);
			return Hooks::execute(Hooks::FILTER_PASSWORD_RANDOM_GENERATE, compact("value"));
		}
		
		
		/**
		 * lookup()
		 *
		 * @return boolean true if user is logged in
		 * @author Matthew
		 **/
		protected function lookup() {
			
			if ( ($user = Cookie::get("pharos_authentication")) !== false ) {
								
				$uid = $user["uid"];
				$sql = sprintf("SELECT * FROM users WHERE user_id = '%d' AND logged_in = 'true' AND DATE_ADD(user_last_login, INTERVAL %d MINUTE) >= NOW() LIMIT 1", $uid, Settings::get("application.users.login_interval"));
				$info = $this->db->Execute($sql);

				if ( !$info->EOF && $info->fields['user_id'] > 0 ) {

					$this->logged_in = true;
					unset($info->fields['user_password']);
					$this->user(clean_object($info->fields));

					define('SECURITY_LVL', $this->user->user_level);

					return true;

				} else {
					$this->logout();
					return false;
				}
				
			} else return false;
			
		}
		
		
		
	}

?>