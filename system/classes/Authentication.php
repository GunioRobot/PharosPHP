<?

	/**
	 * 
	 * Authentication framework
	 *
	 * @package PharosPHP.Core.Classes
	 * @author Matt Brewer
	 *
	 **/

	require_once CLASSES_DIR.'Cookie.php';
	class Authentication {
				
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


		public function __construct() {
			
			if ( self::$instance ) return self::$instance;
			
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
				
				Cookie::set("pharos_authentication[uid]", $this->user->user_id, Settings::get("users.login_interval") * 60 + time());				
				Cookie::set("pharos_authentication[name]", $this->user->user_first_name." ".$this->user->user_last_name, Settings::get("users.login_interval") * 60 + time());
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
		 * Mails the user a new password and redirects to the success/fail page
		 *
		 * @param string $username
		 * @return void
		 * @author Matthew
		 **/
		public function reset_password($username) {

			Modules::load("rmail");

			$info = $this->db->Execute("SELECT * FROM users WHERE user_username = '$username' LIMIT 1");
			if ( $info->fields['user_primary_email'] != '' ) {

				$new_password = random_password();
				$db->Execute("UPDATE users SET user_password = '".$new_password."', last_updated = NOW() WHERE user_id = '".$info->fields['user_id']."' LIMIT 1");

				$html = '<html><body>';
				$html .= '<h2>Password Reset</h2>';
				$html .= 'You requested to have your password reset.  If you did not make the request, please user the system administrator at: <a href="mailto:'.SYS_ADMIN_EMAIL.'?subject=Bad Password Reset">'.SYS_ADMIN_EMAIL.'</a>';
				$html .= '<br /><br /><hr>';
				$html .= 'Your new password is: <strong>'.$new_password.'</strong><br />';
				$html .= 'Login at <a href="'.Template::site_link().'">'.Template::site_link().'</a> with your username and new password.';
				$html .= '</body></html>';

				$mail = new Rmail();
				$mail->setFrom(SERVER_MAILER);
				$mail->setSubject(Settings::get('system.site.name').': Password Reset');
				$mail->setPriority('high');
				$mail->setHTML($html);

				if ( !$mail->send(array($info->fields['user_primary_email'])) ) redirect(site_link('passwordResetFailed/0/'));
				else redirect(Template::controller_link('AdminSessionController', 'passwordSuccessfullyReset/'));

			} else redirect(Template::controller_link('AdminSessionController', 'passwordResetFailed/1/'));
			
		}
		
		
		/**
		 * random_password()
		 *
		 * @return string new_password
		 * @author Matthew
		 **/
		public function random_password() {
			return str_replace('_', '', substr(rand(0,100).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).chr(rand(65,117)).rand(50,100).RESET_PASSWORD_RANDOM_WORD, 0, 49));	// Limit the password to 50 max characters (all the database holds)
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
				$sql = sprintf("SELECT * FROM users WHERE user_id = '%d' AND logged_in = 'true' AND DATE_ADD(user_last_login, INTERVAL %d MINUTE) >= NOW() LIMIT 1", $uid, Settings::get("users.login_interval"));
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